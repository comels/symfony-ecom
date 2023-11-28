<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcountAddressController extends AbstractController
{
    /**
     * @Route("/compte/adresses", name="app_address")
     */
    public function index(): Response
    {
        return $this->render('compte/address.html.twig');
    }

    /**
     * @Route("/compte/ajouter-adresse", name="app_address_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress->setUser($this->getUser());

            $entityManager->persist($adress);
            $entityManager->flush();

            return $this->redirectToRoute('app_address');
        }

        return $this->render('compte/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifier-adresse/{id}", name="app_address_edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $adress = $entityManager->getRepository(Adress::class)->findOneById($id);

        if (!$adress || $adress->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_address');
        }
        $form = $this->createForm(AdressType::class, $adress);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_address');
        }

        return $this->render('compte/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/supprimer-adresse/{id}", name="app_address_delete")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $adress = $entityManager->getRepository(Adress::class)->findOneById($id);

        if ($adress && $adress->getUser() == $this->getUser()) {
            $entityManager->remove($adress);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_address');
    }
}
