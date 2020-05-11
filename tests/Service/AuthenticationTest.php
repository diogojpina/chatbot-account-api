<?php
namespace App\Test\Service;

use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase {
	private $user;

	protected function setUp() {		

		$this->user = new \App\Entity\User();
		$this->user->setId(1);
		$this->user->setUsername('user');
		$this->user->setPassword('pass');
		$this->user->setToken('token');

		

		
	}

	public function testLogin() {
		$entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$entityRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);

		$entityManager
			->method('getRepository')
			->willReturn($entityRepo);

		$entityRepo
			->method('findOneBy')
			->willReturn($this->user);

		$authentication = new \App\Service\Authentication($entityManager);
		$userReturned = $authentication->login('user', 'pass');


		$this->assertEquals('user', $userReturned->getUsername());
	}

	public function testLogoutSuccess() {
		$entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$entityRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);

		$entityManager
			->method('getRepository')
			->willReturn($entityRepo);

		$entityRepo
			->method('findOneBy')
			->willReturn($this->user);

		$authentication = new \App\Service\Authentication($entityManager);
		$userReturned = $authentication->logout('user', 'pass');

		$this->assertEquals(true, $userReturned);
	}

	public function testLogoutFail() {
		$entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$entityRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);

		$entityManager
			->method('getRepository')
			->willReturn($entityRepo);

		$entityRepo
			->method('findOneBy')
			->willReturn(null);

		$authentication = new \App\Service\Authentication($entityManager);
		$userReturned = $authentication->logout('user', 'pass');

		$this->assertEquals(false, $userReturned);
	}

	public function testValidateSuccess() {
		$entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$entityRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);

		$entityManager
			->method('getRepository')
			->willReturn($entityRepo);

		$entityRepo
			->method('findOneBy')
			->willReturn($this->user);

		$authentication = new \App\Service\Authentication($entityManager);
		$userReturned = $authentication->validate('user', 'pass');

		$this->assertEquals(true, $userReturned);
	}

	public function testValidateFail() {
		$entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
		$entityRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);

		$entityManager
			->method('getRepository')
			->willReturn($entityRepo);

		$entityRepo
			->method('findOneBy')
			->willReturn(null);

		$authentication = new \App\Service\Authentication($entityManager);
		$userReturned = $authentication->validate('user', 'pass');

		$this->assertEquals(false, $userReturned);
	}

}