<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    private ArticleRepository $articleRepository;

    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
    }

    /**
     * Get articles with filters.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getArticles(array $filters): LengthAwarePaginator
    {
        return $this->articleRepository->fetchArticles($filters);
    }

    /**
     * Get an article by ID.
     *
     * @param int $id
     * @return \App\Models\Article
     * @throws ModelNotFoundException
     */
    public function getArticleById(int $id): \App\Models\Article
    {
        return $this->articleRepository->findById($id);
    }
}
