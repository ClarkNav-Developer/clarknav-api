<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="ClarkNav API",
 *     version="1.0.1",
 *     description="API documentation for ClarkNav"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"firstname", "lastname", "email", "password"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="firstname", type="string"),
 *     @OA\Property(property="lastname", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 *     @OA\Property(property="isAdmin", type="boolean"),
 *     @OA\Property(property="isUser", type="boolean"),
 *     @OA\Property(property="remember_token", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="CustomRoute",
 *     type="object",
 *     required={"origin", "destination", "route_name", "fare", "duration", "departure_time", "arrival_time", "departure_date"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="user_id", type="integer", format="int64"),
 *     @OA\Property(property="origin", type="string"),
 *     @OA\Property(property="destination", type="string"),
 *     @OA\Property(property="route_name", type="string"),
 *     @OA\Property(property="fare", type="number", format="float"),
 *     @OA\Property(property="student_fare", type="number", format="float"),
 *     @OA\Property(property="duration", type="string"),
 *     @OA\Property(property="departure_time", type="string"),
 *     @OA\Property(property="arrival_time", type="string"),
 *     @OA\Property(property="departure_date", type="string", format="date"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Feedback",
 *     type="object",
 *     required={"title"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="user_id", type="integer", format="int64"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="feature", type="string"),
 *     @OA\Property(property="usability", type="string"),
 *     @OA\Property(property="performance", type="string"),
 *     @OA\Property(property="experience", type="string"),
 *     @OA\Property(property="suggestions", type="string"),
 *     @OA\Property(property="priority", type="string", enum={"LOW", "MEDIUM", "HIGH"}),
 *     @OA\Property(property="status", type="string", enum={"UNDER_REVIEW", "IN_PROGRESS", "IMPLEMENTED", "CLOSED"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="LocationSearch",
 *     type="object",
 *     required={"origin", "destination"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="user_id", type="integer", format="int64"),
 *     @OA\Property(property="origin", type="string"),
 *     @OA\Property(property="destination", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="NavigationHistory",
 *     type="object",
 *     required={"origin", "destination", "route_details", "navigation_confirmed"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="user_id", type="integer", format="int64"),
 *     @OA\Property(property="origin", type="string"),
 *     @OA\Property(property="destination", type="string"),
 *     @OA\Property(property="route_details", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="navigation_confirmed", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="RouteUsage",
 *     type="object",
 *     required={"route_id", "route_name", "color", "origin", "destination", "route_type"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="user_id", type="integer", format="int64"),
 *     @OA\Property(property="route_id", type="string"),
 *     @OA\Property(property="route_name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="color", type="string"),
 *     @OA\Property(property="origin", type="string"),
 *     @OA\Property(property="destination", type="string"),
 *     @OA\Property(property="route_type", type="string", enum={"Jeepney", "Bus", "Taxi"}),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}