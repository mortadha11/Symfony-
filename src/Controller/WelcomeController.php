<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WelcomeController extends AbstractController
{
   #[Route('/welcome', name: 'app_welcome')]
public function index(): Response
{
    $message = "Welcome to my Symfony App sa7bi amen !";

    return $this->render('welcome/index.html.twig', [
        'message' => $message,
    ]);
}

}
