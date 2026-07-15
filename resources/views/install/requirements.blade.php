@extends('install.layout')

@section('title', 'Requirements - PayCan Installer')
@section('subtitle', 'Checking server requirements')

@section('content')
<div>
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Server Requirements</h3>
        
        <!-- PHP Version -->
        <div class="mb-4 p-4 rounded-lg {{ $checks['php_version']['ok'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($checks['php_version']['ok'])
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium {{ $checks['php_version']['ok'] ? 'text-green-800' : 'text-red-800' }}">
                        PHP {{ $checks['php_version']['required'] }}+ Required
                    </p>
                    <p class="text-sm {{ $checks['php_version']['ok'] ? 'text-green-600' : 'text-red-600' }}">
                        Current: {{ $checks['php_version']['current'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Extensions -->
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">PHP Extensions</h4>
            <div class="grid grid-cols-2 gap-2">
                @foreach($checks['extensions'] as $extension => $loaded)
                    <div class="flex items-center p-2 rounded {{ $loaded ? 'bg-green-50' : 'bg-red-50' }}">
                        @if($loaded)
                            <svg class="h-4 w-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-4 w-4 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        <span class="text-sm {{ $loaded ? 'text-green-800' : 'text-red-800' }}">
                            {{ strtoupper($extension) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Permissions -->
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Directory Permissions</h4>
            <div class="space-y-2">
                @foreach($checks['write_permissions'] as $directory => $writable)
                    <div class="flex items-center p-2 rounded {{ $writable ? 'bg-green-50' : 'bg-red-50' }}">
                        @if($writable)
                            <svg class="h-4 w-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-4 w-4 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        <span class="text-sm {{ $writable ? 'text-green-800' : 'text-red-800' }}">
                            {{ str_replace('_', '/', $directory) }}/
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $allRequirementsMet = $checks['php_version']['ok'] && 
                             !in_array(false, $checks['extensions']) && 
                             !in_array(false, $checks['write_permissions']);
    @endphp

    <div class="flex space-x-4">
        <a href="{{ route('install.welcome') }}" 
           class="flex-1 flex justify-center py-3 px-6 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            Back
        </a>
        
        @if($allRequirementsMet)
            <a href="{{ route('install.database') }}" 
               class="flex-1 flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                Continue
            </a>
        @else
            <button disabled 
                    class="flex-1 flex justify-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed">
                Fix Requirements First
            </button>
        @endif
    </div>
</div>
@endsection

@section('progress')
<a href="{{ route('install.welcome') }}" class="flex items-center space-x-2 opacity-75 hover:opacity-100 transition-opacity cursor-pointer">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span>Welcome</span>
</a>
<div class="flex items-center space-x-2">
    <div class="h-2 w-2 bg-white rounded-full"></div>
    <span class="font-medium">Requirements</span>
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