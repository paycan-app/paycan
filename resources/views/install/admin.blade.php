@extends('install.layout')

@section('title', 'Admin Setup - PayCan Installer')
@section('subtitle', 'Create your admin account')

@section('content')
<form method="POST" action="{{ route('install.admin.store') }}">
    @csrf
    
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Application Settings</h3>
        
        <div class="space-y-4">
            <div>
                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                <input type="text" name="app_name" id="app_name" value="{{ old('app_name', 'PayCan') }}" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                <p class="mt-2 text-sm text-gray-500">This will be displayed throughout the application.</p>
            </div>
            
            <div>
                <label for="app_url" class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                <input type="url" name="app_url" id="app_url" value="{{ old('app_url', request()->getSchemeAndHttpHost()) }}" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                <p class="mt-2 text-sm text-gray-500">The base URL where your application will be accessible.</p>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Administrator Account</h3>
        
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                <p class="mt-2 text-sm text-gray-500">This will be your login email for the admin panel.</p>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
                <p class="mt-2 text-sm text-gray-500">Must be at least 8 characters long.</p>
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 text-sm bg-gray-50 focus:bg-white">
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Final Step
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>This will create your admin account and complete the installation. You'll be able to access the admin panel at <strong>/admin</strong> after completion.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <a href="{{ route('install.database') }}" 
           class="flex-1 flex justify-center py-3 px-6 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            Back
        </a>
        
        <button type="submit" 
                class="flex-1 flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            Complete Installation
        </button>
    </div>
</form>
@endsection

@section('progress')
<a href="{{ route('install.welcome') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Welcome</span>
</a>
<a href="{{ route('install.requirements') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Requirements</span>
</a>
<a href="{{ route('install.database') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Database</span>
</a>
<div class="flex items-center space-x-2">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span class="font-medium">Admin</span>
</div>
@endsection