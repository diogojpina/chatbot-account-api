<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController {

	protected function transformJsonBody(Request $request){
	    $data = json_decode($request->getContent(), true);

	    if (json_last_error() !== JSON_ERROR_NONE) {
	        return $request;
	    }

	    if ($data === null) {
	        return $request;
	    }

	    $request->request->replace($data);

	    return $request;
	}

}