<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute("app_home");
        }
        if (!($order->getState())) {
            // vider la session "cart"
            $cart->remove();
            // modifier le statut isPaid de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();
            // envoyer un email à notre client pour lui confirmer sa commande
        }

        // afficher les qulques informations de la commande de l'utilisateur 
        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
