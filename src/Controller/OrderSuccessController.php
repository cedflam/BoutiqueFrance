<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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

            // Evois du mail de confirmation
            $content = 'Bonjour '.$order->getUser()->getFirstname().'. Merci pour votre commande. <br/> <br/> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias architecto consequatur deserunt error illo modi numquam quae rem saepe veritatis.';
            $mail =  new Mail();
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande La boutique Française est bien validée !', $content);

        }


        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
