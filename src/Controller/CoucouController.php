<?php

namespace App\Controller;

# on va charger le Repository (manager) de Section
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CoucouController extends AbstractController
{
    #[Route('/', name: 'coucou')]
    public function index(SectionRepository $sections): Response
    {
        return $this->render('coucou/index.html.twig', [
            'title' => 'Homepage',
            'sections' => $sections->findAll(),
        ]);
    }


    #[Route('/section/{id}', name: 'section')]
    public function section(int $id, SectionRepository $sections): Response
    {
        # Sélection de la section quand son id vaut celui de la page
        $section = $sections->findOneBy(['id' => $id]);
        return $this->render('coucou/section.html.twig', [
            'title' => $section->getSectionTitle(),
            'section' => $section,
            'sections' => $sections->findAll(),
        ]);
    }
}
