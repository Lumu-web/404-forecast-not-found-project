<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Left: Logo -->
            <div class="flex items-center space-x-4">
                <!-- Logo SVG -->
                <a href="#" class="flex-shrink-0">
                    <svg
                        class="h-9 w-auto text-gray-800"
                        viewBox="0 0 300 100"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-label="404 Forecast Not Found Logo"
                        role="img"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <!-- Cloud Shape -->
                        <g fill="currentColor" fill-opacity="0.3" stroke="none">
                            <ellipse cx="80" cy="50" rx="50" ry="30" />
                            <ellipse cx="120" cy="40" rx="40" ry="25" />
                            <ellipse cx="60" cy="40" rx="30" ry="20" />
                        </g>

                        <!-- 404 Text inside cloud -->
                        <text x="60" y="55" font-size="26" font-family="monospace" fill="currentColor" font-weight="bold">404</text>

                        <!-- Broken signal icon -->
                        <g transform="translate(180, 30)" stroke="currentColor" stroke-width="4">
                            <path d="M10 30 C 20 10, 40 10, 50 30" />
                            <path d="M20 30 C 25 20, 35 20, 40 30" />
                            <line x1="27" y1="25" x2="33" y2="35" stroke="#e74c3c" stroke-width="2" />
                        </g>
                    </svg>
                </a>

                <!-- Site Name in separate div -->
                <div class="text-gray-800 font-bold text-xl select-none">
                    404 Forecast Not Found
                </div>
            </div>

            <!-- Right: Auth buttons (guest only) -->
            <div class="flex items-center space-x-4">
                @if(!isset($user) && !isset($user['name']))
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                        Register
                    </button>
                    <button type="button" class="btn btn-secondary hover:text-blue-600" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Login
                    </button>
                @else
                    <!-- Dropdown for authenticated user -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                {{ $user['name'] }}
                                <svg class="ms-2 fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="#">Profile</x-dropdown-link>

                                @csrf
                                <x-dropdown-link href="#" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endguest
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex sm:hidden">
                <button
                    @click="open = ! open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-menu"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke="currentColor" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke="currentColor" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
    @guest

        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="#">Profile</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link href="#" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endguest
    </div>
</nav>
