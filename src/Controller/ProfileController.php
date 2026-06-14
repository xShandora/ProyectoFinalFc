<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    #[Route('/perfil', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $pedidos = $user->getOrders();

        return $this->render('profile/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/perfil/configuracion', name: 'app_profile_settings')]
    public function settings(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/settings.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/perfil/admin/pedidos', name: 'app_profile_admin_orders')]
    public function adminOrders(EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes permiso para acceder a esta sección.');
        }

        $pedidos = $entityManager->getRepository(Order::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('profile/admin_orders.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/perfil/admin/pedidos/{id}/estado/{estado}', name: 'app_profile_admin_update_status')]
    public function updateOrderStatus(Order $order, string $estado, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes permiso para realizar esta acción.');
        }

        $estadosValidos = ['PENDIENTE', 'ENVIADO', 'ENTREGADO', 'CANCELADO'];
        if (in_array(strtoupper($estado), $estadosValidos)) {
            $order->setStatus(strtoupper($estado));
            $entityManager->flush(); // Guardar en base de datos
        }

        return $this->redirectToRoute('app_profile_admin_orders');
    }
}