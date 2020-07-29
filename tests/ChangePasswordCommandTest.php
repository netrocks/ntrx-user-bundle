<?php

namespace Ntrx\UserBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class ChangePasswordCommandTest extends KernelTestCase
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

		$userClass = $kernel->getContainer()->getParameter('ntrx_user.user_class');
		$user = new $userClass;
		$user->setEmail('test@example.com');
		$user->setPlainPassword('password');
		$user->setEnabled();
		$em->persist($user);
		$em->flush();
	}


	public function testSuccess() {
		$command = self::$application->find('user:change-password');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['test@example.com', 'password']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('What\'s the e-mail address of the specific user?', $output);
		$this->assertStringContainsString('Define the new password.', $output);
		$this->assertStringContainsString('Password successfully changed!', $output);
	}


	public function testFailNotExistingEmailAddress() {
		$command = self::$application->find('user:change-password');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['not-existing-mail@example.com']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('What\'s the e-mail address of the specific user?', $output);
		$this->assertStringContainsString('Specified email address not found!', $output);
	}
}
