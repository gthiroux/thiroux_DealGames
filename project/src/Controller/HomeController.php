<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdFormType;
use App\Form\RegistrationFormType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(AdRepository $ad_repo ):Response{
    $ads=$ad_repo->findAll();

    return $this->render('home/index.html.twig',[
        'ads'=>$ads,
    ]); 
    }

    #[Route('/create_ad', name: 'home.ad.create')]
    public function create_ad(Request $request,EntityManagerInterface $em): Response
    {   $ad=new Ad();
        $ad_form=$this->createForm(AdFormType::class, $ad);
        $ad_form->handleRequest($request);
        if ($ad_form->isSubmitted()){
            if ($ad_form->isValid()) {
                $ad->setUser($this->getUser());
                $em->persist($ad);
                $em->flush();
                return $this->redirectToRoute('home');
            }}

        return $this->render('home/ad.html.twig', [
            'ad_form'=>$ad_form,
        ]);
    }

}
