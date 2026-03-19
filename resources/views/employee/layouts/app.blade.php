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

        .employee-sidebar-header {
            padding: 16px 18px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .employee-sidebar-title {
            font-size: 12px;
            letter-spacing: 0.14em;
            color: #e5e5e5;
            font-weight: 600;
        }

        .employee-logo-block {
            padding: 10px 16px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .employee-logo-circle {
            width: 62px;
            height: 62px;
            border-radius: 999px;
            overflow: hidden;
            background: #ca8a04;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .employee-logo-text {
            text-align: center;
            line-height: 1.1;
        }

        .employee-nav {
            padding: 0 14px;
            margin-top: 6px;
        }

        .employee-nav ul {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .employee-bottom {
            padding: 18px 14px 20px;
        }

        .employee-bottom ul {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .sidebar-collapsed {
            width: 4rem;
        }
        
        .main-collapsed {
            margin-left: 4rem;
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
            <div class="employee-sidebar-header">
                <span class="sidebar-text employee-sidebar-title">EMPLOYEE</span>
                <button onclick="toggleSidebar()" class="text-gray-300 hover:text-white transition-colors">
                    <svg class="w-5 h-5 collapse-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <svg class="w-5 h-5 expand-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div class="employee-logo-block">
                <div class="employee-logo-circle">
                    <img 
                        src="{{ asset('images/logo-cms.png') }}" 
                        alt="Logo" 
                        class="w-full h-full object-cover"
                    >
                </div>
                <div class="sidebar-text employee-logo-text">
                    <span class="text-white font-bold text-sm tracking-wide">METALIFT</span>
                    <p class="text-gray-300 text-xs">Construction</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="employee-nav flex-1">
                <ul>
                    <li>
                        <a href="{{ route('employee.projects.employeedashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('employee.projects.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Projects</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee.team.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('employee.team.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 0 0-5-3.87M17 20H7m10 0v-1c0-.66-.13-1.3-.38-1.87M7 20H2v-1a4 4 0 0 1 5-3.87M7 20v-1c0-.66.13-1.3.38-1.87m9.24 0a5 5 0 0 0-9.24 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Team</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee.inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('employee.inventory.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Inventory</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bottom Section -->
            <div class="employee-bottom border-t border-gray-700">
                <ul>
                    <li>
                        <a href="{{ route('employee.profile.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('employee.profile.*') ? 'bg-[#334155] text-yellow-500' : 'text-gray-300 hover:bg-yellow-600' }} transition-all">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="sidebar-text text-sm font-medium">Profile</span>
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
