<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>NHAG Savings Platform</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

    <!-- Shared JS dependencies are loaded before page content because several views initialize tables/charts inline. -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #1abc9c;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
            color: #333;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background-color: var(--primary-color);
            color: white;
            min-height: calc(100vh - 56px);
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(0, 0, 0, 0.2);
            color: white;
            border-left-color: var(--secondary-color);
        }

        .main-content {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: calc(100vh - 56px);
        }

        .card {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .badge-primary {
            background-color: var(--secondary-color);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
        }

        .stat-card {
            border-left: 4px solid var(--secondary-color);
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .stat-card h5 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card h2 {
            color: var(--secondary-color);
            font-weight: bold;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo base_url('dashboard'); ?>">
                <i class="fas fa-piggy-bank"></i> NHAG Savings
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <?php
                    $header_notifications = array();
                    $header_unread_count = 0;
                    if ($this->session->userdata('user_id')) {
                        $this->db->where('user_id', $this->session->userdata('user_id'));
                        $this->db->where('is_read', 0);
                        $header_unread_count = $this->db->count_all_results('notifications');

                        $this->db->where('user_id', $this->session->userdata('user_id'));
                        $this->db->order_by('created_at', 'DESC');
                        $this->db->limit(5);
                        $header_notifications = $this->db->get('notifications')->result_array();
                    }
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-bell"></i> Notifications
                            <?php if ($header_unread_count > 0): ?>
                                <span class="notification-badge" data-notification-count><?php echo $header_unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 320px;">
                            <h6 class="dropdown-header">Recent Notifications</h6>
                            <?php if (!empty($header_notifications)): ?>
                                <?php foreach ($header_notifications as $notification): ?>
                                    <?php
                                    $notification_url = base_url('dashboard/notifications');
                                    if (($notification['related_entity_type'] ?? '') === 'member_profile_request' && in_array((int) $this->session->userdata('role_id'), array(1, 2))) {
                                        $notification_url = base_url('admin/member-requests/view/' . $notification['related_entity_id']);
                                    }
                                    ?>
                                    <a class="dropdown-item small <?php echo empty($notification['is_read']) ? 'font-weight-bold' : ''; ?>"
                                        href="<?php echo $notification_url; ?>">
                                        <div><?php echo html_escape($notification['title']); ?></div>
                                        <span class="text-muted"><?php echo html_escape(character_limiter($notification['message'], 70)); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="dropdown-item text-muted small">No notifications yet.</span>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?php echo base_url('dashboard/notifications'); ?>">
                                View notification center
                            </a>
                        </div>
                    </li>
                    <?php
                    $account_name = $this->session->userdata('name') ?: 'My Account';
                    $account_profile_url = base_url('dashboard');
                    if ($this->session->userdata('user_id')) {
                        $this->db->select('id');
                        $member_profile = $this->db->get_where('members', array('user_id' => $this->session->userdata('user_id')))->row_array();
                        if ($member_profile) {
                            $account_profile_url = base_url('members/view/' . $member_profile['id']);
                        } elseif (in_array((int) $this->session->userdata('role_id'), array(1, 2))) {
                            $account_profile_url = base_url('admin/users/edit/' . $this->session->userdata('user_id'));
                        }
                    }
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <?php echo html_escape($account_name); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo $account_profile_url; ?>">
                                <i class="fas fa-id-card"></i> View Profile
                            </a>
                            <?php if (!empty($member_profile)): ?>
                                <a class="dropdown-item" href="<?php echo base_url('statements'); ?>">
                                    <i class="fas fa-file-alt"></i> My Statements
                                </a>
                            <?php endif; ?>
                            <?php if (in_array((int) $this->session->userdata('role_id'), array(1, 2, 3))): ?>
                                <a class="dropdown-item" href="<?php echo base_url('data-entries'); ?>">
                                    <i class="fas fa-clipboard-list"></i> Data Entries
                                </a>
                            <?php endif; ?>
                            <?php if (in_array((int) $this->session->userdata('role_id'), array(1, 2))): ?>
                                <a class="dropdown-item" href="<?php echo base_url('admin'); ?>">
                                    <i class="fas fa-cog"></i> Administration
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo base_url('auth/logout'); ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <?php if (isset($show_sidebar) && $show_sidebar !== false): ?>
                <div class="col-md-2 sidebar">
                    <nav class="nav flex-column">
                        <!-- Dashboard -->
                        <a class="nav-link <?php echo (isset($current_page) && $current_page === 'dashboard') ? 'active' : ''; ?>"
                            href="<?php echo base_url('dashboard'); ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>

                        <?php
                        $role_id = (int) $this->session->userdata('role_id');
                        $is_super_admin = $role_id === 1;
                        $is_admin_user = in_array($role_id, array(1, 2));
                        $is_finance = $role_id === 3;
                        $is_member_user = $role_id === 4;
                        ?>

                        <?php if ($is_admin_user): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'members') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('members'); ?>">
                                <i class="fas fa-users"></i> Members
                            </a>
                        <?php elseif ($is_member_user && !empty($member_profile)): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'members') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('members/view/' . $member_profile['id']); ?>">
                                <i class="fas fa-id-card"></i> My Profile
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user || $is_finance): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'contributions') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('contributions'); ?>">
                                <i class="fas fa-handshake"></i> Contributions
                            </a>
                        <?php elseif ($is_member_user && !empty($member_profile)): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'contributions') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('contributions/submit'); ?>">
                                <i class="fas fa-handshake"></i> Submit Contribution
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user || $is_finance || ($is_member_user && !empty($member_profile))): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'ledger') !== false) ? 'active' : ''; ?>"
                                href="<?php echo ($is_admin_user || $is_finance) ? base_url('ledger') : base_url('ledger/member/' . $member_profile['id']); ?>">
                                <i class="fas fa-book"></i> <?php echo ($is_admin_user || $is_finance) ? 'Ledger' : 'My Ledger'; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user || $is_finance): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'data_entries') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('data-entries'); ?>">
                                <i class="fas fa-clipboard-list"></i> Data Entries
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user || $is_finance || ($is_member_user && !empty($member_profile))): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'statements') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('statements'); ?>">
                                <i class="fas fa-file-alt"></i> <?php echo ($is_admin_user || $is_finance) ? 'Statements' : 'My Statements'; ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user || $is_finance): ?>
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'reports') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('reports'); ?>">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        <?php endif; ?>

                        <?php if ($is_admin_user): ?>
                            <hr style="background-color: rgba(255,255,255,0.2); margin: 15px 0;">
                            <a class="nav-link <?php echo (isset($current_page) && strpos($current_page, 'admin') !== false) ? 'active' : ''; ?>"
                                href="<?php echo base_url('admin'); ?>">
                                <i class="fas fa-cog"></i> Administration
                            </a>
                            <a class="nav-link <?php echo (isset($current_page) && $current_page === 'admin_users') ? 'active' : ''; ?>" href="<?php echo base_url('admin/users'); ?>">
                                <i class="fas fa-user-tie"></i> Users
                            </a>
                            <?php if ($is_super_admin): ?>
                                <a class="nav-link <?php echo (isset($current_page) && $current_page === 'admin_roles') ? 'active' : ''; ?>" href="<?php echo base_url('admin/roles'); ?>">
                                    <i class="fas fa-shield-alt"></i> Roles
                                </a>
                                <a class="nav-link <?php echo (isset($current_page) && $current_page === 'admin_permissions') ? 'active' : ''; ?>" href="<?php echo base_url('admin/permissions'); ?>">
                                    <i class="fas fa-lock"></i> Permissions
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </nav>
                </div>

                <!-- Main Content -->
                <div class="col-md-10 main-content">
                <?php else: ?>
                    <div class="col-12 main-content" style="background-color: white;">
                    <?php endif; ?>


