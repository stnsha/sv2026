<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-grey-50 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-xl rounded-2xl px-8 py-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-grey-900">Welcome Back</h1>
                <p class="text-grey-500 mt-2">Sign in to your admin account</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-danger-50 border border-danger-200 text-danger-700 rounded-xl">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-5">
                    <label class="block text-grey-700 text-sm font-semibold mb-2" for="email">
                        Email Address
                    </label>
                    <input
                        class="w-full px-4 py-3 border border-grey-300 rounded-xl text-grey-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
                        id="email"
                        name="email"
                        type="email"
                        placeholder="admin@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label class="block text-grey-700 text-sm font-semibold mb-2" for="password">
                        Password
                    </label>
                    <input
                        class="w-full px-4 py-3 border border-grey-300 rounded-xl text-grey-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-primary-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition-all"
                >
                    Sign In
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-grey-500 text-sm mt-6">
            Admin Dashboard
        </p>
    </div>
</body>
</html>
