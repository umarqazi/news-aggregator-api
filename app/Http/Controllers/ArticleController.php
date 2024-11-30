<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleFilterRequest;
use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponse;

    private ArticleService $articleService;

    public function __construct()
    {
        $this->articleService = new ArticleService();
    }

    /**
     * Article Listing
     *
     * @param ArticleFilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ArticleFilterRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $filters = $request->only(['keyword', 'category', 'source', 'date']);
            $articles = $this->articleService->getArticles($filters);

            return $this->successResponse(
                data: $articles,
                message: 'Articles retrieved successfully',
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 500
            );
        }
    }

    /**
     * Fetch Article Detail
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $article = $this->articleService->getArticleById($id);

            return $this->successResponse(
                data: $article,
                message: 'Article retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(
                message: 'Article not found',
                statusCode: 404,
                errors: ['error' => 'Article with the specified ID does not exist.']
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Failed to retrieve the article',
                statusCode: 500,
                errors: ['error' => $e->getMessage()]
            );
        }
    }
}
