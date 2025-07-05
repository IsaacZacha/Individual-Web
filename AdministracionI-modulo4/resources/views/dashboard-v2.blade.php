<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📊 Dashboard de Administración | Sistema de Gestión</title>
    
    <!-- Configuration for WebSocket -->
    <script>
        window.appConfig = {
            appKey: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST', '127.0.0.1') }}',
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            scheme: '{{ env('REVERB_SCHEME', 'http') }}',
            csrfToken: '{{ csrf_token() }}',
            version: 'v2-professional'
        };
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --dark-color: #1f2937;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark-color);
        }
        
        .main-container {
            background: var(--light-bg);
            min-height: 100vh;
            padding: 0;
        }
        
        /* Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: var(--card-shadow);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .header-stats {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .connection-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }
        
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .status-connected { background-color: var(--success-color); }
        .status-disconnected { background-color: var(--danger-color); }
        .status-connecting { background-color: var(--warning-color); }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Metrics Cards */
        .metrics-section {
            padding: 2rem 0;
        }
        
        .metric-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            height: 100%;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .metric-icon.empleados { background: linear-gradient(135deg, #10b981, #059669); }
        .metric-icon.vehiculos { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .metric-icon.sucursales { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .metric-icon.usuarios { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .metric-icon.roles { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .metric-icon.asignaciones { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        
        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0.5rem 0;
        }
        
        .metric-label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-change {
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .metric-change.positive { color: var(--success-color); }
        .metric-change.negative { color: var(--danger-color); }
        
        /* Charts Section */
        .charts-section {
            padding: 2rem 0;
        }
        
        .chart-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid #e5e7eb;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        /* Activity Feed */
        .activity-section {
            padding: 2rem 0;
        }
        
        .activity-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid #e5e7eb;
            max-height: 500px;
        }
        
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .activity-feed {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s ease;
        }
        
        .activity-item:hover {
            background-color: #f9fafb;
        }
        
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            color: white;
            flex-shrink: 0;
        }
        
        .activity-icon.created { background-color: var(--success-color); }
        .activity-icon.updated { background-color: var(--info-color); }
        .activity-icon.deleted { background-color: var(--danger-color); }
        
        /* Efecto de actualización para métricas */
        .metric-updated {
            background-color: #10b981 !important;
            color: white !important;
            border-radius: 4px;
            padding: 2px 6px;
            transition: all 0.3s ease;
            transform: scale(1.1);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }
        
        .activity-description {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .activity-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Tables Section */
        .tables-section {
            padding: 2rem 0;
            background: #f8fafc;
        }
        
        .nav-tabs-custom {
            border-bottom: 2px solid #e5e7eb;
        }
        
        .nav-tabs-custom .nav-link {
            background: none;
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .nav-tabs-custom .nav-link.active {
            color: var(--primary-color);
            background-color: white;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .table-actions {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .table-responsive {
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table {
            margin-bottom: 0;
            background: white;
        }
        
        .table thead th {
            background: var(--dark-color) !important;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem 0.75rem;
        }
        
        .table tbody td {
            padding: 0.875rem 0.75rem;
            border-color: #f1f5f9;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .btn-actions {
            display: flex;
            gap: 0.25rem;
        }
        
        .btn-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .bg-purple {
            background-color: #8b5cf6 !important;
        }
        
        .btn-purple {
            color: #8b5cf6;
            border-color: #8b5cf6;
        }
        
        .btn-purple:hover {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
            color: white;
        }
        
        .btn-outline-purple {
            color: #8b5cf6;
            border-color: #8b5cf6;
        }
        
        .btn-outline-purple:hover {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
            color: white;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.disponible { 
            background-color: #dcfce7; 
            color: #166534; 
        }
        
        .status-badge.en-uso { 
            background-color: #dbeafe; 
            color: #1e40af; 
        }
        
        .status-badge.mantenimiento { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        
        .status-badge.fuera-servicio { 
            background-color: #fee2e2; 
            color: #dc2626; 
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .header-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .metric-value {
                font-size: 2rem;
            }
        }
        
        /* Loading States */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-title">
                        <i class="fas fa-tachometer-alt fa-2x"></i>
                        <div>
                            <h1>Dashboard de Administración</h1>
                            <p class="mb-0 opacity-75">Sistema de Gestión Empresarial</p>
                        </div>
                    </div>
                    <div class="header-stats">
                        <div class="connection-status" id="connection-status">
                            <div class="status-indicator status-connecting"></div>
                            <span>Conectando...</span>
                        </div>
                        <div class="text-end">
                            <div class="fs-6 opacity-75">Última actualización</div>
                            <div id="last-update" class="fw-bold">--:--</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Metrics Section -->
        <section class="metrics-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="metric-card">
                            <div class="metric-header">
                                <div class="metric-icon empleados">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Ver detalle</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Agregar empleado</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="metric-value" id="total-empleados">
                                <span class="metric-number">0</span>
                            </div>
                            <div class="metric-label">Total Empleados</div>
                            <div class="metric-change positive" id="empleados-change">
                                <i class="fas fa-arrow-up me-1"></i>+0 este mes
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="metric-card">
                            <div class="metric-header">
                                <div class="metric-icon vehiculos">
                                    <i class="fas fa-car"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Ver flota</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Registrar vehículo</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="metric-value" id="total-vehiculos">
                                <span class="metric-number">0</span>
                            </div>
                            <div class="metric-label">Flota de Vehículos</div>
                            <div class="metric-change positive" id="vehiculos-change">
                                <i class="fas fa-arrow-up me-1"></i>+0 este mes
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="metric-card">
                            <div class="metric-header">
                                <div class="metric-icon sucursales">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Ver sucursales</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Nueva sucursal</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="metric-value" id="total-sucursales">
                                <span class="metric-number">0</span>
                            </div>
                            <div class="metric-label">Sucursales Activas</div>
                            <div class="metric-change positive" id="sucursales-change">
                                <i class="fas fa-arrow-up me-1"></i>+0 este mes
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="metric-card">
                            <div class="metric-header">
                                <div class="metric-icon usuarios">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Ver usuarios</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Crear usuario</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="metric-value" id="total-usuarios">
                                <span class="metric-number">0</span>
                            </div>
                            <div class="metric-label">Usuarios del Sistema</div>
                            <div class="metric-change positive" id="usuarios-change">
                                <i class="fas fa-arrow-up me-1"></i>+0 este mes
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Segunda fila de métricas -->
                <div class="row g-4 mt-2">
                    <div class="col-md-6 col-sm-12">
                        <div class="metric-card">
                            <div class="metric-header">
                                <div class="metric-icon roles">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Ver roles</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-plus me-2"></i>Crear rol</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="metric-value" id="total-roles">
                                <span class="metric-number">0</span>
                            </div>
                            <div class="metric-label">Roles del Sistema</div>
                            <div class="metric-change positive" id="roles-change">
                                <i class="fas fa-arrow-up me-1"></i>+0 este mes
                            </div>
                        </div>
                    </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="charts-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">
                                    <i class="fas fa-chart-line me-2 text-primary"></i>
                                    Estado de Vehículos por Categoría
                                </h3>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary active">Mes</button>
                                    <button class="btn btn-outline-primary">Año</button>
                                </div>
                            </div>
                            <div id="vehiculos-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">
                                    <i class="fas fa-chart-pie me-2 text-success"></i>
                                    Distribución por Sucursal
                                </h3>
                            </div>
                            <div id="sucursales-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Activity Section -->
        <section class="activity-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="activity-card">
                            <div class="activity-header">
                                <h3>
                                    <i class="fas fa-stream me-2 text-info"></i>
                                    Actividad en Tiempo Real
                                </h3>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="clearActivityLog()">
                                        <i class="fas fa-trash me-1"></i>Limpiar
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="exportActivityLog()">
                                        <i class="fas fa-download me-1"></i>Exportar
                                    </button>
                                </div>
                            </div>
                            <div class="activity-feed" id="activity-feed">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-satellite-dish fa-2x mb-3 opacity-50"></i>
                                    <p>Esperando eventos del sistema...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">
                                    <i class="fas fa-tasks me-2 text-warning"></i>
                                    Acciones Rápidas
                                </h3>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <button class="btn btn-outline-success w-100" onclick="showCreateModal('empleado')">
                                        <i class="fas fa-user-plus d-block mb-2"></i>
                                        <small>Nuevo Empleado</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-outline-primary w-100" onclick="showCreateModal('vehiculo')">
                                        <i class="fas fa-car d-block mb-2"></i>
                                        <small>Nuevo Vehículo</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-outline-purple w-100" onclick="showCreateModal('sucursal')">
                                        <i class="fas fa-building d-block mb-2"></i>
                                        <small>Nueva Sucursal</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-outline-warning w-100" onclick="showCreateModal('usuario')">
                                        <i class="fas fa-user-shield d-block mb-2"></i>
                                        <small>Nuevo Usuario</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Data Tables Section -->
        <section class="tables-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>
                                    <i class="fas fa-table me-2 text-secondary"></i>
                                    Gestión de Datos
                                </h3>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="refreshAllTables()">
                                        <i class="fas fa-sync me-1"></i>Actualizar
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="exportAllData()">
                                        <i class="fas fa-file-excel me-1"></i>Exportar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs nav-tabs-custom" id="dataTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="empleados-tab" data-bs-toggle="tab" data-bs-target="#empleados-table" type="button">
                                        <i class="fas fa-users me-2"></i>Empleados <span class="badge bg-success ms-1" id="empleados-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="vehiculos-tab" data-bs-toggle="tab" data-bs-target="#vehiculos-table" type="button">
                                        <i class="fas fa-car me-2"></i>Vehículos <span class="badge bg-primary ms-1" id="vehiculos-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sucursales-tab" data-bs-toggle="tab" data-bs-target="#sucursales-table" type="button">
                                        <i class="fas fa-building me-2"></i>Sucursales <span class="badge bg-purple ms-1" id="sucursales-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios-table" type="button">
                                        <i class="fas fa-user-shield me-2"></i>Usuarios <span class="badge bg-warning ms-1" id="usuarios-count">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="asignaciones-tab" data-bs-toggle="tab" data-bs-target="#asignaciones-table" type="button">
                                        <i class="fas fa-route me-2"></i>Asignaciones <span class="badge bg-info ms-1" id="asignaciones-count">0</span>
                                    </button>
                                </li>
                            </ul>
                            
                            <!-- Tabs Content -->
                            <div class="tab-content mt-3" id="dataTabsContent">
                                <!-- Empleados Table -->
                                <div class="tab-pane fade show active" id="empleados-table" role="tabpanel">
                                    <div class="table-actions mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    <input type="text" class="form-control" placeholder="Buscar empleados..." id="search-empleados">
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="btn btn-success btn-sm" onclick="showCreateModal('empleado')">
                                                    <i class="fas fa-plus me-1"></i>Nuevo Empleado
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Cargo</th>
                                                    <th>Correo</th>
                                                    <th>Teléfono</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="empleados-tbody">
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="loading"></div>
                                                        <p class="mb-0 mt-2">Cargando empleados...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Vehículos Table -->
                                <div class="tab-pane fade" id="vehiculos-table" role="tabpanel">
                                    <div class="table-actions mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    <input type="text" class="form-control" placeholder="Buscar vehículos..." id="search-vehiculos">
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="btn btn-primary btn-sm" onclick="showCreateModal('vehiculo')">
                                                    <i class="fas fa-plus me-1"></i>Nuevo Vehículo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Placa</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Año</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="vehiculos-tbody">
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="loading"></div>
                                                        <p class="mb-0 mt-2">Cargando vehículos...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Sucursales Table -->
                                <div class="tab-pane fade" id="sucursales-table" role="tabpanel">
                                    <div class="table-actions mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    <input type="text" class="form-control" placeholder="Buscar sucursales..." id="search-sucursales">
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="btn btn-purple btn-sm" onclick="showCreateModal('sucursal')">
                                                    <i class="fas fa-plus me-1"></i>Nueva Sucursal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Dirección</th>
                                                    <th>Ciudad</th>
                                                    <th>Teléfono</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sucursales-tbody">
                                                <tr>
                                                    <td colspan="7" class="text-center py-4">
                                                        <div class="loading"></div>
                                                        <p class="mb-0 mt-2">Cargando sucursales...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Usuarios Table -->
                                <div class="tab-pane fade" id="usuarios-table" role="tabpanel">
                                    <div class="table-actions mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    <input type="text" class="form-control" placeholder="Buscar usuarios..." id="search-usuarios">
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="btn btn-warning btn-sm" onclick="showCreateModal('usuario')">
                                                    <i class="fas fa-plus me-1"></i>Nuevo Usuario
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Username</th>
                                                    <th>Empleado</th>
                                                    <th>Rol</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="usuarios-tbody">
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="loading"></div>
                                                        <p class="mb-0 mt-2">Cargando usuarios...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Asignaciones Table -->
                                <div class="tab-pane fade" id="asignaciones-table" role="tabpanel">
                                    <div class="table-actions mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    <input type="text" class="form-control" placeholder="Buscar asignaciones..." id="search-asignaciones">
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button class="btn btn-info btn-sm" onclick="showCreateModal('asignacion')">
                                                    <i class="fas fa-plus me-1"></i>Nueva Asignación
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Vehículo</th>
                                                    <th>Sucursal</th>
                                                    <th>Fecha Asignación</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="asignaciones-tbody">
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="loading"></div>
                                                        <p class="mb-0 mt-2">Cargando asignaciones...</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/build/assets/app-DJdjBoZ6.js" data-navigate-track="reload"></script>
    
    <script>
        console.log('🚀 Dashboard Profesional v2 iniciando...');
        
        // ===== VARIABLES GLOBALES =====
        let socket = null;
        let activityCounter = 0;
        let metricsData = {
            empleados: 0,
            vehiculos: 0,
            sucursales: 0,
            usuarios: 0,
            roles: 0,
            asignaciones: 0
        };
        let charts = {};
        
        // ===== DEDUPLICACIÓN DE EVENTOS =====
        let processedEvents = new Set();
        let eventBuffer = [];
        let processingTimeout = null;
        
        // ===== DEDUPLICACIÓN DE EVENTOS =====
        function generateEventId(eventData) {
            // Crear un ID único basado en el evento, canal y timestamp aproximado
            const eventName = eventData.event || 'unknown';
            const channel = eventData.channel || 'unknown';
            const timestamp = Math.floor(Date.now() / 1000); // Redondear a segundos
            
            return `${eventName}-${channel}-${timestamp}`;
        }
        
        function isDuplicateEvent(eventId) {
            // Tratamiento especial para diferentes tipos de eventos
            const eventTypes = {
                'rol.actualizado': 1000,      // 1 segundo para roles
                'sucursal.creada': 2000,      // 2 segundos para sucursales
                'sucursal.actualizada': 2000,
                'sucursal.eliminada': 2000,
                'empleado.creado': 2000,      // 2 segundos para empleados
                'empleado.actualizado': 2000,
                'empleado.eliminado': 2000,
                'vehiculo.creado': 2000,      // 2 segundos para vehículos
                'vehiculo.actualizado': 2000,
                'vehiculo.eliminado': 2000,
                'vehiculo-sucursal.creado': 3000,    // 3 segundos para asignaciones
                'vehiculo-sucursal.actualizado': 3000,
                'vehiculo-sucursal.eliminado': 3000,
                'vehiculo_sucursal.creado': 3000,    // 3 segundos para asignaciones (formato con guión bajo)
                'vehiculo_sucursal.actualizado': 3000,
                'vehiculo_sucursal.eliminado': 3000
            };
            
            // Extraer el tipo de evento del ID
            const eventTypePattern = /^([^-]+\.[^-]+)-/;
            const match = eventId.match(eventTypePattern);
            
            if (match) {
                const eventType = match[1];
                const timeWindow = eventTypes[eventType];
                
                if (timeWindow) {
                    // Verificar si hay eventos similares en la ventana de tiempo especificada
                    const currentTime = Date.now();
                    const existingSimilarEvents = Array.from(processedEvents).filter(id => 
                        id.startsWith(eventType) && id !== eventId
                    );
                    
                    for (const existingId of existingSimilarEvents) {
                        // Extraer timestamp del ID existente
                        const timestampPattern = /-(\d+)$/;
                        const existingMatch = existingId.match(timestampPattern);
                        const currentMatch = eventId.match(timestampPattern);
                        
                        if (existingMatch && currentMatch) {
                            const existingTime = parseInt(existingMatch[1]) * 1000; // Convertir a ms
                            const currentEventTime = parseInt(currentMatch[1]) * 1000;
                            
                            // Si la diferencia es menor a la ventana de tiempo, es duplicado
                            if (Math.abs(currentEventTime - existingTime) < timeWindow) {
                                console.log(`🔄 Evento ${eventType} duplicado rechazado (ventana: ${timeWindow}ms):`, eventId);
                                return true;
                            }
                        }
                    }
                }
            }
            
            if (processedEvents.has(eventId)) {
                console.log('🔄 Evento duplicado detectado:', eventId);
                return true;
            }
            
            processedEvents.add(eventId);
            
            // Limpiar eventos antiguos (más de 30 segundos)
            if (processedEvents.size > 100) {
                const currentTime = Math.floor(Date.now() / 1000);
                const eventsToRemove = [];
                
                processedEvents.forEach(id => {
                    const parts = id.split('-');
                    const eventTime = parseInt(parts[parts.length - 1]);
                    if (currentTime - eventTime > 30) {
                        eventsToRemove.push(id);
                    }
                });
                
                eventsToRemove.forEach(id => processedEvents.delete(id));
            }
            
            return false;
        }
        
        function processEventBuffer() {
            if (eventBuffer.length === 0) return;
            
            console.log(`📦 Procesando buffer de ${eventBuffer.length} eventos`);
            
            // Agrupar eventos similares que llegaron al mismo tiempo
            const groupedEvents = {};
            
            eventBuffer.forEach(event => {
                // Crear clave más específica para agrupar mejor los eventos
                const eventType = event.event || 'unknown';
                const channel = event.channel || 'unknown';
                
                // Para eventos de entidades específicas, incluir más contexto
                let groupKey = `${eventType}-${channel}`;
                
                // Si el evento tiene datos, intentar extraer ID de entidad
                if (event.data) {
                    try {
                        const eventData = JSON.parse(event.data);
                        const entityId = eventData.id || eventData.id_empleado || eventData.id_vehiculo || 
                                        eventData.id_sucursal || eventData.id_usuario || eventData.id_rol;
                        if (entityId) {
                            groupKey += `-${entityId}`;
                        }
                    } catch (e) {
                        // Si no se puede parsear, usar la clave original
                    }
                }
                
                if (!groupedEvents[groupKey]) {
                    groupedEvents[groupKey] = [];
                }
                groupedEvents[groupKey].push(event);
            });
            
            // Procesar solo el último evento de cada grupo
            Object.entries(groupedEvents).forEach(([groupKey, eventGroup]) => {
                if (eventGroup.length > 1) {
                    console.log(`⚠️ ${eventGroup.length} eventos similares agrupados para ${groupKey}, procesando solo el último`);
                }
                const lastEvent = eventGroup[eventGroup.length - 1];
                processRealTimeEvent(lastEvent);
            });
            
            // Limpiar buffer
            eventBuffer = [];
        }
        
        // ===== WEBSOCKET CONNECTION =====
        function connectWebSocket() {
            try {
                const { wsHost, wsPort, appKey } = window.appConfig;
                const wsUrl = `ws://${wsHost}:${wsPort}/app/${appKey}`;
                
                socket = new WebSocket(wsUrl);
                
                socket.onopen = function() {
                    console.log('✅ WebSocket conectado');
                    updateConnectionStatus('connected', 'Conectado');
                    
                    // Limpiar historial de eventos al reconectar
                    processedEvents.clear();
                    eventBuffer = [];
                    console.log('🧹 Historial de eventos limpiado');
                    
                    // Suscribirse a canales
                    const channels = ['empleados', 'roles', 'usuarios', 'vehiculos', 'sucursales', 'vehiculo-sucursal', 'dashboard'];
                    channels.forEach(channel => {
                        const subscribeMsg = JSON.stringify({
                            event: 'pusher:subscribe',
                            data: { channel: channel }
                        });
                        socket.send(subscribeMsg);
                    });
                    
                    // Cargar métricas iniciales
                    loadInitialMetrics();
                };
                
                socket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    console.log('📨 Mensaje WebSocket recibido:', data);
                    
                    // Generar ID único para el evento
                    const eventId = generateEventId(data);
                    
                    // Verificar si es un evento duplicado
                    if (isDuplicateEvent(eventId)) {
                        return; // Ignorar evento duplicado
                    }
                    
                    if (data.event && !data.event.startsWith('pusher:')) {
                        // Agregar al buffer para procesamiento en lote
                        eventBuffer.push(data);
                        
                        // Cancelar timeout anterior si existe
                        if (processingTimeout) {
                            clearTimeout(processingTimeout);
                        }
                        
                        // Procesar eventos después de 100ms de inactividad
                        processingTimeout = setTimeout(processEventBuffer, 100);
                    } else if (data.event && data.event.startsWith('pusher_internal:')) {
                        // Procesar eventos internos inmediatamente
                        handleRealTimeEvent(data);
                    }
                };
                
                socket.onerror = function(error) {
                    console.error('❌ Error WebSocket:', error);
                    updateConnectionStatus('disconnected', 'Error de conexión');
                };
                
                socket.onclose = function() {
                    console.log('🔌 WebSocket desconectado');
                    updateConnectionStatus('disconnected', 'Desconectado');
                    
                    // Reconectar después de 5 segundos
                    setTimeout(connectWebSocket, 5000);
                };
                
            } catch (error) {
                console.error('❌ Error conectando WebSocket:', error);
                updateConnectionStatus('disconnected', 'Error de conexión');
            }
        }
        
        function updateConnectionStatus(status, text) {
            const statusElement = document.getElementById('connection-status');
            const indicator = statusElement.querySelector('.status-indicator');
            const textElement = statusElement.querySelector('span');
            
            indicator.className = `status-indicator status-${status}`;
            textElement.textContent = text;
            
            // Actualizar última actualización
            document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
        }
        
        // ===== CARGA DE MÉTRICAS =====
        async function loadInitialMetrics() {
            try {
                // Usar queries individuales que sabemos que funcionan
                const queries = {
                    empleados: 'query { empleados { id_empleado } }',
                    vehiculos: 'query { vehiculos { id_vehiculo estado } }',
                    sucursales: 'query { sucursales { id_sucursal } }',
                    usuarios: 'query { users { id_usuario } }',
                    roles: 'query { roles { id_rol } }'
                };
                
                for (const [key, query] of Object.entries(queries)) {
                    try {
                        const data = await graphqlQuery(query);
                        let count = 0;
                        
                        if (key === 'empleados' && data.empleados) count = data.empleados.length;
                        else if (key === 'vehiculos' && data.vehiculos) count = data.vehiculos.length;
                        else if (key === 'sucursales' && data.sucursales) count = data.sucursales.length;
                        else if (key === 'usuarios' && data.users) count = data.users.length;
                        else if (key === 'roles' && data.roles) count = data.roles.length;
                        
                        metricsData[key] = count;
                        updateMetricCard(key, count);
                        
                        // Para vehículos, también procesar estados
                        if (key === 'vehiculos' && data.vehiculos) {
                            processVehiculosData(data.vehiculos);
                        }
                        
                    } catch (error) {
                        console.error(`Error cargando ${key}:`, error);
                        updateMetricCard(key, '--');
                    }
                }
                
                // Inicializar gráficos
                initializeCharts();
                
                // Inicializar asignaciones (se actualizará con eventos en tiempo real)
                metricsData.asignaciones = 0;
                updateMetricCard('asignaciones', 0);
                
            } catch (error) {
                console.error('Error cargando métricas:', error);
            }
        }
        
        function updateMetricCard(type, value) {
            // Mapear tipos a IDs correctos
            const typeIdMap = {
                'empleados': 'total-empleados',
                'vehiculos': 'total-vehiculos',
                'sucursales': 'total-sucursales',
                'usuarios': 'total-usuarios',
                'roles': 'total-roles',
                'asignaciones': 'total-asignaciones'
            };
            
            const elementId = typeIdMap[type] || `total-${type}`;
            const element = document.querySelector(`#${elementId} .metric-number`);
            
            console.log(`🎯 Actualizando tarjeta métrica: ${elementId} = ${value}`);
            
            if (element) {
                element.textContent = value;
                
                // Agregar efecto visual de actualización
                element.classList.add('metric-updated');
                setTimeout(() => {
                    element.classList.remove('metric-updated');
                }, 1000);
                
                console.log(`✅ Tarjeta métrica actualizada: ${elementId}`);
            } else {
                console.log(`⚠️ No se encontró elemento para: #${elementId} .metric-number`);
            }
        }
        
        // ===== MANEJO DE EVENTOS EN TIEMPO REAL =====
        function handleRealTimeEvent(data) {
            console.log('📡 Evento recibido en Dashboard:', data);
            
            // Filtrar eventos internos de Pusher
            if (data.event && data.event.startsWith('pusher_internal:')) {
                // Solo mostrar suscripciones exitosas
                if (data.event === 'pusher_internal:subscription_succeeded') {
                    const channelName = data.channel;
                    const channelDisplayName = getChannelDisplayName(channelName);
                    console.log(`✅ Dashboard suscrito al canal: ${channelDisplayName}`);
                    
                    // Agregar a feed de actividad solo para suscripciones
                    addActivityItem({
                        event: 'subscription_succeeded',
                        data: {
                            entity_type: channelName,
                            action: 'connected',
                            message: `Dashboard conectado al canal ${channelDisplayName}`
                        },
                        channel: channelName
                    });
                }
                return; // No procesar otros eventos internos
            }
            
            // Procesar eventos reales de negocio
            processRealTimeEvent(data);
        }
        
        function processRealTimeEvent(data) {
            if (data.event && !data.event.startsWith('pusher:')) {
                console.log('🎯 Procesando evento de negocio:', data.event, 'en canal:', data.channel);
                
                // Convertir el evento al formato esperado por el dashboard
                const businessEvent = convertToBusinessEvent(data);
                if (businessEvent) {
                    addActivityItem(businessEvent);
                    
                    // Actualizar métricas si es necesario
                    updateMetricsFromEvent(businessEvent);
                }
            }
        }
        
        function convertToBusinessEvent(rawEvent) {
            console.log('🔄 Convirtiendo evento:', rawEvent);
            
            const eventName = rawEvent.event;
            const channel = rawEvent.channel;
            const eventData = JSON.parse(rawEvent.data || '{}');
            
            // Mapear eventos específicos
            const eventMapping = {
                'sucursal.creada': { entity: 'sucursales', action: 'created' },
                'sucursal.actualizada': { entity: 'sucursales', action: 'updated' },
                'sucursal.eliminada': { entity: 'sucursales', action: 'deleted' },
                
                'empleado.creado': { entity: 'empleados', action: 'created' },
                'empleado.actualizado': { entity: 'empleados', action: 'updated' },
                'empleado.eliminado': { entity: 'empleados', action: 'deleted' },
                
                'vehiculo.creado': { entity: 'vehiculos', action: 'created' },
                'vehiculo.actualizado': { entity: 'vehiculos', action: 'updated' },
                'vehiculo.eliminado': { entity: 'vehiculos', action: 'deleted' },
                
                'usuario.creado': { entity: 'usuarios', action: 'created' },
                'usuario.actualizado': { entity: 'usuarios', action: 'updated' },
                'usuario.eliminado': { entity: 'usuarios', action: 'deleted' },
                
                'rol.creado': { entity: 'roles', action: 'created' },
                'rol.actualizado': { entity: 'roles', action: 'updated' },
                'rol.eliminado': { entity: 'roles', action: 'deleted' },
                
                'vehiculo-sucursal.creado': { entity: 'vehiculo-sucursal', action: 'assigned' },
                'vehiculo-sucursal.actualizado': { entity: 'vehiculo-sucursal', action: 'updated' },
                'vehiculo-sucursal.eliminado': { entity: 'vehiculo-sucursal', action: 'deleted' },
                
                // Mapeos adicionales para el formato con guión bajo
                'vehiculo_sucursal.creado': { entity: 'vehiculo-sucursal', action: 'assigned' },
                'vehiculo_sucursal.actualizado': { entity: 'vehiculo-sucursal', action: 'updated' },
                'vehiculo_sucursal.eliminado': { entity: 'vehiculo-sucursal', action: 'deleted' }
            };
            
            const mapping = eventMapping[eventName];
            if (!mapping) {
                console.log('⚠️ Evento no mapeado:', eventName);
                return null;
            }
            
            // Crear evento en formato esperado por el dashboard
            const businessEvent = {
                event: eventName,
                channel: channel,
                data: {
                    entity_type: mapping.entity,
                    action: mapping.action,
                    message: eventData.message || `${mapping.action} en ${mapping.entity}`,
                    raw_data: eventData
                }
            };
            
            console.log('✅ Evento convertido:', businessEvent);
            return businessEvent;
        }
        
        function updateMetricsFromEvent(businessEvent) {
            const entityType = businessEvent.data.entity_type;
            const action = businessEvent.data.action;
            
            console.log(`📊 Actualizando métricas: ${entityType} - ${action}`);
            
            // Mapear entidades a métricas
            const entityMetricMap = {
                'empleados': 'empleados',
                'vehiculos': 'vehiculos', 
                'sucursales': 'sucursales',
                'usuarios': 'usuarios',
                'roles': 'roles',
                'vehiculo-sucursal': 'asignaciones'
            };
            
            const metricKey = entityMetricMap[entityType];
            if (!metricKey) {
                console.log('⚠️ Entidad no tiene métrica asociada:', entityType);
                return;
            }
            
            // Verificar si la métrica existe en metricsData
            if (metricsData[metricKey] === undefined) {
                console.log('⚠️ Métrica no encontrada en metricsData:', metricKey);
                metricsData[metricKey] = 0; // Inicializar si no existe
            }
            
            if (action === 'created' || action === 'assigned') {
                metricsData[metricKey]++;
                updateMetricCard(metricKey, metricsData[metricKey]);
                console.log(`📈 ${metricKey} incrementado a:`, metricsData[metricKey]);
            } else if (action === 'deleted') {
                metricsData[metricKey] = Math.max(0, metricsData[metricKey] - 1);
                updateMetricCard(metricKey, metricsData[metricKey]);
                console.log(`📉 ${metricKey} decrementado a:`, metricsData[metricKey]);
            } else if (action === 'updated') {
                console.log(`🔄 ${metricKey} actualizado (contador sin cambios):`, metricsData[metricKey]);
            }
        }
        
        function getChannelDisplayName(channelName) {
            const channelNames = {
                'empleados': '👥 Empleados',
                'roles': '🔐 Roles',
                'usuarios': '👤 Usuarios',
                'vehiculos': '🚗 Vehículos',
                'sucursales': '🏢 Sucursales',
                'vehiculo-sucursal': '🔗 Asignaciones Vehículo-Sucursal',
                'dashboard': '📊 Dashboard'
            };
            return channelNames[channelName] || channelName;
        }
        
        function addActivityItem(eventData) {
            activityCounter++;
            const feed = document.getElementById('activity-feed');
            
            // Si es el primer evento, limpiar el mensaje de espera
            if (activityCounter === 1) {
                feed.innerHTML = '';
            }
            
            const data = eventData.data;
            const entityType = data.entity_type || 'sistema';
            const action = data.action || 'evento';
            const timestamp = new Date().toLocaleString();
            
            // Verificación mejorada contra duplicados para entidades específicas
            const recentItems = feed.querySelectorAll('.activity-item');
            const currentTime = new Date();
            
            // Definir ventanas de tiempo específicas para cada tipo de entidad
            const duplicateWindows = {
                'sucursales': 3000,      // 3 segundos para sucursales
                'empleados': 3000,       // 3 segundos para empleados  
                'vehiculos': 3000,       // 3 segundos para vehículos
                'vehiculo-sucursal': 5000,  // 5 segundos para asignaciones
                'roles': 2000,           // 2 segundos para roles
                'usuarios': 3000         // 3 segundos para usuarios
            };
            
            const timeWindow = duplicateWindows[entityType] || 2000; // 2 segundos por defecto
            
            for (let i = 0; i < Math.min(10, recentItems.length); i++) {
                const item = recentItems[i];
                const existingTitle = item.querySelector('.activity-title')?.textContent?.trim();
                const existingDescription = item.querySelector('.activity-description')?.textContent?.trim();
                const existingTimeText = item.querySelector('.activity-time')?.textContent?.trim();
                
                try {
                    // Parsear tiempo existente
                    const existingTime = new Date(existingTimeText);
                    const timeDiff = Math.abs(currentTime - existingTime);
                    
                    // Verificar si es el mismo tipo de entidad y acción en la ventana de tiempo
                    const entityDisplayName = getEntityDisplayName(entityType);
                    const actionDisplayName = getActionDisplayName(action);
                    
                    const isSameEntity = existingTitle && existingTitle.includes(entityDisplayName);
                    const isSameAction = existingDescription && 
                        (existingDescription.includes(actionDisplayName) || 
                         existingDescription.includes(action));
                    
                    if (isSameEntity && isSameAction && timeDiff < timeWindow) {
                        console.log(`🔄 Evento similar detectado para ${entityType} ${action} dentro de ${timeWindow}ms, ignorando duplicado`);
                        activityCounter--; // Revertir contador
                        return; // No agregar evento duplicado
                    }
                } catch (timeParseError) {
                    // Si hay error parseando tiempo, continuar con verificación básica
                    const timeDiff = 30000; // Asumir tiempo grande para evitar errores
                    
                    if (existingTitle && existingDescription && timeDiff < timeWindow) {
                        const entityDisplayName = getEntityDisplayName(entityType);
                        const actionDisplayName = getActionDisplayName(action);
                        
                        if (existingTitle.includes(entityDisplayName) && 
                            existingDescription.includes(actionDisplayName)) {
                            console.log(`🔄 Evento similar detectado (fallback), ignorando duplicado`);
                            activityCounter--; // Revertir contador
                            return;
                        }
                    }
                }
            }
            
            // Personalizar mensaje según el evento
            let message = data.message || 'Evento del sistema';
            let title = '';
            
            console.log(`🎨 Creando actividad para: ${entityType} - ${action}`);
            
            // Mensajes específicos por entidad y acción
            if (action === 'connected') {
                title = `✅ ${getChannelDisplayName(entityType)}`;
                message = `Canal conectado exitosamente`;
            } else {
                const entityDisplayName = getEntityDisplayName(entityType);
                const actionDisplayName = getActionDisplayName(action);
                const actionIcon = getActionIcon(action);
                
                title = `<i class="fas ${actionIcon} me-2"></i>${entityDisplayName} ${actionDisplayName}`;
                
                // Mensajes personalizados según la entidad y acción
                switch(entityType) {
                    case 'sucursales':
                        if (action === 'created') {
                            message = `Nueva sucursal creada exitosamente`;
                        } else if (action === 'updated') {
                            message = `Sucursal actualizada correctamente`;
                        } else if (action === 'deleted') {
                            message = `Sucursal eliminada del sistema`;
                        }
                        break;
                    case 'empleados':
                        if (action === 'created') {
                            message = `Nuevo empleado registrado en el sistema`;
                        } else if (action === 'updated') {
                            message = `Información de empleado actualizada`;
                        } else if (action === 'deleted') {
                            message = `Empleado eliminado del sistema`;
                        }
                        break;
                    case 'vehiculos':
                        if (action === 'created') {
                            message = `Nuevo vehículo agregado a la flota`;
                        } else if (action === 'updated') {
                            message = `Información de vehículo actualizada`;
                        } else if (action === 'deleted') {
                            message = `Vehículo eliminado de la flota`;
                        }
                        break;
                    case 'usuarios':
                        if (action === 'created') {
                            message = `Nueva cuenta de usuario creada`;
                        } else if (action === 'updated') {
                            message = `Cuenta de usuario actualizada`;
                        } else if (action === 'deleted') {
                            message = `Usuario eliminado del sistema`;
                        }
                        break;
                    case 'roles':
                        if (action === 'created') {
                            message = `Nuevo rol de sistema creado`;
                        } else if (action === 'updated') {
                            message = `Rol de sistema actualizado`;
                        } else if (action === 'deleted') {
                            message = `Rol eliminado del sistema`;
                        }
                        break;
                    case 'vehiculo-sucursal':
                        if (action === 'assigned' || action === 'created') {
                            message = `Nueva asignación vehículo-sucursal realizada`;
                        } else if (action === 'updated') {
                            message = `Asignación vehículo-sucursal actualizada`;
                        } else if (action === 'deleted') {
                            message = `Asignación vehículo-sucursal eliminada`;
                        }
                        break;
                    default:
                        message = data.message || `${entityDisplayName} ${actionDisplayName}`;
                }
            }
            
            const actionIcon = getActionIcon(action);
            
            const activityHtml = `
                <div class="activity-item">
                    <div class="activity-icon ${action}">
                        <i class="fas ${actionIcon}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">
                            ${title}
                        </div>
                        <div class="activity-description">${message}</div>
                        <div class="activity-time">${timestamp}</div>
                    </div>
                </div>
            `;
            
            feed.insertAdjacentHTML('afterbegin', activityHtml);
            
            // Limitar a 50 eventos
            const items = feed.querySelectorAll('.activity-item');
            if (items.length > 50) {
                items[items.length - 1].remove();
            }
            
            console.log(`✅ Actividad añadida: ${entityType} ${action}`);
        }
        
        function getEntityDisplayName(entityType) {
            const entityNames = {
                'empleado': 'Empleado',
                'empleados': 'Empleados',
                'vehiculo': 'Vehículo',
                'vehiculos': 'Vehículos', 
                'sucursal': 'Sucursal',
                'sucursales': 'Sucursales',
                'usuario': 'Usuario',
                'usuarios': 'Usuarios',
                'rol': 'Rol',
                'roles': 'Roles',
                'vehiculo_sucursal': 'Asignación',
                'vehiculo-sucursal': 'Asignación'
            };
            return entityNames[entityType] || 'Elemento';
        }
        
        function getActionDisplayName(action) {
            const actions = {
                'created': 'creado',
                'updated': 'actualizado',
                'deleted': 'eliminado',
                'assigned': 'asignado',
                'connected': 'conectado'
            };
            return actions[action] || 'modificado';
        }
        
        function getActionIcon(action) {
            const icons = {
                'created': 'fa-plus-circle',
                'updated': 'fa-edit',
                'deleted': 'fa-trash-alt',
                'assigned': 'fa-link',
                'connected': 'fa-plug'
            };
            return icons[action] || 'fa-info-circle';
        }
        
        function getEntityIcon(entityType) {
            const icons = {
                'empleado': 'fa-user',
                'empleados': 'fa-users',
                'vehiculo': 'fa-car',
                'vehiculos': 'fa-car',
                'sucursal': 'fa-building',
                'sucursales': 'fa-building',
                'usuario': 'fa-user-shield',
                'usuarios': 'fa-user-shield',
                'rol': 'fa-key',
                'roles': 'fa-key',
                'vehiculo_sucursal': 'fa-route',
                'vehiculo-sucursal': 'fa-route',
                'dashboard': 'fa-tachometer-alt'
            };
            return icons[entityType] || 'fa-circle';
        }
        
        // ===== GRÁFICOS =====
        function initializeCharts() {
            initVehiculosChart();
            initSucursalesChart();
        }
        
        function initVehiculosChart() {
            const options = {
                series: [{
                    name: 'Vehículos',
                    data: [10, 8, 5, 3] // Datos de ejemplo
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
                },
                colors: ['#3b82f6'],
                xaxis: {
                    categories: ['Disponible', 'En Uso', 'Mantenimiento', 'Fuera de Servicio']
                },
                title: {
                    text: 'Estado de la Flota',
                    style: { fontSize: '14px', fontWeight: 600 }
                }
            };
            
            charts.vehiculos = new ApexCharts(document.querySelector("#vehiculos-chart"), options);
            charts.vehiculos.render();
        }
        
        function initSucursalesChart() {
            const options = {
                series: [44, 25, 31], // Datos de ejemplo
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: ['Sucursal Central', 'Sucursal Norte', 'Sucursal Sur'],
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                legend: {
                    position: 'bottom'
                }
            };
            
            charts.sucursales = new ApexCharts(document.querySelector("#sucursales-chart"), options);
            charts.sucursales.render();
        }
        
        function processVehiculosData(vehiculos) {
            const estados = {};
            vehiculos.forEach(vehiculo => {
                const estado = vehiculo.estado || 'Sin Estado';
                estados[estado] = (estados[estado] || 0) + 1;
            });
            
            // Actualizar gráfico si existe
            if (charts.vehiculos) {
                const data = Object.values(estados);
                const categories = Object.keys(estados);
                
                charts.vehiculos.updateOptions({
                    series: [{ data: data }],
                    xaxis: { categories: categories }
                });
            }
        }
        
        // ===== UTILIDADES =====
        async function graphqlQuery(query) {
            const response = await fetch('/graphql', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.appConfig.csrfToken
                },
                body: JSON.stringify({ query })
            });
            
            const result = await response.json();
            if (result.errors) {
                throw new Error(result.errors[0].message);
            }
            
            return result.data;
        }
        
        function showError(message) {
            console.error('❌ Error:', message);
            
            // Crear notificación de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
            errorDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(errorDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.parentNode.removeChild(errorDiv);
                }
            }, 5000);
        }
        
        function clearActivityLog() {
            document.getElementById('activity-feed').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-satellite-dish fa-2x mb-3 opacity-50"></i>
                    <p>Log limpiado. Esperando nuevos eventos...</p>
                </div>
            `;
            activityCounter = 0;
            
            // También limpiar historial de deduplicación
            processedEvents.clear();
            eventBuffer = [];
            console.log('🧹 Historial de actividad y deduplicación limpiado');
        }
        
        function showDeduplicationStats() {
            console.log('📊 Estadísticas de Deduplicación:');
            console.log(`- Eventos únicos procesados: ${processedEvents.size}`);
            console.log(`- Eventos en buffer: ${eventBuffer.length}`);
            console.log(`- Total actividades mostradas: ${activityCounter}`);
        }
        
        function exportActivityLog() {
            const activities = document.querySelectorAll('.activity-item');
            const data = Array.from(activities).map(item => {
                const title = item.querySelector('.activity-title').textContent.trim();
                const description = item.querySelector('.activity-description').textContent.trim();
                const time = item.querySelector('.activity-time').textContent.trim();
                return `${time} - ${title}: ${description}`;
            }).join('\n');
            
            const blob = new Blob([data], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `actividad-dashboard-${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Dashboard Profesional v2 inicializado');
            
            // Conectar WebSocket
            connectWebSocket();
            
            // Event listeners para las pestañas
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    if (target && target.includes('-table')) {
                        const tableType = target.replace('#', '').replace('-table', '');
                        loadTableData(tableType);
                    }
                });
            });
            
            // Event listeners para búsqueda en las tablas
            const searchInputs = document.querySelectorAll('[id^="search-"]');
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const tableType = this.id.replace('search-', '');
                    searchTable(tableType);
                });
            });
            
            // Cargar datos de la primera pestaña al inicio
            setTimeout(() => {
                loadTableData('empleados');
            }, 1000);
        });
        
        // ===== FUNCIONES DE TABLA =====
        async function loadTableData(tableType) {
            console.log(`Cargando datos de ${tableType}...`);
            
            let query = '';
            
            switch(tableType) {
                case 'empleados':
                    query = `
                        query {
                            empleados {
                                id_empleado
                                nombre
                                cargo
                                correo
                                telefono
                                created_at
                            }
                        }
                    `;
                    break;
                case 'vehiculos':
                    query = `
                        query {
                            vehiculos {
                                id_vehiculo
                                marca
                                modelo
                                anio
                                placa
                                estado
                                tipo_id
                                created_at
                            }
                        }
                    `;
                    break;
                case 'sucursales':
                    query = `
                        query {
                            sucursales {
                                id_sucursal
                                nombre
                                direccion
                                telefono
                                ciudad
                                created_at
                            }
                        }
                    `;
                    break;
                case 'usuarios':
                    query = `
                        query {
                            usuarios {
                                id_usuario
                                username
                                rol_id
                                empleado_id
                                created_at
                                rol {
                                    nombre
                                }
                                empleado {
                                    nombre
                                }
                            }
                        }
                    `;
                    break;
                case 'asignaciones':
                    // Primero intentar con GraphQL
                    query = `
                        query {
                            vehiculoSucursales {
                                id
                                id_vehiculo
                                id_sucursal
                                fecha_asignacion
                                created_at
                            }
                        }
                    `;
                    
                    // Agregar debug específico para asignaciones
                    console.log('🔍 Intentando GraphQL para asignaciones...');
                    
                    fetch('/graphql', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ query })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(`📊 Respuesta GraphQL para ${tableType}:`, data);
                        
                        if (data.errors) {
                            console.error('❌ GraphQL errors:', data.errors);
                            console.log('🔄 Intentando fallback con REST API...');
                            
                            // Fallback a REST API
                            return fetch('/api/vehiculo-sucursal')
                                .then(response => {
                                    console.log('📡 REST Response status:', response.status);
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(restData => {
                                    console.log('✅ Datos REST para asignaciones:', restData);
                                    populateTable('asignaciones', restData);
                                })
                                .catch(restError => {
                                    console.error('❌ Error REST API:', restError);
                                    showError('Error al cargar asignaciones desde ambas fuentes: GraphQL y REST');
                                    
                                    // Mostrar mensaje de no hay datos
                                    const tableBody = document.querySelector(`#${tableType}-table tbody`);
                                    if (tableBody) {
                                        tableBody.innerHTML = `
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">
                                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                    <p class="mb-0">Error al cargar asignaciones. Verifique la conexión.</p>
                                                </td>
                                            </tr>
                                        `;
                                    }
                                });
                        } else {
                            const results = data.data[Object.keys(data.data)[0]];
                            console.log('✅ GraphQL exitoso para asignaciones:', results);
                            populateTable('asignaciones', results);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error de conexión GraphQL:', error);
                        showError(`Error de conexión al cargar ${tableType}`);
                    });
                    
                    return; // Salir temprano para asignaciones
                    break;
            }
            
            console.log(`🔍 Query para ${tableType}:`, query);
            
            fetch('/graphql', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ query })
            })
            .then(response => response.json())
            .then(data => {
                console.log(`📊 Respuesta para ${tableType}:`, data);
                
                if (data.errors) {
                    console.error('GraphQL errors:', data.errors);
                    showError(`Error al cargar datos de ${tableType}: ${data.errors[0].message}`);
                    return;
                }
                
                const results = data.data[Object.keys(data.data)[0]];
                populateTable(tableType, results);
            })
            .catch(error => {
                console.error('Error:', error);
                showError(`Error de conexión al cargar ${tableType}`);
            });
        }
        
        function populateTable(tableType, data) {
            const tableBody = document.querySelector(`#${tableType}-table tbody`);
            
            if (!tableBody) {
                console.error(`No se encontró tbody para ${tableType}`);
                return;
            }
            
            if (!data || data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p class="mb-0">No hay datos disponibles</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tableBody.innerHTML = '';
            
            data.forEach(item => {
                const row = document.createElement('tr');
                
                switch(tableType) {
                    case 'empleados':
                        row.innerHTML = `
                            <td>${item.id_empleado}</td>
                            <td>${item.nombre}</td>
                            <td>${item.cargo}</td>
                            <td>${item.correo}</td>
                            <td>${item.telefono || 'N/A'}</td>
                            <td>${new Date(item.created_at).toLocaleDateString()}</td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewItem('empleados', ${item.id_empleado})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editItem('empleados', ${item.id_empleado})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteItem('empleados', ${item.id_empleado})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        break;
                    case 'vehiculos':
                        const statusClass = {
                            'disponible': 'disponible',
                            'en_uso': 'en-uso',
                            'mantenimiento': 'mantenimiento',
                            'fuera_de_servicio': 'fuera-servicio'
                        }[item.estado] || 'disponible';
                        
                        row.innerHTML = `
                            <td>${item.id_vehiculo}</td>
                            <td><span class="badge bg-dark">${item.placa}</span></td>
                            <td>${item.marca}</td>
                            <td>${item.modelo}</td>
                            <td>${item.anio}</td>
                            <td><span class="status-badge ${statusClass}">${item.estado}</span></td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewItem('vehiculos', ${item.id_vehiculo})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editItem('vehiculos', ${item.id_vehiculo})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteItem('vehiculos', ${item.id_vehiculo})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        break;
                    case 'sucursales':
                        row.innerHTML = `
                            <td>${item.id_sucursal}</td>
                            <td>${item.nombre}</td>
                            <td>${item.direccion}</td>
                            <td>${item.ciudad}</td>
                            <td>${item.telefono || 'N/A'}</td>
                            <td>${new Date(item.created_at).toLocaleDateString()}</td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewItem('sucursales', ${item.id_sucursal})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editItem('sucursales', ${item.id_sucursal})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteItem('sucursales', ${item.id_sucursal})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        break;
                    case 'usuarios':
                        row.innerHTML = `
                            <td>${item.id_usuario}</td>
                            <td>${item.username}</td>
                            <td>${item.empleado?.nombre || 'N/A'}</td>
                            <td><span class="badge bg-primary">${item.rol?.nombre || 'Sin rol'}</span></td>
                            <td>${new Date(item.created_at).toLocaleDateString()}</td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewItem('usuarios', ${item.id_usuario})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editItem('usuarios', ${item.id_usuario})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteItem('usuarios', ${item.id_usuario})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        break;
                    case 'asignaciones':
                        row.innerHTML = `
                            <td>${item.id}</td>
                            <td>Vehículo ID: ${item.id_vehiculo || 'N/A'}</td>
                            <td>Sucursal ID: ${item.id_sucursal || 'N/A'}</td>
                            <td>${item.fecha_asignacion ? new Date(item.fecha_asignacion).toLocaleDateString() : 'N/A'}</td>
                            <td>${item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A'}</td>
                            <td>
                                <div class="btn-actions">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewItem('asignaciones', ${item.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" onclick="editItem('asignaciones', ${item.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteItem('asignaciones', ${item.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        break;
                }
                
                tableBody.appendChild(row);
            });
            
            // Actualizar contador en la pestaña
            const countBadge = document.querySelector(`#${tableType}-count`);
            if (countBadge) {
                countBadge.textContent = data.length;
            }
            
            console.log(`✅ Tabla ${tableType} poblada con ${data.length} registros`);
        }
        
        // Funciones para CRUD
        function viewItem(type, id) {
            console.log(`Ver ${type} con ID: ${id}`);
            alert(`Ver detalles de ${type} ID: ${id}`);
        }
        
        function editItem(type, id) {
            console.log(`Editar ${type} con ID: ${id}`);
            alert(`Editar ${type} ID: ${id}`);
        }
        
        function deleteItem(type, id) {
            if (!confirm(`¿Estás seguro de que deseas eliminar este ${type}?`)) return;
            
            console.log(`Eliminar ${type} con ID: ${id}`);
            alert(`Eliminar ${type} ID: ${id}`);
        }
        
        function showCreateModal(type) {
            console.log(`Crear nuevo ${type}`);
            alert(`Crear nuevo ${type}`);
        }
        
        function searchTable(tableType) {
            const searchInput = document.querySelector(`#search-${tableType}`);
            const tableRows = document.querySelectorAll(`#${tableType}-table tbody tr`);
            
            if (!searchInput || !tableRows.length) return;
            
            const searchTerm = searchInput.value.toLowerCase();
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
        
        function refreshAllTables() {
            const activeTab = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
            if (activeTab) {
                const target = activeTab.getAttribute('data-bs-target');
                const tableType = target.replace('#', '').replace('-table', '');
                loadTableData(tableType);
            }
        }
        
        function exportAllData() {
            alert('Función de exportar datos en desarrollo');
        }
    </script>
</body>
</html>
