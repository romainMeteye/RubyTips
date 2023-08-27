<?php

namespace App\Controller;

use App\Entity\Tuto;
use App\Form\TutoType;
use App\Repository\TutoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/tuto')]
class TutoController extends AbstractController
{
    #[Route('/', name: 'app_tuto_index', methods: ['GET'])]
    public function index(TutoRepository $tutoRepository): Response
    {
        return $this->render('tuto/index.html.twig', [
            'tutos' => $tutoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tuto_new', methods: ['GET', 'POST'])]
public function new(Request $request, TutoRepository $tutoRepository): Response
{
    $tuto = new Tuto();
    $form = $this->createForm(TutoType::class, $tuto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // Pour l'image
        /** @var UploadedFile $imageFile */
        $imageFile = $tuto->getImageFile();

        if ($imageFile) {
            $filesystem = new Filesystem();
            $imageFilename = md5(uniqid()).'.'.$imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('uploads_dir'),
                $imageFilename
            );

            $tuto->setImage($imageFilename);
        }

        // Pour le fichier
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $form['file']->getData();

        if ($uploadedFile) {
            $filesystem = new Filesystem();
            $fileFilename = md5(uniqid()).'.'.$uploadedFile->guessExtension();

            $uploadedFile->move(
                $this->getParameter('uploads_dir'),
                $fileFilename
            );

            $tuto->setFile($fileFilename);
        }

        $tutoRepository->save($tuto, true);

        return $this->redirectToRoute('app_tuto_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('tuto/new.html.twig', [
        'tuto' => $tuto,
        'form' => $form,
    ]);
}

#[Route('/{id}', name: 'app_tuto_show', methods: ['GET'])]
public function show(Tuto $tuto, ManagerRegistry $doctrine): Response
{
    // Récupérez l'utilisateur connecté, s'il y en a un.
    $user = $this->getUser();

    // Si l'utilisateur est connecté, ajoutez ce tutoriel à son historique.
    if ($user) {
        $user->addTuto($tuto);

        // Sauvegardez les changements en base de données.
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

    return $this->render('tuto/show.html.twig', [
        'tuto' => $tuto,
    ]);
}


    #[Route('/{id}/edit', name: 'app_tuto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tuto $tuto, TutoRepository $tutoRepository): Response
    {
        $form = $this->createForm(TutoType::class, $tuto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tutoRepository->save($tuto, true);

            return $this->redirectToRoute('app_tuto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tuto/edit.html.twig', [
            'tuto' => $tuto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tuto_delete', methods: ['POST'])]
    public function delete(Request $request, Tuto $tuto, TutoRepository $tutoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuto->getId(), $request->request->get('_token'))) {
            $tutoRepository->remove($tuto, true);
        }

        return $this->redirectToRoute('app_tuto_index', [], Response::HTTP_SEE_OTHER);
    }
}
