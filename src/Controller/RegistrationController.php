<?php

namespace Ntrx\UserBundle\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Ntrx\UserBundle\Form\RegistrationType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Registration controller.
 */
class RegistrationController extends AbstractController
{
	private $em;
	private $mailer;
	private $params;
	private $userClass;
	private $registrationRedirect;


	public function __construct(EntityManagerInterface $em, MailerInterface $mailer, ParameterBagInterface $params) {
		$this->em = $em;
		$this->mailer = $mailer;
		$this->params = $params;
		$this->userClass = $params->get('ntrx_user.user_class');
		$this->registrationRedirect = $params->get('ntrx_user.registration_redirect');
	}


	/**
	 * @Route("/register", name="ntrx_userbundle_registration", methods={"GET", "POST"})
	 */
	public function reegister(Request $request): Response {
		$user = new $this->userClass();

		$form = $this->createForm(RegistrationType::class, $user, ['method' => 'POST']);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->persist($user);
			$this->em->flush();

			$mail = new TemplatedEmail();
			$mail->from(new Address($this->params->get('ntrx_user.mailer.from_email'), $this->params->get('ntrx_user.mailer.from_name')));
			$mail->to($user->getEmail());
			$mail->subject('Registration');
			$mail->htmlTemplate('@NtrxUser/mailer/registration.html.twig');
			$mail->textTemplate('@NtrxUser/mailer/registration.txt.twig');
			$mail->context(['user' => $user, 'confimation_token' => $user->getConfirmationToken()]);
			$this->mailer->send($mail);

			$this->addFlash('success', 'Registration successfully');
			return $this->redirectToRoute($this->registrationRedirect);
		}

		return $this->render('@NtrxUser/registration/register.html.twig', ['form' => $form->createView()]);
	}
}
