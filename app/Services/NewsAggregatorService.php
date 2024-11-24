<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Services\Mappers\NewsArticleMapper;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    private NewsArticleMapper $mapper;
    protected ArticleRepository $articleRepository;

    public function __construct(
        NewsArticleMapper $mapper,
        ArticleRepository $articleRepository
    )
    {
        $this->mapper = $mapper;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Fetch and store articles from all sources.
     *
     * @return void
     * @throws GuzzleException
     */
    public function fetchAndStoreArticles(): void
    {
        $this->fetchFromNewsAPI();
        $this->fetchFromGuardian();
        $this->fetchFromNYTimes();
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchFromNewsAPI(): void
    {
        try {
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'apiKey' => config('services.newsapi.key'),
                'country' => 'us',
            ]);

            if ($response->successful()) {
                $articles = $response->json()['articles'] ?? [];
                foreach ($articles as $article) {
                    $mappedArticle = $this->mapper->mapFromNewsAPI($article);
                    $this->articleRepository->create($mappedArticle);
                }
            } else {
                throw new \Exception('NewsAPI responded with error: ' . $response->status());
            }
        } catch (GuzzleException $e) {
            Log::error('GuzzleException in NewsAPI: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching from NewsAPI: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchFromGuardian(): void
    {
        try {
            $response = Http::get('https://content.guardianapis.com/search', [
                'api-key' => config('services.guardian.key'),
            ]);

            if ($response->successful()) {
                $articles = $response->json()['response']['results'] ?? [];
                foreach ($articles as $article) {
                    $mappedArticle = $this->mapper->mapFromGuardian($article);
                    $this->articleRepository->create($mappedArticle);
                }
            } else {
                throw new \Exception('The Guardian API responded with error: ' . $response->status());
            }
        } catch (GuzzleException $e) {
            Log::error('GuzzleException in The Guardian: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching from The Guardian: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function fetchFromNYTimes(): void
    {
        try {
            $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
                'api-key' => config('services.nytimes.key'),
            ]);

            if ($response->successful()) {
                $articles = $response->json()['results'] ?? [];

                foreach ($articles as $article) {
                    $mappedArticle = $this->mapper->mapFromNYTimes($article);
                    $this->articleRepository->create($mappedArticle);
                }
            } else {
                throw new \Exception('NY Times API responded with error: ' . $response->status());
            }
        } catch (GuzzleException $e) {
            Log::error('GuzzleException in NY Times: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching from NY Times: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
