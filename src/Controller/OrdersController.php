<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class OrdersController extends AbstractController
{
    private $security;
    public function __construct(Security $security )
    {
        $this->security = $security;
    }
    #[Route('/commandes', name: 'app_orders')]
    public function add(EntityManagerInterface $em,SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        //verification de si l'utilisateur est connecté
       $this->denyAccessUnlessGranted('ROLE_USER');
        //recupereation de session
        $panier = $session->get('panier',[]);
        if($panier === []){
            $this->addFlash('danger','Votre panier est vide');
            return $this->redirectToRoute('main');
        }
        //creation de la commande 
        $order = new Orders();
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());
        foreach($panier as $item=>$quantity)
        {
            $orderDetails = new OrdersDetails();

            //on vas chercher le produit
            $product = $productsRepository->find($item);
            $prix = $product->getPrix();
        
            //on crée les details de la commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($prix);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
            //on persiste et flush

            $em->persist($order);
            $em->flush();
            $session->remove('panier');

            $this->addFlash('success','Votre commande a été enregistrée avec succès');
           return $this->redirectToRoute('main');
        }
        return $this->render('orders/index.html.twig', [
            'controller_name' => 'OrdersController',
        ]);
    }
    
}
