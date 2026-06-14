<?php

namespace App\Controller;

use App\Repository\FlowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CartService;

class CatalogController extends AbstractController
{
    #[Route('/catalogo', name: 'app_catalog')]
    public function index(Request $request, FlowerRepository $flowerRepository, CartService $cartService): Response
    {
        $searchTerm = $request->query->get('q', '');
        $priceFilter = $request->query->get('price', '');

        $qb = $flowerRepository->createQueryBuilder('f');

        if ($searchTerm !== '') {
            $qb->andWhere('f.name LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($priceFilter === 'under_3') {
            $qb->andWhere('f.price < 3');
        } elseif ($priceFilter === '3_to_5') {
            $qb->andWhere('f.price >= 3')
               ->andWhere('f.price <= 5');
        } elseif ($priceFilter === 'over_5') {
            $qb->andWhere('f.price > 5');
        }

        $flores = $qb->getQuery()->getResult();
        
        $carrito = $cartService->getFullCart();
        
        $totalCart = 0;
        $totalItems = 0;
        foreach ($carrito as $item) {
            $totalCart += $item['flor']->getPrice() * $item['cantidad'];
            $totalItems += $item['cantidad'];
        }

        return $this->render('catalog/index.html.twig', [
            'flores' => $flores,
            'carrito' => $carrito,
            'totalCart' => $totalCart,
            'totalItems' => $totalItems,
            'searchTerm' => $searchTerm,
            'priceFilter' => $priceFilter,
        ]);
    }

    #[Route('/catalogo/flor/{id}', name: 'app_catalog_detail')]
    public function detail(int $id, FlowerRepository $flowerRepository): Response
    {
        $flor = $flowerRepository->find($id);

        if (!$flor) {
            throw $this->createNotFoundException('La flor solicitada no existe.');
        }

        return $this->render('catalog/detail.html.twig', [
            'flor' => $flor,
        ]);
    }
}