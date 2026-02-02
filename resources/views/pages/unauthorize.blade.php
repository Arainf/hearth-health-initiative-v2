<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
<div class="bg-white shadow-xl rounded-2xl p-8 max-w-md w-full text-center">
    <div class="text-6xl font-bold text-red-500 mb-4">403</div>
    <h1 class="text-2xl font-semibold mb-2">Unauthorized Access</h1>
    <p class="text-gray-600 mb-6">
        Sorry, you don’t have permission to access this page.
    </p>

    <div class="flex flex-col gap-3">
        <a href="{{ url()->previous() }}"
           class="w-full rounded-xl bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium transition">
            Go Back
        </a>

        <a href="{{ route('dashboard') }}"
           class="w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 font-medium transition">
            Go to Dashboard
        </a>
    </div>
</div>
</body>
</html>
