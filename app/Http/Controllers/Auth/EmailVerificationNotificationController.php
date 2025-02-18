<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/email/verification-notification",
     *     summary="Send email verification notification",
     *     description="Sends a new email verification notification",
     *     operationId="sendEmailVerificationNotification",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Verification link sent"
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Already verified"
     *     ),
     *     @OA\Header(
     *         header="X-CSRF-TOKEN",
     *         description="CSRF token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    }
}