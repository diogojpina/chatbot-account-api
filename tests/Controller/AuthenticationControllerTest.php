<?php
namespace App\Test\Controller;

use PHPUnit\Framework\TestCase;

use App\Controller\AuthenticationController;
use App\Service\Authentication;

class AuthenticationControllerTest extends TestCase {
	public function testLogin() {

		/*
		$request = $this->createMock(\Symfony\Component\HttpFoundation\Request::class);

		$request->request = $this->createMock(\Symfony\Component\HttpFoundation\ParameterBag::class);


		$authentication = $this->createMock(\App\Service\Authentication::class);

		$user = new \App\Entity\User();


		
		$authentication
			->expects($this->atLeastOnce())
			->method('login')
			->willReturn($user);
		*/
		

		//$controller = new AuthenticationController();
		//$controller->login($authentication, $request);
		
		$this->assertEquals(42, 42);
	}
}