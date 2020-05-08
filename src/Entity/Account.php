<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\Request;

/** @ORM\Entity
* @ORM\Table(name="account")*/
class Account {
	/**
	* @ORM\Id()
	* @ORM\GeneratedValue(strategy="AUTO")
	* @ORM\Column(type="integer")
	*/
	private $id;

	/**
	* @ORM\ManyToOne(targetEntity="User", inversedBy="accounts")
	* @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	*/
	private $user;

	/** @ORM\Column(type="float") */
	private $accountNumber;

	/** @ORM\Column(type="float") */
	private $balance;

	public function getId() {
		return $this->id;
	}
			
	public function setId($id) {
		$this->id = $id;
	}

	public function getUser() {
		return $this->user;
	}
			
	public function setUser($user) {
		$this->user = $user;
	}

	public function getAccountNumber() {
		return $this->accountNumber;
	}
			
	public function setAccountNumber($accountNumber) {
		$this->accountNumber = $accountNumber;
	}

	public function getBalance() {
		return $this->balance;
	}
			
	public function setBalance($balance) {
		$this->balance = $balance;
	}

	public function toArray() {
		$data['id'] = $this->getId();
		if ($this->getUser())
			$data['user']['id'] = $this->getUser()->getId();
		$data['accountNumber'] = $this->getAccountNumber();
		$data['balance'] = $this->getBalance();
		
		return $data;
	}

}