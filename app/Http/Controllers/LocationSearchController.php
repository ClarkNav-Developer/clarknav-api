<?php

namespace App\Http\Controllers;

use App\Models\LocationSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationSearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/location-searches",
     *     summary="Get all location searches",
     *     tags={"Location Searches"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of location searches",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="frequency", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         ))
     *     )
     * )
     */
    public function index()
    {
        $searches = LocationSearch::all();
        return response()->json($searches);
    }

    /**
     * @OA\Post(
     *     path="/api/location-searches",
     *     summary="Store a new location search",
     *     tags={"Location Searches"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origin","destination"},
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location search created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="frequency", type="integer", example=1),
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
        ]);

        $search = LocationSearch::create([
            'user_id' => Auth::id(),
            'origin' => $request->origin,
            'destination' => $request->destination,
            'frequency' => 1
        ]);

        return response()->json($search, 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/location-searches/{id}/increment",
     *     summary="Increment the frequency of a location search",
     *     tags={"Location Searches"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location search frequency incremented successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="New York"),
     *             @OA\Property(property="destination", type="string", example="Los Angeles"),
     *             @OA\Property(property="frequency", type="integer", example=2),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location search not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Location search not found.")
     *         )
     *     )
     * )
     */
    public function incrementFrequency($id)
    {
        $search = LocationSearch::findOrFail($id);
        $search->increment('frequency');
        return response()->json($search);
    }
}