<?php
namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

use App\Entity\User;

class UserRepository {
	private $entityManager;
	private $repository;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(User::class);
	}

	public function getByUsername($username) {
		return $this->repository->findOneBy(['username' => $username]);
	}

	public function add(User $user) {
		$this->entityManager->persist($user);
 	   	$this->entityManager->flush();
	}

}