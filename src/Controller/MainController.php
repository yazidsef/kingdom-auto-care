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
}

