<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard_admin(): Response
    {
        if($this->getUser()){

            $user = $this->getUser();
            if(in_array('ROLE_ADMIN', $user->getRoles())){
                $role = 'Administrateur';
                return $this->render('dashboard/dashboard_admin.html.twig', [
                    'controller_name' => 'DashboardController',
                    'user' => $user,
                    'role' => $role
                ]);

            }else{

                return $this->render('dashboard/dashboard.html.twig', [
                    'controller_name' => 'DashboardController',
                    'user' => $user
                ]);

            }

        }else{

            return $this->render('security/login.html.twig', [
                'controller_name' => 'DashboardController'
            ]);

        }

    }
}
