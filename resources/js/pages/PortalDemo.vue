<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Alert, AlertDescription } from '@/components/ui/alert'

interface User {
  id: string
  name: string
  email: string
}

const props = defineProps<{
  portalUrl: string
  embedCode: string
  user: User
}>()

const showEmbedCode = ref(false)

function copyToClipboard(text: string) {
  navigator.clipboard.writeText(text)
  alert('Copied to clipboard!')
}

function openInNewTab() {
  window.open(props.portalUrl, '_blank')
}
</script>

<template>
  <Head title="Portal Demo" />

  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-8 dark:from-slate-950 dark:to-slate-900">
    <div class="mx-auto max-w-7xl space-y-6">
      <!-- Header -->
      <div class="text-center">
        <h1 class="text-4xl font-bold tracking-tight">PayCan Portal Demo</h1>
        <p class="mt-2 text-lg text-muted-foreground">
          Test the embedded payment portal
        </p>
      </div>

      <!-- Info Cards -->
      <div class="grid gap-6 md:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <span class="text-2xl">🔐</span>
              Secure Access
            </CardTitle>
            <CardDescription>Signed URL with 24h expiration</CardDescription>
          </CardHeader>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <span class="text-2xl">🛒</span>
              Full Features
            </CardTitle>
            <CardDescription>Products, checkout, orders & subscriptions</CardDescription>
          </CardHeader>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <span class="text-2xl">📱</span>
              Iframe Ready
            </CardTitle>
            <CardDescription>Embeddable in any application</CardDescription>
          </CardHeader>
        </Card>
      </div>

      <!-- Demo User Info -->
      <Alert>
        <AlertDescription>
          <div class="flex items-center justify-between">
            <div>
              <strong>Demo User:</strong> {{ user.name }} ({{ user.email }})
            </div>
            <Badge variant="secondary">User ID: {{ user.id }}</Badge>
          </div>
        </AlertDescription>
      </Alert>

      <!-- Controls -->
      <Card>
        <CardHeader>
          <CardTitle>Portal Controls</CardTitle>
          <CardDescription>Interact with the demo portal</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="flex flex-wrap gap-2">
            <Button @click="openInNewTab">Open in New Tab</Button>
            <Button variant="outline" @click="showEmbedCode = !showEmbedCode">
              {{ showEmbedCode ? 'Hide' : 'Show' }} Embed Code
            </Button>
            <Button variant="outline" @click="copyToClipboard(portalUrl)">
              Copy Portal URL
            </Button>
            <Button variant="outline" as="a" :href="portalUrl" target="_blank">
              Direct Link
            </Button>
          </div>

          <div v-if="showEmbedCode" class="space-y-2">
            <Separator />
            <div>
              <p class="mb-2 text-sm font-medium">HTML Embed Code:</p>
              <div class="relative">
                <pre
                  class="overflow-x-auto rounded-lg bg-slate-900 p-4 text-sm text-slate-50 dark:bg-slate-800"
                ><code>{{ embedCode }}</code></pre>
                <Button
                  size="sm"
                  variant="ghost"
                  class="absolute right-2 top-2"
                  @click="copyToClipboard(embedCode)"
                >
                  Copy
                </Button>
              </div>
            </div>

            <div>
              <p class="mb-2 text-sm font-medium">Portal URL:</p>
              <div class="relative">
                <pre
                  class="overflow-x-auto rounded-lg bg-slate-900 p-4 text-sm text-slate-50 dark:bg-slate-800"
                ><code>{{ portalUrl }}</code></pre>
                <Button
                  size="sm"
                  variant="ghost"
                  class="absolute right-2 top-2"
                  @click="copyToClipboard(portalUrl)"
                >
                  Copy
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Embedded Portal -->
      <Card>
        <CardHeader>
          <CardTitle>Embedded Portal Preview</CardTitle>
          <CardDescription>
            This is how the portal looks when embedded in your application
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="overflow-hidden rounded-lg border bg-white shadow-sm dark:bg-slate-950">
            <iframe
              :src="portalUrl"
              width="100%"
              height="800"
              frameborder="0"
              class="w-full"
              title="PayCan Portal"
            ></iframe>
          </div>
        </CardContent>
      </Card>

      <!-- Documentation -->
      <Card>
        <CardHeader>
          <CardTitle>Next Steps</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div>
            <h3 class="mb-2 font-medium">1. Generate Portal URLs</h3>
            <pre
              class="overflow-x-auto rounded bg-slate-100 p-3 text-sm dark:bg-slate-900"
            ><code>use App\Services\PortalService;

$portalUrl = PortalService::generatePortalUrl($userId, 24);</code></pre>
          </div>

          <div>
            <h3 class="mb-2 font-medium">2. Embed in Your App</h3>
            <pre
              class="overflow-x-auto rounded bg-slate-100 p-3 text-sm dark:bg-slate-900"
            ><code>&lt;iframe src="{{ $portalUrl }}" width="100%" height="800"&gt;&lt;/iframe&gt;</code></pre>
          </div>

          <div>
            <h3 class="mb-2 font-medium">3. Use the SDK</h3>
            <pre
              class="overflow-x-auto rounded bg-slate-100 p-3 text-sm dark:bg-slate-900"
            ><code>import PayCan from '@paycan/sdk'

const paycan = new PayCan({ apiUrl: 'https://pay.yourapp.com' })
const products = await paycan.products.list()</code></pre>
          </div>

          <Separator />

          <div class="flex flex-wrap gap-2">
            <Badge variant="outline">📄 See PORTAL.md for full documentation</Badge>
            <Badge variant="outline">🔒 Secure signed URLs</Badge>
            <Badge variant="outline">⚡ Ready for production</Badge>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<style scoped>
/* Ensure iframe is responsive */
iframe {
  min-height: 600px;
}
</style>
