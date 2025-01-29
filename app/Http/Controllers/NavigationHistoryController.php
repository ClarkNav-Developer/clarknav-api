<?php

namespace App\Http\Controllers;

use App\Models\NavigationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationHistoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/navigation-histories",
     *     summary="Get all navigation histories for the authenticated user",
     *     tags={"Navigation Histories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of navigation histories",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="route_details", type="array", @OA\Items(type="string"), example={"Waypoint 1", "Waypoint 2"}),
     *             @OA\Property(property="navigation_confirmed", type="boolean", example=true),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         ))
     *     )
     * )
     */
    public function index()
    {
        $histories = NavigationHistory::where('user_id', Auth::id())->get();
        return response()->json($histories);
    }

    /**
     * @OA\Post(
     *     path="/api/navigation-histories",
     *     summary="Store a new navigation history",
     *     tags={"Navigation Histories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origin","destination","route_details","navigation_confirmed"},
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="route_details", type="array", @OA\Items(type="string"), example={"Waypoint 1", "Waypoint 2"}),
     *             @OA\Property(property="navigation_confirmed", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Navigation history created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="route_details", type="array", @OA\Items(type="string"), example={"Waypoint 1", "Waypoint 2"}),
     *             @OA\Property(property="navigation_confirmed", type="boolean", example=true),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'route_details' => 'required|array',
            'navigation_confirmed' => 'required|boolean',
        ]);

        $history = NavigationHistory::create([
            'user_id' => Auth::id(),
            'origin' => $request->origin,
            'destination' => $request->destination,
            'route_details' => $request->route_details,
            'navigation_confirmed' => $request->navigation_confirmed,
        ]);

        return response()->json($history, 201);
    }
}