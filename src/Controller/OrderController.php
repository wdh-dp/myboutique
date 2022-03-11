<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Request $request, Cart $cart, ProductRepository $repo, EntityManagerInterface $manager): Response
    {

        if (count($this->getUser()->getAddresses()) == 0) {

            return $this->redirectToRoute('account_address_add');
        }


        //recupération des produits de la commande
        $cart = $cart->get_cart();
        $productsCart = [];
        foreach ($cart as $id => $quantity) {
            $productsCart[] = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity
            ];
        }


        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request); // on récupère la requete

        if ($form->isSubmitted() && $form->isValid()) {

            // dd($form->get('transporteurs')->getData());

            $order = new Order();
            $order->setUser($this->getUser())
                ->setCreatedAt(new \DateTime())
                ->setCarrier($form->get('transporteurs')->getData())
                ->setDelivery($form->get('addresses')->getData())
                ->setStatut(0);

            $date = new \DateTime();
            $date = $date->format('dmY');
            $order
                ->setReference($date . '-' . uniqid());

            $manager->persist($order);

            foreach ($productsCart as $product) {
                $orderDetails = new OrderDetails();

                $orderDetails->setMyOrder($order)
                    ->setProduct($product['product'])
                    ->setPrice($product['product']->getPrice())
                    ->setQuantity($product['quantity']);

                $products_for_stripe[] = [

                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [$_SERVER['HTTP_ORIGIN'] . '/uploads/' . $product['product']->getIllustration()]
                        ],
                        'unit_amount' => $product['product']->getPrice(),
                    ],
                    'quantity' => $product['quantity'],

                ];


                $manager->persist($orderDetails);
            }


            $products_for_stripe[] = [


                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $order->getCarrier()->getName(),

                    ],
                    'unit_amount' => $order->getCarrier()->getPrice(),
                ],
                'quantity' => 1,

            ];


            // dd($products_for_stripe);
            //  dd($_SERVER);
            //  $manager->flush();



            Stripe::setApiKey($this->getParameter('stripe_key'));
            // $YOUR_DOMAIN = 'http://localhost:8000';

            $checkout_session = \Stripe\Checkout\Session::create([
                'customer_email' => $this->getUser()->getEmail(),
                'line_items' => $products_for_stripe,
                'mode' => 'payment',
                'success_url' => $_SERVER['HTTP_ORIGIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $_SERVER['HTTP_ORIGIN'] . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);


            $order->setCheckoutSessionId($checkout_session->id);
            $manager->persist($order);
            $manager->flush();

            // dd($checkout_session->url);

            return $this->render('order/recap.html.twig',  [
                'cart' => $productsCart,
                'order' => $order,
                'stripeUrl' => $checkout_session->url
            ]);
        }



        return $this->render(
            'order/index.html.twig',
            [
                'form' => $form->createView(),
                'cart' => $productsCart
            ]
        );
    }
}
