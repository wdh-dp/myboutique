<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    // Obtenir le contenu du panier
    public function get_cart()
    {
        return $this->session->get('cart', []);
    }


    // Ajouter des produits
    public function add($id)
    {

        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    // Diminution d'un produit
    public function decrease($id)
    {

        $cart = $this->session->get('cart', []);

        if (($cart[$id] > 1)) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        $this->session->set('cart', $cart);
    }

    // Supprimer un produit
    public function delete($id)
    {

        $cart = $this->session->get('cart', []);
        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    // Vide le panier
    public function remove_cart()
    {
        return $this->session->remove('cart');
    }
}
