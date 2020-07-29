<?php

namespace Ntrx\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class NtrxUserExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container) {
		// Load services.yaml
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
		$loader->load('services.yaml');

		// Load and process configuration
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		// Set container Prameters
		$container->setParameter('ntrx_user.user_class', $config['user_class']);
		$container->setParameter('ntrx_user.login_redirect', $config['login_redirect']);
		$container->setParameter('ntrx_user.registration_redirect', $config['registration_redirect']);
		$container->setParameter('ntrx_user.password_reset_token_ttl', $config['password_reset_token_ttl']);
		$container->setParameter('ntrx_user.mailer.from_email', $config['mailer']['from_email']);
		$container->setParameter('ntrx_user.mailer.from_name', $config['mailer']['from_name']);

		// dd($container);
	}
}
