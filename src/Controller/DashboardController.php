<?php

namespace App\Controller;

use App\Entity\BotInfos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DashboardController extends AbstractController
{


    #[Route('/dashboard/restartBot', name: 'restart_bot_dashboard')]
    public function restartBot(HttpClientInterface $client)
    {
        $response = $client->request('POST', 'http://193.168.146.71:3000/restart_bot');
        $content = $response->getContent();
        return new Response($content);
    }

    #[Route('/dashboard/startBot', name: 'start_bot_dashboard')]
    public function startBot(HttpClientInterface $client)
    {
        $response = $client->request('POST', 'http://193.168.146.71:3000/start_bot');
        $content = $response->getContent();
        return new Response($content);
    }

    #[Route('/dashboard/updateBot', name: 'update_bot_dashboard')]
    public function updateBot(HttpClientInterface $client)
    {
        $response = $client->request('POST', 'http://193.168.146.71:3000/update_bot');
        $content = $response->getContent();
        return new Response($content);
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard_admin(EntityManagerInterface $em): Response
    {

        if($this->getUser()){

            $user = $this->getUser();
            if(in_array('ROLE_ADMIN', $user->getRoles())){

                    $contentsUsers = file_get_contents('http://193.168.146.71:3000/users');
                    $dataUsers = json_decode($contentsUsers);
                    $usersRequest = $dataUsers->users;

                    $contentsGuilds = file_get_contents('http://193.168.146.71:3000/guilds');
                    $dataGuilds = json_decode($contentsGuilds);
                    $guildsRequest = $dataGuilds->guilds;

                    $contentsUptime = file_get_contents('http://193.168.146.71:3000/uptime');
                    $dataUptime = json_decode($contentsUptime);
                    $uptimeRequest = $dataUptime->uptime;

                    $repoBotInfos = $em->getRepository(BotInfos::class);
                    $infosDB = $repoBotInfos->findAll();
                    $lengthInfos = count($infosDB);

                    if($lengthInfos < 1){
                        $usersDB = 0;
                        $guildsDB = 0;
                        $uptimeDB = 0;
                    }else{
                        $usersDB = $infosDB[0]->getUsers();
                        $guildsDB = $infosDB[0]->getGuilds();
                        $uptimeDB = $infosDB[0]->getUptime();
                    }

                    if($usersDB > $usersRequest){
                        $users = $usersDB;
                        $stateUsers = 'arrow-top-right';
                    }else if($usersDB == $usersRequest){
                        $users = $usersDB;
                        $stateUsers = 'equal';
                    }else{
                        $users = $usersRequest;
                        $stateUsers = 'arrow-bottom-right';
                        $infosDB[0]->setUsers($usersRequest);
                    }

                    if($guildsDB > $guildsRequest){
                        $guilds = $guildsDB;
                        $stateGuilds = 'arrow-top-right';
                    }else if($guildsDB == $guildsRequest){
                        $guilds = $guildsDB;
                        $stateGuilds = 'equal';
                    }else{
                        $guilds = $guildsRequest;
                        $stateGuilds = 'arrow-bottom-right';
                        $infosDB[0]->setGuilds($guildsRequest);
                    }

                    if($uptimeDB > $uptimeRequest){
                        $uptime = $uptimeDB;
                    }else{
                        $uptime = $uptimeRequest;
                        $infosDB[0]->setUptime($uptimeRequest);
                    }

                    $em->flush();

                // }else{
                //     $users = "";
                //     $guilds = "";
                //     $uptime = "";
                //     $stateGuilds = "alert";
                //     $stateUsers = "alert";
                // }

                $role = 'Administrateur';
                return $this->render('dashboard/dashboard_admin.html.twig', [
                    'controller_name' => 'DashboardController',
                    'user' => $user,
                    'role' => $role,
                    'users' => $users,
                    'guilds' => $guilds,
                    'uptime' => $uptime,
                    'stateUsers' => $stateUsers,
                    'stateGuilds' => $stateGuilds,
                ]);

            }else{

                return $this->render('dashboard/dashboard.html.twig', [
                    'controller_name' => 'DashboardController',
                    'user' => $user
                ]);

            }

        }else{

            return $this->render('main/index.html.twig', [
                'controller_name' => 'MainController'
            ]);

        }

    }
}