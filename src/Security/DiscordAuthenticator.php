<?php

namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\DiscordClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class DiscordAuthenticator extends OAuth2Authenticator{

    private $clientRegistry;
    private $entityManager;
    private $router;

    /**
     * The above function is a constructor function that takes in three parameters: ,
     * , and . 
     * 
     * The constructor function is a special function that is called when an object is created. 
     * 
     * The constructor function is used to initialize the object's properties. 
     * 
     * The constructor function is called automatically
     * 
     * @param ClientRegistry clientRegistry This is the service that manages the OAuth clients.
     * @param EntityManagerInterface entityManager This is the entity manager that will be used to
     * persist the user.
     * @param RouterInterface router The router service.
    */
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * If the route is oauth_check and the service is discord, then continue.
     * 
     * @param Request request The request object
     * 
     * @return ?bool The return value of this method must be one of the following:
    */
    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === 'discord';
    }

    /**
     * If the user is already in the database, return the user. If not, create a new user and return
     * that
     * 
     * @param Request request The incoming request.
     * 
     * @return Passport The user object.
    */
    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('discord');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var DiscordUser $discordUser */
                $discordUser = $client->fetchUserFromToken($accessToken);

                $email = $discordUser->getemail();

                // 1) have they logged in with Facebook before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['discordId' => $discordUser->getid()]);

                if ($existingUser) {
                    return $existingUser;
                }

                // 3) Maybe you just want to "register" them by creating
                $user = new User();
                $user->setUsername($discordUser->getusername());
                if($discordUser->getid() === "256892994504884224" or "724693796499095552" or "537619289960873985"){
                    $user->setRoles(['ROLE_ADMIN']);
                }else{
                    $user->setRoles(['ROLE_USER']);
                }
                $user->setEmail($email);
                $user->setDiscordId($discordUser->getid());
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    /**
     * If the user is authenticated, redirect them to the login page.
     * 
     * @param Request request The request that resulted in an AuthenticationException
     * @param TokenInterface token The token that was used to authenticate the user.
     * @param string firewallName The name of the firewall that was used to authenticate the user.
     * 
     * @return ?Response A RedirectResponse object.
    */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_login');

        return new RedirectResponse($targetUrl);
    }


    /**
     * If the user fails to authenticate, return a response with a message that says "Authentication
     * failed"
     * 
     * @param Request request The request that resulted in an AuthenticationException
     * @param AuthenticationException exception The exception that was thrown during authentication.
     * 
     * @return ?Response A Response object with a message and a HTTP status code.
    */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
    */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}