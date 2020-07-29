<?php

namespace Ntrx\UserBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * NtrxUserBundle User model
 */
abstract class User implements UserInterface
{
	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 * @Assert\NotBlank
	 * @Assert\Email
	 */
	protected ?string $email = null;

	/**
	 * The hashed password
	 * @ORM\Column(type="string")
	 */
	protected ?string $password = null;
	protected ?string $plainPassword = null;


	/**
	 * @ORM\Column(type="boolean")
	 */
	protected bool $enabled = false;

	/**
	 * @ORM\Column(type="json")
	 */
	protected array $roles = [];

	/**
	 * @ORM\Column(type="string", length=64, nullable=true, unique=true)
	 */
	protected ?string $confirmationToken;

	/**
	 * @ORM\Column(type="string", length=64, nullable=true, unique=true)
	 */
	protected ?string $passwordResetToken;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected ?\DateTime $passwordResettedAt = null;



	/*
	 * Constructors and lifecycle callbacks
	 */

	public function __construct() {
	}



	/*
	 * Getter and setter methods
	 */

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = $email;
		return $this;
	}


	/**
	 * @see UserInterface
	 */
	public function getPassword(): string {
		return (string)$this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	public function getPlainPassword(): string {
		return (string)$this->plainPassword;
	}

	public function setPlainPassword(?string $plainPassword = null): self {
		$this->plainPassword = $plainPassword;
		return $this;
	}


	public function getEnabled(): bool {
		return $this->enabled;
	}

	public function setEnabled(?bool $enabled = true): User {
		$this->enabled = $enabled;
		return $this;
	}


	/**
	 * @see UserInterface
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		$roles[] = 'ROLE_USER';
		return array_unique($roles);
	}

	public function setRoles(array $roles): self {
		$this->roles = $roles;
		$roles = array_unique($roles);
		return $this;
	}

	public function addRole(string $role): self {
		$roles = $this->roles;
		if ($role != 'ROLE_USER') {
			$roles[] = $role;
		}
		$roles = array_unique($roles);
		$this->roles = $roles;
		return $this;
	}

	public function removeRole(string $role): self {
		$key = array_search($role, $this->roles);
		if ($key !== false) {
			unset($this->roles[$key]);
		}
		return $this;
	}


	public function getConfirmationToken(): ?string {
		return $this->confirmationToken;
	}

	public function setConfirmationToken(?string $confirmationToken = null): self {
		$this->confirmationToken = $confirmationToken;
		return $this;
	}


	public function getPasswordResetToken(): ?string {
		return $this->passwordResetToken;
	}

	public function setPasswordResetToken(?string $passwordResetToken = null): self {
		$this->passwordResetToken = $passwordResetToken;
		return $this;
	}


	public function getPasswordResettedAt(): \DateTime {
		return $this->passwordResettedAt;
	}

	public function setPasswordResettedAt(?\DateTime $passwordResettedAt = null): User {
		$this->passwordResettedAt = $passwordResettedAt;
		return $this;
	}



	/*
	 * Other inherited methods from UserInterface
	 */

	/**
	 * @see UserInterface
	 */
	public function getUsername(): string {
		return (string)$this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getSalt() {
		// not needed when using the "bcrypt" algorithm in security.yaml
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials() {
		// If you store any temporary, sensitive data on the user, clear it here
		$this->plainPassword = null;
	}



	/**
	 * Other entity related methods
	 */
}
