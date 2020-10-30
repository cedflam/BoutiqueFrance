<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
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
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     * @param $stripeSessionId
     * @return Response
     */
    public function index($stripeSessionId): Response
    {

        $order = $this->manager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
