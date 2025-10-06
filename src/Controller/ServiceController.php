<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
    #[Route(path: 'goToIndex', name: 'go_to_index')]
    public function goToIndex(): Response
    {
        return $this->redirectToRoute('app_service');
    }

    #[Route(path: '/serviceshow/{name}', name :'show_service')]
    public function showService($name): Response
    {
        return $this->render('service/showservice.html.twig', [
            'name' => $name,
        ]);
    }
}
