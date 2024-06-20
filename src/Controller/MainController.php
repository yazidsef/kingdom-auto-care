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
     #[Route('/test', name: 'test')]
     public function test(ProductsRepository $products ,PaginatorInterface $paginator , Request $request): Response
     {       
        $query = $products->createQueryBuilder('p');

        $pagination = $paginator->paginate(
            $query, // pass query, not result
            $request->query->getInt('page', 1), // page number
            4// limit per page
        );
    
        return $this->render('main/test.html.twig', ['pagination' => $pagination]);
     }
}

