<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;

class ExchangeService {
	private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function formatValue($user, $value) {
    	return $user->getCurrency() . ' ' . number_format($value, 2, '.', ',');
    }

    public function identifyCurrencyValue($user, $text) {
    	$text = trim($text);

    	if (preg_match('/^[a-zA-Z]{3}(.)*\d+(\.\d*)?$/', $text, $matches)) {
    		preg_match('/^[a-zA-Z]{3}/', $text, $matches);
    		$currencyCode = $matches[0];

    		preg_match('/\d+(\.\d*)?$/', $text, $matches);
    		$value = 1.0 * $matches[0];

    		return array('currencyCode' => $currencyCode, 'value' => $value);
    	}
    	else if (preg_match('/\d+(\.\d*)?$/', $text, $matches)) {
    		preg_match('/\d+(\.\d*)?$/', $text, $matches);
    		$value = 1.0 * $matches[0];

    		return array('currencyCode' => $user->getCurrency(), 'value' => $value);
    	}

    	return false;
    }

    public function exchange($user, $currencyMoney) {
    	if ($user->getCurrency() == $currencyMoney['currencyCode']) {
    		return $currencyMoney;
    	}
    	else {
    		$value = $this->convertMoney($currencyMoney['currencyCode'], $user->getCurrency(), $currencyMoney['value']);

    		if ($value === false) {
    			return false;
    		}
    		return array('currencyCode' => $user->getCurrency(), 'value' => $value);
    	}
    }

    public function convertMoney($currencyFrom, $currencyTo, $value) {
    	return 4 * $value;
    }

}