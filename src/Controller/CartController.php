<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="app_cart")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart): Response
    {
        $cartComplete = [];
        if ($cart->get()) {
            foreach ($cart->get() as $id => $quantity) {
                $cartComplete[] = [
                    'product' => $entityManager->getRepository(Product::class)->findOneById($id),
                    'quantity' => $quantity
                ];
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="app_add-to-cart")
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('app_products');
    }

    /**
     * @Route("/cart/remove", name="app_remove-cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('app_products');
    }

    /**
     * @Route("/cart/delete/{id}", name="app_delete-cart")
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="app_decrease-cart")
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }
}
