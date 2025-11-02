<?php
require_once "HijriCalendar.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="robots" content="noindex">
        <title>SEZ, Colombo Ziyarat Portal Admin Panel</title>
        
        <!-- Bootstrap 5.3.2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- FontAwesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- DataTables Bootstrap 5 -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <!-- Bootstrap Datepicker -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Ekko Lightbox -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" integrity="sha512-Velp0ebMKjcd9RiCoaHhLXkR1sFoCCWXNp6w4zj1hfMifYB5441C+sKeBl/T/Ka6NjBiRfBBQRaQq65ekYz3UQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Bootstrap 5 Compatible Lightbox CSS -->
        <style>
        .ekko-lightbox .modal-dialog {
            max-width: 90vw;
            max-height: 90vh;
        }
        .ekko-lightbox .modal-content {
            background: transparent;
            border: none;
        }
        .ekko-lightbox .modal-body {
            padding: 0;
            text-align: center;
        }
        .ekko-lightbox .modal-body img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }
        </style>
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">
        
        <!-- Global Admin Brown Theme CSS -->
        <style>
        /* Brown theme variables */
        :root {
            --brown-primary: #8B4513;
            --brown-secondary: #A0522D;
            --brown-light: #D2B48C;
            --brown-dark: #654321;
            --brown-accent: #DEB887;
        }

        /* Bootstrap 5 Navbar brown theme for admin */
        .navbar.bg-brown {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 0.95rem;
            color: white !important;
        }

        .navbar-brand:hover {
            color: var(--brown-light) !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: var(--brown-light) !important;
        }

        .dropdown-menu {
            border: 1px solid var(--brown-light);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        }

        .dropdown-item:hover {
            background-color: var(--brown-light);
            color: var(--brown-dark);
        }

        /* Global brown theme classes */
        .bg-brown {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
        }

        .bg-brown-light {
            background: linear-gradient(90deg, var(--brown-light), var(--brown-accent)) !important;
        }

        .btn-brown {
            background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary));
            border-color: var(--brown-primary);
            color: white;
            font-weight: 600;
        }

        .btn-brown:hover {
            background: linear-gradient(135deg, var(--brown-dark), var(--brown-primary));
            border-color: var(--brown-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
        }

        .text-brown {
            color: var(--brown-primary) !important;
        }

        .text-brown:hover {
            color: var(--brown-dark) !important;
            text-decoration: none;
        }

        /* Modern breadcrumb styling */
        .breadcrumb {
            background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            box-shadow: 0 2px 6px rgba(139, 69, 19, 0.08);
            border: 1px solid #e8e2db;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-weight: bold;
            color: #6c757d;
        }

        /* Module styling with proper brown theme */
        .module {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(139, 69, 19, 0.08);
            overflow: hidden;
            border: 1px solid #e8e2db;
            margin-bottom: 1rem;
        }

        .module-head {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
            color: white !important;
            padding: 0.75rem 1rem;
            margin: 0;
            border: none;
        }

        .module-head h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: white !important;
        }

        .module-head h3 i {
            margin-right: 0.4rem;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .module-body {
            padding: 1rem;
            background: #ffffff;
        }

        /* Admin sidebar styling */
        .admin-sidebar {
            background: linear-gradient(135deg, #faf9f7 0%, #f5f1eb 100%);
            border-radius: 8px;
            border: 1px solid #e8e2db;
            box-shadow: 0 2px 8px rgba(139, 69, 19, 0.08);
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .admin-sidebar .list-group-item {
            background: transparent;
            border: none;
            border-bottom: 1px solid #e8e2db;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .admin-sidebar .list-group-item:last-child {
            border-bottom: none;
        }

        .admin-sidebar .list-group-item.active {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%);
            color: white;
            border-color: var(--brown-primary);
        }

        .admin-sidebar .list-group-item:hover:not(.active) {
            background: var(--brown-light);
            color: var(--brown-dark);
        }

        .admin-sidebar .list-group-item a {
            color: inherit;
            text-decoration: none;
            display: block;
            width: 100%;
        }

        .admin-sidebar .list-group-item i {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
            font-size: 0.8rem;
        }

        /* Enhanced alert styling */
        .alert {
            border: none;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Main content area */
        .admin-content {
            padding-top: 70px;
            min-height: calc(100vh - 100px);
        }

        /* Footer styling */
        .admin-footer {
            background: linear-gradient(135deg, var(--brown-dark) 0%, var(--brown-primary) 100%);
            color: white;
            padding: 1rem 0;
            margin-top: auto;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .module-head {
                padding: 0.6rem 0.8rem;
            }
            
            .module-head h3 {
                font-size: 0.9rem;
            }
            
            .module-body {
                padding: 0.8rem;
            }
            
            .breadcrumb {
                padding: 0.5rem 0.75rem;
                margin-bottom: 0.75rem;
                font-size: 0.8rem;
            }
        }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <!-- Admin Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-brown fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo $server_link; ?>">
                    <img src="<?php echo $server_link; ?>images/shabab_logo_white.png" width="40" height="40" class="me-3" alt="SEZ Logo" />
                    <span class="d-none d-md-inline">SEZ Admin Panel</span>
                    <span class="d-md-none">Admin</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $server_link; ?>../">
                                <i class="fas fa-external-link-alt me-1"></i>User Portal
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2" style="font-size: 1.5rem;"></i>
                                <span class="d-none d-md-inline"><?php echo isset($user_name) ? $user_name : 'Admin'; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header"><?php echo isset($user_designation) ? $user_designation : 'Administrator'; ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $server_link; ?>../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="admin-content flex-grow-1">
            <div class="container-fluid">
                <div class="row">
                    <!-- Admin Sidebar -->
                    <div class="col-lg-2 col-md-3 d-none d-md-block">
                        <div class="admin-sidebar">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item <?php if($view == 'home') echo 'active'; ?>">
                                    <a href="<?php echo $server_link; ?>">
                                        <i class="fas fa-tachometer-alt"></i>Dashboard
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Safar Management Section -->
                            <div class="accordion" id="adminAccordion">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header" id="safarHeading">
                                        <button class="accordion-button collapsed bg-transparent border-0 text-brown fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#safarCollapse" aria-expanded="false" aria-controls="safarCollapse">
                                            <i class="fas fa-cog me-2"></i>Safar Management
                                        </button>
                                    </h2>
                                    <div id="safarCollapse" class="accordion-collapse collapse" aria-labelledby="safarHeading" data-bs-parent="#adminAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="list-group list-group-flush">
                                                <div class="list-group-item <?php if($view == 'all-safars') echo 'active'; ?>">
                                                    <a href="<?php echo $server_link;?>safars">
                                                        <i class="fas fa-list"></i>All Safars
                                                    </a>
                                                </div>
                                                <div class="list-group-item <?php if($view == 'active-safars') echo 'active'; ?>">
                                                    <a href="<?php echo $server_link;?>active-safars">
                                                        <i class="fas fa-edit"></i>Running Safars
                                                    </a>
                                                </div>
                                                <div class="list-group-item <?php if($view == 'new-safar') echo 'active'; ?>">
                                                    <a href="<?php echo $server_link;?>new-safar">
                                                        <i class="fas fa-plus"></i>New Safar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content Area -->
                    <div class="col-lg-10 col-md-9 col-12">
                        <div class="content py-4">
                            <?php include $content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Admin Footer -->
        <footer class="admin-footer mt-auto">
            <div class="container-fluid">
                <div class="text-center">
                    <strong>&copy; <?php echo date('Y'); ?> SEZ Admin Panel - Shababul Eidiz Zahabi, Colombo</strong> 
                    <span class="d-none d-md-inline">| All rights reserved.</span>
                </div>
            </div>
        </footer>
        
        <!-- Scripts -->
        <!-- jQuery 3.6.0 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- jQuery UI (updated) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Bootstrap 5.3.2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <!-- DataTables (Bootstrap 5 compatible) -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <!-- Bootstrap Datepicker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Chart.js (replacement for Flot) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Ekko Lightbox -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js" integrity="sha512-Y2IiVZeaBwXG1wSV7f13plqlmFOx8MdjuHyYFVoYzhyRr3nH/NMDjTBSswijzADdNzMyWNetbLMp6j6jPSe+SQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Custom Admin Scripts -->
        <script src="<?php echo $server_link; ?>scripts/common.js" type="text/javascript"></script>
        
        <!-- Mobile View Debug CSS -->
        <style>
        @media (max-width: 991.98px) {
            .d-lg-none {
                display: block !important;
            }
            .d-none.d-lg-block {
                display: none !important;
            }
        }
        @media (min-width: 992px) {
            .d-lg-none {
                display: none !important;
            }
            .d-none.d-lg-block {
                display: block !important;
            }
        }
        </style>
    </body>
</html>