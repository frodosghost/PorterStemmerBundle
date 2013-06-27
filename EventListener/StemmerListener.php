<?php

namespace Manhattan\PorterStemmerBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Annotations\Reader;

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
     * Configuration
     *
     * @var array
     */
    private $configuration;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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
     * Generate slug on objects being updated during flush
     * if they require changing
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

        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if (($class = $this->getClassString()) !== null) {
                if ($meta->hasAssociation($class .'s')) {

                }
            }

        }

        foreach ($uow->getScheduledEntityDeletions() AS $entity) {

        }

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

        foreach ($class->getProperties() as $property) {
            $propertyAnnotations = $reader->getPropertyAnnotations($property);

            if (count($propertyAnnotations) > 0) {
                foreach ($propertyAnnotations as $annotation) {
                    if ($annotation instanceof Stem) {
                        $this->configuration['PorterStemmer']['fields'][$property->name] = $annotation->weight;
                    }
                }
            }
        }

        foreach ($reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof PorterStemmer) {
                $this->configuration['PorterStemmer']['class'] = $annotation->class;

                $meta->mapOneToMany(array(
                    'targetEntity' => $this->configuration['PorterStemmer']['class'],
                    'fieldName' => $this->getClassString() .'s',
                    'mappedBy' => $this->getClassString()
                ));
            }
        }
    }

    /**
     * Formats classname to be lowercase string
     *
     * @return string
     */
    private function getClassString()
    {
        $loweredString = null;

        if (isset($this->configuration['PorterStemmer']['class'])) {
            $parts = explode('\\', $this->configuration['PorterStemmer']['class']);
            $mapped = end($parts);

            $loweredString = strtolower($mapped);
        }

        return $loweredString;
    }

}
