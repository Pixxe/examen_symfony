<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $em;

    /**
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(ProductRepository $productRepository, EntityManagerInterface $em)
    {
        $this->productRepository = $productRepository;
        $this->em = $em;
    }


    #[Route('/product', name: 'app_product')]
    public function allProduct(): Response
    {

        $user = $this->getUser();
        if ($user !== null) {
            $products = $this->productRepository->findBy(['createdBy' => $user->getId()]);
            dump($products);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    //Ajouter un article
    #[Route('/product_add', name: 'app_product_add')]
    public function addProduct(Request $request): Response
    {
        $user = $this->getUser();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTime());
            $product->setCreatedBy($user);
            $product = $form->getData();
            $this->em->persist($product);
            $this->em->flush();
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView()
        ]);

    }

    //Supprimer un article
    #[Route('/product_delete/{id}', name: 'app_product_delete')]
    public function deleteProduct($id): Response
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $this->em->remove($product);
        $this->em->flush();

        return $this->redirectToRoute('app_product');
    }

    //Update d'un produit
    #[Route('/product_update{id}', name: 'app_product_update')]
    public function updateProduct($id, Request $request): Response
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //Voir les détails d'un produit
    #[Route('/product_details/{id}', name: 'app_product_details')]
    public function productDetails($id): Response
    {
        $productDetails = $this->productRepository->findOneBy(['id'=>$id]);

        return $this->render('product/details.html.twig', [
            'product' => $productDetails
        ]);
    }


}
