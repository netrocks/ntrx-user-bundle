<?php

namespace Ntrx\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ChangePasswordCommand extends Command
{
	protected static $defaultName = 'user:change-password';

	private $em;

	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
		$this->em = $em;
		$this->params = $params;
		parent::__construct();
	}

	protected function configure() {
		$this->setDescription('Sets a new password for a user');
		$this->setHelp('This command allows you to set a new password for a user. You will be ask for the e-mail address of the specific user and for the new password.');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$io = new SymfonyStyle($input, $output);
		$io->title('This command allows you to set a new password for a user. You will be ask for the e-mail address of the specific user and for the new password.');

		$email = $io->ask('What\'s the e-mail address of the specific user?');

		$userClass = $this->params->get('ntrx_user.user_class');
		$user = $this->em->getRepository($userClass)->findOneBy(['email' => $email]);
		if (!$user) {
			$io->warning('Specified email address not found!');
			return 0;
		}

		$randomPassword = $this->generateRandomString();
		$password = $io->ask('Define the new password.', $randomPassword);

		$user->setPassword('');
		$user->setPlainPassword($password);
		$this->em->persist($user);
		$this->em->flush();

		$io->success('Password successfully changed!');
		return 0;
	}


	private function generateRandomString(int $length = 20, string $characters = '23456789abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'): ?string {
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
