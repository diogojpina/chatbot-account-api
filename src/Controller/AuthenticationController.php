<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\UserRepository;

use App\Entity\User;
use App\Entity\Account;

use App\Service\Authentication;
use App\Service\ExchangeService;


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

        $user = $this->getUser();

        $success = $authentication->logout($user->getToken());

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

    /**
    * @Route("authentication/signup", name="auth_signup")
    */
    public function signup(UserRepository $userRepo, ExchangeService $exchangeService, Request $request) {
        $request = $this->transformJsonBody($request);

        $firstname = $request->request->get('firstname', '');
        $lastname = $request->request->get('lastname', '');
        $username = $request->request->get('username', '');
        $pass = $request->request->get('password', '');
        $currency = $request->request->get('currency', '');

        if (strlen($username) < 3) {
            return $this->json(['success' => false, 'message' => 'Username has to be at least 3 characteres.']);   
        }

        if (strlen($pass) < 6) {
            return $this->json(['success' => false, 'message' => 'Password has to be at least 6 characteres.']);   
        }

        if ($userRepo->getByUsername($username)) {
            return $this->json(['success' => false, 'message' => 'Username has already registered.']);   
        }

        if ($exchangeService->validateCode($currency) === false) {
            return $this->json(['success' => false, 'message' => 'Currency code invalid.']);   
        }

        $pass = md5($pass);

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setUsername($username);
        $user->setPassword($pass);
        $user->setCurrency($currency);
        $user->setIsActive(true);

        $account = new Account();
        $account->setUser($user);
        $account->setAccountNumber(1);
        $account->setBalance(0);

        $user->addAccount($account);

        $userRepo->add($user);

        return $this->json(['success' => true, 'data' => $user->toArray()]);
    }

}