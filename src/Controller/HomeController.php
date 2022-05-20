<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ProductRepository $productRepository;
    private PaginatorInterface $paginator;

    /**
     * @param ProductRepository $productRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }


    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $qb = $this->productRepository->getQbAll();

        $product = $this->productRepository->findBy(['isActive' => true]);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page',1),
            3
        );
        dump($product);

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination

        ]);

    }

}
