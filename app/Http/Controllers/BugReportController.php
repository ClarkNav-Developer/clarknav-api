<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Enums\BugCategory;
use App\Enums\BugFrequency;
use App\Enums\BugPriority;
use App\Enums\BugStatus;

/**
 * @OA\Post(
 *     path="/api/bug-reports",
 *     summary="Create a new bug report",
 *     tags={"Bug Reports"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","category","description","steps","expected","actual","device","frequency"},
 *             @OA\Property(property="title", type="string", example="App Crashes When Searching for Routes"),
 *             @OA\Property(property="category", type="string", example="UI/UX Issue"),
 *             @OA\Property(property="description", type="string", example="The app crashes when I try to search for a route using public transit."),
 *             @OA\Property(property="steps", type="string", example="1. Open the app\n2. Search for a route\n3. App crashes"),
 *             @OA\Property(property="expected", type="string", example="The app should display the search results."),
 *             @OA\Property(property="actual", type="string", example="The app crashes."),
 *             @OA\Property(property="device", type="string", example="iPhone 12, iOS 14.4"),
 *             @OA\Property(property="frequency", type="string", example="Always"),
 *             @OA\Property(property="screenshots", type="string", format="binary", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Bug report created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="App Crashes When Searching for Routes"),
 *             @OA\Property(property="category", type="string", example="UI/UX Issue"),
 *             @OA\Property(property="description", type="string", example="The app crashes when I try to search for a route using public transit."),
 *             @OA\Property(property="steps", type="string", example="1. Open the app\n2. Search for a route\n3. App crashes"),
 *             @OA\Property(property="expected", type="string", example="The app should display the search results."),
 *             @OA\Property(property="actual", type="string", example="The app crashes."),
 *             @OA\Property(property="device", type="string", example="iPhone 12, iOS 14.4"),
 *             @OA\Property(property="frequency", type="string", example="Always"),
 *             @OA\Property(property="screenshots", type="string", example="screenshots/example.png", nullable=true),
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

class BugReportController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', BugCategory::values()),
            'description' => 'required|string',
            'steps' => 'required|string',
            'expected' => 'required|string',
            'actual' => 'required|string',
            'device' => 'nullable|string',
            'frequency' => 'required|string|in:' . implode(',', BugFrequency::values()),
            'screenshots' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('screenshots')) {
            $filePath = $request->file('screenshots')->store('screenshots', 'public');
            $validatedData['screenshots'] = $filePath;
        }

        if (Auth::check()) {
            $validatedData['user_id'] = Auth::id();
        }

        $bugReport = BugReport::create($validatedData);

        return response()->json($bugReport, 201);
    }
}
