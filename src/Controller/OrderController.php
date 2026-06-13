<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/pedido/tramitar', name: 'app_order_checkout')]
    public function checkout(CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $cartService->getFullCart();
        if (empty($cartItems)) {
            return $this->redirectToRoute('app_catalog');
        }

        $order = new Order();
        $order->setUser($user);
        $order->setReference(uniqid('ORD-'));
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('PENDIENTE');
        $order->setTotal($cartService->getTotal());

        foreach ($cartItems as $item) {
            $orderLine = new OrderLine();
            $orderLine->setFlower($item['flor']);
            $orderLine->setQuantity($item['cantidad']);
            $orderLine->setPrice($item['flor']->getPrice());
        
            $orderLine->setPurchaseOrder($order);
            $entityManager->persist($orderLine);
        }

        $entityManager->persist($order);
        $entityManager->flush();
        $cartService->empty();

        return $this->redirectToRoute('app_catalog');
    }
}