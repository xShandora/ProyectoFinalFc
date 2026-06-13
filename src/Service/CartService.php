<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\FlowerRepository;

class CartService
{
    private $requestStack;
    private $flowerRepository;

    public function __construct(RequestStack $requestStack, FlowerRepository $flowerRepository)
    {
        $this->requestStack = $requestStack;
        $this->flowerRepository = $flowerRepository;
    }

    public function add(int $id, int $cantidad = 1): void
    {
        $session = $this->requestStack->getSession();
        
        $carrito = $session->get('carrito', []);

        if (!empty($carrito[$id])) {
            $carrito[$id] += $cantidad;
        } else {
            $carrito[$id] = $cantidad;
        }

        $session->set('carrito', $carrito);
    }

    public function decrease(int $id): void
    {
        $session = $this->requestStack->getSession();
        $carrito = $session->get('carrito', []);

        if (!empty($carrito[$id])) {
            if ($carrito[$id] > 1) {
                $carrito[$id]--;
            } else {
                unset($carrito[$id]);
            }
        }

        $session->set('carrito', $carrito);
    }

    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $carrito = $session->get('carrito', []);

        unset($carrito[$id]);

        $session->set('carrito', $carrito);
    }

    public function empty(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('carrito');
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $carrito = $session->get('carrito', []);

        $carritoCompleto = [];

        foreach ($carrito as $id => $cantidad) {
            $flor = $this->flowerRepository->find($id);
            
            if ($flor) {
                $carritoCompleto[] = [
                    'flor' => $flor,
                    'cantidad' => $cantidad
                ];
            }
        }

        return $carritoCompleto;
    }


}