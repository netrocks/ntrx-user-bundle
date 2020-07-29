<?php

namespace Ntrx\UserBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;


class TestMailCommandTest extends KernelTestCase
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
		$command = self::$application->find('user:test-mail');
		$commandTester = new CommandTester($command);

		$commandTester->setInputs(['test@example.com', 'What\'s the recipient?', 'subject', 'body']);
		$commandTester->execute([]);

		$output = $commandTester->getDisplay();
		$this->assertStringContainsString('What\'s the sender?', $output);
		$this->assertStringContainsString('What\'s the recipient?', $output);
		$this->assertStringContainsString('What\'s the subject?', $output);
		$this->assertStringContainsString('What\'s the body?', $output);
	}
}
