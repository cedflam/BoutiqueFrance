<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Cart constructor.
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $manager)
    {
        $this->session = $session;
        $this->manager = $manager;
    }

    /**
     * Permet de récupérer l'ensemble des produits sous forme d'objet depuis la session
     * @return array
     */
    public function getFull()
    {
        // Je déclare un tableau
        $cartComplete = [];

        // Si mon panier contient des produits
        if ($this->get()) {

            //Je boucle
            foreach ($this->get() as $id => $quantity) {

                //Je stocke l'objet dans une variable
                $productObject = $this->manager->getRepository(Product::class)->findOneBy(['id' => $id]);

                //Permet d'éviter la saisie d'ajout d'un produit dans l'uri avec un id inconnu donc :
                // Si le produit n'existe pas en bdd alors ne fait rien et passe directement au produit suivant
                // et supprime le produit ajouté de la session
                if (!$productObject){
                    $this->delete($id);
                    continue;
                }
                // Je stocke le résultat dans un tableau
                $cartComplete[] = [
                    'product' => $productObject ,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }

    /**
     * Permet d'ajouter un produit au panier
     * @param $id
     */
    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])){
            $cart[$id] ++;
        }else{
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    /**
     *  Permet de récupérer le panier
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * Permet de supprimer le panier complet
     */
    public function remove()
    {
        $this->session->remove('cart');
    }

    /**
     * Permet de supprimer une ligne produit du panier
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        //Retire le cart portant cet id de la session
        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    /**
     * Permet d'incrementer / decrementer la quantité de produits dans le panier
     * @param $id
     * @return mixed
     */
    public function decrease($id)
    {
        $cart = $this->session->get('cart');
        if ($cart[$id] > 1 ){
            $cart[$id] --;
        }else{
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }
}