<?php

namespace App\Controller;

use App\Repository\FlowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CartService;

class CatalogController extends AbstractController
{
    #[Route('/catalogo', name: 'app_catalog')]
    public function index(FlowerRepository $flowerRepository, CartService $cartService): Response
    {
        $flores = $flowerRepository->findAll();
        
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
        ]);
    }

    #[Route('/catalogo/flor/{id}', name: 'app_catalog_detail')]
    public function detail(int $id, \App\Repository\FlowerRepository $flowerRepository): Response
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