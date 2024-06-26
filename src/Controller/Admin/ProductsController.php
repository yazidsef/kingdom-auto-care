<?php 
namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use App\Service\ImageManipulator;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]  
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository):Response
    {
        $products = $productsRepository->findBy([],['id'=>'asc']);
        return $this->render('admin/products/index.html.twig',compact('products'));
    }
    #[Route('/ajout', name: 'add')]
    public function add(ImageManipulator $imageManipulator,Request $request , EntityManagerInterface $em , SluggerInterface $slugger):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //on creer un nouveau produit
        $product = new Products();
        $form = $this->createForm(ProductsFormType::class, $product);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid())
        {
        //     $imageFile  = $form->get('imageFile')->getData();
        //     if($imageFile){
        //     $targetDir = '/home/yazid-carpro/projets/kingdom-auto-care/public/assets/images/products/';
        //     $imageManipulator->resize($product,$imageFile, 300, 300, $targetDir);

        //     $imageName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME). '.webp';
            
        //     $product->setImage($imageName);
        // }
            
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
    public function edit(PictureService $pictureService ,$id,ProductsRepository $productsRepository , Request $request , EntityManagerInterface $em , SluggerInterface $slugger):Response
    {
        $product = $productsRepository->findOneById($id);
        //on verifie si l'utilisateur peut verifier avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        //on creer un nouveau produit
        
        $form = $this->createForm(ProductsFormType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            
            $this->addFlash('success','Le produit a bien été bien modifier ');
            $em->persist($product);
            $em->flush();
        }
        return $this->render('admin/products/edit.html.twig' , ['form'=>$form->createView(),'product'=>$product]);
    }




    #[Route('/suppression/{id}', name: 'delete')]
    public function delete($id, EntityManagerInterface $em, ProductsRepository $repository): Response
    {
        $product = $repository->find($id);
    
        if (!$product) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('admin_products_index');
        }
    
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
    
        $em->remove($product);
        $em->flush();
    
        $this->addFlash('success', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('admin_products_index');
    }

    // //Delete image method
    // #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    // public function deleteImage(Images $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    // {
    //     // On récupère le contenu de la requête
    //     $data = json_decode($request->getContent(), true);

    //     if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
    //         // Le token csrf est valide
    //         // On récupère le nom de l'image
    //         $nom = $image->getName();

    //         if($pictureService->delete($nom, 'products', 300, 300)){
    //             // On supprime l'image de la base de données
    //             $em->remove($image);
    //             $em->flush();

    //             return new JsonResponse(['success' => true], 200);
    //         }
    //         // La suppression a échoué
    //         return new JsonResponse(['error' => 'Erreur de suppression'], 400);
    //     }

    //     return new JsonResponse(['error' => 'Token invalide'], 400);
    // }

}