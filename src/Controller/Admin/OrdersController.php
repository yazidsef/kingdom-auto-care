<?php 

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/commandes' , name:'admin_orders_')]
class OrdersController extends AbstractController
{
    private $security;
    public function __construct(Security $security )
    {
        $this->security = $security;
    }
    #[Route('' , name:'index')]
    public function index(OrdersRepository $ordersRepository):Response
    {
        $orders = $ordersRepository->findAll();
        return $this->render('admin/orders/index.html.twig',compact('orders'));
    }

    #[Route('/details/{id}' , name:'details')]
    public function details ($id, OrdersDetailsRepository $ordersDetailsRepository):Response
    {
        $details = $ordersDetailsRepository->findBy(['orders'=>$id],['orders'=>'asc']);

        return $this->render('admin/orders/details.html.twig',compact('details'));
    }

}