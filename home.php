<?php
//NEW SAFAR SCRIPT

?>
<!-- Modern breadcrumb navigation -->
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-light">
    <li class="breadcrumb-item"><a href="<?php echo $server_link; ?>" class="text-brown"><i class="icon-home"></i> Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Active Safars</li>
  </ol>
</nav>
<div class="module">
    <div class="module-head">
        <h3><i class="fas fa-calendar-alt"></i> Ongoing Safars</h3>
    </div>
    <div class="module-body">
        <!-- Table wrapper for horizontal scroll -->
        <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>
                        Title
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql_allsafars = "SELECT * FROM zyt_safar 
                                WHERE safar_status IN ('active','planning')
                                ORDER BY FIELD(safar_status, 'active', 'planning', 'paused', 'completed', 'cancelled'), safar_status";
                $result_allsafars = dbQuery($sql_allsafars);
                while($row_safar = dbFetchAssoc($result_allsafars)){
                    extract($row_safar);
                ?>
                <tr class="">
                    <td class="safar-title">
                        <?php if($safar_type == 'kun'){
                        ?>
                        <a href="<?php echo $server_link . "safars/kun-safar/$safar_id"; ?>"><?php echo strtoupper($safar_type) . " - $safar_name"; ?></a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        </div> <!-- End table-responsive -->
    </div>
</div>

<!-- Enhanced Bootstrap 5 Styling for home.php -->
<style>
/* Brown theme variables */
:root {
    --brown-primary: #8B4513;
    --brown-secondary: #A0522D;
    --brown-light: #D2B48C;
    --brown-dark: #654321;
    --brown-accent: #DEB887;
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

.text-brown {
    color: var(--brown-primary) !important;
    text-decoration: none;
}

.text-brown:hover {
    color: var(--brown-dark) !important;
    text-decoration: none;
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

/* Table responsive wrapper */
.table-responsive {
    border-radius: 8px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 8px rgba(139, 69, 19, 0.08);
}

/* Enhanced table styling */
.table {
    margin-bottom: 0;
    font-size: 0.95rem;
    /* min-width: 600px; */
}

.table th {
    background: linear-gradient(135deg, var(--brown-light) 0%, var(--brown-accent) 100%);
    color: var(--brown-dark);
    font-weight: 700;
    border: none;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    vertical-align: middle;
    border-color: #e8e2db;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #faf9f7;
}

.table-striped tbody tr:hover {
    background-color: #f5f1eb;
    transform: scale(1.002);
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(139, 69, 19, 0.1);
}

/* Links styling */
.table a {
    color: var(--brown-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.table a:hover {
    color: white;
    background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary));
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
}

/* Safar title styling */
.safar-title {
    text-align: center;
    font-size: 12px !important;
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
    
    .table {
        font-size: 0.9rem;
        /* min-width: 500px; */
    }
    
    .table th,
    .table td {
        padding: 0.6rem 0.75rem;
    }
    
    .breadcrumb {
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }
    
    .table a {
        font-size: 12px !important;
        /* padding: 0.4rem 0.8rem; */
    }
}

@media (max-width: 576px) {
    .table {
        /* min-width: 400px; */
    }
    
    .module-head h3 {
        font-size: 1rem;
    }
    
    .table th,
    .table td {
        /* padding: 0.625rem 0.75rem; */
    }
}

/* DataTables compatibility */
.dataTables_wrapper {
    padding: 0;
}

.dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_filter input {
    border: 2px solid #e8e2db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    margin-left: 0.5rem;
}

.dataTables_filter input:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    outline: none;
}

.dataTables_length select {
    border: 2px solid #e8e2db;
    border-radius: 6px;
    padding: 0.375rem 0.75rem;
    margin: 0 0.5rem;
}

.dataTables_length select:focus {
    border-color: var(--brown-primary);
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    outline: none;
}

.dataTables_info,
.dataTables_length {
    color: var(--brown-dark);
    font-weight: 500;
    margin-bottom: 1rem;
}

.dataTables_paginate .paginate_button {
    background: linear-gradient(135deg, var(--brown-light), var(--brown-accent));
    border: 1px solid var(--brown-primary);
    color: var(--brown-dark) !important;
    border-radius: 6px;
    margin: 0 2px;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
}

.dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary)) !important;
    color: white !important;
    border-color: var(--brown-dark);
}

.dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, var(--brown-primary), var(--brown-secondary)) !important;
    color: white !important;
    border-color: var(--brown-dark);
}

/* Print styles */
@media print {
    .breadcrumb,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none;
    }
    
    .module {
        box-shadow: none;
        border: 1px solid #000;
        break-inside: avoid;
    }
    
    .module-head {
        background: #f8f9fa !important;
        color: #000 !important;
    }
}
</style>