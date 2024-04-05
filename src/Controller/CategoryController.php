<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{id}', name: 'find_category', methods: ['GET'])]
    public function find(Category $category): JsonResponse
    {
        return $this->json($category);
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function delete(Category $category): JsonResponse
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json($category);
    }

    #[Route('/', name: 'create_category', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $body = $request->toArray();

        $category = new Category();
        $category->setName($body['name'])
            ->setDescription($body['description']);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json($category);
    }
}
