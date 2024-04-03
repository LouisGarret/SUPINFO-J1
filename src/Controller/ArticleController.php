<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{id}', name: 'find_user', methods: ['GET'])]
    public function find(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT', 'PATCH'])]
    public function update(User $user, Request $request): JsonResponse
    {
        $body = $request->toArray();

        if (array_key_exists('firstName', $body)) {
            $user->setFirstName($body['firstName']);
        }

        if (array_key_exists('lastName', $body)) {
            $user->setLastName($body['lastName']);
        }

        if (array_key_exists('email', $body)) {
            $user->setEmail($body['email']);
        }

        if (array_key_exists('bio', $body)) {
            $user->setBio($body['bio']);
        }

        if (array_key_exists('avatar', $body)) {
            $user->setAvatar($body['avatar']);
        }

        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/', name: 'create_user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $body = $request->toArray();

        $user = new User();
        $user->setFirstName($body['firstName'])
            ->setLastName($body['lastName'])
            ->setEmail($body['email'])
            ->setUsername($body['username']);

        if (array_key_exists('bio', $body)) {
            $user->setBio($body['bio']);
        }

        if (array_key_exists('avatar', $body)) {
            $user->setAvatar($body['avatar']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user);
    }
}
