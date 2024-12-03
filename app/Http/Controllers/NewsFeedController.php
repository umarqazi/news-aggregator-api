<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NewsFeedController extends Controller
{
    use ApiResponse;

    private ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Retrieve personalized news feed based on user preferences
     *
     * @return JsonResponse
     */
    public function getPersonalizedFeed(): JsonResponse
    {
        try {
            $userId = Auth::id();
            $articles = $this->articleService->getPersonalizedNewsFeed($userId);

            return $this->successResponse(
                data: $articles,
                message: 'Personalized feed retrieved successfully.',
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 500
            );
        }
    }
}
