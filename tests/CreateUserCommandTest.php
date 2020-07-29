<?php

namespace Ntrx\UserBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class CreateUserCommandTest extends KernelTestCase
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


	public function testSuccess() {
		$command = self::$application->find('user:create');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['test@example.com', 'ROLE_USER', 'no', 'no']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('What\'s the e-mail address of the new user?', $output);
		$this->assertStringContainsString('Which role should the user have?', $output);
		$this->assertStringContainsString('Should the user be enabled?', $output);
		$this->assertStringContainsString('Should the user send a confimation email?', $output);
		$this->assertStringContainsString('User successfully created!', $output);
	}


	public function testFailExistingEmailAddress() {
		$command = self::$application->find('user:create');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['test@example.com']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('What\'s the e-mail address of the new user?', $output);
		$this->assertStringContainsString('Specified email address already registered!', $output);
	}
}
