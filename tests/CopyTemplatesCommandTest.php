<?php

namespace Ntrx\UserBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class CopyTemplatesCommandTest extends KernelTestCase
{
	static $application;


	public static function setUpBeforeClass(): void {
		self::bootKernel();
		$kernel = self::$kernel;
		self::$application = new Application($kernel);
		$em = $kernel->getContainer()->get('doctrine')->getManager();

		$schemaTool = new SchemaTool($em);
		$metadata = $em->getMetadataFactory()->getAllMetadata();
		$schemaTool->dropSchema($metadata);
		$schemaTool->createSchema($metadata);
	}


	public function testAbort() {
		$command = self::$application->find('user:copy-templates');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['no']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('Do you want to copy the templates in the folder "templates/bundles/ntrx-user-bundle/"?', $output);
		$this->assertStringContainsString('Coping skipped!', $output);
	}
}
