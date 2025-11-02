<?php
require_once "HijriCalendar.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="robots" content="noindex">
        <meta charset="utf-8">
        <title>SEZ, Colombo Ziyarat Portal</title>
        <!-- Bootstrap 5.3.2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <!-- FontAwesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Bootstrap Datepicker (compatible with Bootstrap 5) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;1,400;1,600&display=swap" rel="stylesheet">
        <!-- Custom Theme -->
        <link type="text/css" href="<?php echo $server_link; ?>css/theme.css" rel="stylesheet" />
        
        <!-- Global Brown Theme CSS -->
        <style>
        /* Brown theme variables */
        :root {
            --brown-primary: #8B4513;
            --brown-secondary: #A0522D;
            --brown-light: #D2B48C;
            --brown-dark: #654321;
            --brown-accent: #DEB887;
        }

        /* Bootstrap 5 Navbar brown theme */
        .navbar.bg-brown {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.1rem;
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

        .btn-close:focus {
            box-shadow: 0 0 0 0.25rem rgba(139, 69, 19, 0.25);
        }

        /* Ensure proper spacing with Bootstrap 5 */
        .main-content {
            padding-top: 80px;
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
            border-radius: 8px;
            padding: 0.875rem 1.125rem;
            box-shadow: 0 2px 6px rgba(139, 69, 19, 0.08);
            border: 1px solid #e8e2db;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-weight: bold;
            color: #6c757d;
        }

        /* Module styling with proper brown theme */
        .module {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(139, 69, 19, 0.1);
            overflow: hidden;
            border: 1px solid #e8e2db;
            margin-bottom: 1.5rem;
        }

        .module-head {
            background: linear-gradient(135deg, var(--brown-primary) 0%, var(--brown-secondary) 100%) !important;
            color: white !important;
            padding: 1.25rem 1.75rem;
            margin: 0;
            border: none;
        }

        .module-head h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
            color: white !important;
        }

        .module-head h3 i {
            margin-right: 0.5rem;
            opacity: 0.9;
        }

        .module-body {
            padding: 1.5rem;
            background: #ffffff;
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

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .module-head {
                padding: 1rem 1.25rem;
            }
            
            .module-head h3 {
                font-size: 1.1rem;
            }
            
            .module-body {
                padding: 1rem;
            }
            
            .breadcrumb {
                padding: 0.75rem 1rem;
                margin-bottom: 1rem;
            }
        }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-brown fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo $server_link; ?>">
                    <img src="<?php echo $server_link; ?>images/shabab_logo_white.png" width="50" height="50" class="me-3" alt="SEZ Logo" />
                    SEZ, Colombo Ziyarat Portal
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://colombojamaat.org/files/ejamaat_photos/<?php echo $user_id; ?>.jpg" class="rounded-circle me-2" width="32" height="32" alt="User Avatar" />
                                <span>Account</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $server_link; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="wrapper" style="padding-top: 20px;">
            <div class="container-fluid">
                <div class="row">
                    <!--<div class="col-lg-3">
                        <div class="sidebar">
                            <ul class="list-unstyled widget widget-menu">
                                <li <?php if($view == 'home') echo 'class="active"'; ?>>
                                    <a href="<?php echo $server_link; ?>" class="nav-link"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                                </li>
                            </ul>
                            <div class="accordion" id="sidebarAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="safarHeading">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#safarCollapse" aria-expanded="true" aria-controls="safarCollapse">
                                            <i class="fas fa-cog me-2"></i>Safar
                                        </button>
                                    </h2>
                                    <div id="safarCollapse" class="accordion-collapse collapse show" aria-labelledby="safarHeading" data-bs-parent="#sidebarAccordion">
                                        <div class="accordion-body">
                                            <ul class="list-unstyled">
                                                <li <?php if($view == 'all-safars') echo 'class="active"'; ?>><a href="<?php echo $server_link;?>safars" class="nav-link"><i class="fas fa-list me-2"></i>All Safars</a></li>
                                                <li <?php if($view == 'active-safars') echo 'class="active"'; ?>><a href="<?php echo $server_link;?>active-safars" class="nav-link"><i class="fas fa-edit me-2"></i>Active Safars</a></li>
                                                <li <?php if($view == 'new-safar') echo 'class="active"'; ?>><a href="<?php echo $server_link;?>new-safar" class="nav-link"><i class="fas fa-plus me-2"></i>New Safar</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="col-12">
                        <div class="content py-4">
                            <?php include $content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="footer bg-light mt-auto py-3">
            <div class="container">
                <div class="text-center text-muted">
                    <strong>&copy; <?php echo date('Y'); ?> SEZ, Colombo Ziyarat Portal - Shababul Eidiz Zahabi, Colombo</strong> All rights reserved.
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
        <!-- Custom Scripts -->
        <script src="<?php echo $server_link; ?>scripts/common.js" type="text/javascript"></script>
        
      
    </body>
</html>