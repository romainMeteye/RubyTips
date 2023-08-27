<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(): Response
    {
        // Récupérez l'utilisateur actuel
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté
        if ($user) {
            // Récupérez la liste des tutoriels consultés
            $tutos = $user->getTuto()->toArray();
            $tutos = array_reverse($tutos);
        } else {
            $tutos = null;
        }

        return $this->render('history/index.html.twig', [
            'tutos' => $tutos,
        ]);
    }
}
