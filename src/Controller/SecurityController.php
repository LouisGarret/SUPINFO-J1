<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $body = $request->toArray();

        $user = new User();
        $user->setFirstName($body['firstName'])
            ->setLastName($body['lastName'])
            ->setEmail($body['email'])
            ->setUsername($body['username']);

        $user->setPassword($passwordHasher->hashPassword($user, $body['password']));

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
