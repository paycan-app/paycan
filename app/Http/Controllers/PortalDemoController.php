<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PortalService;
use Illuminate\Http\RedirectResponse;

class PortalDemoController extends Controller
{
    /**
     * Generate a demo portal URL and redirect to it
     *
     * This is for testing purposes - generates a signed URL for the first user in the database
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

        // Generate a signed portal URL for demo user (valid for 24 hours)
        $portalUrl = PortalService::generatePortalUrl($user->id, 24);

        // Redirect to the portal
        return redirect($portalUrl);
    }
}
