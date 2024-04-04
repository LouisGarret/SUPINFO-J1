<?php

namespace App\Event;

use App\Entity\Article;
use Symfony\Contracts\EventDispatcher\Event;

class ArticlePublishedEvent extends Event
{
    public function __construct(private readonly Article $article) {}

    public function getArticle(): Article
    {
        return $this->article;
    }
}
