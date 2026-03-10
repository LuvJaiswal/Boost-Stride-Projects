<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Summernote Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --sidebar-width: 280px;
            --sidebar-bg: #0f172a;
            --content-bg: #f8fafc;
            --accent-color: #6366f1;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background: var(--content-bg);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar { 
            width: var(--sidebar-width);
            height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            padding: 2rem 1.5rem;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            font-size: 1.6rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .sidebar-nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.3);
            margin: 2rem 0 1rem 1rem;
            font-weight: 700;
        }

        .sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0.9rem 1.2rem; 
            border-radius: 14px; 
            margin-bottom: 0.4rem; 
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .sidebar a:hover { 
            background: rgba(255, 255, 255, 0.06); 
            color: white; 
            transform: translateX(4px);
        }

        .sidebar a:hover i { transform: scale(1.1); }

        .sidebar a.active { 
            background: var(--primary-gradient);
            color: white; 
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            font-weight: 600;
        }

        .sidebar hr {
            border-color: rgba(255, 255, 255, 0.08);
            margin: 1.5rem 0;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content { 
            padding: 2rem 3rem 4rem 3rem; 
            flex: 1;
        }

        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1.2rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }

        /* Cards and UI Elements */
        .card { 
            border: none; 
            border-radius: 26px; 
            box-shadow: var(--card-shadow);
            background: white;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 62px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: 1.4rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.9rem 2.2rem;
            border-radius: 18px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
        }

        .btn-gradient:hover {
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            transform: translateY(-3px);
            color: white;
        }

        .form-control, .form-select {
            padding: 0.9rem 1.4rem;
            border-radius: 18px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: white;
            border-color: #6366f1;
            box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1);
        }

        /* Badge Styling */
        .badge-premium {
            padding: 0.6rem 1.2rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }

        /* Mobile specific enhancements */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); z-index: 1100; }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .main-content { padding: 1.5rem; }
            .top-navbar { padding: 1rem 1.5rem; }
        }

        /* Custom Scrollbar for Sleek Feel */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button (Only on Small Screens) -->
    <div class="d-lg-none p-3 bg-white border-bottom sticky-top d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold">Boost Stride</h5>
        <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar for Desktop -->
    <nav class="sidebar d-none d-lg-block">
        <div class="sidebar-brand">
            <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="fas fa-rocket text-primary" style="font-size: 1.4rem;"></i>
            </div>
            <span>Boost Stride</span>
        </div>

        <div class="sidebar-nav-label">Main Dashboard</div>
        <a href="?page=dashboard" class="<?php echo ($page ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-grid-2"></i> Dashboard Overview
        </a>

        <div class="sidebar-nav-label">Website Content</div>
        <a href="?page=custom_pages" class="<?php echo ($page ?? '') === 'custom_pages' ? 'active' : ''; ?>">
            <i class="fas fa-layer-group"></i> Manage Pages
        </a>
        <a href="?page=menu" class="<?php echo ($page ?? '') === 'menu' ? 'active' : ''; ?>">
            <i class="fas fa-sitemap"></i> Navigation Menu
        </a>
        <a href="?page=footer" class="<?php echo ($page ?? '') === 'footer' ? 'active' : ''; ?>">
            <i class="fas fa-shoe-prints"></i> Footer Builder
        </a>

        <div class="sidebar-nav-label">Module Editors</div>
        <a href="?page=hero" class="<?php echo ($page ?? '') === 'hero' ? 'active' : ''; ?>">
            <i class="fas fa-window-restore"></i> Hero Section
        </a>
        <a href="?page=services" class="<?php echo ($page ?? '') === 'services' ? 'active' : ''; ?>">
            <i class="fas fa-tools"></i> Services Catalog
        </a>
        <a href="?page=testimonials" class="<?php echo ($page ?? '') === 'testimonials' ? 'active' : ''; ?>">
            <i class="fas fa-comment-dots"></i> Client Reviews
        </a>
        <a href="?page=pages" class="<?php echo ($page ?? '') === 'pages' ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice"></i> Dynamic Sections
        </a>

        <div class="sidebar-nav-label">Settings & SEO</div>
        <a href="?page=seo" class="<?php echo ($page ?? '') === 'seo' ? 'active' : ''; ?>">
            <i class="fas fa-search-plus"></i> Search Engine (SEO)
        </a>
        <a href="?page=settings" class="<?php echo ($page ?? '') === 'settings' ? 'active' : ''; ?>">
            <i class="fas fa-sliders-h"></i> System Config
        </a>
        
        <hr class="mt-4">
        <a href="logout.php" class="text-danger mt-2">
            <i class="fas fa-power-off"></i> Sign Out
        </a>
    </nav>

    <div class="main-wrapper">
    <!-- Top Navbar -->
    <div class="top-navbar d-none d-lg-flex">
        <div class="d-flex align-items-center gap-3">
            <div class="search-box position-relative d-none d-xl-block">
                <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control ps-5 border-0 bg-light" placeholder="Search modules..." style="width: 300px; border-radius: 14px;">
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-2">
                <div class="badge-premium bg-success bg-opacity-10 text-success d-flex align-items-center gap-2">
                    <span class="pulse-green"></span>
                    Systems Operational
                </div>
            </div>
            <div class="vr bg-gray-300 mx-2" style="height: 30px; width: 1px;"></div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold small lh-1">Master Administrator</div>
                    <small class="text-muted extra-small">Full Access Control</small>
                </div>
                <div class="avatar-box">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&bold=true" class="rounded-circle shadow-sm" width="45" alt="Avatar">
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Offcanvas for Mobile -->
    <div class="offcanvas offcanvas-start bg-sidebar text-white" tabindex="-1" id="sidebarOffcanvas" style="background: var(--sidebar-bg);">
        <div class="offcanvas-header pt-4 px-4">
            <div class="sidebar-brand mb-0">
                <i class="fas fa-rocket"></i>
                <span>Boost Stride</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4 pt-0">
            <div class="sidebar p-0 position-relative w-100">
                <a href="?page=dashboard" class="<?php echo ($page ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="?page=hero" class="<?php echo ($page ?? '') === 'hero' ? 'active' : ''; ?>">
                    <i class="fas fa-display"></i> Hero Section
                </a>
                <a href="?page=services" class="<?php echo ($page ?? '') === 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Services
                </a>
                <a href="?page=testimonials" class="<?php echo ($page ?? '') === 'testimonials' ? 'active' : ''; ?>">
                    <i class="fas fa-quote-left"></i> Testimonials
                </a>
                <a href="?page=settings" class="<?php echo ($page ?? '') === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-sliders-h"></i> System Settings
                </a>
                <a href="?page=seo" class="<?php echo ($page ?? '') === 'seo' ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i> SEO & Meta
                </a>
                <a href="?page=footer" class="<?php echo ($page ?? '') === 'footer' ? 'active' : ''; ?>">
                    <i class="fas fa-shoe-prints"></i> Footer Section
                </a>
                <a href="?page=menu" class="<?php echo ($page ?? '') === 'menu' ? 'active' : ''; ?>">
                    <i class="fas fa-bars"></i> Navigation Menu
                </a>
                <a href="?page=pages" class="<?php echo ($page ?? '') === 'pages' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Page Content
                </a>
                <a href="?page=custom_pages" class="<?php echo ($page ?? '') === 'custom_pages' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i> Manage Pages
                </a>
                <hr>
                <a href="logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Slot -->
    <main class="main-content animate-fade-in">
        <!-- Page Header for Mobile -->
        <div class="mb-4 d-lg-none">
            <h3 class="fw-bold"><?php echo ucfirst($page ?? 'dashboard'); ?></h3>
            <p class="text-muted small">Welcome back to your command center.</p>
        </div>
        
        <!-- Dashboard Header for Desktop -->
        <div class="d-none d-lg-flex justify-content-between align-items-end mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 extra-small text-uppercase fw-bold letter-spacing-1">
                        <li class="breadcrumb-item"><a href="?page=dashboard" class="text-decoration-none text-muted">Admin</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page"><?php echo $page ?? 'Dashboard'; ?></li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0" style="letter-spacing: -1px;">
                    <?php 
                        $titles = [
                            'dashboard' => 'Network Overview',
                            'hero' => 'Visual Identity',
                            'services' => 'Service Pipeline',
                            'testimonials' => 'Social Reputation',
                            'settings' => 'System Infrastructure',
                            'seo' => 'Growth & Reach',
                            'footer' => 'Brand Conclusion',
                            'menu' => 'Architecture',
                            'pages' => 'Dynamic Assets',
                            'custom_pages' => 'Unified Directory'
                        ];
                        echo $titles[$page ?? 'dashboard'] ?? ucfirst($page);
                    ?>
                </h2>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-white border px-4 py-2 shadow-sm rounded-pill small fw-bold"><i class="fas fa-redo me-2"></i>Refresh</button>
                <button class="btn btn-dark px-4 py-2 shadow-sm rounded-pill small fw-bold"><i class="fas fa-plus me-2"></i>New Page</button>
            </div>
        </div>

        <style>
            .extra-small { font-size: 0.65rem; }
            .letter-spacing-1 { letter-spacing: 1.5px; }
            .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
            .pulse-green {
                width: 8px;
                height: 8px;
                background: #22c55e;
                border-radius: 50%;
                display: inline-block;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
            }
            .btn-white { background: white; color: #1e293b; }
            .btn-white:hover { background: #f8fafc; color: #6366f1; }
        </style>
