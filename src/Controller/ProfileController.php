<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}