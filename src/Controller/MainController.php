<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * It returns a response object that renders the template `main/index.html.twig` and passes the
     * variable `controller_name` to the template
     * 
     * @return Response A response object
     */
    #[Route('/', name: 'Accueil')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
