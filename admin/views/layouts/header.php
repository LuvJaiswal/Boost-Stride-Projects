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
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --content-bg: #f8fafc;
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background: var(--content-bg);
            color: #1e293b;
        }

        /* Sidebar Styles */
        .sidebar { 
            width: var(--sidebar-width);
            min-height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            padding: 1.5rem;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem 1rem; 
            border-radius: 12px; 
            margin-bottom: 0.5rem; 
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar a:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: white; 
        }

        .sidebar a.active { 
            background: var(--primary-gradient);
            color: white; 
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .sidebar hr {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 1.5rem 0;
        }

        /* Main Content Adjustments */
        .main-content { 
            margin-left: var(--sidebar-width);
            padding: 2.5rem; 
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .top-navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 1.5rem;
            z-index: 900;
        }

        /* Cards and Components */
        .card { 
            border: none; 
            border-radius: 24px; 
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        /* Mobile specific enhancements */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); width: 280px; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1rem; }
            .top-navbar { border-radius: 0; margin: -1rem -1rem 1.5rem -1rem; top: 0; border-top: 0; border-left: 0; border-right: 0; padding: 1rem; }
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 18px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-gradient:hover {
            box-shadow: 0 12px 25px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: white;
        }

        .btn-gradient:active { transform: translateY(0); }

        .form-control, .form-select {
            padding: 0.8rem 1.2rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #fdfdfd;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            background: white;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
            outline: none;
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
            <i class="fas fa-rocket"></i>
            <span>Boost Stride</span>
        </div>
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
        <hr>
        <a href="logout.php" class="text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

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
                <hr>
                <a href="logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-navbar">
            <h4 class="mb-0 fw-bold">
                <?php 
                    echo ucfirst($page ?? 'dashboard'); 
                ?>
            </h4>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-bold">Administrator</div>
                    <small class="text-muted">High Access Level</small>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff" class="rounded-circle" width="45" alt="Avatar">
            </div>
        </div>
