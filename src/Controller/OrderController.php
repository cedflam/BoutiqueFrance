<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * OrderController constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/commande", name="order")
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    public function index(Cart $cart, Request $request): Response
    {

        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $deliveryContent = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $deliveryContent .= '<br/>' . $delivery->getPhone();

            // Si une company à été renseignée dans l'adresse
            if ($delivery->getCompagny()) {
                $deliveryContent .= '<br/>' . $delivery->getCompagny();
            }

            $deliveryContent .= '<br/>' . $delivery->getAddress();
            $deliveryContent .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliveryContent .= '<br/>' . $delivery->getCountry();

            //Enregistrer une commande Order()
            $reference = $date->format('dmY').'-'.uniqid();
            $order = new Order();
            $order
                ->setReference($reference)
                ->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setCarrierName($carriers)
                ->setCarrierPrice($carriers->getPrice())
                ->setDelivery($deliveryContent)
                ->setIsPaid(false);
            $this->manager->persist($order);



            // Enregistrer mes produits OrderDetail()
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetail();
                $orderDetails
                    ->setMyOrder($order)
                    ->setProduct($product['product']->getName())
                    ->setQuantity($product['quantity'])
                    ->setPrice($product['product']->getPrice())
                    ->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->manager->persist($orderDetails);
            }
            $this->manager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $deliveryContent,
                'reference' => $order->getReference()

            ]);
        }

        return $this->redirectToRoute('cart');


    }
}
