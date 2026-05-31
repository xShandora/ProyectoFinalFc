<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/carrito', name: 'app_cart_index')]
    public function index(CartService $cartService): Response
    {
        $carritoCompleto = $cartService->getFullCart();

        return $this->render('cart/index.html.twig', [
            'elementos' => $carritoCompleto,
        ]);
    }

    #[Route('/carrito/agregar/{id}', name: 'app_cart_add')]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->add($id);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/carrito/restar/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, CartService $cartService): Response
    {
        $cartService->decrease($id);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/carrito/eliminar/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/carrito/vaciar', name: 'app_cart_empty')]
    public function empty(CartService $cartService): Response
    {
        $cartService->empty();
        return $this->redirectToRoute('app_cart_index');
    }
}