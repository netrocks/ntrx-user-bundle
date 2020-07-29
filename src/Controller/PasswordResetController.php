<?php

namespace Ntrx\UserBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Ntrx\UserBundle\Form\PasswordRequestType;
use Ntrx\UserBundle\Form\PasswordResetType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Password reset controller.
 */
class PasswordResetController extends AbstractController
{
	private $em;
	private $mailer;
	private $params;
	private $userClass;


	public function __construct(EntityManagerInterface $em, MailerInterface $mailer, ParameterBagInterface $params) {
		$this->em = $em;
		$this->mailer = $mailer;
		$this->params = $params;
		$this->userClass = $params->get('ntrx_user.user_class');
	}


	/**
	 * @Route("/password/request", name="ntrx_userbundle_password_request", methods={"GET","POST"})
	 */
	public function request(Request $request) {
		$form = $this->createForm(PasswordRequestType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$formData = $form->getData();
			$user = $this->em->getRepository($this->userClass)->findOneBy(['email' => $formData['email']]);

			if ($user) {
				$confirmationToken = null;

				do {
					$confirmationToken = $this->generateRandomString();
					$users = $this->em->getRepository($this->userClass)->findBy(['confirmationToken' => $confirmationToken]);
				} while (count($users) > 0);

				$user->setPasswordResetToken($confirmationToken);
				$user->setPasswordResettedAt(new \DateTime());
				$this->em->persist($user);
				$this->em->flush();

				$mail = new TemplatedEmail();
				$mail->from(new Address($this->params->get('ntrx_user.mailer.from_email'), $this->params->get('ntrx_user.mailer.from_name')));
				$mail->to($user->getEmail());
				$mail->subject('Password reset');
				$mail->htmlTemplate('@NtrxUser/mailer/password_reset.html.twig');
				$mail->textTemplate('@NtrxUser/mailer/password_reset.txt.twig');
				$mail->context(['user' => $user->getEmail(), 'password_reset_token' => $user->getPasswordResetToken()]);
				$this->mailer->send($mail);
			}

			$this->addFlash('success', 'Password reset instructions successfully sended to your email address');
			return $this->render('@NtrxUser/password/request.html.twig', ['form' => $form->createView()]);
		}

		return $this->render('@NtrxUser/password/request.html.twig', ['form' => $form->createView()]);
	}


	/**
	 * @Route("/password/reset/{token}", name="ntrx_userbundle_password_reset", methods={"GET", "POST"}, requirements={"token"="[A-Za-z0-9]{64}"})
	 */
	public function reset(Request $request, string $token) {
		$user = $this->em->getRepository($this->userClass)->findOneBy(['passwordResetToken' => $token]);

		if (empty($user)){
			throw new NotFoundHttpException();
		}

		$now = new \DateTime();
		$passwordResetValidityDate = $user->getPasswordResettedAt()->add(new \DateInterval($this->params->get('ntrx_user.password_reset_token_ttl')));
		if ($now > $passwordResetValidityDate) {
			throw new NotFoundHttpException();
		}

		$form = $this->createForm(PasswordResetType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$formData = $form->getData();

			$user->setPlainPassword($formData['password']);
			$user->setPasswordResetToken(null);
			$user->setPasswordResettedAt(null);
			$this->em->persist($user);
			$this->em->flush();

			$this->addFlash('success', 'Password successfully changed');
			$this->redirectToRoute('ntrx_userbundle_login');
		}

		return $this->render('@NtrxUser/password/reset.html.twig', ['form' => $form->createView()]);
	}


	/*
	 * Private methods
	 */

	private function generateRandomString(?int $length = 64): string {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}
}
