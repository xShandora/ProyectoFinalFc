<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function add(int $id, CartService $cartService, Request $request): Response
    {
        $cantidad = (int) $request->request->get('cantidad', 1);

        $cartService->add($id, $cantidad);
        
        if ($request->request->get('redireccion') === 'catalogo') {
            return $this->redirectToRoute('app_catalog');
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_catalog'));
    }

    #[Route('/carrito/restar/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, CartService $cartService, Request $request): Response
    {
        $cartService->decrease($id);
        
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_cart_index'));
    }

    #[Route('/carrito/eliminar/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService, Request $request): Response
    {
        $cartService->remove($id);
        
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_cart_index'));
    }

    #[Route('/carrito/vaciar', name: 'app_cart_empty')]
    public function empty(CartService $cartService): Response
    {
        $cartService->empty();
        
        return $this->redirectToRoute('app_cart_index');
    }
}