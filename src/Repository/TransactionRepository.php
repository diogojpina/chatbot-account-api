<?php
namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

use App\Entity\Transaction;

class TransactionRepository {
	private $entityManager;
	private $repository;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Transaction::class);
	}

	public function add(Transaction $transaction) {
		$this->entityManager->persist($transaction);
 	   	$this->entityManager->flush();
	}

}