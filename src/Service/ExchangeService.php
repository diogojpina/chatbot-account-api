<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Contracts\Cache\ItemInterface;
use GuzzleHttp\Client;

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
    	$key = '8wX2UDShHqXNJx2SK2vGwMSHk4SY2V';
    	$url = "currency.php?api_key=$key&from=$from&to=$to&amount=$amount";
    	$client = new Client(['base_uri' => 'https://www.amdoren.com/api/', 'timeout'  => 10.0]);

    	$response = $client->request('GET', $url);

    	$data = json_decode($response->getBody()->getContents());

    	if ($data->error > 0) {
    		return false;
    	}

    	return $data->amount;
    }

    private function validateCode($code) {
    	$accessKey = '4eecc466ac342f10e5805027cbce00e1';
    	$url = "http://data.fixer.io/api/convert?access_key=$accessKey&from=$from&to=$to&amount=$amount";

    	$value = $pool->get('my_cache_key', function (ItemInterface $item) {
		    $item->expiresAfter(3600);

		    // ... do some HTTP request or heavy computations
		    $computedValue = 'foobar';

		    return $computedValue;
		});
    }

}