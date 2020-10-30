<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * OrderSuccessController constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     * @param $stripeSessionId
     * @param Cart $cart
     * @return Response
     */
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->manager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if (!$order->getIsPaid()){
            // Je vide la session "cart"
            $cart->remove();
            // Je valide le paiement en bdd
            $order->setIsPaid(1);
            // J'enregistre
            $this->manager->flush();
        }


        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
