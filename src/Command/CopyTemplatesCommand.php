<?php

namespace Ntrx\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;


class CopyTemplatesCommand extends Command
{
	protected static $defaultName = 'user:copy-templates';

	public function __construct(ParameterBagInterface $params, KernelInterface $kernel) {
		$this->params = $params;
		$this->kernel = $kernel;
		parent::__construct();
	}

	protected function configure() {
		$this->setDescription('Copies the templates for the UserBundle in the host application.');
		$this->setHelp('This command allows you to copy the Ntrx UserBundle templates in your host application to overwrite them.');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$io = new SymfonyStyle($input, $output);
		$io->title('This command allows you to copy the Ntrx UserBundle templates in your host application to overwrite them.');

		$projectFolder = $this->kernel->getProjectdir();

		$confirmation = $io->confirm('Do you want to copy the templates in the folder "templates/bundles/ntrx-user-bundle/"?', true);

		if ($confirmation) {
			$bundleTemplatesFolder = $projectFolder.'/vendor/ntrx/ntrx-user-bundle/src/Resources/views';
			$hostTemplatesFolder = $projectFolder.'/templates/bundles/ntrx-user-bundle';

			$this->recurseCopy($bundleTemplatesFolder, $hostTemplatesFolder);
			$io->success('Templates successfully copied!');
			return 0;

		} else {
			$io->warning('Coping skipped!');
			return 0;
		}
	}


	private function recurseCopy(string $source, string $destination) {
		$dir = opendir($source);
		@mkdir($destination);

		while (false !== ($file = readdir($dir))) {
			if ($file != '.' && $file != '..' ) {
				if (is_dir($source.'/'.$file)) {
					$this->recurseCopy($source.'/'.$file, $destination.'/'.$file);
				} else {
					copy($source.'/'.$file, $destination.'/'.$file);
				}
			}
		}
		closedir($dir);
	}
}
