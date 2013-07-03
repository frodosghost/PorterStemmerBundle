<?php

namespace Manhattan\PorterStemmerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;

use Manhattan\PorterStemmerBundle\Component\AnnotationParser;

class PorterStemCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('porter:stemmer')
            ->setDescription('Runs Porter Stemmer on configured Entities.')
            ->addArgument('name', InputArgument::REQUIRED, 'Specify the Entity you wish to run the Porter Stemmer on.')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'The path where to generate entities when it cannot be guessed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $reader = $this->getContainer()->get('annotation_reader');

        $parser = $this->getContainer()->get('porter.stemmer.annotation.component');
        $adapter = $this->getContainer()->get('porter.stemmer.adapter');

        $manager = new DisconnectedMetadataFactory($this->getContainer()->get('doctrine'));

        try {
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument('name'));

            $output->writeln(sprintf('Generating entities for bundle "<info>%s</info>"', $bundle->getName()));
            $metadata = $manager->getBundleMetadata($bundle);
        } catch (\InvalidArgumentException $e) {
            $name = strtr($input->getArgument('name'), '/', '\\');

            if (false !== $pos = strpos($name, ':')) {
                $name = $this->getContainer()->get('doctrine')->getAliasNamespace(substr($name, 0, $pos)).'\\'.substr($name, $pos + 1);
            }

            if (class_exists($name)) {
                $output->writeln(sprintf('Generating entity "<info>%s</info>"', $name));
                $metadata = $manager->getClassMetadata($name, $input->getOption('path'));
            } else {
                $output->writeln(sprintf('Generating entities for namespace "<info>%s</info>"', $name));
                $metadata = $manager->getNamespaceMetadata($name, $input->getOption('path'));
            }
        }

        foreach ($metadata->getMetadata() as $m) {
            $em = $doctrine->getManagerForClass($m->name);
            $classMeta = $em->getClassMetadata($m->name);

            $configuration = $parser
                ->configureMetadata($classMeta)
                ->parse();
            if (!empty($configuration)) {
                $adapter->setConfiguration($configuration);

                $entities = $em->getRepository($m->name)->findAll();

                foreach ($entities as $entity) {
                    $output->writeln(sprintf('  > generating <comment>%s</comment>', $m->name));

                    // Remove all existing entities mapped
                    $adapter->remove($em, $entity);
                    // Insert new from updated content
                    $adapter->insert($em, $entity);
                }

                $em->flush();
            }
        }
    }

}
