<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pharma Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    #toast-container > .toast {
        background-image: none !important; /* Remove default icons */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border-radius: 0.75rem !important; /* Rounded-xl */
        padding: 15px 20px 15px 50px !important; /* Adjust padding */
        opacity: 1 !important;
    }

    /* Success: Emerald-600 */
    #toast-container > .toast-success {
        background-color: #059669 !important;
        color: #ffffff !important;
        position: relative;
    }
    /* Add custom checkmark icon for Success */
    #toast-container > .toast-success::before {
        content: "✓";
        position: absolute;
        left: 18px; top: 12px;
        font-size: 18px; font-weight: bold;
    }

    /* Error: Red-500 */
    #toast-container > .toast-error {
        background-color: #ef4444 !important;
        color: white !important;
        position: relative;
    }
    #toast-container > .toast-error::before {
        content: "✕";
        position: absolute;
        left: 20px; top: 12px;
        font-size: 16px; font-weight: bold;
    }

    .toast-title { font-weight: 800 !important; font-size: 1rem; }
    .toast-message { font-size: 0.875rem; }
</style>

<body class="bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: false, sidebarCompact: false }">

    <div class="flex items-center justify-between bg-white shadow-sm px-4 py-3 md:hidden z-20 relative">
        <div class="font-bold text-xl text-emerald-700">PharmaPOS</div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex h-screen overflow-hidden">

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-30 bg-emerald-900 text-white transition-all duration-300 ease-in-out transform md:relative md:flex flex-col shadow-xl"
            :class="sidebarCompact ? 'w-20' : 'w-64'">

            <div class="flex items-center justify-between h-16 px-4 bg-emerald-800 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-lg shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span x-show="!sidebarCompact"
                        class="text-lg font-bold tracking-wide transition-opacity duration-200 whitespace-nowrap">
                        Pharma<span class="text-emerald-300">Admin</span>
                    </span>
                </div>

                <button @click="sidebarCompact = !sidebarCompact"
                    class="hidden md:block text-emerald-300 hover:text-white focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" x-show="!sidebarCompact" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" x-show="sidebarCompact" style="display: none;" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto">

                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('branch.dashboard') }}"
   class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors {{ request()->routeIs('admin.dashboard') || request()->routeIs('branch.dashboard') ? 'bg-emerald-800' : '' }}">

    <svg class="h-6 w-6 text-emerald-300 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
    </svg>

    <span x-show="!sidebarCompact" class="ml-3 whitespace-nowrap">Dashboard</span>
</a>

                <a href="{{ route('inventory.index') }}"
                    class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors {{ request()->routeIs('inventory.*') ? 'bg-emerald-800' : '' }}">
                    <svg class="h-6 w-6 text-emerald-300 group-hover:text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="!sidebarCompact" class="ml-3 whitespace-nowrap">Inventory Stocks</span>
                </a>

                <a href="{{ route('reports.index') }}"
                    class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="h-6 w-6 text-emerald-300 group-hover:text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="!sidebarCompact" class="ml-3 whitespace-nowrap">Revenue Reports</span>
                </a>
                @if(auth()->user()->role === 'admin')
                 <a href="{{ route('branches.index') }}"
                    class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="h-6 w-6 text-emerald-300 group-hover:text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="!sidebarCompact" class="ml-3 whitespace-nowrap">Manage Branches</span>
                </a>
                @endif
                {{-- Branch Manager Links --}}
@if(auth()->user()->role === 'branch_manager')

    <a href="{{ route('branch.staff.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 transition-colors {{ request()->routeIs('branch.staff.*') ? 'bg-emerald-600 text-white' : 'text-white' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        Manage Cashiers
    </a>
@endif
            </nav>

            <div class="p-4 border-t border-emerald-800 bg-emerald-950">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center w-full hover:bg-emerald-800 p-2 rounded-lg transition-colors">
                    <div class="shrink-0">
                        <div
                            class="h-9 w-9 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div x-show="!sidebarCompact" class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-emerald-400 truncate">View Profile</p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-2 py-2 text-sm text-red-300 hover:text-white hover:bg-red-900/50 rounded-lg transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="!sidebarCompact" class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black opacity-50 md:hidden"></div>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
            @yield('content')
        </main>

    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    // Toastr Configuration
   toastr.options = {
        "closeButton": true,
        "progressBar": true,

        // CHANGE THIS LINE:
        "positionClass": "toast-top-right",

        "preventDuplicates": false,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
    };
    // Listen for Laravel Session Flash Messages
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}", "Success");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}", "Error");
    @endif

    @if(Session::has('info'))
        toastr.info("{{ Session::get('info') }}", "Info");
    @endif

    @if(Session::has('warning'))
        toastr.warning("{{ Session::get('warning') }}", "Warning");
    @endif

    // Also catch Validation Errors (optional but helpful)
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error("{{ $error }}", "Validation Error");
        @endforeach
    @endif
</script>
</html>
