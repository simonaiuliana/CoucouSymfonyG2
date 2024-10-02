<?php

namespace App\Controller;

# on va charger le Repository (manager) de Section
use App\Entity\Section;
use App\Repository\SectionRepository;
# on va utiliser l'entité de Post
use App\Entity\Post;
use App\Repository\PostRepository;
# on va charger le gestionnaire d'entité
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CoucouController extends AbstractController
{
    #[Route('/', name: 'coucou')]
    public function index(EntityManagerInterface $em): Response
    {
        # on va charger tous les `Post` publiés classés par ordre de création
        $posts = $em->getRepository(Post::class)->findBy(['postPublished'=>true],['postDateCreated'=>"DESC"]);

        $partic = $em->getRepository(Post::class)->findByTitleDesc();

        return $this->render('coucou/index.html.twig', [
            'title' => 'Homepage',
            'sections' => $em->getRepository(Section::class)->findAll(),
            'posts' => $posts,
            'postPartic' => $partic,
        ]);
    }


    #[Route('/section/{id}', name: 'section')]
    public function section(int $id, SectionRepository $sections): Response
    {
        # Sélection de la section quand son id vaut celui de la page
        $section = $sections->findOneBy(['id' => $id]);
        return $this->render('coucou/section.html.twig', [
            # titre
            'title' => $section->getSectionTitle(),
            # section seule via son id
            'section' => $section,
            # toutes les sections
            'sections' => $sections->findAll(),
        ]);
    }
}
