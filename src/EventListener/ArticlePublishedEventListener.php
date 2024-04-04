<?php

namespace App\EventListener;

use App\Entity\Notification;
use App\Entity\User;
use App\Event\ArticlePublishedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class ArticlePublishedEventListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[AsEventListener(event: ArticlePublishedEvent::class)]
    public function onArticlePublishedEvent(ArticlePublishedEvent $event): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if ($user === $event->getArticle()->getAuthor()) {
                continue;
            }

            $notification = new Notification();
            $notification->setMessage(
                sprintf('New article from %s published', $event->getArticle()->getAuthor()->getUsername())
            )
            ->setUser($user);

            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }
}
