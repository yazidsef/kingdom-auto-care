<?php
namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card',name:'cart_')]
class CartController extends AbstractController
{

    #[Route('/',name:'index')]
    public function index(SessionInterface $session , ProductsRepository $productsRepository):Response
    {
        $panier  = $session->get('panier',[]);
        //on initialise les variables
        $data=[];
        $total = 0;
        foreach($panier as $id => $quantite)
        {
            $product = $productsRepository->find($id);
            $data[]=['product'=>$product,'quantity'=>$quantite];

            $total += $product->getPrix() * $quantite;
        }
        
        return $this->render('card/index.html.twig',compact('data','total'));
    }
    #[Route('/add/{id}',name:'add')]
    public function add (Products $product , SessionInterface $session):Response
    {
        //on recupere le id de produit
        $id = $product->getId();

        //on recupere le panier existant dans la session 
        
        $panier = $session->get('panier',[]); // si le panier n'existe pas alors on le crée par []
        
        //on ajoute le produit dans le panier s'il y'a pas encore , sinon on incremente la quantité
        if(empty($panier[$id])){
            $panier [$id] = 1;
        }else{//si le produit existe deja dans le panier (not empty)
            $panier[$id]++;
        }
        $session->set('panier',$panier);
        //on redirige vers la page de panier
        return $this->redirectToRoute('cart_index');

    }
    #[Route('/remove/{id}',name:'remove')]
    public function remove(Products $product , SessionInterface $session):Response
    {
        //on recupere le id de produit
        $id = $product->getId();

        //on recupere le panier existant dans la session 
        
        $panier = $session->get('panier',[]); // si le panier n'existe pas alors on le crée par []
        
        //on retire le produit (1 par 1) dans le panier s'il 1 exemplaire , sinon on décremente la quantité
        if(!empty($panier[$id])){
            if($panier[$id]>1){
                $panier[$id]--;
            }
        }else{
            unset($panier[$id]);
        }
        $session->set('panier',$panier);
        //on redirige vers la page de panier
        return $this->redirectToRoute('cart_index');

    }

    #[Route('/delete/{id}',name:'delete')]
    public function delete(Products $product , SessionInterface $session):Response
    {
        //on recupere le id de produit
        $id = $product->getId();

        //on recupere le panier existant dans la session 
        
        $panier = $session->get('panier',[]); // si le panier n'existe pas alors on le crée par []
        
        //on retire le produit (1 par 1) dans le panier s'il 1 exemplaire , sinon on décremente la quantité
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier',$panier);
        //on redirige vers la page de panier
        return $this->redirectToRoute('cart_index');

    }
   
    #[Route('/empty',name:'empty')]
    public function empty(SessionInterface $session):Response
    {
      $session->remove('panier');
        return $this->redirectToRoute('cart_index');
    }
}