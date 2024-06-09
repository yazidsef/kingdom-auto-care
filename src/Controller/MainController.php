<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {       
        $categories = $categoriesRepository->ProductsWithCategories(1,10);
 
        return $this->render('main/index.html.twig',compact('categories'));
    }
    // #[Route('/test', name: 'main')]
    // public function test(ProductsRepository $products): Response
    // {       
    //     $products = $products->findAll();
    //     return $this->render('main/test.html.twig',compact('products'));
    // }
}

