<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Transaction;
use App\Entity\TransactionType;

use App\Repository\TransactionRepository;
use App\Repository\TransactionTypeRepository;

class TransactionController extends ApiController {
	private $transactionRepo;
	private $transactionTypeRepo;

	public function __construct(TransactionRepository $transactionRepo, TransactionTypeRepository $transactionTypeRepo) {
		$this->transactionRepo = $transactionRepo;
		$this->transactionTypeRepo = $transactionTypeRepo;
	}

	/**
	* @Route("/transaction/balance/{accountNumber}", name="transaction_balance", methods={"GET"})
	*/
	public function balance($accountNumber) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'data' => 'Account not found!'));
		}

		$transactionType = $this->transactionTypeRepo->getByCode('balance');
		$dateTime = new \DateTime();

		$transaction = new Transaction();
		$transaction->setAccount($account);
		$transaction->setType($transactionType);
		$transaction->setValue(0);
		$transaction->setDatetime($dateTime);

		$this->transactionRepo->add($transaction);

		return $this->json(array('success' => true, 'data' => $account->getBalance()));
	}

	/**
	* @Route("/transaction/deposit/{accountNumber}", name="transaction_deposit", methods={"POST"})
	*/
	public function deposit($accountNumber, Request $request) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'data' => 'Account not found!'));
		}

		$request = $this->transformJsonBody($request);
		$value = (float) $request->request->get('value', 0);

		if ($value <= 0) {
			return $this->json(array('success' => false, 'data' => 'Value has to be positive!'));
		}

		$transactionType = $this->transactionTypeRepo->getByCode('deposit');
		$dateTime = new \DateTime();

		$transaction = new Transaction();
		$transaction->setAccount($account);
		$transaction->setType($transactionType);
		$transaction->setValue($value);
		$transaction->setDatetime($dateTime);

		$newBalance = $account->getBalance() + $value;
		$account->setBalance($newBalance);

		$this->transactionRepo->add($transaction);

		return $this->json(array('success' => false, 'data' => $transaction->toArray()));
	}

	/**
	* @Route("/transaction/withdraw/{accountNumber}", name="transaction_deposit", methods={"POST"})
	*/
	public function withdraw($accountNumber, Request $request) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'data' => 'Account not found!'));
		}

		$request = $this->transformJsonBody($request);
		$value = (float) $request->request->get('value', 0);

		if ($value <= 0) {
			return $this->json(array('success' => false, 'data' => 'Value has to be positive!'));
		}

		if ($account->getBalance() < $value) {
			return $this->json(array('success' => false, 'data' => 'Insufficient funds!'));
		}

		$transactionType = $this->transactionTypeRepo->getByCode('deposit');
		$dateTime = new \DateTime();

		$transaction = new Transaction();
		$transaction->setAccount($account);
		$transaction->setType($transactionType);
		$transaction->setValue($value);
		$transaction->setDatetime($dateTime);

		$newBalance = $account->getBalance() - $value;
		$account->setBalance($newBalance);

		$this->transactionRepo->add($transaction);

		return $this->json(array('success' => false, 'data' => $transaction->toArray()));
	}

}