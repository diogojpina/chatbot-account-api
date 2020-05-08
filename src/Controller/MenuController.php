<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends ApiController {

	private function getOptions() {
		$options = array();
		$options[1] = array('name' => 'Deposit', 'code' => 'deposit');
		$options[2] = array('name' => 'Withdraw', 'code' => 'withdraw');
		$options[3] = array('name' => 'Balance', 'code' => 'balance');

		return $options;
	}

	/**
	* @Route("/menu", name="menu_list", methods={"GET"})
	*/
	public function list() {
    	return $this->json(array('success' => true, 'data' => $this->getOptions()));
	}

	/**
	* @Route("/menu/chooseOption", name="menu_choose_option", methods={"POST"})
	*/
	public function chooseOption(Request $request) {
		$request = $this->transformJsonBody($request);
		$option = $request->request->get('option', '');
		$options = $this->getOptions();

		foreach ($options as $key => $optionValue) {
			if ($key == $option) {
				return $this->json(['success' => true, $key => $optionValue]);
			}
		}


		return $this->json(['success' => false, 'message' => 'Option not found!']);
	}


}