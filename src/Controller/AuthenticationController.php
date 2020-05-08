<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use App\Service\Authentication;


class AuthenticationController extends ApiController {
    /**
    * @Route("authentication/login", name="auth_login")
    */
    public function login(Authentication $authentication, Request $request) {
        $request = $this->transformJsonBody($request);

        $username = $request->request->get('username', '');
        $pass = $request->request->get('password', '');

        
        $user = $authentication->login($username, $pass);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User or password is wrong.']);   
        }

        $data = $user->toArray();

        return $this->json(['success' => true, 'data' => $data]);
    }

    /**
    * @Route("authentication/logout", name="auth_logout")
    */
    public function logout(Authentication $authentication, Request $request) {
        $request = $this->transformJsonBody($request);

        $token = $request->headers->get('token', '');

        $success = $authentication->logout($token);

        return $this->json(['success' => $success]);
    }

    /**
    * @Route("authentication/validate", name="auth_validate")
    */
    public function validate(Authentication $authentication, Request $request) {
        $request = $this->transformJsonBody($request);

        $headers = getallheaders();
        $token = '';
        if (array_key_exists('token', $headers)) {
            $token = $headers['token'];
        }
        else if (array_key_exists('Authorization', $headers)) {
            $token = $headers['Authorization'];
        }
        else {
            $token = $request->request->get('token', '');
        }

        $success = $authentication->validate($token);

        return $this->json(['success' => $success, 'message' => "User token expired! Login again."]);
    }

}