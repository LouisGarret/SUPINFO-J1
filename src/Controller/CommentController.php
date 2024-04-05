<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/comments')]
class CommentController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/comments/{id}', name: 'find_comment', methods: ['GET'])]
    public function find(Comment $comment): JsonResponse
    {
        return $this->json($comment);
    }

    #[Route('/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function delete(Comment $comment): JsonResponse
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $this->json($comment);
    }

    #[Route('/', name: 'create_comment', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $body = $request->toArray();

        $article = $this->entityManager->getRepository(Article::class)->find($body['article']);

        if (!$article) {
            return $this->json(['error' => 'No article found'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->entityManager->getRepository(User::class)->find($body['user']);

        if (!$user) {
            return $this->json(['error' => 'No user found'], Response::HTTP_NOT_FOUND);
        }

        $comment = new Comment();
        $comment->setContent($body['content'])
            ->setArticle($article)
            ->setAuthor($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $this->json($comment);
    }
}
