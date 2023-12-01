<?php

namespace App\Controller;

// Importation des classes nécessaires.

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe OrderController qui hérite d'AbstractController.
class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="app_order")
     */
    public function index(Cart $cart): Response
    {
        // Vérification si l'utilisateur connecté a des adresses enregistrées.
        // Si l'utilisateur n'a pas d'adresses, il est redirigé vers la route 'app_address_add'.
        if (!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('app_address_add');
        }

        // Création d'un formulaire de type OrderType.
        // 'user' est passé en option au formulaire pour une utilisation éventuelle dans sa construction.
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        // Rendu du template 'order/index.html.twig' avec le formulaire.
        // 'form' est passé à la vue pour qu'il soit rendu dans le template.
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="app_order_recap", methods={"POST", "GET"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        // Création d'un formulaire de type OrderType.
        // 'user' est passé en option au formulaire pour une utilisation éventuelle dans sa construction.
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new \DateTimeImmutable();
            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            $deliveryContent = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $deliveryContent .= '<br/>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryContent .= '<br/>' . $delivery->getCompany();
            }
            $deliveryContent .= '<br/>' . $delivery->getAddress();
            $deliveryContent .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliveryContent .= '<br/>' . $delivery->getCountry();


            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($deliveryContent);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            //$this->entityManager->flush();
              
            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carrier,
                'delivery' => $deliveryContent
            ]);
        }
        return $this->redirectToRoute('app_cart');
    }
}
