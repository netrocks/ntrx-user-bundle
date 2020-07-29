<?php

namespace Ntrx\UserBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserPersistanceListener
{
	private $em;
	private $passwordEncoder;
	private $userClass;


	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, UserPasswordEncoderInterface $passwordEncoder) {
		$this->em = $em;
		$this->params = $params;
		$this->userClass = $params->get('ntrx_user.user_class');
		$this->passwordEncoder = $passwordEncoder;
	}


	public function prePersist($user, LifecycleEventArgs $event) {
		$confirmationToken = null;

		do {
			$confirmationToken = $this->generateRandomString();
			$users = $this->em->getRepository($this->userClass)->findBy(['confirmationToken' => $confirmationToken]);
		} while (count($users) > 0);

		$user->setConfirmationToken($confirmationToken);
		$this->encodePlainPassword($user);
	}


	public function preUpdate($user, LifecycleEventArgs $event) {
		$this->encodePlainPassword($user);
	}


	private function encodePlainPassword($user): void {
		if (!empty($user->getPlainPassword())) {
			$encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($encodedPassword);
			$user->setPlainPassword(null);
		}
	}


	private function generateRandomString(?int $length = 64): string {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}
}
