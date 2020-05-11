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

use App\Service\ExchangeService;

class TransactionController extends ApiController {
	private $transactionRepo;
	private $transactionTypeRepo;

	private $exchangeService;

	public function __construct(TransactionRepository $transactionRepo, TransactionTypeRepository $transactionTypeRepo, ExchangeService $exchangeService) {
		$this->transactionRepo = $transactionRepo;
		$this->transactionTypeRepo = $transactionTypeRepo;

		$this->exchangeService = $exchangeService;
	}

	/**
	* @Route("/transaction/balance/{accountNumber}", name="transaction_balance", methods={"GET"})
	*/
	public function balance($accountNumber) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'message' => 'Account not found!'));
		}

		$transactionType = $this->transactionTypeRepo->getByCode('balance');
		$dateTime = new \DateTime();

		$transaction = new Transaction();
		$transaction->setAccount($account);
		$transaction->setType($transactionType);
		$transaction->setValue(0);
		$transaction->setDatetime($dateTime);

		$this->transactionRepo->add($transaction);

		$balance = $this->exchangeService->formatValue($user, $account->getBalance());
		return $this->json(array('success' => true, 'data' => $balance));
	}

	/**
	* @Route("/transaction/deposit/{accountNumber}", name="transaction_deposit", methods={"POST"})
	*/
	public function deposit($accountNumber, Request $request) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'message' => 'Account not found!'));
		}

		$request = $this->transformJsonBody($request);
		$value = $request->request->get('value', 0);

		$currencyMoney = $this->exchangeService->identifyCurrencyValue($user, $value);

		if ($currencyMoney === false) {
			return $this->json(array('success' => false, 'message' => 'Value invalid!'));
		}

		if ($currencyMoney['value'] <= 0) {
			return $this->json(array('success' => false, 'message' => 'Value has to be positive!'));
		}

		$myCurrencyMoney = $this->exchangeService->exchange($user, $currencyMoney);

		if ($myCurrencyMoney === false) {
			return $this->json(array('success' => false, 'message' => 'Problem to convert the currency!'));
		}

		$value = $myCurrencyMoney['value'];

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

		$valueFormated = $this->exchangeService->formatValue($user, $value);
		return $this->json(array('success' => true, 'data' => $transaction->toArray(), 'value' => $valueFormated));
	}

	/**
	* @Route("/transaction/withdraw/{accountNumber}", name="transaction_withdraw", methods={"POST"})
	*/
	public function withdraw($accountNumber, Request $request) {
		$user = $this->getUser();

		$account = $user->getAccountByNumber($accountNumber);
		if (!$account) {
			return $this->json(array('success' => false, 'message' => 'Account not found!'));
		}

		$request = $this->transformJsonBody($request);
		$value = $request->request->get('value', '');

		$currencyMoney = $this->exchangeService->identifyCurrencyValue($user, $value);

		if ($currencyMoney === false) {
			return $this->json(array('success' => false, 'message' => 'Value invalid!'));
		}

		if ($currencyMoney['value'] <= 0) {
			return $this->json(array('success' => false, 'message' => 'Value has to be positive!'));
		}

		$myCurrencyMoney = $this->exchangeService->exchange($user, $currencyMoney);

		if ($myCurrencyMoney === false) {
			return $this->json(array('success' => false, 'message' => 'Problem to convert the currency!'));
		}

		$value = $myCurrencyMoney['value'];

		if ($account->getBalance() < $value) {
			return $this->json(array('success' => false, 'message' => 'Insufficient funds!'));
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

		$valueFormated = $this->exchangeService->formatValue($user, $value);
		return $this->json(array('success' => true, 'data' => $transaction->toArray(), 'value' => $valueFormated));
	}

}