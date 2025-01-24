<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Enums\FeedbackCategory;
use App\Enums\FeedbackPriority;
use App\Enums\FeedbackStatus;

/**
 * @OA\Post(
 *     path="/api/feedback",
 *     summary="Submit feedback",
 *     tags={"Feedback"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","comments","category","priority","status"},
 *             @OA\Property(property="title", type="string", example="Suggestions for Improving Navigation Experience"),
 *             @OA\Property(property="comments", type="string", example="It would be great if the app could display a 'real-time' indicator for when the next bus or train is arriving."),
 *             @OA\Property(property="category", type="string", enum={"FEATURE_SUGGESTION","USABILITY_ISSUE","APP_PERFORMANCE","ROUTE_ACCURACY","GENERAL_EXPERIENCE","ADDITIONAL_SUGGESTIONS"}, example="FEATURE_SUGGESTION"),
 *             @OA\Property(property="priority", type="string", enum={"LOW","MEDIUM","HIGH"}, example="MEDIUM"),
 *             @OA\Property(property="status", type="string", enum={"UNDER_REVIEW","IN_PROGRESS","IMPLEMENTED","CLOSED"}, example="UNDER_REVIEW")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Feedback submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Suggestions for Improving Navigation Experience"),
 *             @OA\Property(property="comments", type="string", example="It would be great if the app could display a 'real-time' indicator for when the next bus or train is arriving."),
 *             @OA\Property(property="category", type="string", enum={"FEATURE_SUGGESTION", "USABILITY_ISSUE", "APP_PERFORMANCE", "ROUTE_ACCURACY", "GENERAL_EXPERIENCE", "ADDITIONAL_SUGGESTIONS"}, example="FEATURE_SUGGESTION"),
 *             @OA\Property(property="priority", type="string", enum={"LOW", "MEDIUM", "HIGH"}, example="MEDIUM"),
 *             @OA\Property(property="status", type="string", enum={"UNDER_REVIEW", "IN_PROGRESS", "IMPLEMENTED", "CLOSED"}, example="UNDER_REVIEW"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */
class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'comments' => 'required|string',
            'category' => 'required|in:FEATURE_SUGGESTION,USABILITY_ISSUE,APP_PERFORMANCE,ROUTE_ACCURACY,GENERAL_EXPERIENCE,ADDITIONAL_SUGGESTIONS',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'status' => 'required|in:UNDER_REVIEW,IN_PROGRESS,IMPLEMENTED,CLOSED',
        ]);

        $feedback = Feedback::create($validatedData);

        return response()->json($feedback, 201);
    }
}