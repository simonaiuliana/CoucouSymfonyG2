<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Tag;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// (C)afé, (R)écupération après avoir cassé le code, (U)ltrarapide prise de panique, (D)ebug toute la nuit !
// (C)'est (R)elou, (U)nique dans sa capacité à (D)éclencher des bugs incompréhensibles. 😑
#[Route('/admin/post')]
final class AdminPostController extends AbstractController
{
    #[Route(name: 'app_admin_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('admin_post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'title' => 'Liste des posts',
        ]);
    }

    #[Route('/new', name: 'app_admin_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tagRepository = $entityManager->getRepository(Tag::class);
        $tags = $tagRepository->findAll();
        $tagNames = [];
        foreach($tags as $tag) $tagNames[$tag->getTagName()] = $tag->getId();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post, ['tags' => $tagNames]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($post->getTagsId() as $tagId){
                $post->addTag($tagRepository->findOneBy([
                    'id' => $tagId
                ]));
            }
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_post/new.html.twig', [
            'post' => $post,
            'form' => $form,
            'title' => 'Nouveau post',
        ]);
    }

    #[Route('/{id}', name: 'app_admin_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('admin_post/show.html.twig', [
            'post' => $post,
            'title' => $post->getPostTitle(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $tagRepository = $entityManager->getRepository(Tag::class);
        $tags = $tagRepository->findAll();
        $tagNames = [];
        $tagDatas = [];
        foreach($tags as $tag) $tagNames[$tag->getTagName()] = $tag->getId();
        foreach($post->getTags() as $tag) $tagDatas[] = $tag->getId();
        $form = $this->createForm(PostType::class, $post, ['tags' => $tagNames, 'tagDatas' => $tagDatas]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
            'title' => 'Modifier '.$post->getPostTitle(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
