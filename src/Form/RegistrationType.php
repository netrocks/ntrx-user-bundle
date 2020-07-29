<?php

namespace Ntrx\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class RegistrationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('email', EmailType::class, [
			'required' => true,
		]);
		$builder->add('plain_password', RepeatedType::class, [
			'type' => PasswordType::class,
			'required' => true,
			'invalid_message' => 'The password fields must match.',
			'first_options' => ['label' => 'Password'],
			'second_options' => ['label' => 'Repeat Password'],
		]);
		$builder->add('save', SubmitType::class);
	}
}
