<?php

namespace Ntrx\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder('ntrx_user');

		$treeBuilder->getRootNode()->children()
			->scalarNode('user_class')->end()
			->scalarNode('login_redirect')->end()
			->scalarNode('registration_redirect')->end()
			->scalarNode('password_reset_token_ttl')->end()
			->arrayNode('mailer')
				->children()
					->scalarNode('from_name')->end()
					->scalarNode('from_email')->end()
				->end()
			->end()
		->end();

		return $treeBuilder;
	}
}
