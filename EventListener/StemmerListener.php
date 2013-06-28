<?php

namespace Manhattan\PorterStemmerBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;
use Manhattan\PorterStemmerBundle\Adapter\AdapterInterface;

use Manhattan\PorterStemmerBundle\Mapping\Annotation\PorterStemmer;
use Manhattan\PorterStemmerBundle\Mapping\Annotation\Stem;

/**
 * StemmerListener
 *
 * @author James Rickard <james@frodosghost.com>
 */
class StemmerListener implements EventSubscriber
{

    /**
     * AnnotationReader
     *
     * @var Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationReader;

    /**
     * AdapterInterface
     * @var AdapterInterface
     */
    private $ormAdapter;

    /**
     * Configuration
     *
     * @var array
     */
    private $configuration;

    public function __construct(Reader $annotationReader, AdapterInterface $adapter)
    {
        $this->annotationReader = $annotationReader;
        $this->ormAdapter = $adapter;
    }

    /**
     * Specifies the list of events to listen
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
            Events::loadClassMetadata
        );
    }

    /**
     * Generate stemmed phrases and persist when content is updated
     *
     * @param EventArgs $args
     * @return void
     */
    public function onFlush(EventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $empty = $args->getEmptyInstance();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if (($config = $this->configuration) !== null) {
                $this->getOrmAdapter()->setConfiguration($config);

                $this->getOrmAdapter()->insert($em, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if (($config = $this->configuration) !== null) {
                $this->getOrmAdapter()->setConfiguration($config);

                // Remove all existing entities mapped
                $this->getOrmAdapter()->remove($em, $entity);
                // Insert new from updated content
                $this->getOrmAdapter()->insert($em, $entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if (($config = $this->configuration) !== null) {
                $this->getOrmAdapter()->setConfiguration($config);

                $this->getOrmAdapter()->remove($em, $entity);
            }
        }

        $uow->computeChangeSets();
    }

    /**
     * Loads Metadata into Object
     *
     * @param  EventArgs $args
     */
    public function loadClassMetadata(EventArgs $args)
    {
        // annotation reader gets the annotations for the class
        $reader = $this->annotationReader;
        $meta = $args->getClassMetadata();

        // the annotation reader accepts a ReflectionClass, which can be
        // obtained from the $metadata
        $class = $meta->getReflectionClass();

        if ($class instanceof \ReflectionClass) {
            $annotations = $reader->getClassAnnotations($class);
        } else {
            $annotations = false;
        }

        if ($annotations) {
            foreach ($reader->getClassAnnotations($class) as $annotation) {
                if ($annotation instanceof PorterStemmer) {
                    $this->configuration['objectClass'] = $annotation->class;
                    $this->configuration['mappedField'] = strtolower($this->getName($meta->name));

                    foreach ($class->getProperties() as $property) {
                        $propertyAnnotations = $reader->getPropertyAnnotations($property);

                        if (count($propertyAnnotations) > 0) {
                            foreach ($propertyAnnotations as $annotation) {
                                if ($annotation instanceof Stem) {
                                    $this->configuration['fields'][] = array(
                                        'name'   => $property->name,
                                        'weight' => $annotation->weight
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * Returns last part of namespace
     */
    private function getName($name)
    {
        $parts = explode('\\', $name);
        return end($parts);
    }

    public function getOrmAdapter()
    {
        return $this->ormAdapter;
    }

}
