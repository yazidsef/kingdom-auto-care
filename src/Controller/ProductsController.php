<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products', name: 'products_')]
class ProductsController extends AbstractController
{
    private $productsRepository;
    public function __construct(ProductsRepository $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('products/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(string $slug , ): Response
    {
        $products = $this->productsRepository->findWithCategoryAndImages($slug);
        if(!$products){
            throw $this->createNotFoundException('le produit demandé n\'existe pas');
        }
        
        return $this->render('products/details.html.twig', compact('products'));
    }
}
