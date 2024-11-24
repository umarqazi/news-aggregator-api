<?php

namespace App\Services\Mappers;

use Carbon\Carbon;

class NewsArticleMapper
{
    /**
     * Map NewsAPI response to unified structure.
     *
     * @param array $article
     * @return array
     */
    public function mapFromNewsAPI(array $article): array
    {
        return [
            'title' => $article['title'] ?? '',
            'content' => $article['content'] ?? $article['description'] ?? '',
            'author' => $article['author'] ?? 'Unknown',
            'category' => $article['category'] ?? null, // NewsAPI may not provide categories.
            'source' => 'NewsAPI',
            'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : now(),
        ];
    }

    /**
     * Map Guardian API response to unified structure.
     *
     * @param array $article
     * @return array
     */
    public function mapFromGuardian(array $article): array
    {
        return [
            'title' => $article['webTitle'] ?? '',
            'content' => $article['fields']['bodyText'] ?? '',
            'author' => $article['fields']['byline'] ?? 'Unknown',
            'category' => $article['sectionName'] ?? null,
            'source' => 'The Guardian',
            'published_at' => isset($article['webPublicationDate']) ? Carbon::parse($article['webPublicationDate']) : now(),
        ];
    }

    /**
     * Map NYTimes response to unified structure.
     *
     * @param array $article
     * @return array
     */
    public function mapFromNYTimes(array $article): array
    {
        return [
            'title' => $article['headline']['main'] ?? '',
            'content' => $article['abstract'] ?? '',
            'author' => $article['byline']['original'] ?? 'Unknown',
            'category' => $article['section'] ?? null,
            'source' => 'New York Times',
            'published_at' => isset($article['pub_date']) ? Carbon::parse($article['pub_date']) : now(),
        ];
    }
}
