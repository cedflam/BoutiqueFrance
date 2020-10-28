<?php

namespace App\Controller;

use App\Classe\Search;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $manager;
    private $productRepository;

    /**
     * ProductController constructor.
     * @param EntityManagerInterface $manager
     * @param ProductRepository $productRepository
     */
    public function __construct(EntityManagerInterface $manager, ProductRepository $productRepository)
    {
        $this->manager = $manager;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/nos-produits", name="products")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->productRepository->findWithSearch($search);
        }else{
            $products = $this->productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     * @param $slug
     * @return Response
     */
    public function show($slug)
    {
        $product = $this->productRepository->findOneBy(['slug' => $slug]);

        if (!$product){
           return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
