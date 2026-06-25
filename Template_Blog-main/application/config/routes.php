<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
$route['auth/login'] = 'auth/login';
$route['auth/register'] = 'auth/register';
$route['auth/logout'] = 'auth/logout';

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================
$route['dashboard'] = 'dashboard/index';
$route['dashboard/notifications'] = 'dashboard/notifications';
$route['dashboard/get-notifications'] = 'dashboard/get_notifications';
$route['dashboard/mark-notification-read'] = 'dashboard/mark_notification_read';

// ============================================================================
// MEMBER MANAGEMENT ROUTES
// ============================================================================
$route['members'] = 'members/index';
$route['members/view/(:num)'] = 'members/view/$1';
$route['members/create'] = 'members/create';
$route['members/edit/(:num)'] = 'members/edit/$1';
$route['members/suspend/(:num)'] = 'members/suspend/$1';
$route['members/activate/(:num)'] = 'members/activate/$1';
$route['members/search'] = 'members/search';
$route['member/request'] = 'member_requests/request';

// ============================================================================
// CONTRIBUTIONS ROUTES
// ============================================================================
$route['contributions'] = 'contributions/index';
$route['contributions/view/(:num)'] = 'contributions/view/$1';
$route['contributions/submit'] = 'contributions/submit';
$route['contributions/approve/(:num)'] = 'contributions/approve/$1';
$route['contributions/reject/(:num)'] = 'contributions/reject/$1';
$route['contributions/download-document/(:num)'] = 'contributions/download_document/$1';
$route['contributions/get-pending'] = 'contributions/get_pending';

// ============================================================================
// SAVINGS LEDGER ROUTES
// ============================================================================
$route['ledger'] = 'ledger/index';
$route['ledger/member/(:num)'] = 'ledger/member/$1';
$route['ledger/summary'] = 'ledger/summary';

// ============================================================================
// REGIONAL GROUP DATA ENTRY ROUTES
// ============================================================================
$route['data-entries'] = 'data_entries/index';
$route['data-entries/create'] = 'data_entries/create';
$route['data-entries/view/(:num)'] = 'data_entries/view/$1';

// ============================================================================
// STATEMENTS ROUTES
// ============================================================================
$route['statements'] = 'statements/index';
$route['statements/view/(:num)'] = 'statements/view/$1';
$route['statements/generate'] = 'statements/generate';
$route['statements/download-pdf/(:num)'] = 'statements/download_pdf/$1';

// ============================================================================
// REPORTS ROUTES
// ============================================================================
$route['reports'] = 'reports/index';
$route['reports/savings'] = 'reports/savings_report';
$route['reports/contributions'] = 'reports/contributions_report';
$route['reports/members'] = 'reports/members_report';
$route['reports/export/(:any)'] = 'reports/export/$1';

// ============================================================================
// ADMIN ROUTES
// ============================================================================
$route['admin'] = 'admin/index';
$route['admin/users'] = 'admin/users';
$route['admin/users/create'] = 'admin/create_user';
$route['admin/users/edit/(:num)'] = 'admin/edit_user/$1';
$route['admin/roles'] = 'admin/roles';
$route['admin/permissions'] = 'admin/permissions';
$route['admin/audit-logs'] = 'admin/audit_logs';
$route['admin/settings'] = 'admin/settings';
$route['admin/member-requests'] = 'member_requests/admin_index';
$route['admin/member-requests/view/(:num)'] = 'member_requests/view/$1';
$route['admin/member-requests/approve/(:num)'] = 'member_requests/approve/$1';
$route['admin/member-requests/reject/(:num)'] = 'member_requests/reject/$1';

// ============================================================================
// LEGACY BLOG ROUTES (Keep existing functionality)
// ============================================================================
$route['posts/index'] = 'posts/index';
$route['posts/create'] = 'posts/create';
$route['posts/update'] = 'posts/update';
$route['posts/(:any)'] = 'posts/view/$1';
$route['posts'] = 'posts/index';

$route['categories'] = 'categories/index';
$route['categories/create'] = 'categories/create';
$route['categories/posts/(:any)'] = 'categories/posts/$1';

// ============================================================================
// DEFAULT ROUTES
// ============================================================================
$route['default_controller'] = 'auth/login';

$route['(:any)'] = 'pages/view/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

