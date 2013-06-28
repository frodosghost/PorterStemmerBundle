<?php

namespace Manhattan\PorterStemmerBundle\Adapter\ORM;

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Manhattan\PorterStemmerBundle\Tools\PorterStemmer;
use Manhattan\PorterStemmerBundle\Adapter\AdapterInterface;

/**
 * DoctrineAdapter
 *
 * @author James Rickard <james@frodosghost.com>
 */
class DoctrineAdapter implements AdapterInterface
{
    private $configuration;

    private $porterStemmer;

    /**
     * Constructor
     *
     * @param PorterStemmer $porterStemmer
     */
    public function __construct(PorterStemmer $porterStemmer)
    {
        $this->porterStemmer = $porterStemmer;
    }

    /**
     * Remove all relationships from main table
     *
     * @param  Doctrine\ORM\EntityManager $em
     * @param  Object                     $object
     */
    public function remove($em, $object)
    {
        $meta = $em->getClassMetadata(get_class($object));
        $uow = $em->getUnitOfWork();

        $qb = $em->createQueryBuilder();

        try {
            $propertyName = $meta->getSingleIdentifierFieldName();
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            $propertyName = false;
        }

        if ($propertyName) {
            $mappedId = $meta->getReflectionProperty($propertyName)->getValue($object);

            $qb->select('node')
                ->from($this->configuration['objectClass'], 'node')
                ->where($qb->expr()->eq('node.'. $this->configuration['mappedField'], ':mappedField'))
                ->setParameter(':mappedField', null === $mappedId ?
                    'NULL' :
                    (is_string($mappedId) ? $qb->expr()->literal($mappedId) : $mappedId)
                );
            $q = $qb->getQuery();

            // get nodes for deletion
            $nodes = $q->getResult();
            foreach ((array)$nodes as $removalNode) {
                $uow->scheduleForDelete($removalNode);
            }
        }
    }

    /**
     * Insert all new phrases created
     *
     * @param  Doctrine\ORM\EntityManager $em
     * @param  Object                     $object
     */
    public function insert($em, $object)
    {
        $meta = $em->getClassMetadata(get_class($object));
        $uow = $em->getUnitOfWork();

        $words = $this->createStems($meta, $object);

        foreach ($words as $word => $weight) {
            $new = $em->getClassMetadata($this->configuration['objectClass']);
            $newEntity = $new->newInstance();

            $new->getReflectionProperty('word')->setValue($newEntity, $word);
            $new->getReflectionProperty('weight')->setValue($newEntity, $weight);
            $new->getReflectionProperty($this->getName($meta->name))->setValue($newEntity, $object);

            $uow->scheduleForInsert($newEntity);
        }
    }

    /**
     * Creates the Stemmed Words
     *
     * @param  ClassMetadata $meta
     * @param  Object        $object
     * @return array
     */
    private function createStems(ClassMetadata $meta, $object)
    {
        $arrayText = array();
        $rawText = '';
        $properties = $meta->getReflectionProperties();

        foreach ($this->configuration['fields'] as $field) {
            if (array_key_exists($field['name'], $properties)) {
                $refProperty = $meta->getReflectionProperty($field['name']);
                $fieldValue = $refProperty->getValue($object);

                // Update fieldText to format array to text
                if ($fieldValue instanceof PersistentCollection) {
                    $arrayText = $fieldValue->map(function($item) {
                        return $item->__toString();
                    });

                    $fieldValue = implode(" ", $arrayText->toArray());
                }

                $rawText .= str_repeat(' '. $fieldValue, $field['weight']);
            }
        }

        $stemmedWords = $this->porterStemmer->stemPhrase($rawText);

        return array_count_values($stemmedWords);
    }

    /**
     * Set Configuration
     *
     * @param array $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Gets current mapped name from passes Namespace
     *
     * @param  string $name Namespaced Entity class name
     * @return string
     */
    private function getName($name)
    {
        $parts = explode('\\', $name);
        $mapped = end($parts);

        return strtolower($mapped);
    }
}
