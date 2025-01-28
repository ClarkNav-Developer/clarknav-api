<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
 *             required={"title","feature"},
 *             @OA\Property(property="title", type="string", example="Suggestions for Improving Navigation Experience"),
 *             @OA\Property(property="feature", type="string", example="It would be great if the app could display a 'real-time' indicator for when the next bus or train is arriving."),
 *             @OA\Property(property="usability", type="string", example="The app is difficult to navigate."),
 *             @OA\Property(property="performance", type="string", example="The app is slow to load."),
 *             @OA\Property(property="experience", type="string", example="Overall, the app is useful but could be improved."),
 *             @OA\Property(property="suggestions", type="string", example="Add more features for route planning."),
 *             @OA\Property(property="priority", type="string", enum={"LOW","MEDIUM","HIGH"}, example="LOW"),
 *             @OA\Property(property="status", type="string", enum={"UNDER_REVIEW","IN_PROGRESS","IMPLEMENTED","CLOSED"}, example="UNDER_REVIEW")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Feedback submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Suggestions for Improving Navigation Experience"),
 *             @OA\Property(property="feature", type="string", example="It would be great if the app could display a 'real-time' indicator for when the next bus or train is arriving."),
 *             @OA\Property(property="usability", type="string", example="The app is difficult to navigate."),
 *             @OA\Property(property="performance", type="string", example="The app is slow to load."),
 *             @OA\Property(property="experience", type="string", example="Overall, the app is useful but could be improved."),
 *             @OA\Property(property="suggestions", type="string", example="Add more features for route planning."),
 *             @OA\Property(property="priority", type="string", enum={"LOW", "MEDIUM", "HIGH"}, example="LOW"),
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
            'feature' => 'nullable|string',
            'usability' => 'nullable|string',
            'performance' => 'nullable|string',
            'experience' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'priority' => 'required|string|in:' . implode(',', FeedbackPriority::values()),
            'status' => 'required|string|in:' . implode(',', FeedbackStatus::values()),
        ]);

        if (Auth::check()) {
            $validatedData['user_id'] = Auth::id();
        }

        $feedback = Feedback::create($validatedData);

        return response()->json($feedback, 201);
    }
}