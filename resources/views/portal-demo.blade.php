<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayCan Portal Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl" x-data="{ showEmbedCode: false }">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">PayCan Portal Demo</h1>
            <p class="text-lg text-slate-600">Test the embedded payment portal</p>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-2">🔐</div>
                <h3 class="text-lg font-semibold mb-1">Secure Access</h3>
                <p class="text-sm text-slate-600">Signed URL with 24h expiration</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-2">🛒</div>
                <h3 class="text-lg font-semibold mb-1">Full Features</h3>
                <p class="text-sm text-slate-600">Products, checkout, orders & subscriptions</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-3xl mb-2">📱</div>
                <h3 class="text-lg font-semibold mb-1">Iframe Ready</h3>
                <p class="text-sm text-slate-600">Embeddable in any application</p>
            </div>
        </div>

        <!-- Demo User Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <strong class="text-blue-900">Demo User:</strong>
                    <span class="text-blue-800">{{ $user->name }} ({{ $user->email }})</span>
                </div>
                <span class="bg-blue-200 text-blue-900 px-3 py-1 rounded-full text-sm font-medium">
                    User ID: {{ $user->id }}
                </span>
            </div>
        </div>

        <!-- Controls -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Portal Controls</h2>
            <div class="flex flex-wrap gap-3 mb-4">
                <a href="{{ $portalUrl }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    Open in New Tab
                </a>
                <button @click="showEmbedCode = !showEmbedCode" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <span x-text="showEmbedCode ? 'Hide Embed Code' : 'Show Embed Code'"></span>
                </button>
                <button onclick="copyToClipboard('{{ $portalUrl }}')" class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg font-medium transition">
                    Copy Portal URL
                </button>
            </div>

            <div x-show="showEmbedCode" x-cloak class="space-y-4 mt-6">
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-slate-700">HTML Embed Code:</p>
                        <button onclick="copyToClipboard(`{{ addslashes($embedCode) }}`)" class="text-sm text-blue-600 hover:text-blue-700">
                            Copy
                        </button>
                    </div>
                    <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{{ $embedCode }}</code></pre>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-slate-700">Portal URL:</p>
                        <button onclick="copyToClipboard('{{ $portalUrl }}')" class="text-sm text-blue-600 hover:text-blue-700">
                            Copy
                        </button>
                    </div>
                    <pre class="bg-slate-900 text-slate-100 p-4 rounded-lg overflow-x-auto text-sm break-all"><code>{{ $portalUrl }}</code></pre>
                </div>
            </div>
        </div>

        <!-- Embedded Portal -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Embedded Portal Preview</h2>
            <p class="text-sm text-slate-600 mb-4">This is how the portal looks when embedded in your application</p>
            <div class="border rounded-lg overflow-hidden shadow-sm bg-white">
                <iframe
                    src="{{ $portalUrl }}"
                    width="100%"
                    height="800"
                    frameborder="0"
                    class="w-full"
                    title="PayCan Portal"
                ></iframe>
            </div>
        </div>

        <!-- Documentation -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Next Steps</h2>

            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold mb-2">1. Generate Portal URLs</h3>
                    <pre class="bg-slate-100 p-3 rounded text-sm overflow-x-auto"><code>use App\Services\PortalService;

$portalUrl = PortalService::generatePortalUrl($userId, 24);</code></pre>
                </div>

                <div>
                    <h3 class="font-semibold mb-2">2. Embed in Your App</h3>
                    <pre class="bg-slate-100 p-3 rounded text-sm overflow-x-auto"><code>&lt;iframe src="{{ $portalUrl }}" width="100%" height="800"&gt;&lt;/iframe&gt;</code></pre>
                </div>

                <div>
                    <h3 class="font-semibold mb-2">3. Use the SDK</h3>
                    <pre class="bg-slate-100 p-3 rounded text-sm overflow-x-auto"><code>import PayCan from '@paycan/sdk'

const paycan = new PayCan({ apiUrl: 'https://pay.yourapp.com' })
const products = await paycan.products.list()</code></pre>
                </div>

                <div class="border-t pt-4 mt-4">
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm">📄 See PORTAL.md for full documentation</span>
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm">🔒 Secure signed URLs</span>
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm">⚡ Ready for production</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-slate-600">
            <p>Portal URL valid for 24 hours • Demo products included • Fully functional payment portal</p>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            });
        }
    </script>
</body>
</html>
