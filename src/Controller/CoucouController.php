<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CoucouController extends AbstractController
{
    #[Route('/', name: 'coucou')]
    public function index(): Response
    {
        return $this->render('coucou/index.html.twig', [
            'title' => 'Homepage',
        ]);
    }
}
