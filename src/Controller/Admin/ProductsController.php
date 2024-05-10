<?php 
namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]  
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index():Response
    {
        return $this->render('admin/products/index.html.twig');
    }
    #[Route('/ajout', name: 'add')]
    public function add(PictureService $pictureService ,Request $request , EntityManagerInterface $em , SluggerInterface $slugger):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //on creer un nouveau produit
        $product = new Products();
        $form = $this->createForm(ProductsFormType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $images = $form->get('images')->getData();
            foreach($images as $image)
            {
                //on definit le dossier de destination
                $folder = 'products';

                //on apl le service pictureService pour ajouter l'image
                $fichier  =$pictureService->add($image , $folder , 300 , 300);
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
        
            }
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
           
            $em->persist($product);
            $em->flush(); 
            $this->addFlash('success','Le produit a bien été ajouté');
            $this->redirectToRoute('admin_products_index');
        }
        
        return $this->render('admin/products/add.html.twig', ['form'=>$form->createView()]);
        
    }


    #[Route('/edition/{id}', name: 'edit')]
    public function edit($id,ProductsRepository $productsRepository , Request $request , EntityManagerInterface $em , SluggerInterface $slugger):Response
    {
        $product = $productsRepository->findOneById($id);
        //on verifie si l'utilisateur peut verifier avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        //on creer un nouveau produit
        
        $form = $this->createForm(ProductsFormType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //on recupere les images
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            
            $this->addFlash('success','Le produit a bien été bien modifier ');
            $em->persist($product);
            $em->flush();
        }
        return $this->render('admin/products/edit.html.twig' , ['form'=>$form->createView()]);
    }
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product):Response
    {
    $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig');
    }

}