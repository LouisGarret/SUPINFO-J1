<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class ArticleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    public function add(Request $request): Article
    {
        $data = $request->toArray();
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if (!$currentUser) {
            throw new AccessDeniedException('User not logged in');
        }

        $category = $this->entityManager->getRepository(Category::class)->find($data['category']);

        if (!$category) {
            throw new BadRequestException('Category not found');
        }

        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);
        $article->setAuthor($currentUser);
        $article->setCategory($category);
        $article->setStatus(Article::STATUS_PUBLISHED);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    public function delete(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}