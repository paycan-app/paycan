/**
 * Example Backend Token Endpoint
 *
 * This endpoint returns a PayCan token for your authenticated user.
 * The frontend calls this endpoint to get a token, then uses setUserToken().
 *
 * SECURITY PRINCIPLES:
 * 1. Always verify the user is authenticated in YOUR system first (use auth middleware)
 * 2. Get the user ID from the session/JWT, NEVER from the request body
 * 3. Keep your PAYCAN_API_SECRET on the backend, NEVER expose it to the frontend
 * 4. Return ONLY the token { token: "..." }, nothing sensitive
 */

// ===================================
// Express.js Example
// ===================================

const express = require('express');
const fetch = require('node-fetch');

const app = express();
app.use(express.json());

// Middleware to verify user is logged in
const requireAuth = (req, res, next) => {
  if (!req.session || !req.session.userId) {
    return res.status(401).json({ error: 'Not authenticated' });
  }
  next();
};

app.post('/api/paycan/token', requireAuth, async (req, res) => {
  try {
    // SECURITY: Get user ID from session, NOT from request body
    const userId = req.session.userId;

    // Get user details from your database
    const user = await getUserFromDatabase(userId);

    if (!user) {
      return res.status(404).json({ error: 'User not found' });
    }

    // Call PayCan's sync endpoint to get a token
    const response = await fetch(`${process.env.PAYCAN_URL}/api/admin/users/sync`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-Key': process.env.PAYCAN_API_SECRET, // NEVER expose this to frontend
      },
      body: JSON.stringify({
        user_id: user.id,
        user: {
          name: user.name,
          email: user.email,
        },
      }),
    });

    if (!response.ok) {
      throw new Error(`PayCan API error: ${response.statusText}`);
    }

    const data = await response.json();

    // Return ONLY the token to the frontend
    res.json({ token: data.token });
  } catch (error) {
    console.error('PayCan token error:', error);
    res.status(500).json({ error: 'Failed to get PayCan token' });
  }
});

// ===================================
// Next.js API Route Example
// ===================================

// app/api/paycan/token/route.js (App Router)
import { getServerSession } from 'next-auth';

export async function POST(req) {
  // Get user from session
  const session = await getServerSession();

  if (!session || !session.user) {
    return Response.json({ error: 'Not authenticated' }, { status: 401 });
  }

  try {
    // SECURITY: Get user ID from session, NOT from request body
    const userId = session.user.id;

    // Call PayCan sync endpoint
    const response = await fetch(`${process.env.PAYCAN_URL}/api/admin/users/sync`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-Key': process.env.PAYCAN_API_SECRET,
      },
      body: JSON.stringify({
        user_id: userId,
        user: {
          name: session.user.name,
          email: session.user.email,
        },
      }),
    });

    const data = await response.json();

    // Return ONLY the token
    return Response.json({ token: data.token });
  } catch (error) {
    console.error('PayCan token error:', error);
    return Response.json({ error: 'Failed to get PayCan token' }, { status: 500 });
  }
}

// ===================================
// Laravel Example
// ===================================

/*
// routes/api.php
Route::post('/paycan/token', [PayCanController::class, 'getToken'])->middleware('auth:sanctum');

// app/Http/Controllers/PayCanController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayCanController extends Controller
{
    public function getToken(Request $request)
    {
        // SECURITY: Get user from auth middleware, NOT from request
        $user = $request->user();

        try {
            $response = Http::withHeaders([
                'X-API-Key' => config('services.paycan.secret'),
            ])->post(config('services.paycan.url') . '/api/admin/users/sync', [
                'user_id' => $user->id,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

            // Return ONLY the token
            return response()->json([
                'token' => $response->json('token')
            ]);
        } catch (\Exception $e) {
            logger()->error('PayCan token error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to get PayCan token'], 500);
        }
    }
}
*/

// ===================================
// Django Example
// ===================================

/*
# views.py
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.decorators import login_required
import requests
import os

@csrf_exempt
@login_required
def paycan_token(request):
    if request.method != 'POST':
        return JsonResponse({'error': 'Method not allowed'}, status=405)

    # SECURITY: Get user from auth decorator, NOT from request body
    user = request.user

    try:
        response = requests.post(
            f"{os.environ.get('PAYCAN_URL')}/api/admin/users/sync",
            headers={
                'Content-Type': 'application/json',
                'X-API-Key': os.environ.get('PAYCAN_API_SECRET'),
            },
            json={
                'user_id': str(user.id),
                'user': {
                    'name': user.get_full_name(),
                    'email': user.email,
                }
            }
        )

        data = response.json()

        # Return ONLY the token
        return JsonResponse({'token': data['token']})
    except Exception as e:
        return JsonResponse({'error': 'Failed to get PayCan token'}, status=500)


# urls.py
from django.urls import path
from . import views

urlpatterns = [
    path('api/paycan/token', views.paycan_token, name='paycan_token'),
]
*/

// Helper function (example)
async function getUserFromDatabase(userId) {
  // Replace with your actual database query
  return {
    id: userId,
    name: 'John Doe',
    email: 'john@example.com',
  };
}
