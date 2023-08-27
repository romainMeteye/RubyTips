<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tuto;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Tuto::class);
        $tutos = $repository->findAll();
        $userIsConnected = $this->getUser() !== null;

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tutos' => $tutos,
            'userIsConnected' => $userIsConnected,
        ]);
    }
}
