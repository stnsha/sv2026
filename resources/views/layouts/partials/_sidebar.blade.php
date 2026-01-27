<aside
    id="sidebar"
    class="fixed left-0 top-[60px] bottom-0 w-[230px] bg-[#FFFFFF] border-r border-grey-200 z-[1040] transition-transform duration-300 ease-in-out overflow-hidden flex flex-col -translate-x-full"
>
    <!-- User Info (hidden on mobile, visible on desktop) -->
    <div class="hidden lg:flex justify-between items-center px-4 py-4 border-b border-grey-200">
        <div>
            <h6 class="text-[14px] font-medium text-grey-900 mb-0.5">{{ auth()->user()->name }}</h6>
            <small class="text-[13px] text-grey-500 opacity-70">Administrator</small>
        </div>
        <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white text-sm font-medium ml-2">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>

    <!-- Navigation Menu (Scrollable) -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-grey-300 scrollbar-track-grey-50">
        <nav class="pb-4">
            <ul class="list-none p-0 m-0">
                <!-- Dashboard -->
                <li>
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 text-grey-900 hover:bg-grey-100 transition-all text-[13px] font-medium border-l-[3px] cursor-pointer {{ request()->routeIs('admin.dashboard') || (request()->routeIs('admin.bookings.show') && !request()->query('from')) ? 'bg-grey-100 border-l-primary-600' : 'border-l-transparent' }}"
                    >
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Capacity -->
                <li>
                    <a
                        href="{{ route('admin.capacity.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-grey-900 hover:bg-grey-100 transition-all text-[13px] font-medium border-l-[3px] cursor-pointer {{ request()->routeIs('admin.capacity.*') || request()->routeIs('admin.bookings.edit') || request()->query('from') === 'capacity' ? 'bg-grey-100 border-l-primary-600' : 'border-l-transparent' }}"
                    >
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span>Capacity</span>
                    </a>
                </li>

                <!-- Customers -->
                <li>
                    <a
                        href="{{ route('admin.customers.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-grey-900 hover:bg-grey-100 transition-all text-[13px] font-medium border-l-[3px] cursor-pointer {{ request()->routeIs('admin.customers.*') || request()->query('from') === 'customers' ? 'bg-grey-100 border-l-primary-600' : 'border-l-transparent' }}"
                    >
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Customers</span>
                    </a>
                </li>

                <!-- Reports -->
                <li>
                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-grey-900 hover:bg-grey-100 transition-all text-[13px] font-medium border-l-[3px] cursor-pointer {{ request()->routeIs('admin.reports.*') ? 'bg-grey-100 border-l-primary-600' : 'border-l-transparent' }}"
                    >
                        <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- User Profile (visible on mobile/tablet only) -->
    <div class="lg:hidden px-4 py-4 border-t border-grey-200">
        <div class="flex items-center mb-3">
            <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white text-sm font-medium mr-2">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h6 class="text-[14px] font-medium text-grey-900 mb-0.5">{{ auth()->user()->name }}</h6>
                <small class="text-[13px] text-grey-500 opacity-70">Administrator</small>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-3 py-2 text-[13px] font-medium text-primary-600 border border-primary-600 rounded-xl hover:bg-primary-600 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>
