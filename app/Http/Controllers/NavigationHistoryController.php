<?php

namespace App\Http\Controllers;

use App\Models\NavigationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'isUser']);
    }

    /**
     * @OA\Get(
     *     path="/api/navigation-histories",
     *     summary="Get all navigation histories",
     *     tags={"Navigation Histories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of navigation histories",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/NavigationHistory"))
     *     )
     * )
     */
    public function index()
    {
        $userId = Auth::id();
        $histories = NavigationHistory::where('user_id', $userId)->get();
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
     *         @OA\JsonContent(ref="#/components/schemas/NavigationHistory")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Navigation history created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/NavigationHistory")
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
            'user_id' => Auth::check() ? Auth::id() : null,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'route_details' => $request->route_details,
            'navigation_confirmed' => $request->navigation_confirmed,
        ]);

        return response()->json($history, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/navigation-histories/{id}",
     *     summary="Delete a navigation history",
     *     tags={"Navigation Histories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Navigation history deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Navigation history not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Navigation history not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $history = NavigationHistory::findOrFail($id);
        $history->delete();

        return response()->json(null, 204);
    }
}