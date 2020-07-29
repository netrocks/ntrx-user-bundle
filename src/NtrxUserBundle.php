<?php

namespace Ntrx\UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Ntrx\UserBundle\DependencyInjection\NtrxUserExtension;


class NtrxUserBundle extends Bundle
{
	/**
	 * {@inheritdoc}
	 */
	public function build(ContainerBuilder $container): void {
		parent::build($container);
	}

	public function getContainerExtension() {
		return new NtrxUserExtension();
	}
}
