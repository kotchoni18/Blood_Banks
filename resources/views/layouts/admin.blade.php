<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Interface Admin - Banque de Sang')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            width: 250px;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 0.2rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link.active {
            background: var(--primary-gradient);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .stat-card {
            background: var(--danger-gradient);
            color: white;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar position-fixed">
            <div class="p-3">
                <h4 class="text-white mb-4 text-center">
                    <i class="bi bi-droplet-fill text-danger me-2"></i>
                    BloodBank Admin
                </h4>
                
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Tableau de Bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i>
                            Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.stocks.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                            <i class="bi bi-box me-2"></i>
                            Stocks
                        </a>
                    </li>

                    <li class="nav-item">
    <a href="{{ route('admin.reports.users') }}" 
       class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line me-2"></i>
        Rapports
    </a>
    <ul class="nav flex-column ms-3">
        <li class="nav-item">
            <a href="{{ route('admin.reports.users') }}" 
               class="nav-link {{ request()->routeIs('admin.reports.users') ? 'active' : '' }}">
                Utilisateurs
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.reports.stocks') }}" 
               class="nav-link {{ request()->routeIs('admin.reports.stocks') ? 'active' : '' }}">
                Stocks
            </a>
        </li><li class="nav-item">
            <a href="{{ route('admin.reports.donations') }}" 
               class="nav-link {{ request()->routeIs('admin.reports.donations') ? 'active' : '' }}">
                Dons
            </a>
        </li>
        <!-- Ajouter d'autres rapports si besoin -->
    </ul>
</li>

                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">@yield('page-title', 'Tableau de Bord')</h2>
                    <small class="text-muted">@yield('page-subtitle', 'Vue d\'ensemble')</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        {{ auth()->user()->full_name }}
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @stack('scripts')
</body>
</html>