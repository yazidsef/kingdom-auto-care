<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoriesRepository $categoriesRepository , PaginatorInterface $paginator , Request $request): Response
    {       
        $categories = $categoriesRepository->ProductsWithCategories(1,10);
        
        return $this->render('main/index.html.twig',compact('categories'));
    }
    //  #[Route('/test', name: 'test')]
    //  public function test(ProductsRepository $products ,PaginatorInterface $paginator , Request $request): Response
    //  {       
        
    //     $products = $products->testTwo();
    //     $allCategories = []; // Initialize an array to hold all categories

    //     foreach ($products as $product) {
    //         foreach ($product->getCategories() as $category) {
    //             $allCategories[] = $category; // Add each category to the array
    //         }
    //     }
    
    //     // Remove duplicates if necessary. This step requires that your Category entity
    //     // can be compared for uniqueness (e.g., by id or name). You might need a custom
    //     // function to do this efficiently, especially if $allCategories is large.
    
    //     // Use dd() to dump the categories for debugging. Remove or comment out this line for production.
    //     return $this->render('main/test.html.twig', ['products' => $products ]);
    //  }
}

