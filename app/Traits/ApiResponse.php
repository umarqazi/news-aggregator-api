<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Send a successful response.
     *
     * @param mixed|null $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(mixed $data = null, string $message = 'Success', int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $message, int $statusCode = 400, mixed $errors = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Send a response for resource not found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFoundResponse(string $message = 'Resource not found'): \Illuminate\Http\JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Send a response for validation errors.
     *
     * @param mixed $errors
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationErrorResponse(mixed $errors, string $message = 'Validation failed'): \Illuminate\Http\JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }
}
