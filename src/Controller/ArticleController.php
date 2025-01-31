<?php

namespace App\Controller;

use App\Entity\Article;
use App\Service\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/articles')]
final class ArticleController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager){
    }

    #[Route('/', name: 'get_all_articles', methods: ['GET'])]
    public function getAllArticles(): JsonResponse
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        return new JsonResponse($articles);
    }

    #[Route('/', name: 'add_article', methods: ['POST'])]
    public function addArticle(Request $request, ArticleService $articleService): JsonResponse
    {
        $article = $articleService->add($request);

        return $this->json(data: $article, context: ['groups' => ['articles:read']]);
    }

    #[Route('/{id}', name: 'delete_article', methods: ['DELETE'])]
    #[IsGranted('POST_DELETE', 'article', 'You can\'t delete an article that is not yours')]
    public function deleteArticle(Article $article, ArticleService $articleService): JsonResponse
    {
        $articleService->delete($article);

        return $this->json('OK');
    }
}
