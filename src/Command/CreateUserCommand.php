<?php

namespace Ntrx\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class CreateUserCommand extends Command
{
	protected static $defaultName = 'user:create';

	private $em;

	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
		$this->em = $em;
		$this->params = $params;
		parent::__construct();
	}

	protected function configure() {
		$this->setDescription('Creates a user.');
		$this->setHelp('This command allows you to create a user for your application. You will be ask for the e-mail address, a password, and the roles.');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$io = new SymfonyStyle($input, $output);
		$io->title('This command allows you to create a user for your application. You will be ask for the e-mail address, a password, and the roles.');

		$userClass = $this->params->get('ntrx_user.user_class');

		$email = $io->ask('What\'s the e-mail address of the new user?');
		$user = $this->em->getRepository($userClass)->findOneBy(['email' => $email]);
		if ($user) {
			$io->warning('Specified email address already registered!');
			return 0;
		}

		$password = $io->ask('What\'s the password of the new user? Leave it blank to don\'t define a password. The user will be able to set a new password by the password recovery.');
		$role = $io->ask('Which role should the user have?', 'ROLE_USER');
		$enabled = $io->confirm('Should the user be enabled?', true);
		if (!$enabled) {
			$sendConfirmation = $io->confirm('Should the user send a confimation email?', false);
		}

		$user = new $userClass;
		$user->setEmail($email);
		if ($password) {
			$user->setPlainPassword($password);
		} else {
			$user->setPlainPassword($this->generateRandomString());
		}
		if ($enabled) {
			$user->setEnabled();
			$user->setConfirmationToken(null);
		} else {
			$user->setEnabled(false);
			if ($sendConfirmation) {
				// TODO Send confimation
			}
		}
		$user->addRole($role);

		$this->em->persist($user);
		$this->em->flush();

		$io->success('User successfully created!');
		return 0;
	}


	public function generateRandomString(int $length = 64, string $characters = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ'): ?string {
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
