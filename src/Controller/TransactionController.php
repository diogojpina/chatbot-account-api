<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends ApiController {

	/**
	* @Route("/transaction/balance/{accountNumber}", name="transaction_balance", methods={"GET"})
	*/
	public function balance($accountNumber) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'data' => 'Account not found!'));
		}

		return $this->json(array('success' => true, 'data' => $account->getBalance()));
	}





	

}