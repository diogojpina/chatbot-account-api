<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

    public function convertMoney($from, $to, $amount) {
        if ($this->validateCode($from) === false || $this->validateCode($to) === false) {
            return false;
        }

        $url = "https://api.exchangeratesapi.io/latest?base=$from&symbols=$to";
        $client = new Client(['timeout'  => 10.0]);

        try {
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data = json_decode($e->getResponse()->getBody()->getContents());
                if (isset($data->error)) {
                    return false;
                }

                return false;
            }
        }

        $rate = $data->rates->$to;

        return $amount * $rate;
    }


    public function validateCode($code) {
        $defaultLifetime = 7 * 24 * 60 * 60; // 7 days
        $cache = new FilesystemAdapter('', $defaultLifetime, null); 

        $cacheKey = 'currency_' . $code;
        $item = $cache->getItem($cacheKey);
        if ($item->isHit()) {
            return true;
        }

        $this->updateCache();
        $item = $cache->getItem($cacheKey);
        if ($item->isHit()) {
            return true;
        }

        return false;
    }

    private function updateCache() {
        $url = "https://api.exchangeratesapi.io/latest?base=USD";
        $client = new Client(['timeout'  => 10.0]);
        
        try {
            $response = $client->request('GET', $url);
            $data = json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $data = json_decode($e->getResponse()->getBody()->getContents());
                if (isset($data->error)) {
                    return false;
                }

                return false;
            }
        }

        $defaultLifetime = 7 * 24 * 60 * 60; // 7 days
        $cache = new FilesystemAdapter('', $defaultLifetime, null); 

        foreach ($data->rates as $code => $rate) {
            $cacheKey = 'currency_' . $code;
            $item = $cache->getItem($cacheKey);
            $item->set(true);
            $cache->save($item);
        }
    }

}