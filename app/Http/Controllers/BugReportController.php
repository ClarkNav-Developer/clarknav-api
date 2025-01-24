<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\Request;
use App\Enums\BugStatus;
use App\Enums\BugPriority;
use App\Enums\DeviceOrOSType;
use App\Enums\BrowserType;

/**
 * @OA\Post(
 *     path="/api/bug-reports",
 *     summary="Create a new bug report",
 *     tags={"Bug Reports"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","description","priority","status","device","device_os","browser"},
 *             @OA\Property(property="title", type="string", example="App Crashes When Searching for Routes"),
 *             @OA\Property(property="description", type="string", example="The app crashes when I try to search for a route using public transit."),
 *             @OA\Property(property="priority", type="string", enum={"LOW","MEDIUM","HIGH","CRITICAL"}, example="HIGH"),
 *             @OA\Property(property="status", type="string", enum={"OPEN","IN_PROGRESS","RESOLVED","CLOSED"}, example="OPEN"),
 *             @OA\Property(property="device", type="string", example="iPhone 12"),
 *             @OA\Property(property="device_os", type="string", enum={"ANDROID","IOS","WINDOWS","MACOS","LINUX","OTHER"}, example="IOS"),
 *             @OA\Property(property="browser", type="string", enum={"CHROME","SAFARI","FIREFOX","EDGE","OPERA","OTHER"}, example="SAFARI")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Bug report created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="App Crashes When Searching for Routes"),
 *             @OA\Property(property="description", type="string", example="The app crashes when I try to search for a route using public transit."),
 *             @OA\Property(property="priority", type="string", enum={"LOW", "MEDIUM", "HIGH", "CRITICAL"}, example="HIGH"),
 *             @OA\Property(property="status", type="string", enum={"OPEN", "IN_PROGRESS", "RESOLVED", "CLOSED"}, example="OPEN"),
 *             @OA\Property(property="device", type="string", example="iPhone 12"),
 *             @OA\Property(property="device_os", type="string", enum={"ANDROID", "IOS", "WINDOWS", "MACOS", "LINUX", "OTHER"}, example="IOS"),
 *             @OA\Property(property="browser", type="string", enum={"CHROME", "SAFARI", "FIREFOX", "EDGE", "OPERA", "OTHER"}, example="SAFARI"),
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
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED',
            'device' => 'required|string|max:255',
            'device_os' => 'required|in:ANDROID,IOS,WINDOWS,MACOS,LINUX,OTHER',
            'browser' => 'required|in:CHROME,SAFARI,FIREFOX,EDGE,OPERA,OTHER',
        ]);

        $bugReport = BugReport::create($validatedData);

        return response()->json($bugReport, 201);
    }
}