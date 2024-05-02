<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {       
        $categories = $categoriesRepository->findBy([],['categoryOrder'=>'ASC']);
        return $this->render('main/index.html.twig',compact('categories'));
    }
}
