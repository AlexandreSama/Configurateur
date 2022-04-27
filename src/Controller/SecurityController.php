<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{
    /**
     * It renders the login page
     * 
     * @param AuthenticationUtils authenticationUtils This is a Symfony service that provides some
     * useful methods for getting information about the user's login attempt.
     * @param EntityManagerInterface em
     * @param UserPasswordHasherInterface hasher This is the service that will be used to hash the
     * password.
     * 
     * @return Response A response object.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * "Get the Discord client from the ClientRegistry and redirect the user to the Discord OAuth2
     * server."
     * 
     * The ClientRegistry is a service that holds all of the OAuth2 clients that you've configured. In
     * this case, we're getting the Discord client
     * 
     * @param ClientRegistry clientRegistry This is the service that will be used to get the client.
     * 
     * @return RedirectResponse A redirect response to the discord login page.
     */
    #[Route(path: '/connect/discord', name: 'discord_connect')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        $client = $clientRegistry->getClient('discord');
        return $client->redirect(['identify', 'email']);
    }

    /**
     * This method can be blank - it will be intercepted by the logout key on your firewall.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
