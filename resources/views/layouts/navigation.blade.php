<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm fixed top-0 left-0 right-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-gray-900">Gganbu</span>
                            <span class="text-sm font-medium text-blue-600">Banking</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-700 hover:text-blue-600">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 hover:text-blue-600">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')" class="text-gray-700 hover:text-blue-600">
                            {{ __('Manage Users') }}
                        </x-nav-link>
                    @endif
                    
                    <x-nav-link href="{{ route('transfer.create') }}" :active="request()->routeIs('transfer.*')" class="text-gray-700 hover:text-blue-600">
                        {{ __('Transfer') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none transition duration-150 ease-in-out">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                    <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <x-dropdown-link href="{{ route('profile.edit') }}">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('Profile Settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    {{ __('Sign Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" x-cloak class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-gray-700 hover:text-blue-600">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 hover:text-blue-600">
                    {{ __('Admin Panel') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')" class="text-gray-700 hover:text-blue-600">
                    {{ __('Manage Users') }}
                </x-responsive-nav-link>
            @endif
            
            <x-responsive-nav-link href="{{ route('transfer.create') }}" :active="request()->routeIs('transfer.*')" class="text-gray-700 hover:text-blue-600">
                {{ __('Transfer') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.edit') }}" class="text-gray-700 hover:text-blue-600">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        this.closest('form').submit();"
                        class="text-gray-700 hover:text-blue-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>