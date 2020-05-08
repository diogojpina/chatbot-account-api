<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\Request;

/** @ORM\Entity
* @ORM\Table(name="transaction")*/
class Transaction {
	/**
	* @ORM\Id()
	* @ORM\GeneratedValue(strategy="AUTO")
	* @ORM\Column(type="integer")
	*/
	private $id;

	/**
	* @ORM\ManyToOne(targetEntity="Account", inversedBy="transactions")
	* @ORM\JoinColumn(name="account_id", referencedColumnName="id")
	*/
	private $account;

	/**
	* @ORM\ManyToOne(targetEntity="TransactionType", inversedBy="transactions")
	* @ORM\JoinColumn(name="type_id", referencedColumnName="id")
	*/
	private $type;

	/** @ORM\Column(type="float") */
	private $value;

	/** @ORM\Column(type="datetime") */
	private $datetime;

	/**
	* @ORM\OneToMany(targetEntity="Transaction", mappedBy="operationType")
	**/
	protected $transactions = null;	

	public function getId() {
		return $this->id;
	}
			
	public function setId($id) {
		$this->id = $id;
	}

	public function getAccount() {
		return $this->account;
	}
			
	public function setAccount($account) {
		$this->account = $account;
	}

	public function getType() {
		return $this->type;
	}
			
	public function setType($type) {
		$this->type = $type;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getDateTime() {
		return $this->datetime;
	}
	
	public function setDateTime($datetime) {
		$this->datetime = $datetime;
	}

	public function getTransactions() {
		return $this->operations;
	}

	public function toArray() {
		$data['id'] = $this->getId();
		if ($this->getAccount())
			$data['account']['id'] = $this->getAccount()->getId();
		if ($this->getType())
			$data['type']['id'] = $this->getType()->getId();
		$data['value'] = $this->getValue();

		$data['datetime'] = $this->getDateTime()->format("Y-m-d");
		$data['datetimeFormated'] = $this->getDateTime()->format("m/d/Y");
		
		return $data;
	}
}