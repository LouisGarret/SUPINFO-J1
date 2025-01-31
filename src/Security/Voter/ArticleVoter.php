<?php

namespace App\Security\Voter;

use App\Entity\Article;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ArticleVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const DELETE = 'POST_DELETE';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT, self::VIEW])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($user, $subject),
            self::DELETE => $this->canDelete($user, $subject),
            self::VIEW => $this->canView($subject),
            default => throw new LogicException('Code should not reach this point')
        };
    }

    private function canEdit(UserInterface $user, Article $article): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles(), true) || $user === $article->getAuthor();
    }

    private function canDelete(UserInterface $user, Article $article): bool
    {
        return $user === $article->getAuthor();
    }

    private function canView(Article $article): bool
    {
        return $article->getStatus() === Article::STATUS_PUBLISHED;
    }
}
