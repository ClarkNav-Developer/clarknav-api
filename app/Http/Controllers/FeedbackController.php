<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\FeedbackPriority;
use App\Enums\FeedbackStatus;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
        $this->middleware(['auth:sanctum', 'isAdmin'])->only(['index', 'show', 'update', 'destroy']);
    }

    /**
     * @OA\Post(
     *     path="/api/feedback",
     *     summary="Submit feedback",
     *     tags={"Feedback"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Feedback")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Feedback submitted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Feedback")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'feature' => 'nullable|string',
            'usability' => 'nullable|string',
            'performance' => 'nullable|string',
            'experience' => 'nullable|string',
            'suggestions' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $validatedData['user_id'] = auth()->check() ? auth()->id() : null;

        $feedback = Feedback::create($validatedData);

        return response()->json($feedback, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/feedback",
     *     summary="Get all feedback",
     *     tags={"Feedback"},
     *     @OA\Response(
     *         response=200,
     *         description="List of feedback",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Feedback"))
     *     )
     * )
     */
    public function index()
    {
        $feedbacks = Feedback::all();
        return response()->json($feedbacks);
    }

    /**
     * @OA\Get(
     *     path="/api/feedback/{id}",
     *     summary="Get a specific feedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback details",
     *         @OA\JsonContent(ref="#/components/schemas/Feedback")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Feedback not found"
     *     )
     * )
     */
    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        return response()->json($feedback);
    }

    /**
     * @OA\Put(
     *     path="/api/feedback/{id}",
     *     summary="Update feedback status",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"UNDER_REVIEW","IN_PROGRESS","IMPLEMENTED","CLOSED"}, example="IN_PROGRESS")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Feedback")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Feedback not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:' . implode(',', FeedbackStatus::values()),
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update($validatedData);

        return response()->json($feedback);
    }

    /**
     * @OA\Delete(
     *     path="/api/feedback/{id}",
     *     summary="Delete feedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Feedback not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted successfully']);
    }
}