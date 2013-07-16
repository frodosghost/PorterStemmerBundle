<?php

namespace Manhattan\PorterStemmerBundle\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Manhattan\PorterStemmerBundle\Adapter\AdapterInterface;
use Manhattan\PorterStemmerBundle\Component\AnnotationParser;
use Manhattan\PorterStemmerBundle\Exception\ConfigurationException;

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
     * AnnotationParser
     *
     * @var Manhattan\PorterStemmerBundle\Component\AnnotationParser
     */
    private $annotationParser;

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

    public function __construct(AnnotationParser $annotationParser, AdapterInterface $adapter)
    {
        $this->annotationParser = $annotationParser;
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

            if ( (($config = $this->configuration) !== null) && ($config->isMappedClass($entity)) ) {
                $this->getOrmAdapter()->setConfiguration($config);

                $this->getOrmAdapter()->insert($em, $entity);
                $uow->recomputeSingleEntityChangeSet($meta, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if ( (($config = $this->configuration) !== null) && ($config->isMappedClass($entity)) ) {
                $this->getOrmAdapter()->setConfiguration($config);

                // Remove all existing entities mapped
                $this->getOrmAdapter()->remove($em, $entity);
                // Insert new from updated content
                $this->getOrmAdapter()->insert($em, $entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() AS $entity) {
            $meta = $em->getClassMetadata(get_class($entity));

            if ( (($config = $this->configuration) !== null) && ($config->isMappedClass($entity)) ) {
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
        $meta = $args->getClassMetadata();

        try {
            $configuration = $this->getAnnotationParser()
                ->configureMetadata($meta)
                ->parse();
        } catch (ConfigurationException $e) {
            $configuration = false;
        }
        if ($configuration) {
            $this->configuration = $configuration;
        }

    }

    public function getAnnotationParser()
    {
        return $this->annotationParser;
    }

    public function getOrmAdapter()
    {
        return $this->ormAdapter;
    }

}
