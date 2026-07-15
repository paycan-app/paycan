@extends('install.layout')

@section('title', 'Welcome - PayCan Installer')
@section('subtitle', 'Welcome to PayCan Installation')

@section('content')
<div class="text-center">
    <div class="mb-6">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100">
            <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2m-5 4v6m-3-3h6"></path>
            </svg>
        </div>
    </div>
    
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        Welcome to PayCan
    </h3>
    
    <p class="text-sm text-gray-600 mb-6">
        PayCan is a powerful payment processing platform built with Laravel and Filament. 
        This installer will guide you through the setup process in just a few simple steps.
    </p>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-2">What you'll need:</h4>
        <ul class="text-sm text-gray-600 space-y-1">
            <li>• PHP 8.2 or higher</li>
            <li>• Database (SQLite, MySQL, or PostgreSQL)</li>
            <li>• Composer and Node.js</li>
            <li>• A few minutes of your time</li>
        </ul>
    </div>

    <div class="space-y-3">
        <a href="{{ route('install.requirements') }}" 
           class="w-full flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            Start Installation
        </a>
    </div>
</div>
@endsection

@section('progress')
<div class="flex items-center space-x-2">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span class="font-medium">Welcome</span>
</div>
<div class="flex items-center space-x-2 opacity-50 cursor-not-allowed">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Requirements</span>
</div>
<div class="flex items-center space-x-2 opacity-50 cursor-not-allowed">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Database</span>
</div>
<div class="flex items-center space-x-2 opacity-50 cursor-not-allowed">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Admin</span>
</div>
@endsection