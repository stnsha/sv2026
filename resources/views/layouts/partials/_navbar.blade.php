<nav class="fixed top-0 left-0 right-0 bg-[#FFFFFF] border-b border-grey-200 z-[1050] h-[60px] shadow-sm">
    <div class="container-fluid px-3 h-full">
        <div class="flex items-center justify-between h-full">
            <!-- Left: Toggle Button (desktop) and Logo -->
            <div class="flex items-center">
                <!-- Desktop Toggle Button (visible on desktop only) -->
                <button
                    onclick="toggleSidebar()"
                    class="hidden lg:block mr-2 text-grey-900 bg-transparent border-none p-2 hover:text-grey-600 transition-colors focus:outline-none"
                    type="button"
                    aria-label="Toggle sidebar"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                    <span class="text-xl font-bold text-grey-900">Sand Village</span>
                    <span class="ml-2 text-xs font-semibold text-primary-600 bg-primary-100 px-2 py-1 rounded-xl">Admin</span>
                </a>
            </div>

            <!-- Right: Icon Buttons (desktop) and Hamburger (mobile) -->
            <div class="flex items-center">
                <!-- Icon Buttons: Notification, Settings, Logout (hidden on mobile/tablet) -->
                <div class="hidden lg:flex items-center gap-2">
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-full border border-grey-200 bg-white text-grey-900 hover:bg-grey-100 transition-all focus:outline-none focus:ring-3 focus:ring-primary-600/10"
                        type="button"
                        aria-label="Notifications"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </button>
                    @if(auth()->user()->isSuperAdmin())
                    <a
                        href="{{ route('admin.settings.index') }}"
                        class="w-10 h-10 flex items-center justify-center rounded-full border border-grey-200 bg-white text-grey-900 hover:bg-grey-100 transition-all focus:outline-none focus:ring-3 focus:ring-primary-600/10 {{ request()->routeIs('admin.settings.*') ? 'bg-primary-100 border-primary-300 text-primary-600' : '' }}"
                        aria-label="Settings"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="w-10 h-10 flex items-center justify-center rounded-full border border-grey-200 bg-white text-grey-900 hover:bg-grey-100 transition-all focus:outline-none focus:ring-3 focus:ring-primary-600/10"
                            aria-label="Logout"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Hamburger Toggle Button (visible on mobile/tablet only) -->
                <button
                    onclick="toggleSidebar()"
                    class="lg:hidden text-grey-900 bg-transparent border-none p-2 hover:text-grey-600 transition-colors focus:outline-none"
                    type="button"
                    aria-label="Toggle sidebar"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
