<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginTokenAuthenticator extends AbstractGuardAuthenticator {
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request) {
        if ('auth_login' == $request->attributes->get('_route')) return false;        
                
        return true;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request) {
        //$token = $request->query->get('token', '');
        
        /*
        $token = $request->headers->get('token', '');
        if (!$token)
            $token = $request->headers->get('token', '');
        */
        
       
        $headers = getallheaders();
        $token = '';
        if (array_key_exists('token', $headers)) {
            $token = $headers['token'];
        }
        else if (array_key_exists('Authorization', $headers)) {
            $token = $headers['Authorization'];
        }
        else {
            $token = $request->query->get('token', '');
        }

        return [
            'token' => $token,
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = $credentials['token'];

        if (null === $token) {
            return;
        }

        // if a User object, checkCredentials() is called
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['token' => $token]);
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $token = $credentials['token'];

        if ($user->getToken() != $token) 
            return false;

        $loginExpires = $user->getLoginExpires();
        $loginDiff = $loginExpires->getTimestamp() - strtotime("now");

        if ($loginDiff < 0)
            return false;

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = [
            'success' => false,
            'code' => 'loginFailure',
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null) {
        $data = [
            'success' => false,
            'code' => 'authRequired',
            // you might translate this message
            'message' => 'Authentication Required!'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return false;
    }
}