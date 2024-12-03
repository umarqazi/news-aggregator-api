<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArticleRepository
{
    private Article $article;

    public function __construct()
    {
        $this->article = new Article();
    }

    /**
     * Fetch articles with optional filters.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function fetchArticles(array $filters): LengthAwarePaginator
    {
        $query = $this->article->query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('author', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('published_at', $filters['date']);
        }

        return $query->paginate(10);
    }

    /**
     * Fetch an article by ID.
     *
     * @param int $id
     * @return Article
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Article
    {
        return $this->article->findOrFail($id);
    }

    /**
     * Create New Article
     *
     * @param array $data
     * @return Article
     */
    public function create(array $data): Article
    {
        return $this->article->create($data);
    }


    /**
     * Fetch News Articles based on User Preferences.
     *
     * @param array $preferences
     * @return LengthAwarePaginator
     */
    public function fetchPersonalizedNewsFeed(array $preferences): LengthAwarePaginator
    {
        $query = $this->article->query();

        if (!empty($preferences['preferred_categories'])) {
            $query->whereIn('category', $preferences['preferred_categories']);
        }

        if (!empty($preferences['preferred_sources'])) {
            $query->whereIn('source', $preferences['preferred_sources']);
        }

        if (!empty($preferences['preferred_authors'])) {
            $query->whereIn('source', $preferences['preferred_authors']);
        }

        return $query->paginate(10);
    }
}
