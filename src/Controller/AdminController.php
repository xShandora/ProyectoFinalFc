<?php

namespace App\Controller;

use App\Entity\Flower;
use App\Form\FlowerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // --- 1. RUTA PARA AÑADIR FLORES (El formulario) ---
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $flor = new Flower();
        $formulario = $this->createForm(FlowerType::class, $flor);

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
            $imageFile = $formulario->get('image')->getData();
            
            if ($imageFile) {
                $nuevoNombre = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/flowers',
                    $nuevoNombre
                );

                $flor->setImage($nuevoNombre);
            }

            $entityManager->persist($flor);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/index.html.twig', [
            'formulario' => $formulario->createView(),
        ]);
    }

    // --- 2. RUTA PARA BORRAR FLORES (La que usa el JavaScript) ---
    #[Route('/admin/flor/borrar/{id}', name: 'app_admin_flower_delete', methods: ['DELETE'])]
    public function delete(int $id, \App\Repository\FlowerRepository $flowerRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $flor = $flowerRepository->find($id);

        if ($flor) {
            $nombreImagen = $flor->getImage();
            if ($nombreImagen) {
                $rutaImagen = $this->getParameter('kernel.project_dir').'/public/uploads/flowers/'.$nombreImagen;
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            $entityManager->remove($flor);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false], 404);
    }
}