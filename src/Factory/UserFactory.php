<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $username,
        string $password,
        ?string $bio = null,
        ?string $avatar = null
    ): User {
        $user = new User();
        $user->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setUsername($username);

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        if ($bio)
        {
            $user->setBio($bio);
        }

        if ($avatar) {
            $user->setAvatar($avatar);
        }

        return $user;
    }
}
