<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutPageController extends Controller
{
    /**
     * Show the checkout SPA
     *
     * Validates the signed URL and generates a JWT token for the user.
     * The checkout page is a stateless SPA that uses the JWT token to authenticate API requests.
     */
    public function index(Request $request): Response
    {
        // Validate signed URL
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired checkout link');
        }

        // Get user ID from query parameter
        $userId = $request->query('user');
        if (! $userId) {
            abort(403, 'Invalid checkout link');
        }

        // Find the user
        $user = User::find($userId);
        if (! $user) {
            abort(403, 'User not found');
        }

        // Generate a user token for the SPA, expiring with the checkout link
        // so leaked tokens cannot be used indefinitely
        $expiresAt = now()->addHours((int) config('portal.link_expiration_hours', 24));
        $userToken = $user->createToken('checkout-access', ['*'], $expiresAt)->plainTextToken;

        // Optional initial selection via URL
        $initialProductId = $request->query('product');
        $initialPriceId = $request->query('price');

        return Inertia::render('Checkout/App', [
            'userToken' => $userToken,
            'apiBaseUrl' => config('app.url'),
            'initialProductId' => $initialProductId,
            'initialPriceId' => $initialPriceId,
        ]);
    }
}
