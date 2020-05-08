<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/** @ORM\Entity
* @ORM\Table(name="user")*/
class User implements UserInterface {
	/**
	* @ORM\Id()
	* @ORM\GeneratedValue(strategy="AUTO")
	* @ORM\Column(type="integer")
	*/
	private $id;


	/** @ORM\Column(type="string") */
	private $firstname;

	/** @ORM\Column(type="string") */
	private $lastname;

	/** @ORM\Column(type="string") */
	private $currency;

	/** @ORM\Column(type="string") */
	private $email;

	/** @ORM\Column(type="string") */
	private $password;

	/** @ORM\Column(type="string") */
	private $token;

	/** @ORM\Column(type="datetime", name="loginExpires") */
	private $loginExpires;

	/** @ORM\Column(type="string", name="isActive") */
	private $isActive;

	/**
	* @ORM\OneToMany(targetEntity="Account", mappedBy="user")
	**/
	protected $accounts;


	public function __construct() {	
		$this->accounts = new ArrayCollection();
	}


	public function getId() {
		return $this->id;
	}
			
	public function setId($id) {
		$this->id = $id;
	}

	public function getFirstname() {
		return $this->firstname;
	}
			
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}

	public function getLastname() {
		return $this->lastname;
	}
			
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}

	public function getCurrency() {
		return $this->currency;
	}
			
	public function setCurrency($currency) {
		$this->currency = $currency;
	}

	public function getEmail() {
		return $this->email;
	}

			
	public function setEmail ($email) {
		$this->email = $email;
	}

	public function getPassword() {
		return $this->password;
	}
			
	public function setPassword ($password) {
		$this->password = $password;
	}

	public function getToken() {
		return $this->token;
	}
			
	public function setToken ($token) {
		$this->token = $token;
	}

	public function getLoginExpires() {
		return $this->loginExpires;
	}
			
	public function setLoginExpires ($loginExpires) {
		$this->loginExpires = $loginExpires;
	}

	public function getIsActive() {
		return $this->isActive;
	}
			
	public function setIsActive ($isActive) {
		$this->isActive = $isActive;
	}

	public function getAccounts() {
		return $this->accounts;
	}

	public function getAccountByNumber($accountNumber) {
		foreach ($this->getAccounts() as $account) {
			if ($account->getAccountNumber() == $accountNumber) {
				return $account;
			}
		}
		return null;
	}
				
	public function addAccount($account) {
		$this->accounts->add($account);
	}

	public function removeAccount($account) {
		if ($this->accounts->contains($account)) {
			$this->accounts->removeElement($account);
		}
	}

	public function toArray() :array {
		$data['id'] = $this->getId();
		$data['firstname'] = $this->getFirstname();
		$data['lastname'] = $this->getLastname();
		$data['currency'] = $this->getCurrency();
		$data['email'] = $this->getEmail();
		$data['password'] = $this->getPassword();
		$data['token'] = $this->getToken();
		$data['loginExpires'] = $this->getLoginExpires();
		$data['isActive'] = $this->getIsActive();

		$data['accounts'] = array();
		foreach ($this->getAccounts() as $account) {
			$data['account'][] = array('id' => $account->getId(), 'accountNumber' => $account->getAccountNumber());
		}
		
		return $data;
	}

	public function getUsername(): ?string {
        return $this->email;
    }

	public function getRoles(): array {
        $permissions = array();
        
        return array_unique($permissions);
    }

    public function getSalt(): ?string {

        return null;
    }

    public function eraseCredentials(): void {      
    }
}
