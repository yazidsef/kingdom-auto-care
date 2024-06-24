<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{   

    #[Route('/{slug}' , name:'list')]
    public function list(Request $request ,string $slug , ProductsRepository $productsRepository ,PaginatorInterface $paginator): Response
    {
        //on vas chercher le numero de page dans l'url
        $allProducts = $productsRepository->testTwo($slug);
        $products = $paginator->paginate(
            $allProducts,
            $request->query->getInt('page', 1),
            4
        );
        
        return $this->render('categories/list.html.twig',compact('products'));
    }
}

