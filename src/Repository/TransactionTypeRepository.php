<?php
namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

use App\Entity\TransactionType;

class TransactionTypeRepository {
	private $entityManager;
	private $repository;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(TransactionType::class);
	}

	public function getByCode($code) {
		return $this->repository->findOneBy(['code' => $code]);
	}

}