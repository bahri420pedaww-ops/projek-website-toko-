{{-- PLACEHOLDER (backend). Pesan dari backend: session 'success', 'warning', 'error' --}}
@if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
@endif
@if (session('warning'))
    <div class="mb-4 rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">{{ session('warning') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
@endif
