<?php

namespace Cms\Bundle\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class GenerateAdminControllerCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('cms-admin:generate:admin-controller')
            ->setDescription('Generate full admin controller base on BaseAdminController from CmsAdminBundle')
            ->setDefinition(array(
                new InputArgument('bundle_name', InputArgument::OPTIONAL, 'The bundle name. ex: CmsDemoBundle'),
                new InputArgument('entity_name', InputArgument::OPTIONAL, 'The entity name. ex: MyFirstEntity'),
            ));
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');

        $available_bundle_names = $em->getConfiguration()->getEntityNamespaces();

        $bundle_name = $input->getArgument('bundle_name');
        while (true) {
            if ($bundle_name) {
                if (!array_key_exists($bundle_name, $available_bundle_names)) {
                    $output->writeln($formatter->formatBlock('This bundle doesn\'t exist or doesn`t contain doctrine entities', 'error'));
                    $bundle_name = null;
                } else {
                    break;
                }
            }
            if (!$bundle_name) {

                $output->writeln('The bundle name. ex: CmsDemoBundle');
                $question = new Question('Fill in the bundle name : ');
                $question->setAutocompleterValues(array_keys($available_bundle_names));
                $question->setValidator(function ($bundle_name) {
                    if (empty($bundle_name)) {
                        throw new \RuntimeException('Bundle name can not be empty');
                    }
                    return $bundle_name;
                });

                $bundle_name = $helper->ask( $input, $output, $question);
            }
        }

        $output->writeln($formatter->formatBlock('Bundle name OK', 'bg=yellow;options=bold'));

        /** @var Bundle $bundle */
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle_name);
        $r = new \ReflectionClass($bundle);
        $bundle_namespace = $r->getNamespaceName();

        $available_entity_names = array();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $m */
        foreach ($meta as $m) {
            if (strpos($m->namespace, $bundle_namespace) === 0) {
                $e = preg_replace('#^'.addslashes($m->namespace.'\\').'#', '', $m->getName());
                $available_entity_names[$e] = $m->getName();
            }
        }

        $entity_name = $input->getArgument('entity_name');
        if (!$entity_name || !array_key_exists($entity_name, $available_entity_names)) {
            $output->writeln('The entity name. ex: MyFirstEntity');

            $question = new Question('Fill in the entity name : ');
            $question->setAutocompleterValues(array_keys($available_entity_names));
            $question->setValidator(function($entity_name) use ($available_entity_names) {
                if (empty($entity_name)) {
                    throw new \RuntimeException('Entity name can not be empty');
                }

                if (!array_key_exists($entity_name, $available_entity_names)) {
                    throw new \RuntimeException(sprintf('This entity could not be found, available are : %s', implode(", ", array_keys($available_entity_names))));
                }

                return $entity_name;
            });

            $entity_name = $helper->ask( $input, $output, $question);
        }

        $entity_namespace = $available_entity_names[$entity_name];

        $output->writeln($formatter->formatBlock('Entity name OK', 'bg=yellow;options=bold'));

        $guess_human_entity_name = $this->humanize($entity_name);

        $question = new Question('Fill in the human readable entity name or press enter : <comment>['.$guess_human_entity_name.']</comment> ', $guess_human_entity_name);

        $human_entity_name = $helper->ask($input, $output, $question);

        $output->writeln($formatter->formatBlock('Human entity name OK', 'bg=yellow;options=bold'));

        $guess_human_plural_entity_name = $human_entity_name.'s';

        $question = new Question('Fill in the plural human readable entity name or press enter : <comment>['.$guess_human_plural_entity_name.']</comment> ', $guess_human_plural_entity_name);

        $human_plural_entity_name = $helper->ask($input, $output, $question);

        $output->writeln($formatter->formatBlock('Plural human entity name OK', 'bg=yellow;options=bold'));

        /** @var ConfirmationQuestion $question */
        $question = new ConfirmationQuestion('<question>This entity name is feminine (y/n) ?</question> <comment>[n]</comment>', false);

        $feminine = $helper->ask($input, $output, $question);

        $human_entity_name_prefix = $this->buildEntityNamePrefix($entity_name, $feminine);

        $output->writeln("\n".$formatter->formatBlock(""
                . 'This parameters will be use to generate your controller, his translation, his routing file and his FormType :'."\n"
                . '    Bundle path : '.$bundle->getPath()."\n"
                . '    Bundle namespace : '.$bundle_namespace."\n"
                . '    Bundle name : '.$bundle_name."\n"
                . '    Entity namespace : '.$entity_namespace."\n"
                . '    Entity name : '.$entity_name."\n"
                . '    Human entity name : '.$human_entity_name."\n"
                . '    Human plural entity name : '.$human_plural_entity_name."\n"
                . '    Feminine ? : '.$feminine."\n"
                . '    [FR only] Entity name prefix : '.$human_entity_name_prefix,
                'fg=black;bg=cyan;options=bold'
            )
        );

        $question = new ConfirmationQuestion('<question>Would you like to generate your controller (y/n) ?</question> <comment>[y]</comment>', true);
        $continue = $helper->ask($input, $output, $question);

        if ($continue) {
            $output->writeln($formatter->formatBlock('Generating routing file', 'bg=yellow;options=bold'));
            $this->generateRouting($bundle->getPath(), $bundle_name, $entity_name);
            $output->writeln($formatter->formatBlock('Generating translations file', 'bg=yellow;options=bold'));
            $this->generateTranslations($bundle->getPath(), $bundle_name, $entity_name, $human_entity_name, $human_plural_entity_name, $feminine, $human_entity_name_prefix);
            $output->writeln($formatter->formatBlock('Generating FormType file', 'bg=yellow;options=bold'));
            $this->generateFormType($bundle->getPath(), $bundle_name, $entity_name, $bundle_namespace, $entity_namespace);
            $output->writeln($formatter->formatBlock('Generating Controller file', 'bg=yellow;options=bold'));
            $this->generateController($bundle->getPath(), $bundle_name, $entity_name, $bundle_namespace);

            $output->writeln($formatter->formatBlock('Don\'t forget to add the routing file to your app/config/routing.yml', 'fg=green'));
        }
    }

    protected function humanize($str) {
        $new_str = trim(preg_replace('#([A-Z])#', ' $1', $str));
        $new_str = ucwords(strtolower($new_str));
        $new_str = preg_replace('#[_\-\.]+#', ' ', $new_str);
        return $new_str;
    }

    protected function buildEntityNamePrefix($entity_name, $feminine) {
        if (preg_match('#^[aeiouy]#i', $entity_name)) {
            $str = "'";
        } else if ($feminine) {
            $str = 'a ';
        } else {
            $str = 'e ';
        }
        return $str;
    }

    protected function generateRouting($bundle_path, $bundle_name, $entity_name) {
        /* @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $skeleton_file = $kernel->locateResource('@CmsAdminBundle/Resources/skeleton/config/routing/admin_ENTITY_NAME.yml.skel');
        $skeleton_file_content = file_get_contents($skeleton_file);
        $bundle_file = $bundle_path.'/Resources/config/routing/admin_'.Container::underscore($entity_name).'.yml';
        $fs = new Filesystem();
        if (!$fs->exists($bundle_file)) {
            $fs->touch($bundle_file);
            file_put_contents($bundle_file, strtr($skeleton_file_content, array(
                '#BUNDLE_NAME#'=>$bundle_name,
                '#BUNDLE_NAME_#'=>str_replace('_bundle', '', Container::underscore($bundle_name)),
                '#ENTITY_NAME_#'=>Container::underscore($entity_name),
                '#ENTITY_NAME#'=>$entity_name
            )));
        }
    }

    protected function generateTranslations($bundle_path, $bundle_name, $entity_name, $human_entity_name, $human_plural_entity_name, $feminine, $human_entity_name_prefix) {
        /* @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $skeleton_dir = $kernel->locateResource('@CmsAdminBundle/Resources/skeleton/translations');
        $finder = new Finder();
        foreach ($finder->in($skeleton_dir) as $f) {
            $skeleton_file = $f->__toString();
            $skeleton_file_content = file_get_contents($skeleton_file);
            $filename = strtr($f->getFilename(), array(
                'BUNDLE_NAME'=>$bundle_name,
                '.skel'=>''
            ));
            $bundle_file = $bundle_path.'/Resources/translations/'.$filename;
            $fs = new Filesystem();
            $fs->touch($bundle_file);
            file_put_contents($bundle_file, strtr($skeleton_file_content, array(
                '#ENTITY_NAME_#'=>Container::underscore($entity_name),
                '#ENTITY_SINGLE_NAME#' => $human_entity_name,
                '#ENTITY_SINGLE_NAME_#' => strtolower($human_entity_name),
                '#ENTITY_PLURAL_NAME#' => $human_plural_entity_name,
                '#ENTITY_PLURAL_NAME_#' => strtolower($human_plural_entity_name),
                '#ENTITY_NAME_PREFIX_#' => $human_entity_name_prefix,
                '#FEMININE_#' => $feminine ? 'e' : ''

            )), FILE_APPEND);
        }
    }

    protected function generateFormType($bundle_path, $bundle_name, $entity_name, $bundle_namespace, $entity_namespace) {
        /* @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $skeleton_file = $kernel->locateResource('@CmsAdminBundle/Resources/skeleton/Form/Type/EntityNameFormType.php.skel');
        $skeleton_file_content = file_get_contents($skeleton_file);
        $bundle_file = $bundle_path.'/Form/Type/'.$entity_name.'FormType.php';
        $fs = new Filesystem();
        if (!$fs->exists($bundle_file)) {
            $fs->touch($bundle_file);
            file_put_contents($bundle_file, strtr($skeleton_file_content, array(
                '#BUNDLE_NAMESPACE#'=>$bundle_namespace,
                '#BUNDLE_NAME_#'=>Container::underscore($bundle_name),
                '#ENTITY_NAMESPACE#'=>$entity_namespace,
                '#ENTITY_NAME#'=>$entity_name,
                '#ENTITY_NAME_#'=>Container::underscore($entity_name)
            )));
        }
    }

    protected function generateController($bundle_path, $bundle_name, $entity_name, $bundle_namespace) {
        /* @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $skeleton_file = $kernel->locateResource('@CmsAdminBundle/Resources/skeleton/Controller/AdminEntityNameController.php.skel');
        $skeleton_file_content = file_get_contents($skeleton_file);
        $bundle_file = $bundle_path.'/Controller/Admin'.$entity_name.'Controller.php';
        $fs = new Filesystem();
        if (!$fs->exists($bundle_file)) {
            $fs->touch($bundle_file);
            file_put_contents($bundle_file, strtr($skeleton_file_content, array(
                '#BUNDLE_NAMESPACE#'=>$bundle_namespace,
                '#BUNDLE_NAME#'=>$bundle_name,
                '#ENTITY_NAME#'=>$entity_name,
            )));
        }
    }
}
