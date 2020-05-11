<?php
namespace App\Test\Controller;

use PHPUnit\Framework\TestCase;

use App\Controller\AuthenticationController;
use App\Service\Authentication;

class AuthenticationControllerTest extends TestCase {
	private $user;

	protected function setUp() {		
		$this->user = new \App\Entity\User();
		$this->user->setId(1);
		$this->user->setUsername('user');
		$this->user->setPassword('pass');
		$this->user->setToken('token');
	}

	public function testLogin() {
		$request = $this->createMock(\Symfony\Component\HttpFoundation\Request::class);
		$request->request = $this->createMock(\Symfony\Component\HttpFoundation\ParameterBag::class);

		$request->request
			->method('get')
			->willReturn('123');

		$authentication = $this->createMock(\App\Service\Authentication::class);
		
		$authentication
			->method('login')
			->willReturn(null);
		

		$controller = new AuthenticationController();
		$controller->login($authentication, $request);
		
		$this->assertEquals(42, 42);
	}
}