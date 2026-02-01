@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage system settings and users')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create User Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
                    <h2 class="text-lg font-semibold text-grey-900">Create User</h2>
                    <p class="text-sm text-grey-500">Add a new admin user to the system</p>
                </div>
                <form action="{{ route('admin.settings.users.store') }}" method="POST" class="p-4 lg:p-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-grey-700 mb-1">Name</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-grey-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-danger-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-grey-700 mb-1">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            class="w-full px-3 py-2 border border-grey-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-danger-500 @enderror"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-grey-700 mb-1">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="w-full px-3 py-2 border border-grey-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-danger-500 @enderror"
                            required
                        >
                        <p class="mt-1 text-xs text-grey-500">Min 8 characters, mixed case, with numbers</p>
                        @error('password')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-grey-700 mb-1">Role</label>
                        <select
                            name="role"
                            id="role"
                            class="w-full px-3 py-2 border border-grey-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('role') border-danger-500 @enderror"
                            required
                        >
                            <option value="">Select a role</option>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        Create User
                    </button>
                </form>
            </div>
        </div>

        <!-- Users List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-4 lg:px-6 py-4 border-b border-grey-200">
                    <h2 class="text-lg font-semibold text-grey-900">Users</h2>
                    <p class="text-sm text-grey-500">All admin users with access to the system</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-grey-200">
                        <thead class="bg-grey-50">
                            <tr>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden sm:table-cell">
                                    Email
                                </th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-grey-500 uppercase tracking-wider hidden md:table-cell">
                                    Created
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-grey-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-grey-50">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-grey-900">{{ $user->name }}</div>
                                        <div class="text-xs text-grey-500 sm:hidden">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden sm:table-cell">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                        @foreach($user->roles as $role)
                                            @if($role->role === 'superadmin')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-700">
                                                    Super Admin
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-secondary-100 text-secondary-700">
                                                    Admin
                                                </span>
                                            @endif
                                        @endforeach
                                        @if($user->roles->isEmpty())
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-grey-100 text-grey-600">
                                                No Role
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-grey-600 hidden md:table-cell">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 lg:px-6 py-8 text-center text-grey-500">
                                        No users found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
