<?php

namespace Ntrx\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * Login controller.
 */
class LoginController extends AbstractController
{
	/**
	 * @Route("/login", name="ntrx_userbundle_login", methods={"GET", "POST"})
	 */
	public function create(Request $request, AuthenticationUtils $authenticationUtils): Response {
		// if ($this->getUser()) {
		// 	return $this->redirectToRoute('TODO');
		// }

		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('@NtrxUser/login/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
	}


	/**
	 * @Route("/logout", name="ntrx_userbundle_logout", methods={"GET", "POST"})
	 */
	public function logout() {
	}
}
