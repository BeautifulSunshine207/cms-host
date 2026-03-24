<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'METALIFT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-collapsed {
            width: 4rem;
        }
        
        .main-collapsed {
            margin-left: 4rem;
        }

        .admin-logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .admin-logo-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            overflow: hidden;
            background: #ca8a04;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-collapsed-toggle {
            display: none;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            background: #f6c915;
            color: #1f2937;
            align-items: center;
            justify-content: center;
        }

        #sidebar.sidebar-collapsed .admin-top-toggle {
            display: none;
        }

        #sidebar.sidebar-collapsed .admin-logo-circle {
            display: none;
        }

        #sidebar.sidebar-collapsed .admin-logo-wrap {
            width: 100%;
            justify-content: center;
        }

        #sidebar.sidebar-collapsed .admin-collapsed-toggle {
            display: inline-flex;
        }

        @media print {
            body {
                background: #ffffff !important;
            }
            #sidebar {
                display: none !important;
            }
            #main-content {
                margin-left: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-reset {
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
            }
            a[href]:after {
                content: "" !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-[#f5f7fa]">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 h-screen w-56 bg-[#3C3C3C] flex flex-col z-50 transition-all duration-300">
            <!-- Logo Section -->
            <div class="p-4 flex items-center justify-between border-b border-gray-700">
                <div class="admin-logo-wrap flex items-center gap-3">
                    <div class="admin-logo-circle w-10 h-10 rounded-full overflow-hidden bg-yellow-600 flex items-center justify-center">
                        <img
                            src="{{ asset('images/logo-cms.png') }}"
                            alt="Logo"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <button type="button" onclick="toggleSidebar()" class="admin-collapsed-toggle" aria-label="Expand sidebar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>
                    <div class="sidebar-text">
                        <span class="text-white font-bold text-sm tracking-wide">METALIFT</span>
                        <p class="text-gray-400 text-xs">Construction</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="admin-top-toggle text-gray-400 hover:text-white transition-colors" aria-label="Toggle sidebar">
                    <svg class="w-5 h-5 collapse-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <svg class="w-5 h-5 expand-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Admin Label -->
            <div class="px-4 py-3">
                <span class="sidebar-text text-gray-500 text-xs uppercase tracking-wider">Admin</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('projects.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Project</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('team.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('team.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Team</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('finance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('finance.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Finance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('inventory.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Inventory</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('materials.overview') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('materials.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Materials</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bottom Section -->
            <div class="px-2 pb-4 border-t border-gray-700 pt-0 mb-10">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.account.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.account.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Account</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-yellow-600 transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Report a Problem</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-yellow-600 transition-all">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="sidebar-text text-sm font-medium">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="ml-56 flex-1 transition-all duration-300">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-report="print"]');
            if (!trigger) return;
            event.preventDefault();
            window.print();
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            const collapseIcon = document.querySelector('.collapse-icon');
            const expandIcon = document.querySelector('.expand-icon');
            
            sidebar.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('main-collapsed');
            
            sidebarTexts.forEach(text => {
                text.classList.toggle('hidden');
            });
            
            collapseIcon.classList.toggle('hidden');
            expandIcon.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
