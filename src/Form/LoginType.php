<?php

namespace Ntrx\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class LoginType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('email', EmailType::class, [
			'required' => true,
		]);
		$builder->add('password', PasswordType::class, [
			'required' => true,
		]);
		$builder->add('save', SubmitType::class);
	}
}
