<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortalController extends Controller
{
    /**
     * Show the portal SPA
     *
     * Validates the signed URL and generates a JWT token for the user.
     * The portal is a stateless SPA that uses the JWT token to authenticate API requests.
     */
    public function index(Request $request): Response
    {
        // Validate signed URL
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired portal link');
        }

        // Get user ID from query parameter
        $userId = $request->query('user');

        if (! $userId) {
            abort(403, 'Invalid portal link');
        }

        // Find the user
        $user = User::find($userId);

        if (! $user) {
            abort(403, 'User not found');
        }

        // Generate a user token for the portal, expiring with the portal link
        // so leaked tokens cannot be used indefinitely
        $expiresAt = now()->addHours((int) config('portal.link_expiration_hours', 24));
        $userToken = $user->createToken('portal-access', ['*'], $expiresAt)->plainTextToken;

        return Inertia::render('Portal/App', [
            'userToken' => $userToken,
            'apiBaseUrl' => config('app.url'),
        ]);
    }
}
