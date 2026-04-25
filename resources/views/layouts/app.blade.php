<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TicketFlow')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body>
    <div class="app-container">
        {{-- Top Navbar --}}
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                    <div class="brand-logo">TF</div>
                    <span>TicketFlow</span>
                </a>
            </div>

            <div class="navbar-user">
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </nav>

        <div class="main-wrapper">
            {{-- Left Sidebar --}}
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <div class="sidebar-section">
                        <div class="sidebar-title">Overview</div>
                        <a href="{{ route('dashboard') }}" @class(['nav-item', 'nav-item-active' => request()->routeIs('dashboard')])>
                            <span class="nav-icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-title">Projects</div>
                        <a href="{{ route('projects.index') }}" @class(['nav-item', 'nav-item-active' => request()->routeIs('projects.*')])>
                            <span class="nav-icon">🏗️</span>
                            <span>All Projects</span>
                        </a>
                    </div>

                    <div class="sidebar-section">
                        <div class="sidebar-title">Tickets</div>
                        <a href="{{ route('tickets.index') }}" @class(['nav-item', 'nav-item-active' => request()->routeIs('tickets.index')])>
                            <span class="nav-icon">📋</span>
                            <span>All Tickets</span>
                        </a>
                        <a href="{{ route('tickets.create') }}" @class(['nav-item', 'nav-item-active' => request()->routeIs('tickets.create')])>
                            <span class="nav-icon">➕</span>
                            <span>New Ticket</span>
                        </a>
                    </div>

                    @if (auth()->user()->role === 'admin')
                        <div class="sidebar-section">
                            <div class="sidebar-title">Management</div>
                            <a href="{{ route('departments.index') }}" @class(['nav-item', 'nav-item-active' => request()->routeIs('departments.*')])>
                                <span class="nav-icon">🏢</span>
                                <span>Departments</span>
                            </a>
                        </div>
                    @endif
                </nav>

                <div class="sidebar-footer">
                    <a href="{{ route('logout') }}" class="nav-item nav-item-logout">
                        <span class="nav-icon">🚪</span>
                        <span>Logout</span>
                    </a>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="main-content">
                @if (session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <div style="font-weight: 700; margin-bottom: 8px;">⚠️ Please fix the following errors:</div>
                        <ul style="margin-left: 16px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
