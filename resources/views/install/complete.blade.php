@extends('install.layout')

@section('title', 'Installation Complete - PayCan Installer')
@section('subtitle', 'Your PayCan installation is ready!')

@section('content')
<div class="text-center">
    <div class="mb-6">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>
    
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        🎉 Installation Complete!
    </h3>
    
    <p class="text-sm text-gray-600 mb-6">
        PayCan has been successfully installed and configured. You can now start processing payments and managing your business.
    </p>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h4 class="text-sm font-medium text-gray-900 mb-3">What's Next?</h4>
        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex items-center">
                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Access your admin panel to configure payment providers
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Create your first products and pricing plans
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Set up email notifications and webhooks
            </div>
            <div class="flex items-center">
                <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Integrate the PayCan SDK into your applications
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <a href="/admin" 
           class="w-full flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            Access Admin Panel
        </a>
        
        <a href="/" 
           class="w-full flex justify-center py-3 px-6 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            View Homepage
        </a>
    </div>

    <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-500">
            Need help? Check out our documentation or contact support.
        </p>
    </div>
</div>
@endsection

@section('progress')
<div class="flex items-center space-x-2">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Complete!</span>
</div>
@endsection