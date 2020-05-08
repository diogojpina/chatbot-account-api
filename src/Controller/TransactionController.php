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

		foreach ($user->getAccounts() as $account) {
			if ($account->getAccountNumber() == $accountNumber) {
				return $this->json(array('success' => true, 'data' => $account->getBalance()));
			}
		}

    	return $this->json(array('success' => false, 'data' => 'Account not found!'));
	}



	

}