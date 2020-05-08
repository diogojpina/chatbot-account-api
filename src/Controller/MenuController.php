<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends ApiController {

	private function getOptions() {
		$options = array();
		$options[] = array('name' => 'Deposit', 'code' => 'deposit');
		$options[] = array('name' => 'Withdraw', 'code' => 'withdraw');
		$options[] = array('name' => 'Balance', 'code' => 'balance');

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

		foreach ($options as $optionValue) {
			if ($optionValue['code'] == $option) {
				return $this->json(['success' => true, 'data' => $optionValue]);
			}
		}


		return $this->json(['success' => false, 'message' => 'Option not found!']);
	}


}