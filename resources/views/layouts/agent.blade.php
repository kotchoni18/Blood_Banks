<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace Agent')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #fff;
            border-right: 1px solid #ddd;
            padding: 20px 0;
            position: fixed;
            top: 0;
            bottom: 0;
        }

        .sidebar .brand {
            font-size: 1.4rem;
            font-weight: bold;
            padding: 0 20px 15px;
            color: #c0392b;
            display: flex;
            align-items: center;
        }

        .sidebar .brand i {
            font-size: 1.8rem;
            margin-right: 10px;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #e74c3c;
            color: white;
        }

        /* Main content */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 0;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info i {
            font-size: 1.4rem;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <i class="bi bi-heart-pulse"></i> Agent Médical
        </div>
        <a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
        </a>
        <a href="{{ route('agent.stocks.index') }}" class="{{ request()->routeIs('agent.stocks.*') ? 'active' : '' }}">
            <i class="bi bi-droplet me-2"></i> Consulter Stocks
        </a>
        <a href="{{ route('agent.donations.index') }}" class="{{ request()->routeIs('agent.donations.*') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i> Liste des Dons
        </a>
        <a href="{{ route('agent.donations.history') }}" class="{{ request()->routeIs('agent.history') ? 'active' : '' }}">
            <i class="bi bi-clock-history me-2"></i> Historique Dons
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Header -->
        <header class="header">
            <h5 class="m-0">Bienvenue, {{ auth()->user()->full_name ?? 'Agent' }}</h5>
            <div class="user-info">
                <span id="currentDateTime">{{ now()->format('d/m/Y H:i') }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-4">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateDateTime() {
            const now = new Date();
            document.getElementById('currentDateTime').textContent =
                now.toLocaleDateString('fr-FR') + ' ' +
                now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateDateTime, 60000);
    </script>
    @stack('scripts')
</body>
</html>
