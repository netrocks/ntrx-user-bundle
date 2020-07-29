<?php

namespace Ntrx\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Email confirmation controller.
 */
class EmailConfirmationController extends AbstractController
{
	private $em;
	private $userClass;


	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
		$this->em = $em;
		$this->userClass = $params->get('ntrx_user.user_class');
	}


	/**
	 * @Route("/confirm/{token}", name="ntrx_userbundle_email_confirmation", methods={"GET"}, requirements={"token"="[A-Za-z0-9]{64}"})
	 */
	public function confirm(string $token) {
		$user = $this->em->getRepository($this->userClass)->findOneBy(['confirmationToken' => $token]);

		if (empty($user)) {
			$this->addFlash('warning', 'Confirmation token unknown');
			return $this->redirectToRoute('ntrx_userbundle_login');
		}

		$user->setConfirmationToken(null);
		$user->setEnabled(true);
		$this->em->persist($user);
		$this->em->flush();

		$this->addFlash('success', 'Email successfully confirmed');
		return $this->redirectToRoute('ntrx_userbundle_login');
	}
}
