<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class AdController extends AbstractController
{
    #[Route(name: 'app_ad_index', methods: ['GET'])]
    public function index(AdRepository $adRepository): Response
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    #[Route('/ad/new', name: 'app_ad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ad);
            $entityManager->flush();

            return $this->redirectToRoute('app_ad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/ad/{id}', name: 'app_ad_show', methods: ['GET'])]
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    #[Route('/ad/{id}/edit', name: 'app_ad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/ad/{id}', name: 'app_ad_delete', methods: ['POST'])]
    public function delete(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ad_index', [], Response::HTTP_SEE_OTHER);
    }
}
