<?php

namespace Ntrx\UserBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class UserProvider implements UserProviderInterface
{
	private $em;
	private $userClass;


	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
		$this->em = $em;
		$this->userClass = $params->get('ntrx_user.user_class');
	}


	public function loadUserByUsername(string $username): UserInterface {
		$user = $this->em->getRepository($this->userClass)->findOneBy(['email' => $username]);

		if (!$user instanceof $this->userClass) throw new UsernameNotFoundException('Username does not exist.');
		if (!empty($user->getConfirmationToken())) throw new DisabledException('Email confirmation is pending.');
		if ($user->getEnabled()) throw new DisabledException('User locked.');

		return $user;
	}


	public function refreshUser(UserInterface $user): void {
		if (!$user instanceof $this->userClass) throw new UnsupportedUserException('Username does not exist.');
	}


	public function supportsClass(string $class): bool {
		return $this->userClass === $class;
	}
}
