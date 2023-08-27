<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Récupérez l'utilisateur actuel
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function deleteAccount(Request $request, TokenStorageInterface $tokenStorage, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $submittedToken = $request->request->get('token');

    // Vérifier si le token CSRF est valide
    if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
        // Supprimer le compte utilisateur
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Déconnecter l'utilisateur
        $tokenStorage->setToken(null);

        return $this->redirectToRoute('app_home');
    }

    return $this->redirectToRoute('app_profile');
}

}
