<?php
namespace App\Http\Controllers;

use App\Models\LocationSearch;
use App\Models\RouteUsage;
use App\Models\CustomRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function storeLocationSearch(Request $request)
    {
        $validatedData = $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
        ]);

        $validatedData['user_id'] = Auth::id();

        $locationSearch = LocationSearch::create($validatedData);

        return response()->json($locationSearch, 201);
    }

    public function storeRouteUsage(Request $request)
    {
        $validatedData = $request->validate([
            'route_name' => 'required|string',
            'waypoints' => 'required|array',
        ]);

        $validatedData['user_id'] = Auth::id();

        $routeUsage = RouteUsage::create($validatedData);

        return response()->json($routeUsage, 201);
    }

    public function storeCustomRoute(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'transport_type' => 'required|string',
            'waypoints' => 'required|array',
            'color' => 'required|string',
            'fare' => 'nullable|numeric',
            'duration' => 'nullable|date_format:H:i:s',
        ]);

        $validatedData['user_id'] = Auth::id();

        $customRoute = CustomRoute::create($validatedData);

        return response()->json($customRoute, 201);
    }
}