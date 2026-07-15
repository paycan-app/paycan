<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class CheckoutPageDemoController extends Controller
{
    /**
     * Generate a demo checkout URL and redirect to it
     *
     * This is for testing purposes - generates a signed URL for the first user or creates a demo user
     */
    public function index(): RedirectResponse
    {
        // Get or create a demo user
        $user = User::firstOr(function () {
            return User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@paycan.test',
            ]);
        });

        // Generate a signed checkout URL for demo user (valid for 24 hours)
        $checkoutUrl = URL::temporarySignedRoute(
            'checkout',
            now()->addHours(24),
            ['user' => $user->id]
        );

        return redirect($checkoutUrl);
    }
}