<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Services\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', []);
    }


    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Request $request, EntityManagerInterface $manager, Cart $cart): Response
    {

        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        //dump($user);

        $form->handleRequest($request); //on récupère la requète

        if ($form->isSubmitted() && $form->isValid()) {

            $address->setUser($this->getUser());

            $manager->persist($address);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'adresse a bien été enregistrée"
            );
            //  dd($address);
            if ($cart->get_cart()) {

                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_address');
            }



            return $this->redirectToRoute('account_address');
        }



        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     */
    public function edit(Request $request, EntityManagerInterface $manager, Address $address): Response
    {

        if ($address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request); //on récupère la requète

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            $this->addFlash(
                'success',
                "L'adresse a bien été modifiée"
            );
            //  dd($address);

            return $this->redirectToRoute('account_address');
        }


        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView()
        ]);
    }




    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     */
    public function delete(Request $request, EntityManagerInterface $manager, Address $address): Response
    {

        if ($address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $manager->remove($address);
        $manager->flush();

        $this->addFlash(
            'success',
            "l'adresse a bien été supprimée"
        );



        return $this->redirectToRoute('account_address');
    }
}
