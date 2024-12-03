<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\UserPreferenceRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticleService
{
    private ArticleRepository $articleRepository;
    private UserPreferenceRepository $userPreferenceRepository;

    public function __construct()
    {
        $this->articleRepository = new ArticleRepository();
        $this->userPreferenceRepository = new UserPreferenceRepository();
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
     * @return Article
     * @throws ModelNotFoundException
     */
    public function getArticleById(int $id): Article
    {
        return $this->articleRepository->findById($id);
    }

    /**
     * Get Personalized News Feed
     *
     * @param int $userId
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function getPersonalizedNewsFeed(int $userId): LengthAwarePaginator
    {
        $preferences = $this->userPreferenceRepository->findByUserId($userId);
        if (empty($preferences)) {
            throw new \Exception('No preferences set for personalized feed.');
        }

        return $this->articleRepository->fetchPersonalizedNewsFeed($preferences->toArray());
    }
}
