<?php
/**
 * Created by PhpStorm.
 * User: franc
 * Date: 25/10/2016
 * Time: 17:16
 */

namespace Cms\Bundle\AdminBundle\Tests\Command;


use Cms\Bundle\AdminBundle\Command\GenerateAdminControllerCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateAdminControllerCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $commandTester = $this->createCommandTester($this->getContainer());
        $exitCode = $commandTester->execute(array(
            'bundle_name' => 'CmsDemoBundle',
            'entity_name' => 'MyFirstEntity'
        ), array(
            'decorated' => false,
            'interactive' => false,
        ));
        $this->assertSame(0, $exitCode, 'Returns 0 in case of success');
        $this->assertRegExp('/Bundle name OK/', $commandTester->getDisplay());
    }

    /**
     * @param ContainerInterface $container
     * @param Application|null   $application
     *
     * @return CommandTester
     */
    private function createCommandTester(ContainerInterface $container, Application $application = null)
    {
        if (null === $application) {
            $application = new Application();
        }
        $application->setAutoExit(false);
        $command = new GenerateAdminControllerCommand();
        $command->setContainer($container);
        $application->add($command);
        return new CommandTester($application->find($command->getName()));
    }

    private function getContainer()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();

        return $container;
    }
}
