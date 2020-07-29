<?php

namespace Ntrx\UserBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EmailConfirmationControllerTest extends WebTestCase
{
	static $client;
	static $em;


	public static function setUpBeforeClass(): void {
		self::$client = self::createClient();
		$em = self::$container->get('doctrine')->getManager();

		$schemaTool = new SchemaTool($em);
		$metadata = $em->getMetadataFactory()->getAllMetadata();
		$schemaTool->dropSchema($metadata);
		$schemaTool->createSchema($metadata);

		$userClass = self::$container->getParameter('ntrx_user.user_class');
		$user = new $userClass;
		$user->setEmail('test@example.com');
		$user->setPlainPassword('password');
		$user->setEnabled();
		$em->persist($user);
		$em->flush();
	}


	public function test() {
		$this->assertTrue(true);
	}
}
