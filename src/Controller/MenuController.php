<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use App\Service\Authentication;


class MenuController extends ApiController {

	private function getMenuOptions() {
		$menu = array();
		$menu[1] = 'Deposit';
		$menu[2] = 'Withdraw';
		$menu[3] = 'Balance';

		return $menu;
	}

	/**
	* @Route("/menu", name="menu_list", methods={"GET"})
	*/
	public function list() {
    	return $this->json(array('success' => true, 'data' => $this->getMenuOptions()));
	}


}