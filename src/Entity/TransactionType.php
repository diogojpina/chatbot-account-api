<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\Request;

/** @ORM\Entity
* @ORM\Table(name="operation_type")*/
class OperationType {

	/**
	* @ORM\Id()
	* @ORM\GeneratedValue(strategy="AUTO")
	* @ORM\Column(type="integer")
	*/
	private $id;

	/** @ORM\Column(type="string") */
	private $code;

	/** @ORM\Column(type="string") */
	private $name;


	/**
	* @ORM\OneToMany(targetEntity="Transaction", mappedBy="operationType")
	**/
	protected $transactions = null;	


	public function __construct() {
		$this->transactions = new ArrayCollection();	
	}

	public function getId() {
		return $this->id;
	}
			
	public function setId($id) {
		$this->id = $id;
	}

	public function getCode() {
		return $this->code;
	}
			
	public function setCode($code) {
		$this->code = $code;
	}

	public function getName() {
		return $this->name;
	}
			
	public function setName($name) {
		$this->name = $name;
	}

	public function getTransactions() {
		return $this->operations;
	}

	public function toArray() {
		$data['id'] = $this->getId();
		$data['code'] = $this->getCode();
		$data['name'] = $this->getName();
		
		return $data;
	}
}