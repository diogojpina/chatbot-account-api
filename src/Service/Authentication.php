<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;

class Authentication {
	private $entityManager;
	private $mailer;

    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

	public function login($email, $password) {
		$password = md5($password);

		$where = array('email' => $email, 'password' => $password, 'isActive' => true);	

		$repository = $this->entityManager->getRepository(User::class);
		$user = $repository->findOneBy($where);
		if ($user) {
			$token = md5($user->getId() . time());
			$user->setToken($token);

			$loginExpires = new \DateTime();
			$loginExpires->setTimestamp(strtotime("+6 hour"));
			$user->setLoginExpires($loginExpires);

			$this->entityManager->persist($user);
			$this->entityManager->flush();	
		}

        return $user;
	}

	public function logout($token) {
		$where = array('token' => $token);

		$repository = $this->entityManager->getRepository(User::class);
		$user = $repository->findOneBy($where);

		if ($user) {
			$user->setToken('');

			$this->entityManager->persist($user);
			$this->entityManager->flush();	

			return true;
		}

		return false;
	}

}