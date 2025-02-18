<?php

namespace App\Http\Controllers;

use App\Models\CustomRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomRouteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'checkUser']);
    }

    /**
     * @OA\Get(
     *     path="/api/custom-routes",
     *     summary="Get all custom routes",
     *     tags={"Custom Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of custom routes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CustomRoute"))
     *     )
     * )
     */
    public function index()
    {
        $userId = Auth::id();
        $routes = CustomRoute::where('user_id', $userId)->get();
        return response()->json($routes);
    }

    /**
     * @OA\Post(
     *     path="/api/custom-routes",
     *     summary="Store a new custom route",
     *     tags={"Custom Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CustomRoute")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Custom route created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CustomRoute")
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
        $validatedData = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'fare' => 'required|numeric',
            'student_fare' => 'nullable|numeric',
            'duration' => 'required|string|max:255',
            'departure_time' => 'required|string|max:255',
            'arrival_time' => 'required|string|max:255',
            'departure_date' => 'required|date',
        ]);

        $validatedData['user_id'] = Auth::id();

        $route = CustomRoute::create($validatedData);
        return response()->json($route, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/custom-routes/{id}",
     *     summary="Get a specific custom route",
     *     tags={"Custom Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Custom route details",
     *         @OA\JsonContent(ref="#/components/schemas/CustomRoute")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Custom route not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Custom route not found.")
     *         )
     *     )
     * )
     */
    public function show(CustomRoute $route)
    {
        return response()->json($route);
    }

    /**
     * @OA\Put(
     *     path="/api/custom-routes/{id}",
     *     summary="Update a specific custom route",
     *     tags={"Custom Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CustomRoute")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Custom route updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CustomRoute")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Custom route not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Custom route not found.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, CustomRoute $route)
    {
        $validatedData = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'fare' => 'required|numeric',
            'student_fare' => 'nullable|numeric',
            'duration' => 'required|string|max:255',
            'departure_time' => 'required|string|max:255',
            'arrival_time' => 'required|string|max:255',
            'departure_date' => 'required|date',
        ]);

        $route->update($validatedData);
        return response()->json($route);
    }

    /**
     * @OA\Delete(
     *     path="/api/custom-routes/{id}",
     *     summary="Delete a specific custom route",
     *     tags={"Custom Routes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Custom route deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Custom route not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Custom route not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $route = CustomRoute::find($id);

        if (!$route) {
            return response()->json(['message' => 'Custom route not found.'], 404);
        }

        $route->delete();
        return response()->json(['message' => 'Custom route deleted successfully.'], 204);
    }
}