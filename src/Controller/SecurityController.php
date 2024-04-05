<?php

namespace App\Controller;

use App\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function create(Request $request, UserFactory $userFactory): JsonResponse
    {
        $body = $request->toArray();

        $user = $userFactory->create(
            $body['firstName'],
            $body['lastName'],
            $body['email'],
            $body['username'],
            $body['password'],
            $body['bio'] ?? null,
            $body['avatar'] ?? null,
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user);
    }
}
