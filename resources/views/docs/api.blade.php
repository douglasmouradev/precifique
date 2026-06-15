<x-guest-layout>
    <div class="max-w-3xl mx-auto py-12 px-4 prose prose-slate">
        <h1>Precifique API v1</h1>
        <p>REST API for integrations. Base URL: <code>{{ url('/api/v1') }}</code></p>

        <h2>Authentication</h2>
        <pre><code>POST /api/v1/auth/token
{
  "email": "you@example.com",
  "password": "...",
  "device_name": "my-integration",
  "abilities": ["dashboard:read", "products:read", "sales:write"]
}</code></pre>

        <h2>Endpoints</h2>
        <ul>
            <li><code>GET /dashboard/summary</code> — requires <code>dashboard:read</code></li>
            <li><code>GET /products</code> — requires <code>products:read</code></li>
            <li><code>PATCH /products/{id}/stock</code> — requires <code>products:write</code></li>
            <li><code>GET /sales</code> — requires <code>sales:read</code></li>
            <li><code>POST /sales</code> — requires <code>sales:write</code></li>
        </ul>

        <p>Manage tokens in <strong>My account</strong> after logging in. Full reference: <a href="https://github.com/douglasmouradev/precifique/blob/main/docs/API.md">docs/API.md</a></p>
    </div>
</x-guest-layout>
