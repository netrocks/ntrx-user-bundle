<?php

namespace Ntrx\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class TestMailCommand extends Command
{
	protected static $defaultName = 'user:test-mail';
	private $mailer;

	public function __construct(MailerInterface $mailer, ParameterBagInterface $params) {
			$this->mailer = $mailer;
			$this->params = $params;
			parent::__construct();
	}

	protected function configure() {
		$this->setDescription('Sends a test mail to a specific mail address.');
		$this->setHelp('This command allows you to send a custom test mail to a mail address. You will be ask for the sender address, the recipient, the subject and the body.');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$io = new SymfonyStyle($input, $output);
		$io->title('This command allows you to send a custom test mail to a mail address. You will be ask for the sender address, the recipient, the subject and the body.');

		$from = $io->ask('What\'s the sender?', $this->params->get('ntrx_user.mailer.from_email'));
		$to = $io->ask('What\'s the recipient?', $this->params->get('ntrx_user.mailer.from_email'));
		$subject = $io->ask('What\'s the subject?', 'Test mail');
		$body = $io->ask('What\'s the body?', 'This is a test mail.');

		try {
			$email = new Email();
			$email->from($from);
			$email->to($to);
			$email->subject($subject);
			$email->text($body);
			$this->mailer->send($email);

			$io->success('E-mail successfully sended!');
			return 0;

		} catch (\Throwable $th) {
			$io->error('E-mail not sended with the following message:');
			$io->error($th->getMessage());
			return 1;
		}

	}
}
