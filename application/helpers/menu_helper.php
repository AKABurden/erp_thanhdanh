<?php

defined('BASEPATH') or exit('No direct script access allowed');

function app_init_admin_sidebar_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('dashboard', [
        'name'     => _l('als_dashboard'),
        'href'     => admin_url(),
        'position' => 1,
        'icon'     => 'fa fa-home',
    ]);


    $option_menu_active = json_decode(get_option('aside_menu_active'));
    foreach ($option_menu_active as $key => $value) {
        if (has_permission($key, '', 'view')) {
            $CI->app_menu->add_sidebar_menu_item($key, [
                'name'     => _l($key),
                'href'     => admin_url($value->url),
                'position' => $value->position,
                'icon'     => $value->icon,
            ]);
        }
        if (!empty($value->children)) {
            foreach ($value->children as $_key => $_value) {
                $CI->app_menu->add_sidebar_children_item($key, [
                    'slug'     => $_key,
                    'name'     => _l($_key),
                    'href'     => admin_url($_value->url),
                    'position' => $_value->position,
                    'icon'     => $_value->icon,
                ]);
                if (!empty($_value->children)) {
                    foreach ($_value->children as $__key => $__value) {
                        $CI->app_menu->add_sidebar_children_item($_key, [
                            'slug'     => $__key,
                            'name'     => _l($__key),
                            'href'     => admin_url($__value->url),
                            'position' => $__value->position,
                            'icon'     => $__value->icon,
                        ]);
                    }
                }
            }
        }
    }
    /*
 *
 * BACKUP MENU MẶC ĐỊNH
 *
 */



    //    if (has_permission('customers', '', 'view')
    //        || (have_assigned_customers()
    //        || (!have_assigned_customers() && has_permission('customers', '', 'create')))) {
    //        $CI->app_menu->add_sidebar_menu_item('customers', [
    //            'name'     => _l('als_clients'),
    //            'href'     => admin_url('clients'),
    //            'position' => 5,
    //            'icon'     => 'fa fa-user-o',
    //        ]);
    //    }
    //
    //    $CI->app_menu->add_sidebar_menu_item('sales', [
    //            'collapse' => true,
    //            'name'     => _l('als_sales'),
    //            'position' => 10,
    //            'icon'     => 'fa fa-balance-scale',
    //        ]);
    //
    //    if ((has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own'))
    //        || (staff_has_assigned_proposals() && get_option('allow_staff_view_proposals_assigned') == 1)) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'proposals',
    //                'name'     => _l('proposals'),
    //                'href'     => admin_url('proposals'),
    //                'position' => 5,
    //        ]);
    //    }
    //
    //    if ((has_permission('estimates', '', 'view') || has_permission('estimates', '', 'view_own'))
    //        || (staff_has_assigned_estimates() && get_option('allow_staff_view_estimates_assigned') == 1)) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'estimates',
    //                'name'     => _l('estimates'),
    //                'href'     => admin_url('estimates'),
    //                'position' => 10,
    //        ]);
    //    }
    //
    //    if ((has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own'))
    //         || (staff_has_assigned_invoices() && get_option('allow_staff_view_invoices_assigned') == 1)) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'invoices',
    //                'name'     => _l('invoices'),
    //                'href'     => admin_url('invoices'),
    //                'position' => 15,
    //        ]);
    //    }
    //
    //    if (has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')
    //           || (get_option('allow_staff_view_invoices_assigned') == 1 && staff_has_assigned_invoices())) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'payments',
    //                'name'     => _l('payments'),
    //                'href'     => admin_url('payments'),
    //                'position' => 20,
    //        ]);
    //    }
    //
    //    if (has_permission('credit_notes', '', 'view') || has_permission('credit_notes', '', 'view_own')) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'credit_notes',
    //                'name'     => _l('credit_notes'),
    //                'href'     => admin_url('credit_notes'),
    //                'position' => 25,
    //        ]);
    //    }
    //
    //    if (has_permission('items', '', 'view')) {
    //        $CI->app_menu->add_sidebar_children_item('sales', [
    //                'slug'     => 'items',
    //                'name'     => _l('items'),
    //                'href'     => admin_url('invoice_items'),
    //                'position' => 30,
    //        ]);
    //    }
    //
    //    if (has_permission('subscriptions', '', 'view') || has_permission('subscriptions', '', 'view_own')) {
    //        $CI->app_menu->add_sidebar_menu_item('subscriptions', [
    //                'name'     => _l('subscriptions'),
    //                'href'     => admin_url('subscriptions'),
    //                'icon'     => 'fa fa-repeat',
    //                'position' => 15,
    //        ]);
    //    }
    //
    //    if (has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')) {
    //        $CI->app_menu->add_sidebar_menu_item('expenses', [
    //                'name'     => _l('expenses'),
    //                'href'     => admin_url('expenses'),
    //                'icon'     => 'fa fa-file-text-o',
    //                'position' => 20,
    //        ]);
    //    }
    //
    //    if (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')) {
    //        $CI->app_menu->add_sidebar_menu_item('contracts', [
    //                'name'     => _l('contracts'),
    //                'href'     => admin_url('contracts'),
    //                'icon'     => 'fa fa-file',
    //                'position' => 25,
    //        ]);
    //    }
    //
    //    $CI->app_menu->add_sidebar_menu_item('projects', [
    //                'name'     => _l('projects'),
    //                'href'     => admin_url('projects'),
    //                'icon'     => 'fa fa-bars',
    //                'position' => 30,
    //        ]);
    //
    //    $CI->app_menu->add_sidebar_menu_item('tasks', [
    //                'name'     => _l('als_tasks'),
    //                'href'     => admin_url('tasks'),
    //                'icon'     => 'fa fa-tasks',
    //                'position' => 35,
    //        ]);
    //
    //    if ((!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1) || is_staff_member()) {
    //        $CI->app_menu->add_sidebar_menu_item('support', [
    //                'name'     => _l('support'),
    //                'href'     => admin_url('tickets'),
    //                'icon'     => 'fa fa-ticket',
    //                'position' => 40,
    //        ]);
    //    }
    //
    //    if (is_staff_member()) {
    //        $CI->app_menu->add_sidebar_menu_item('leads', [
    //                'name'     => _l('als_leads'),
    //                'href'     => admin_url('leads'),
    //                'icon'     => 'fa fa-tty',
    //                'position' => 45,
    //        ]);
    //    }
    //
    //    if (has_permission('knowledge_base', '', 'view')) {
    //        $CI->app_menu->add_sidebar_menu_item('knowledge-base', [
    //                'name'     => _l('als_kb'),
    //                'href'     => admin_url('knowledge_base'),
    //                'icon'     => 'fa fa-folder-open-o',
    //                'position' => 50,
    //        ]);
    //    }
    //
    //    // Utilities
    //    $CI->app_menu->add_sidebar_menu_item('utilities', [
    //            'collapse' => true,
    //            'name'     => _l('als_utilities'),
    //            'position' => 55,
    //            'icon'     => 'fa fa-cogs',
    //        ]);
    //
    //    $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'media',
    //                'name'     => _l('als_media'),
    //                'href'     => admin_url('utilities/media'),
    //                'position' => 5,
    //        ]);
    //
    //    if (has_permission('bulk_pdf_exporter', '', 'view')) {
    //        $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'bulk-pdf-exporter',
    //                'name'     => _l('bulk_pdf_exporter'),
    //                'href'     => admin_url('utilities/bulk_pdf_exporter'),
    //                'position' => 10,
    //        ]);
    //    }
    //
    //    $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'calendar',
    //                'name'     => _l('als_calendar_submenu'),
    //                'href'     => admin_url('utilities/calendar'),
    //                'position' => 15,
    //        ]);
    //
    //
    //    if (is_admin()) {
    //        $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'announcements',
    //                'name'     => _l('als_announcements_submenu'),
    //                'href'     => admin_url('announcements'),
    //                'position' => 20,
    //        ]);
    //
    //        $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'activity-log',
    //                'name'     => _l('als_activity_log_submenu'),
    //                'href'     => admin_url('utilities/activity_log'),
    //                'position' => 25,
    //        ]);
    //
    //        $CI->app_menu->add_sidebar_children_item('utilities', [
    //                'slug'     => 'ticket-pipe-log',
    //                'name'     => _l('ticket_pipe_log'),
    //                'href'     => admin_url('utilities/pipe_log'),
    //                'position' => 30,
    //        ]);
    //    }
    //
    //    if (has_permission('reports', '', 'view')) {
    //        $CI->app_menu->add_sidebar_menu_item('reports', [
    //                'collapse' => true,
    //                'name'     => _l('als_reports'),
    //                'href'     => admin_url('reports'),
    //                'icon'     => 'fa fa-area-chart',
    //                'position' => 60,
    //        ]);
    //        $CI->app_menu->add_sidebar_children_item('reports', [
    //                'slug'     => 'sales-reports',
    //                'name'     => _l('als_reports_sales_submenu'),
    //                'href'     => admin_url('reports/sales'),
    //                'position' => 5,
    //        ]);
    //        $CI->app_menu->add_sidebar_children_item('reports', [
    //                'slug'     => 'expenses-reports',
    //                'name'     => _l('als_reports_expenses'),
    //                'href'     => admin_url('reports/expenses'),
    //                'position' => 10,
    //        ]);
    //        $CI->app_menu->add_sidebar_children_item('reports', [
    //                'slug'     => 'expenses-vs-income-reports',
    //                'name'     => _l('als_expenses_vs_income'),
    //                'href'     => admin_url('reports/expenses_vs_income'),
    //                'position' => 15,
    //        ]);
    //        $CI->app_menu->add_sidebar_children_item('reports', [
    //                'slug'     => 'leads-reports',
    //                'name'     => _l('als_reports_leads_submenu'),
    //                'href'     => admin_url('reports/leads'),
    //                'position' => 20,
    //        ]);
    //
    //        if (is_admin()) {
    //            $CI->app_menu->add_sidebar_children_item('reports', [
    //                    'slug'     => 'timesheets-reports',
    //                    'name'     => _l('timesheets_overview'),
    //                    'href'     => admin_url('staff/timesheets?view=all'),
    //                    'position' => 25,
    //            ]);
    //        }
    //
    //        $CI->app_menu->add_sidebar_children_item('reports', [
    //                    'slug'     => 'knowledge-base-reports',
    //                    'name'     => _l('als_kb_articles_submenu'),
    //                    'href'     => admin_url('reports/knowledge_base_articles'),
    //                    'position' => 30,
    //            ]);
    //    }
    //
    //    // Setup menu
    //    if (has_permission('staff', '', 'view')) {
    //        $CI->app_menu->add_setup_menu_item('staff', [
    //                    'name'     => _l('als_staff'),
    //                    'href'     => admin_url('staff'),
    //                    'position' => 5,
    //            ]);
    //    }
    //
    //    if (is_admin()) {
    //        $CI->app_menu->add_setup_menu_item('customers', [
    //                    'collapse' => true,
    //                    'name'     => _l('clients'),
    //                    'position' => 10,
    //            ]);
    //
    //        $CI->app_menu->add_setup_children_item('customers', [
    //                    'slug'     => 'customer-groups',
    //                    'name'     => _l('customer_groups'),
    //                    'href'     => admin_url('clients/groups'),
    //                    'position' => 5,
    //            ]);
    //        $CI->app_menu->add_setup_menu_item('support', [
    //                    'collapse' => true,
    //                    'name'     => _l('support'),
    //                    'position' => 15,
    //            ]);
    //
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'departments',
    //                    'name'     => _l('acs_departments'),
    //                    'href'     => admin_url('departments'),
    //                    'position' => 5,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'tickets-predefined-replies',
    //                    'name'     => _l('acs_ticket_predefined_replies_submenu'),
    //                    'href'     => admin_url('tickets/predefined_replies'),
    //                    'position' => 10,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'tickets-priorities',
    //                    'name'     => _l('acs_ticket_priority_submenu'),
    //                    'href'     => admin_url('tickets/priorities'),
    //                    'position' => 15,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'tickets-statuses',
    //                    'name'     => _l('acs_ticket_statuses_submenu'),
    //                    'href'     => admin_url('tickets/statuses'),
    //                    'position' => 20,
    //            ]);
    //
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'tickets-services',
    //                    'name'     => _l('acs_ticket_services_submenu'),
    //                    'href'     => admin_url('tickets/services'),
    //                    'position' => 25,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('support', [
    //                    'slug'     => 'tickets-spam-filters',
    //                    'name'     => _l('spam_filters'),
    //                    'href'     => admin_url('spam_filters/view/tickets'),
    //                    'position' => 30,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('leads', [
    //                    'collapse' => true,
    //                    'name'     => _l('acs_leads'),
    //                    'position' => 20,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('leads', [
    //                    'slug'     => 'leads-sources',
    //                    'name'     => _l('acs_leads_sources_submenu'),
    //                    'href'     => admin_url('leads/sources'),
    //                    'position' => 5,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('leads', [
    //                    'slug'     => 'leads-statuses',
    //                    'name'     => _l('acs_leads_statuses_submenu'),
    //                    'href'     => admin_url('leads/statuses'),
    //                    'position' => 10,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('leads', [
    //                    'slug'     => 'leads-email-integration',
    //                    'name'     => _l('leads_email_integration'),
    //                    'href'     => admin_url('leads/email_integration'),
    //                    'position' => 15,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('leads', [
    //                    'slug'     => 'web-to-lead',
    //                    'name'     => _l('web_to_lead'),
    //                    'href'     => admin_url('leads/forms'),
    //                    'position' => 20,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('finance', [
    //                    'collapse' => true,
    //                    'name'     => _l('acs_finance'),
    //                    'position' => 25,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('finance', [
    //                    'slug'     => 'taxes',
    //                    'name'     => _l('acs_sales_taxes_submenu'),
    //                    'href'     => admin_url('taxes'),
    //                    'position' => 5,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('finance', [
    //                    'slug'     => 'currencies',
    //                    'name'     => _l('acs_sales_currencies_submenu'),
    //                    'href'     => admin_url('currencies'),
    //                    'position' => 10,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('finance', [
    //                    'slug'     => 'payment-modes',
    //                    'name'     => _l('acs_sales_payment_modes_submenu'),
    //                    'href'     => admin_url('paymentmodes'),
    //                    'position' => 15,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('finance', [
    //                    'slug'     => 'expenses-categories',
    //                    'name'     => _l('acs_expense_categories'),
    //                    'href'     => admin_url('expenses/categories'),
    //                    'position' => 20,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('contracts', [
    //                    'collapse' => true,
    //                    'name'     => _l('acs_contracts'),
    //                    'position' => 30,
    //            ]);
    //        $CI->app_menu->add_setup_children_item('contracts', [
    //                    'slug'     => 'contracts-types',
    //                    'name'     => _l('acs_contract_types'),
    //                    'href'     => admin_url('contracts/types'),
    //                    'position' => 5,
    //            ]);
    //
    //        $modules_name = _l('modules');
    //
    //        if ($modulesNeedsUpgrade = $CI->app_modules->number_of_modules_that_require_database_upgrade()) {
    //            $modules_name .= '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
    //        }
    //
    //        $CI->app_menu->add_setup_menu_item('modules', [
    //                    'href'     => admin_url('modules'),
    //                    'name'     => $modules_name,
    //                    'position' => 35,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('custom-fields', [
    //                    'href'     => admin_url('custom_fields'),
    //                    'name'     => _l('asc_custom_fields'),
    //                    'position' => 45,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('gdpr', [
    //                    'href'     => admin_url('gdpr'),
    //                    'name'     => _l('gdpr_short'),
    //                    'position' => 50,
    //            ]);
    //
    //        $CI->app_menu->add_setup_menu_item('roles', [
    //                    'href'     => admin_url('roles'),
    //                    'name'     => _l('acs_roles'),
    //                    'position' => 55,
    //            ]);
    //
    ///*             $CI->app_menu->add_setup_menu_item('api', [
    //                          'href'     => admin_url('api'),
    //                          'name'     => 'API',
    //                          'position' => 65,
    //                  ]);*/
    //
    //    }
    //
    //    if (has_permission('settings', '', 'view')) {
    //        $CI->app_menu->add_setup_menu_item('settings', [
    //                    'href'     => admin_url('settings'),
    //                    'name'     => _l('acs_settings'),
    //                    'position' => 200,
    //            ]);
    //    }
    //
    //    if (has_permission('email_templates', '', 'view')) {
    //        $CI->app_menu->add_setup_menu_item('email-templates', [
    //                    'href'     => admin_url('emails'),
    //                    'name'     => _l('acs_email_templates'),
    //                    'position' => 40,
    //            ]);
    //    }

    /*
     *
     * END  BACKUP MENU MẶC ĐỊNH
     *
     */
}

if (!function_exists('checkPermission')) {
    function checkPermission($permission, $staff_id, $is_admin, $role = '')
    {
        if (!$is_admin) {
            if (!empty($role)) {
                if (has_permission($permission, $staff_id, 'view') || has_permission(
                    $permission,
                    $staff_id,
                    'view_own'
                ) || has_permission(
                    $permission,
                    $staff_id,
                    '' . $role . ''
                )) {
                    return true;
                }
            } else {
                if (has_permission($permission, $staff_id, 'view') || has_permission(
                    $permission,
                    $staff_id,
                    'view_own'
                )) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }
}

if (!function_exists('getMenuDashboard1')) {
    function getMenuDashboard1()
    {
        $CI = &get_instance();
        $staff_id = get_staff_user_id();
        $is_admin = is_admin($staff_id);
        $menu = [];
        $menu['category']['name'] = lang('Hạng mục');

        $menu['category']['items']['created_group'] = [
            'name' => lang('I. Data - Danh Mục'),
            'sub_name' => lang('MASTER DATA'),
            'sub_menu_one' => [
                [
                    'key' => 'info_clients',
                    'name' => lang('Thông tin khách hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients__groups',
                            'is_permission' => checkPermission('groups', $staff_id, $is_admin),
                            'name' => lang('Nhóm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/status_client',
                            'key' => 'clients__status_client',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Phân Loại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients__clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/categories_other/standard_customer',
                            'key' => 'standard_customer',
                            'is_permission' => true,
                            'name' => lang('Tiêu Chuẩn Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Chiết Khấu'),
                        ],
                        [
                            'link' => 'admin/contracts_sales',
                            'key' => 'contracts_sales',
                            'is_permission' => checkPermission('contracts_sales', $staff_id, $is_admin),
                            'name' => lang('Hợp Đồng Bán'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Loại Hình Thuế'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('BG SP'),
                        ],
                    ],
                ],
                [
                    'key' => 'suppliers',
                    'name' => lang('Thông Tin Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('Nhóm NCC'),
                        ],
                        [
                            'link' => 'admin/supplier_classify',
                            'key' => 'suppliers',
                            'is_permission' => 1,
                            'name' => lang('Phân Loại NCC'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách NCC'),
                        ],
                        [
                            'link' => 'admin/suppliers/evaluation_criteria',
                            'key' => 'suppliers__evaluation_criteria',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Tiêu Chuẩn NCC'),
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('Chiết Khấu'),
                        ],
                        [
                            'link' => 'admin/contracts_supplier',
                            'key' => 'contracts_supplier',
                            'is_permission' => 1,
                            'name' => lang('Hợp Đồng Mua'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá NCC'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Loại Hình Thuế'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'staffs',
                    'name' => lang('Thông Tin Hành Chính Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Giấy Phép'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/kpi/detail_task',
                            'key' => 'detail_task',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Mô Tả Công Việc - Vị Trí'),
                        ],
                        [
                            'link' => 'admin/kpi/list',
                            'key' => 'kpi',
                            'is_permission' => checkPermission('kpi', $staff_id, $is_admin),
                            'name' => lang('KPI'),
                        ],
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chi Nhánh'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('Kho'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Công Việc'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('Bảng Lương'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=educate',
                            'key' => 'evaluate',
                            'is_permission' => 1,
                            'name' => lang('Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Đánh Giá'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Tuyển Dụng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('5S'),
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('Bảo Dưỡng'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('PCCC'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('Quyết Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Quy Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Quy Trình'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Hợp Đồng'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Phép'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Thiết Bị SX'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Thiết bị VP'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Khám Sức Khỏe'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Thiết bị Đo Kiểm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Hiệu Chuẩn'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phương Tiện'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Định mức(Năng Suất/Công Việc)'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Bảo Hiểm'),
                        ],
                    ],
                ],
                [
                    'key' => 'machines',
                    'name' => lang('Thông Tin Thiết Bị'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/category_machines',
                            'key' => 'category_machines',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Nhóm Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Mã Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Năng Suất Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Danh Sách Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Khấu Hao'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/tools_supplies/category',
                            'key' => 'tools_supplies__category',
                            'is_permission' => checkPermission('tools_supplies_category', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thay Thế'),
                        ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('Vật Tư Thay Thế'),
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'products',
                    'name' => lang('Thông Tin Sản Phẩm'),
                    'sub' => [
                        [
                            'link' => 'admin/products/category',
                            'key' => 'products__category',
                            'is_permission' => checkPermission('products_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Tính Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/category_stages',
                            'key' => 'category__stages',
                            'name' => lang('Nhóm công đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/bom',
                            'key' => 'products',
                            'is_permission' => checkPermission('products_bom', $staff_id, $is_admin),
                            'name' => lang('Định Mức BOM'),
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn Sản Phẩm'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('Thông Tin Tồn Kho'),
                        ],
                    ],
                ],
                [
                    'key' => 'items',
                    'name' => lang('Thông Tin Nguyên Vật Liệu'),
                    'sub' => [
                        [
                            'link' => 'admin/items/category',
                            'key' => 'items__category',
                            'is_permission' => checkPermission('items_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm Nguyên Vật Liệu'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_settings' => 1,
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại NPL'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Nguyên Vật Liệu'),
                        ],
                        [
                            'link' => 'admin/units?type_title=materials',
                            'key' => 'units',
                            'name' => lang('Đơn Vị Tính Nguyên Vật Liệu'),
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                        ],
                        // [
                        //     'link' => 'admin/units',
                        //     'key' => 'units',
                        //     'name' => lang('Đơn Vị Thanh Toán'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/units',
                        //     'key' => 'units',
                        //     'name' => lang('Đơn Vị Nhập Kho'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/units',
                        //     'key' => 'units',
                        //     'name' => lang('Đơn Vị Chuyển Đổi'),
                        //     'is_settings' => 1,
                        // ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước Nguyên Vật Liệu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('Thông Tin Tồn Kho'),
                        ],
                    ]
                ],
            ],
            'sub_menu_four' => [
                // [
                //     'key' => 'items',
                //     'name' => lang('Thông Tin Nguyên Vật Liệu'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/items/category',
                //             'key' => 'items__category',
                //             'name' => lang('Nhóm Nguyên Vật Liệu'),
                //         ],
                //         [
                //             'link' => 'admin/items',
                //             'key' => 'items',
                //             'name' => lang('Danh Sách Nguyên Vật Liệu'),
                //         ],
                //     ]
                // ],
                [
                    'key' => 'other',
                    'name' => lang('Thông tin khác'),
                    'sub' => [
                        // [
                        //     'link' => 'admin/leads/sources',
                        //     'key' => 'leads__sources',
                        //     'name' => lang('Nguồn Tiếp Cận'),
                        //     'is_settings' => 1,
                        // ],
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin),
                            'name' => lang('Loại Bàn Giao'),
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin),
                            'name' => lang('Tiêu Chí Bàn Giao'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Loại Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Nhóm Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin),
                            'name' => lang('Phiếu Kiểm Kê'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Phiếu Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'name' => lang('Loại Hình In'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble',
                            'key' => 'trouble',
                            'name' => lang('Loại Sự Cố'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble/category_problem',
                            'key' => 'category_problem',
                            'is_permission' => true,
                            'name' => lang('Nhóm Sự Cố'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'name' => lang('Loại Chi Phí'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'is_permission' => true,
                            'name' => lang('Nhóm Chi Phí'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/allowance_reduce',
                            'key' => 'allowance_reduce',
                            'name' => lang('Phụ Cấp - Giảm Trừ'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/utilities/media',
                            'key' => 'utilities',
                            'name' => lang('Thư Viện'),
                            'is_permission' => true,
                            'is_settings' => 0,
                        ],
                        // [
                        //     'link' => 'admin/type_orders',
                        //     'key' => 'type_orders',
                        //     'name' => lang('Loại Đơn Hàng'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/species',
                        //     'key' => 'species',
                        //     'name' => lang('Chủng Loại'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/branch',
                        //     'key' => 'branch',
                        //     'name' => lang('Chi Nhánh Xưởng'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/products/colors',
                        //     'key' => 'products__colors',
                        //     'name' => lang('Màu Sắc'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/units',
                        //     'key' => 'units',
                        //     'name' => lang('Đơn Vị Tính'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/measurement',
                        //     'key' => 'measurement',
                        //     'name' => lang('Kích Thước'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/categories/capacity',
                        //     'key' => 'categories__capacity',
                        //     'name' => lang('Danh Mục Lỗi'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories__machines',
                        //     'name' => lang('Máy Móc Thiết Bị'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/trouble',
                        //     'key' => 'trouble',
                        //     'name' => lang('Sự Cố'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/category_tasks',
                        //     'key' => 'category_tasks',
                        //     'name' => lang('Mã Công Việc'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/categories/packaging',
                        //     'key' => 'category_packaging',
                        //     'name' => lang('Tiêu Chuẩn'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/costs',
                        //     'key' => 'costs',
                        //     'name' => lang('Loại Chi Phí'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/categories/mode_materials',
                        //     'key' => 'categories__mode_materials',
                        //     'name' => lang('Quy Cách NPL'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/collect_categories',
                        //     'key' => 'collect_categories',
                        //     'name' => lang('Danh Mục Thu'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/print_type',
                        //     'key' => 'print_type',
                        //     'name' => lang('Loại Hình In'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/handover_item',
                        //     'key' => 'handover_item',
                        //     'name' => lang('Hạng Mục Bàn Giao'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/allowance_reduce',
                        //     'key' => 'allowance_reduce',
                        //     'name' => lang('Phụ Cấp - Giảm Trừ'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/recommended_list',
                        //     'key' => 'recommended_list',
                        //     'name' => lang('Danh Mục Đề Xuất'),
                        //     'is_settings' => 1,
                        // ],

                    ]
                ]
            ]
        ];

        $menu['category']['items']['crm'] = [
            'name' => lang('II. CRM - Quản Lý Khách Hàng'),
            'sub_name' => lang('Customer Relationship Management'),
            'sub_menu_one' => [
                [
                    'key' => 'cs',
                    'name' => lang('Customer Service - Chăm Sóc Khách Hàng'),
                    'sub' => [
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Cổng Thông Tin Khách Hàng'),
                        // ],
                        [
                            'link' => 'admin/coupon_support/customer_order',
                            'key' => 'coupon_support',
                            'is_permission' => true,
                            'name' => lang('Phiếu Chăm Sóc Khách Hàng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'customer',
                    'name' => lang('Khách hàng'),
                    'sub' => [
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Cổng Thông Tin Khách Hàng'),
                        // ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Công Đoạn Theo Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Sản Phẩm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Loại Khách Hàng'),
                        ],

                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Danh Sách Tiếu Chuẩn Chất Lượng'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Danh Sách Tiếu Chuẩn Đóng Gói'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Danh Sách Tiếu Chuẩn Giao Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=certification',
                        //     'key' => 'evaluate',
                        //     'name' => lang('Danh Sách Chứng Nhận'),
                        // ],
                        // [
                        //     'link' => 'admin/contracts_sales',
                        //     'key' => 'contracts_sales',
                        //     'name' => lang('Danh Sách Hợp Đồng'),
                        // ],
                        // [
                        //     'link' => 'admin/taxes',
                        //     'key' => 'taxes',
                        //     'name' => lang('Danh Sách Loại Thuế'),
                        // ],
                        // [
                        //     'link' => 'admin/quote_stage',
                        //     'key' => 'quote_stage',
                        //     'name' => lang('Danh Sách Bảng Giá Công Đoạn'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Danh Sách Chiết Khấu'),
                        // ],
                        // [
                        //     'link' => 'admin/import_price_group',
                        //     'key' => 'import_price_group',
                        //     'name' => lang('Danh Sách Bảng Giá KH'),
                        // ],
                        // [
                        //     'link' => 'admin/clients/status_client',
                        //     'key' => 'clients__status_client',
                        //     'name' => lang('Danh Sách Phân Loại KH'),
                        // ],
                    ],
                ],
            ]
        ];

        $menu['category']['items']['scc'] = [
            'name' => lang('III. SCC - Kiểm Soát Chuỗi Cung Ứng'),
            'sub_name' => lang('Supplier Chain Control'),
            'sub_menu_one' => [
                [
                    'key' => 'purchaser',
                    'name' => lang('PM (Purchasing Management)'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_evaluate?type=supplier',
                            'key' => 'supplier_evaluate?type=supplier',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Đánh Giá Nhà Cung Cấp'),
                        ],
                        // [
                        //     'link' => 'admin/purchases',
                        //     'key' => 'purchases',
                        //     'name' => lang('Yêu Cầu Mua Hàng (PR)'),
                        // ],
                        // [
                        //     'link' => 'admin/purchase_order',
                        //     'key' => 'purchase_order',
                        //     'name' => lang('Đơn Đặt Hàng (PO)'),
                        // ],
                        [
                            'link' => 'admin/service',
                            'key' => 'service',
                            'is_permission' => checkPermission('service', $staff_id, $is_admin),
                            'name' => lang('Đơn Đặt Dịch Vụ (SV)'),
                        ],
                        // [
                        //     'link' => 'admin/import',
                        //     'key' => 'import',
                        //     'name' => lang('Nhập Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/return_suppliers',
                        //     'key' => 'return_suppliers',
                        //     'name' => lang('Trả lại hàng mua'),
                        // ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                // [
                //     'key' => 'quality_evalution',
                //     'name' => lang('Đánh Giá Chất Lượng'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/suppliers/evaluation_criteria',
                //             'key' => 'suppliers__evaluation_criteria',
                //             'name' => lang('Tiêu Chí đánh giá NCC'),
                //         ],
                //         [
                //             'link' => 'admin/supplier_evaluate',
                //             'key' => 'supplier_evaluate',
                //             'name' => lang('Đánh Giá Chất Lượng NCC'),
                //         ],
                //         [
                //             'link' => 'admin/supplier_classify',
                //             'key' => 'supplier_classify',
                //             'name' => lang('Phân Loại NCC'),
                //         ],
                //     ],
                // ]
                [
                    'key' => 'suppliers',
                    'name' => lang('Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Loại Nhà Cung Cấp'),
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                // [
                //     'key' => 'price_list',
                //     'name' => lang('Quản Lý Bảng Giá - Chiết Khấu'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/import_price',
                //             'key' => 'import_price',
                //             'name' => lang('Bảng Giá - Nhà Cung Cấp'),
                //         ],
                //     ]
                // ]
            ]
        ];

        $menu['category']['items']['erp'] = [
            'name' => lang('IV. ERP - Hoạch Định Nguồn Lực DN'),
            'sub_name' => lang('Enterprise Resource Planning'),
            'is_not_click' => 1,
            // 'sub_menu_one' => [
            //     [
            //         'key' => 'office_management',
            //         'name' => lang('Office Management'),
            //         'sub' => [
            //             [
            //                 'link' => 'admin/quotes',
            //                 'key' => 'quotes',
            //                 'name' => lang('BGPTM (Phát Triển Mẫu)'),
            //             ],
            //             [
            //                 'link' => 'admin/quote_stage',
            //                 'key' => 'quote_stage',
            //                 'name' => lang('Bảng Giá Công Đoạn'),
            //             ],
            //             [
            //                 'link' => 'admin/quotes',
            //                 'key' => 'quotes',
            //                 'name' => lang('Bảng Báo Giá'),
            //             ],
            //         ],
            //     ]
            // ],
            // 'sub_menu_two' => [
            //     [
            //         'key' => 'orders_delivery',
            //         'name' => lang('Bán Hàng - Xuất Hàng'),
            //         'sub' => [
            //             [
            //                 'link' => 'admin/orders',
            //                 'key' => 'orders',
            //                 'name' => lang('Đơn Đặt Hàng'),
            //             ],
            //             [
            //                 'link' => 'admin/orders/information',
            //                 'key' => 'orders__information',
            //                 'name' => lang('Thống Kê Đơn Hàng'),
            //             ],
            //             [
            //                 'link' => 'admin/releases',
            //                 'key' => 'releases',
            //                 'name' => lang('Xuất Hàng'),
            //             ],
            //             [
            //                 'link' => 'admin/returned_goods',
            //                 'key' => 'returned_goods',
            //                 'name' => lang('Trả Lại Hàng Bán'),
            //             ],
            //             [
            //                 'link' => 'admin/contracts_sales',
            //                 'key' => 'contracts_sales',
            //                 'name' => lang('Hợp Đồng Bán'),
            //             ],
            //         ],
            //     ]
            // ],
        ];

        $menu['category']['items']['office_management'] = [
            'name' => lang('1. Office Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                // [
                //     'key' => 'bgptm',
                //     'name' => lang('BGPTM (Phát Triển Mẫu)'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/quote_stage',
                //             'key' => 'quote_stage',
                //             'name' => lang('Bảng Giá Công Đoạn'),
                //         ],
                //         [
                //             'link' => 'admin/quotes',
                //             'key' => 'quotes',
                //             'name' => lang('Bảng Báo Giá'),
                //         ],
                //     ],
                // ],
                // [
                //     'key' => 'orders_delivery',
                //     'name' => lang('Bán Hàng - Xuất Hàng'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/orders',
                //             'key' => 'orders',
                //             'name' => lang('Đơn Đặt Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/orders/information',
                //             'key' => 'orders__information',
                //             'name' => lang('Kế hoạch Đơn Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/releases',
                //             'key' => 'releases',
                //             'name' => lang('Xuất Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/returned_goods',
                //             'key' => 'returned_goods',
                //             'name' => lang('Trả Lại Hàng Bán'),
                //         ],
                //         [
                //             'link' => 'admin/contracts_sales',
                //             'key' => 'contracts_sales',
                //             'name' => lang('Hợp Đồng Bán'),
                //         ],
                //     ],
                // ]
                [
                    'key' => 'hr',
                    'name' => lang('HR(Human Resources)'),
                    'sub' => [
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Chi Nhánh'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/category_tasks',
                            'key' => 'category_tasks',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Danh Sách Mô Tả Vị Trí Công Việc'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Trang Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/kpi/category_kpi',
                            'key' => 'category_kpi',
                            'is_permission' => true,
                            'name' => lang('Danh Sách KPI'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Quyết Định'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Hợp Đồng'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Giấy Phép'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Chứng Nhận'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                // [
                //     'key' => 'sales_debt',
                //     'name' => lang('Công Nợ Bán'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/coupon_invoice',
                //             'key' => 'coupon_invoice',
                //             'name' => lang('Hoá Đơn Bán Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/vouchers_coupon',
                //             'key' => 'vouchers_coupon',
                //             'name' => lang('Phiếu Thu Bán Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/debt_clients',
                //             'key' => 'debt_clients',
                //             'name' => lang('Công Nợ Bán Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/other_payslips_coupon',
                //             'key' => 'other_payslips_coupon',
                //             'name' => lang('Phiếu Thu Khác'),
                //         ],
                //     ],
                // ],
                // [
                //     'key' => 'purchase_debt',
                //     'name' => lang('Công Nợ Mua'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/purchase_invoice',
                //             'key' => 'purchase_invoice',
                //             'name' => lang('Hoá Đơn Mua Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/pay_slip',
                //             'key' => 'pay_slip',
                //             'name' => lang('Phiếu Chi Mua Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/debt_suppliers',
                //             'key' => 'debt_suppliers',
                //             'name' => lang('Công Nợ Mua Hàng'),
                //         ],
                //         [
                //             'link' => 'admin/suggestion',
                //             'key' => 'suggestion',
                //             'name' => lang('Phiếu Đề Xuất Tài Chính'),
                //         ],
                //         [
                //             'link' => 'admin/advance',
                //             'key' => 'advance',
                //             'name' => lang('Phiếu Tạm Ứng'),
                //         ],
                //         [
                //             'link' => 'admin/other_payslips',
                //             'key' => 'other_payslips',
                //             'name' => lang('Phiếu Chi Dịch Vụ'),
                //         ],
                //     ],
                // ]
                [
                    'key' => 'bgptm',
                    'name' => lang('BGPTM (Phát Triển Mẫu)'),
                    'sub' => [
                        // [
                        //     'link' => 'admin/quote_stage',
                        //     'key' => 'quote_stage',
                        //     'name' => lang('Bảng Giá Công Đoạn'),
                        // ],
                        [
                            'link' => 'admin/quotes',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('Bảng Báo Giá'),
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('Danh sách Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'name' => lang('Danh Sách Thông Tin Thiết Bị'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Loại Hình In'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thông Tin Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thông Tin Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thông Tin Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thông Tin NPL'),
                        ],
                    ],
                ],
                [
                    'key' => 'orders_delivery',
                    'name' => lang('Bán Hàng - Xuất Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/orders/import_orders',
                            'key' => 'import_orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin, 'create'),
                            'name' => lang('Import Tạo Đơn Hàng'),
                        ],
                        [
                            'link' => 'admin/orders',
                            'key' => 'orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin),
                            'name' => lang('Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/orders/information',
                            'key' => 'orders__information',
                            'is_permission' => true,
                            'name' => lang('Thống Kê Đơn Hàng'),
                        ],
                        // [
                        //     'link' => 'admin/releases',
                        //     'key' => 'releases',
                        //     'name' => lang('Xuất Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/returned_goods',
                        //     'key' => 'returned_goods',
                        //     'name' => lang('Trả Lại Hàng Bán'),
                        // ],
                        // [
                        //     'link' => 'admin/contracts_sales',
                        //     'key' => 'contracts_sales',
                        //     'name' => lang('Hợp Đồng Bán'),
                        // ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                // [
                //     'key' => 'work',
                //     'name' => lang('Quản lý nghiệp vụ - công  việc (Work+)'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/internal_proposal',
                //             'key' => 'internal_proposal',
                //             'name' => lang('Đề Xuất Nội Bộ'),
                //         ],
                //         [
                //             'link' => 'admin/hand_over/delivery_records',
                //             'key' => 'delivery_records',
                //             'name' => lang('Bàn Giao Công Việc'),
                //         ],
                //         [
                //             'link' => 'admin/production_list/moderation_plan?group=253',
                //             'key' => 'plan_propose',
                //             'name' => lang('Phiếu Kế Hoạch'),
                //         ],
                //         [
                //             'link' => 'admin/tasks?not_kanban=true',
                //             'key' => 'tasks',
                //             'name' => lang('Phiếu Công Việc'),
                //         ],
                //         [
                //             'link' => 'admin/tasks?kanban=true',
                //             'key' => 'tasks',
                //             'name' => lang('Công Việc Kanban'),
                //         ],
                //         [
                //             'link' => 'admin/tasks/calendar_pod',
                //             'key' => 'tasks__calendar_pod',
                //             'name' => lang('Lịch Công Việc'),
                //         ],
                //         [
                //             'link' => 'admin/gantt',
                //             'key' => 'gantt',
                //             'name' => lang('Sơ Đồ Gantt'),
                //         ],
                //         [
                //             'link' => 'admin/work_plan/handling',
                //             'key' => 'work_plan',
                //             'name' => lang('Kế hoạch công việc'),
                //         ],
                //     ]
                // ]
                [
                    'key' => 'debt_orders',
                    'name' => lang('Công Nợ Bán'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_invoice/synthetic_coupon_invoice',
                            'key' => 'synthetic_coupon_invoice',
                            'is_permission' => checkPermission('coupon_invoice', $staff_id, $is_admin),
                            'name' => lang('dt_coupon_invoice'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/debt_clients',
                            'key' => 'debt_clients',
                            'is_permission' => checkPermission('debt_clients', $staff_id, $is_admin),
                            'name' => lang('Công Nợ Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips_coupon',
                            'key' => 'other_payslips_coupon',
                            'is_permission' => checkPermission('other_payslips_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Khác'),
                        ],
                        // [
                        //     'link' => 'admin/releases',
                        //     'key' => 'releases',
                        //     'is_permission' => checkPermission('releases_deliveries', $staff_id, $is_admin),
                        //     'name' => lang('Giao Hàng - Xuất Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/returned_goods',
                        //     'key' => 'returned_goods',
                        //     'is_permission' => true,
                        //     'name' => lang('Trả Lại Hàng Bán'),
                        // ],
                    ],
                ],
                [
                    'key' => 'debt_purchases',
                    'name' => lang('Công Nợ Mua'),
                    'sub' => [
                        [
                            'link' => 'admin/purchases/synthetic_purchase',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('dt_purchases'),
                        ],
                        [
                            'link' => 'admin/import/synthetic_import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('dt_import'),
                        ],
                        [
                            'link' => 'admin/import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('Nhập Kho'),
                        ],
                        [
                            'link' => 'admin/return_suppliers',
                            'key' => 'return_suppliers',
                            'is_permission' => checkPermission('return_suppliers', $staff_id, $is_admin),
                            'name' => lang('Trả lại hàng mua'),
                        ],
                        [
                            'link' => 'admin/purchase_invoice/synthetic_invoice',
                            'key' => 'purchase_invoice',
                            'is_permission' => checkPermission('purchase_invoice', $staff_id, $is_admin),
                            'name' => lang('dt_purchase_invoice'),
                        ],
                        [
                            'link' => 'admin/pay_slip/synthetic_payslip',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('dt_pay_slip'),
                        ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => 'debt_suppliers',
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                            'name' => lang('Công Nợ Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/suggestion',
                            'key' => 'suggestion',
                            'is_permission' => checkPermission('suggestion', $staff_id, $is_admin),
                            'name' => lang('Phiếu Đề Xuất Tài Chính'),
                        ],
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => true,
                            'name' => lang('Phiếu Tạm Ứng'),
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('ch_other_payslips'),
                        ],
                        [
                            'link' => 'admin/other_payslips/other_payslip_manage',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('dt_other_payslips'),
                        ],
                        [
                            'link' => 'admin/spending_plan',
                            'key' => 'spending_plan',
                            'is_permission' => checkPermission('spending_plan', $staff_id, $is_admin),
                            'name' => lang('spending_plan'),
                        ],
                    ],
                ]
            ],
            'sub_menu_four' => [
                // [
                //     'key' => 'personnel',
                //     'name' => lang('Hồ Sơ Nhân Sự'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/staff',
                //             'key' => 'staff',
                //             'name' => lang('Danh sách nhân viên'),
                //         ],
                //         [
                //             'link' => 'admin/departments',
                //             'key' => 'departments',
                //             'name' => lang('Phòng Ban'),
                //         ],
                //         [
                //             'link' => 'admin/roles',
                //             'key' => 'roles',
                //             'name' => lang('Chức Vụ'),
                //         ],
                //     ]
                // ],
                // [
                //     'key' => 'timekeeping',
                //     'name' => lang('Chấm Công'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/paid_holidays/paid_holiday_leave',
                //             'key' => 'paid_holidays__paid_holiday_leave',
                //             'name' => lang('Đơn xin nghĩ phép'),
                //         ],
                //         [
                //             'link' => 'admin/salary/timekeeping',
                //             'key' => 'salary__timekeeping',
                //             'name' => lang('Chi tiết giờ công'),
                //         ],
                //         [
                //             'link' => 'admin/salary/dashboard_timekeeping',
                //             'key' => 'salary__dashboard_timekeeping',
                //             'name' => lang('Tổng hợp giờ công'),
                //         ],
                //     ]
                // ],
                // [
                //     'key' => 'overtime',
                //     'name' => lang('Tăng ca'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/suggest_overtime',
                //             'key' => 'suggest_overtime',
                //             'name' => lang('Phiếu Đề Xuất Tăng Ca'),
                //         ],
                //         [
                //             'link' => 'admin/business_fee_other/business_fee_other_overtime',
                //             'key' => 'business_fee_other__business_fee_other_overtime',
                //             'name' => lang('Tăng Ca Tháng'),
                //         ],
                //         [
                //             'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                //             'key' => 'business_fee_other__report_business_fee_other_overtime',
                //             'name' => lang('Thống Kê Giờ Tăng Ca'),
                //         ],
                //         [
                //             'link' => 'admin/business_fee_other/business_fee_other_calculate',
                //             'key' => 'business_fee_other__business_fee_other_calculate',
                //             'name' => lang('Bảng Tính Tăng Ca'),
                //         ],
                //     ]
                // ],
                // [
                //     'key' => 'kpi',
                //     'name' => lang('KPI'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/kpi/criteria',
                //             'key' => 'kpi__criteria',
                //             'name' => lang('Tiêu Chí KPI'),
                //         ],
                //         [
                //             'link' => 'admin/kpi/list',
                //             'key' => 'kpi__list',
                //             'name' => lang('Danh sách KPI'),
                //         ],
                //     ]
                // ],
                // [
                //     'key' => 'salary',
                //     'name' => lang('Bảng Lương'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/payroll/payroll_payment',
                //             'key' => 'payroll__payroll_payment',
                //             'name' => lang('Phiếu Tạm Ứng Lương'),
                //         ],
                //         [
                //             'link' => 'admin/payroll/payroll_salary',
                //             'key' => 'payroll__payroll_salary',
                //             'name' => lang('Bảng Lương'),
                //         ],
                //     ]
                // ],
                // [
                //     'key' => 'other',
                //     'name' => lang('Khác'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/evaluate?type=evaluate',
                //             'key' => 'evaluate',
                //             'name' => lang('Phiếu Đánh Giá'),
                //         ],
                //         [
                //             'link' => 'admin/evaluate?type=educate',
                //             'key' => 'evaluate',
                //             'name' => lang('Phiếu Đào Tạo'),
                //         ],
                //         [
                //             'link' => 'admin/evaluate?type=license',
                //             'key' => 'evaluate',
                //             'name' => lang('Giấy Phép'),
                //         ],
                //         [
                //             'link' => 'admin/evaluate?type=certification',
                //             'key' => 'evaluate',
                //             'name' => lang('Chứng Nhận'),
                //         ],
                //         [
                //             'link' => 'admin/evaluate?type=certificate',
                //             'key' => 'evaluate',
                //             'name' => lang('Chứng Chỉ'),
                //         ],
                //     ]
                // ]
                [
                    'key' => 'work',
                    'name' => lang('Quản lý nghiệp vụ - công  việc (Work+)'),
                    'sub' => [
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => 'internal_proposal',
                            'is_permission' => checkPermission('internal_proposal', $staff_id, $is_admin),
                            'name' => lang('Đề Xuất Nội Bộ'),
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'delivery_records',
                            'is_permission' => checkPermission('delivery_records', $staff_id, $is_admin),
                            'name' => lang('Bàn Giao Công Việc'),
                        ],
                        // [
                        //     'link' => 'admin/production_list/moderation_plan?group=253',
                        //     'key' => 'plan_propose',
                        //     'name' => lang('Phiếu Kế Hoạch'),
                        // ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Phiếu Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks?kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Công Việc Kanban'),
                        ],
                        [
                            'link' => 'admin/tasks/calendar_pod',
                            'key' => 'tasks__calendar_pod',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Lịch Công Việc'),
                        ],
                        [
                            'link' => 'admin/gantt',
                            'key' => 'gantt',
                            'is_permission' => checkPermission('gantt', $staff_id, $is_admin),
                            'name' => lang('Sơ Đồ Gantt'),
                        ],
                        [
                            'link' => 'admin/work_plan/handling',
                            'key' => 'work_plan',
                            'is_permission' => checkPermission('work_plan', $staff_id, $is_admin),
                            'name' => lang('Kế hoạch công việc'),
                        ],
                    ]
                ],
                [
                    'key' => 'personnel',
                    'name' => lang('Hồ Sơ Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('Danh sách nhân viên'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                            'name' => lang('Chức Vụ'),
                        ],
                    ]
                ],
                [
                    'key' => 'timekeeping',
                    'name' => lang('Chấm Công'),
                    'sub' => [
                        [
                            'link' => 'admin/paid_holidays/paid_holiday_leave',
                            'key' => 'paid_holidays__paid_holiday_leave',
                            'is_permission' => true,
                            'name' => lang('Đơn xin nghĩ phép'),
                        ],
                        [
                            'link' => 'admin/salary/timekeeping',
                            'key' => 'salary__timekeeping',
                            'is_permission' => checkPermission('timekeeping', $staff_id, $is_admin),
                            'name' => lang('Chi tiết giờ công'),
                        ],
                        [
                            'link' => 'admin/salary/dashboard_timekeeping',
                            'key' => 'salary__dashboard_timekeeping',
                            'is_permission' => checkPermission('dashboard_timekeeping', $staff_id, $is_admin),
                            'name' => lang('Tổng hợp giờ công'),
                        ],
                    ]
                ],
                [
                    'key' => 'overtime',
                    'name' => lang('Tăng ca'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_overtime',
                            'key' => 'suggest_overtime',
                            'is_permission' => true,
                            'name' => lang('Phiếu Đề Xuất Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => true,
                            'name' => lang('Tăng Ca Tháng'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                            'key' => 'business_fee_other__report_business_fee_other_overtime',
                            'name' => lang('Thống Kê Giờ Tăng Ca'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_calculate',
                            'key' => 'business_fee_other__business_fee_other_calculate',
                            'name' => lang('Bảng Tính Tăng Ca'),
                            'is_permission' => true,
                        ],
                    ]
                ],
            ],
        ];

        $menu['category']['items']['production_management'] = [
            'name' => lang('2. Production Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'plan',
                    'name' => lang('PLAN(Production Planning)'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('Tổng Hợp Sản Xuất'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('Kế Hoạch Điều Độ'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/plan_propose?group=train',
                            'key' => 'plan_propose',
                            'name' => lang('6. Kế Hoạch Điều Động'),
                            'is_permission' => checkPermission('plan_propose', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('Phiếu Xuất Tồn Kho(NPL/TP)'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/manufacture/index',
                            'key' => 'manufacture__index',
                            'name' => lang('Phiếu Xả Khổ Giấy'),
                            'is_permission' => checkPermission('manufacture', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_zinc',
                            'key' => 'synthetic_zinc',
                            'name' => lang('Tổng Hợp Xuất Kẽm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('DS Đơn Vị Gia Công'),
                        ],
                        // [
                        //     'link' => 'admin/business_plan',
                        //     'key' => 'business_plan',
                        //     'name' => lang('Kế Hoạch Thành Phẩm'),
                        //     'is_permission' => checkPermission('business_plan', $staff_id, $is_admin),
                        // ],
                        // [
                        //     'link' => 'admin/manufactures/list_manufactures',
                        //     'key' => 'manufactures__list_manufactures',
                        //     'name' => lang('Kế Hoạch Sản Xuất'),
                        //     'is_permission' => true,
                        // ],
                    ],
                ],
                [
                    'key' => 'plan_npl',
                    'name' => lang('KẾ HOẠCH NPL'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_plan',
                            'key' => 'manufactures__productions_plan',
                            'name' => lang('Kế Hoạch NPL'),
                            'is_permission' => checkPermission('manufactures_productions_plan', $staff_id, $is_admin),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('Kế Hoạch Về NPL'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => 'reports__purchase',
                            'name' => lang('Kế Hoạch Điều Động Về NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/manufactures/productions_plan_purchase',
                            'key' => 'manufactures__productions_plan_purchase',
                            'name' => lang('Tổng Hợp Mua Hàng'),
                            'is_permission' => true
                        ]
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'manufactures',
                    'name' => lang('SẢN XUẤT'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('Lệnh Sản Xuất Tổng'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/order_production_details',
                            'key' => 'manufactures__order_production_details',
                            'name' => lang('Lệnh Sản Xuất Chi Tiết'),
                            'is_permission' => checkPermission('manufactures_order_production_details', $staff_id, $is_admin)
                        ],
                        // [
                        //     'link' => 'admin/manufacture/index',
                        //     'key' => 'manufacture__index',
                        //     'name' => lang('Phiếu Xả Khổ'),
                        //     'is_permission' => checkPermission('manufacture', $staff_id, $is_admin)
                        // ],
                        // [
                        //     'link' => 'admin/synthetic_zinc',
                        //     'key' => 'synthetic_zinc',
                        //     'name' => lang('Tổng Hợp Xuất Kẽm'),
                        //     'is_permission' => true
                        // ],
                        [
                            'link' => 'admin/synthetic_stage',
                            'key' => 'synthetic_stage',
                            'name' => lang('Lệnh Sản Xuất Theo Công Đoạn'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                    ]
                ],
                [
                    'key' => 'qa',
                    'name' => lang('QA(Quality Assurance )'),
                    'sub' => [
                        [
                            'link' => 'admin/quality_control/check_quality',
                            'key' => 'quality_control__check_quality',
                            'name' => lang('Kiểm Tra Chất Lượng'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/category_errors',
                            'key' => 'quality_control__category_errors',
                            'name' => lang('Danh Mục Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/detail_errors',
                            'key' => 'quality_control__detail_errors',
                            'name' => lang('Chi Tiết Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('Báo Cáo Không Phù Hợp'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Sách Thiết Bị Đo Kiểm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Sách Tiêu Chuẩn Chất Lượng SP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Tiêu Chí/Quy Trình Bàn Giao'),
                            'is_permission' => 1,
                        ],
                    ]
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'mro',
                    'name' => lang('MRO(Maintenance Repair Operation)'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Thiết Bị Máy Móc'),

                        ],
                        [
                            'link' => 'admin/maintenance/category',
                            'key' => 'maintenance__category',
                            'name' => lang('Hạng Mục Bảo Trì'),
                            'is_permission' => checkPermission('category_maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance/calendar',
                            'key' => 'maintenance__calendar',
                            'name' => lang('Lịch Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('Phiếu Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Quy Trình Bảo Trì'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'name' => lang('Danh Sách Khấu Hao'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'name' => lang('Danh Sách Định Mức Thiết Bị'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('DS Hiệu Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('DS Đơn Vị Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'prop',
                    'name' => lang('PROP(Production Process)'),
                    'sub' => [
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'name' => lang('Loại Bàn Giao'),
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'name' => lang('Tiêu Chí Bàn Giao'),
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'hand_over__delivery_records',
                            'name' => lang('Biên Bản Bàn Giao'),
                            'is_permission' => checkPermission('delivery_records', $staff_id, $is_admin)
                        ]
                    ]
                ],
                [
                    'key' => 'releases',
                    'name' => lang('GIAO HÀNG THANH TOÁN'),
                    'sub' => [
                        [
                            'link' => 'admin/releases/synthetic_releases',
                            'key' => 'synthetic_releases',
                            'is_permission' => checkPermission('releases_deliveries', $staff_id, $is_admin),
                            'name' => lang('dt_delivery'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chí Giao Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chuẩn Đóng Gói'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Phí Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thanh Toán'),
                        ],
                        [
                            'link' => 'admin/clients/all_shipping',
                            'key' => 'clients__all_shipping',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Địa Chỉ Giao Hàng'),
                        ],
                    ]
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'warehouse',
                    'name' => lang('KHO HÀNG(Warehouse)'),
                    'sub' => [
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'name' => lang('Kiểm Kê'),
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('DS Kho Hàng'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse/localtion',
                            'key' => 'warehouse__localtion',
                            'name' => lang('Vị Trí Kho'),
                            'is_permission' => checkPermission('warehouse_localtion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer',
                            'key' => 'transfer',
                            'name' => lang('Chuyển Kho'),
                            'is_permission' => checkPermission('transfer', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer_bussiness',
                            'key' => 'transfer_bussiness',
                            'name' => lang('Giữ Kho (Trên Chuyền)'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('Xuất Kho NPL Tồn'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/purchase_products',
                            'key' => 'stock__purchase_products',
                            'name' => lang('Nhập Kho Thành Phẩm'),
                            'is_permission' => checkPermission('stock_purchase_products', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/stock/purchase_internal',
                            'key' => 'stock__purchase_internal',
                            'name' => lang('Thu hồi NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/export_different',
                            'key' => 'stock__export_different',
                            'name' => lang('Xuất Kho Khác(Khẩn/Gấp)'),
                            'is_permission' => checkPermission('export_different', $staff_id, $is_admin)
                        ],
                    ]
                ]
            ]
        ];

        $menu['category']['items']['reports'] = [
            'name' => lang('3. Report'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'report',
                    'name' => lang('Báo Cáo(Report)'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('Báo Cáo Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('Báo Cáo Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => 'reports__sales',
                            'name' => lang('Báo Cáo Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer',
                            'key' => 'reports__debt_customer',
                            'name' => lang('Công Nợ Phải Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => 'reports__fund_balance',
                            'name' => lang('Tồn Quỹ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('Tồn Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('Quản Lý Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/expenses_vs_income',
                            'key' => 'reports__expenses_vs_income',
                            'name' => lang('Quản Lý Lợi Nhuận'),
                            'is_permission' => checkPermission('expenses_vs_income', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'report',
                    'name' => lang('Báo Cáo(Report)'),
                    'sub' => [
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('Phiếu Báo Cáo'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/production_report/incident_tracking',
                            'key' => 'incident_tracking',
                            'name' => lang('Phiếu Theo Dõi Sự Cố'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ],
        ];

        // $menu['category']['items']['report'] = [
        //     'name' => lang('V. Thống Kê - Báo Cáo'),
        //     // 'sub_menu_one' => [
        //     //     [
        //     //         'key' => 'customer',
        //     //         'name' => lang('Khách hàng'),
        //     //         'sub' => [
        //     //             [
        //     //                 'link' => '',
        //     //                 'key' => '',
        //     //                 'name' => lang('Nhóm khách hàng'),
        //     //             ],
        //     //             [
        //     //                 'link' => '',
        //     //                 'key' => '',
        //     //                 'name' => lang('Phân loại khách hàng'),
        //     //             ],
        //     //         ],
        //     //     ]
        //     // ]
        // ];

        $menu['category']['items']['kpi'] = [
            'name' => lang('V. Đánh Giá KPI Tháng/Năm'),
            // 'sub_menu_one' => [
            //     [
            //         'key' => 'customer',
            //         'name' => lang('Khách hàng'),
            //         'sub' => [
            //             [
            //                 'link' => '',
            //                 'key' => '',
            //                 'name' => lang('Nhóm khách hàng'),
            //             ],
            //             [
            //                 'link' => '',
            //                 'key' => '',
            //                 'name' => lang('Phân loại khách hàng'),
            //             ],
            //         ],
            //     ]
            // ]
        ];

        $menu['category']['items']['power_bi'] = [
            'name' => lang('VI. Dashboard Power BI'),
            'sub_menu_one' => [
                [
                    'key' => 'customer',
                    'name' => lang('Dashbood Power BI'),
                    'sub' => [
                        [
                            'link' => 'admin/report_dashboard/dashboard_quotes',
                            'key' => 'dashboard__quotes',
                            'name' => lang('DASHBOARD Báo Giá Phát Triển Mẫu'),
                            'is_permission' => checkPermission('dashboard_quotes', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_revenue',
                            'key' => 'dashboard__revenue',
                            'name' => lang('DASHBOARD Doanh Thu'),
                            'is_permission' => checkPermission('dashboard_revenue', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_cost',
                            'key' => 'dashboard__cost',
                            'name' => lang('DASHBOARD Chi Phí'),
                            'is_permission' => checkPermission('dashboard_cost', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_stock',
                            'key' => 'dashboard__stock',
                            'name' => lang('DASHBOARD Tồn Kho'),
                            'is_permission' => checkPermission('dashboard_stock', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_manufactures',
                            'key' => 'dashboard__manufactures',
                            'name' => lang('DASHBOARD Sản Xuất'),
                            'is_permission' => checkPermission('dashboard_manufactures', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_task',
                            'key' => 'dashboard__task',
                            'name' => lang('DASHBOARD Công Việc'),
                            'is_permission' => checkPermission('dashboard_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_personnel',
                            'key' => 'dashboard__personnel',
                            'name' => lang('DASHBOARD Hành Chính - Nhân Sự'),
                            'is_permission' => checkPermission('dashboard_personnel', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_purchases',
                            'key' => 'dashboard__purchases',
                            'name' => lang('DASHBOARD Mua Hàng'),
                            'is_permission' => checkPermission('dashboard_purchases', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_business_results',
                            'key' => 'dashboard__business_results',
                            'name' => lang('DASHBOARD Kết Quả Kinh Doanh'),
                            'is_permission' => checkPermission('dashboard_business_results', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ]
        ];

        return $menu;
    }
}

if (!function_exists('getTroubleViolationList')) {
    function getTroubleViolationList($filter = [])
    {
        $response = [];
        $arrColor = ['#f9d2b4', '#f6ba8c', '#ce6500'];
        $trouble_violation_category = get_table_where('tbltrouble_violation_point');
        foreach ($trouble_violation_category as $categoryKey => $categoryValue) {
            $CI = &get_instance();
            $CI->db->select('COUNT(tblproduction_report.trouble_violation_point_id) as count_trouble_violation, SUM(tblproduction_report.trouble_violation_point) as point');
            $CI->db->where('tblproduction_report.trouble_violation_point_id', $categoryValue['id']);
            if (empty($filter['this_month'])) {
                $CI->db->group_start();
                $CI->db->where('tblproduction_report.date_create >= ', date('Y-m-d') . ' 00:00:00');
                $CI->db->where('tblproduction_report.date_create <= ', date('Y-m-d') . ' 23:59:59');
                $CI->db->group_end();
            }
            $result = $CI->db->get('tblproduction_report')->row_array();
            $result['name'] = $categoryValue['name'];
            $result['color'] = (!empty($arrColor[$categoryKey]) ? $arrColor[$categoryKey] : '#ce6500');

            $response[] = $result;
        }

        return $response;
    }
}

if (!function_exists('getProductionReport')) {
    function getProductionReport($filter = [])
    {
        $response = [];
        $kpiResult = calResult();
        krsort($kpiResult);
        foreach ($kpiResult as $kpiResultKey => $kpiResultValue) {
            $staffDepartments = "(
                SELECT
                    tblstaff_departments.staffid as staffid,
                    GROUP_CONCAT(tbldepartments.name) as name_department 
                FROM tblstaff_departments
                INNER JOIN tbldepartments ON tbldepartments.departmentid = tblstaff_departments.departmentid
                GROUP BY tblstaff_departments.staffid
            ) tb_staff_departments";

            $tbStaff = "(
                SELECT 
                    tblstaff.staffid as staffid,
                    tblstaff.firstname as firstname,
                    tblstaff.lastname as lastname,
                    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as fullname,
                    tblroles.name as name_role,
                    tb_staff_departments.name_department as name_department
                FROM tblstaff
                LEFT JOIN tblroles ON tblroles.roleid = tblstaff.role
                LEFT JOIN $staffDepartments ON tb_staff_departments.staffid = tblstaff.staffid
            ) tb_staff";

            $CI = &get_instance();
            $CI->db->select('
                tbl_kpi.staff as object_id,
                IF(tbl_kpi.type_kpi = 1, tb_staff.fullname, IF (tbl_kpi.type_kpi = 2, tbldepartments.name, "")) as object_name,
                tbl_kpi.type_kpi as object_type,
                tbl_kpi.point_kpi as point_kpi
            ');
            $CI->db->join($tbStaff, 'tb_staff.staffid = tbl_kpi.staff AND tbl_kpi.type_kpi = 1', 'left');
            $CI->db->join('tbldepartments', 'tbldepartments.departmentid = tbl_kpi.staff AND tbl_kpi.type_kpi = 2', 'left');
            $CI->db->where('tbl_kpi.result_kpi', $kpiResultKey);
            if (empty($filter['this_month'])) {
                $CI->db->group_start();
                $CI->db->where('tbl_kpi.date_created >= ', date('Y-m-d') . ' 00:00:00');
                $CI->db->where('tbl_kpi.date_created <= ', date('Y-m-d') . ' 23:59:59');
                $CI->db->group_end();
            }
            $CI->db->order_by('tbl_kpi.point_kpi desc');
            $result = $CI->db->get('tbl_kpi')->result_array();
            $response[$kpiResultValue] = $result;
        }
        return $response;
    }
}

if (!function_exists('getMenuDashboard2')) {
    function getMenuDashboard2()
    {
        $CI = &get_instance();
        $staff_id = get_staff_user_id();
        $is_admin = is_admin($staff_id);
        $menu = [];
        $menu['category']['name'] = lang('Hạng mục');

        $menu['category']['items']['created_group'] = [
            'name' => lang('I. Data - Danh Mục'),
            'sub_name' => lang('MASTER DATA'),
            'sub_menu_one' => [
                [
                    'key' => 'info_clients',
                    'name' => lang('Thông tin khách hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients/brand',
                            'key' => 'brand',
                            'is_permission' => true,
                            'name' => lang('Mã Brand'),
                        ],
                        // [
                        //     'link' => 'admin/clients/groups',
                        //     'key' => 'clients__groups',
                        //     'is_permission' => checkPermission('groups', $staff_id, $is_admin),
                        //     'name' => lang('Nhóm Khách Hàng'),
                        // ],
                        [
                            'link' => 'admin/clients/status_client',
                            'key' => 'clients__status_client',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Phân Loại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients__clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Khách Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Tiêu Chuẩn Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Chiết Khấu'),
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/contracts_sales',
                            'key' => 'contracts_sales',
                            'is_permission' => checkPermission('contracts_sales', $staff_id, $is_admin),
                            'name' => lang('Hợp Đồng Bán'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Thuế VAT'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Sản Phẩm'),
                        ],
                    ],
                ],
                [
                    'key' => 'suppliers',
                    'name' => lang('Thông Tin Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('Nhóm Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/supplier_classify',
                            'key' => 'suppliers',
                            'is_permission' => 1,
                            'name' => lang('Phân Loại Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/evaluation_criteria',
                            'key' => 'suppliers__evaluation_criteria',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Tiêu Chuẩn Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('Chiết Khấu'),
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/contracts_supplier',
                            'key' => 'contracts_supplier',
                            'is_permission' => 1,
                            'name' => lang('Hợp Đồng Mua'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Thuế VAT'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá NPL'),
                        ],
                    ],
                ],
                [
                    'key' => 'sale_marketing',
                    'name' => lang('Phòng Sale/Marketing'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mã Số Xuất Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mã Số Nhập Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Biểu Thuế'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Báo Giá Vận Chuyển'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Chứng Từ Xuất Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Chứng Từ Nhập Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mã Số Bưu Điện'),
                        ],
                    ],
                ],
                [
                    'key' => 'accountant',
                    'name' => lang('Phòng Kế Toán'),
                    'sub' => [
                        [
                            'link' => 'admin/units',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Thanh Toán'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'is_permission' => true,
                            'name' => lang('Khấu Hao'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'is_permission' => true,
                            'name' => lang('Nhóm Chi Phí'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'name' => lang('Loại Chi Phí'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_payslips',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('Phiếu Yêu Cầu Chi'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Khoản Chi'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mục Chi'),
                        ],
                        [
                            'link' => 'admin/paymentmodes',
                            'key' => 'paymentmodes',
                            'name' => lang('Hình Thức Thanh Toán'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/coupon_invoice',
                            'key' => 'coupon_invoice',
                            'is_permission' => checkPermission('coupon_invoice', $staff_id, $is_admin),
                            'name' => lang('Hoá Đơn Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips_coupon',
                            'key' => 'other_payslips_coupon',
                            'is_permission' => checkPermission('other_payslips_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Khác'),
                        ],
                        [
                            'link' => 'admin/purchase_invoice',
                            'key' => 'purchase_invoice',
                            'is_permission' => checkPermission('purchase_invoice', $staff_id, $is_admin),
                            'name' => lang('Hoá Đơn Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('Phiếu Chi Phí Ngoài'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Chi Mua Hàng (theo YCMH)'),
                        ],
                        [
                            'link' => 'admin/other_payslips/other_payslip_manage',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('Phiếu Chi Quản Lý'),
                        ],
                        [
                            'link' => 'admin/spending_plan',
                            'key' => 'spending_plan',
                            'is_permission' => true,
                            'name' => lang('Phiếu Kế Hoạch Chi'),
                        ],
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => checkPermission('advance', $staff_id, $is_admin),
                            'name' => lang('Phiếu Chi Tạm Ứng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'staffs',
                    'name' => lang('Thông Tin Hành Chính Nhân Sự'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Tiêu Chí Bàn Giao Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin),
                            'name' => lang('Loại Bàn Giao'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Khu Vực Vệ Sinh ATLĐ-5S'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Giấy Phép'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Văn Phòng - Chi Nhánh'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Hội Đồng'),
                        ],
                        [
                            'link' => 'admin/board',
                            'key' => 'board',
                            'is_permission' => true,
                            'name' => lang('Ban'),
                        ],
                        [
                            'link' => 'admin/block',
                            'key' => 'block',
                            'is_permission' => true,
                            'name' => lang('Khối'),
                        ],
                        [
                            'link' => 'admin/room',
                            'key' => 'room',
                            'is_permission' => true,
                            'name' => lang('Phòng'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => true,
                            'name' => lang('Bộ Phận'),
                        ],
                        [
                            'link' => 'admin/nest',
                            'key' => 'nest',
                            'is_permission' => true,
                            'name' => lang('Tổ'),
                        ],
                        [
                            'link' => 'admin/group',
                            'key' => 'group',
                            'is_permission' => true,
                            'name' => lang('Nhóm'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                            'name' => lang('Mã Vị Trí'),
                        ],
                        [
                            'link' => 'admin/kpi/detail_task',
                            'key' => 'detail_task',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Mô Tả Công Việc'),
                        ],
                        [
                            'link' => 'admin/category_salary/category_permission',
                            'key' => 'category_permission',
                            'is_permission' => true,
                            'name' => lang('Nhóm Phép'),
                        ],
                        [
                            'link' => 'admin/category_salary/permission',
                            'key' => 'permission',
                            'is_permission' => true,
                            'name' => lang('Phép'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('Lương'),
                        ],
                        [
                            'link' => 'admin/category_salary/coefficient_salary',
                            'key' => 'coefficient_salary',
                            'is_permission' => true,
                            'name' => lang('Hệ Số Lương Năng Lực'),
                        ],
                        [
                            'link' => 'admin/category_salary/step_salary',
                            'key' => 'step_salary',
                            'is_permission' => true,
                            'name' => lang('Hệ Số Lương Công Việc'),
                        ],
                        [
                            'link' => 'admin/kpi/category_kpi',
                            'key' => 'category_kpi',
                            'is_permission' => true,
                            'name' => lang('KPIs'),
                        ],
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('Thông Tin Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('Hợp Đồng Lao Động'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Khám Sức Khỏe'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mã Quy Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Quy Định Chung'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Quy Định Chung'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nội Quy'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Nội Quy'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Nội Quy'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('Quyết Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Bảo Hiểm'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Nhóm Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Loại Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/type_reports',
                            'key' => 'type_reports',
                            'is_permission' => true,
                            'name' => lang('Nhóm Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/group_reports',
                            'key' => 'group_reports',
                            'is_permission' => true,
                            'name' => lang('Loại Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/type_error',
                            'key' => 'type_error',
                            'is_permission' => true,
                            'name' => lang('Nhóm Lỗi'),
                        ],
                        [
                            'link' => 'admin/group_error',
                            'key' => 'group_error',
                            'is_permission' => true,
                            'name' => lang('Lỗi'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=educate',
                            'key' => 'evaluate_educate',
                            'is_permission' => true,
                            'name' => lang('Nhóm Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/type_evaluate?type=educate',
                            'key' => 'type_evaluate_educate',
                            'is_permission' => true,
                            'name' => lang('Loại Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Mã Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/category_evaluate',
                            'key' => 'category_evaluate',
                            'is_permission' => true,
                            'name' => lang('Nhóm Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/type_evaluate?type=evaluate',
                            'key' => 'type_evaluate_evaluate',
                            'is_permission' => true,
                            'name' => lang('Loại Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/category_complaints',
                            'key' => 'category_complaints',
                            'is_permission' => true,
                            'name' => lang('Nhóm Khiếu Nại'),
                        ],
                        [
                            'link' => 'admin/category_improve',
                            'key' => 'category_improve',
                            'is_permission' => true,
                            'name' => lang('Nhóm Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/type_improve',
                            'key' => 'type_improve',
                            'is_permission' => true,
                            'name' => lang('Loại Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/type_system',
                            'key' => 'type_system',
                            'is_permission' => true,
                            'name' => lang('Loại Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/category_system',
                            'key' => 'category_system',
                            'is_permission' => true,
                            'name' => lang('Nhóm Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/system',
                            'key' => 'system',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Hệ Thống'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Định Mức Thời Gian'),
                        ],
                        [
                            'link' => 'admin/allowance_reduce',
                            'key' => 'allowance_reduce',
                            'name' => lang('Phụ Cấp - Giảm Trừ'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble',
                            'key' => 'trouble',
                            'name' => lang('Loại Sự Cố'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble/category_problem',
                            'key' => 'category_problem',
                            'is_permission' => true,
                            'name' => lang('Nhóm Sự Cố'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Định Mức Năng Suất'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Định Mức Công Việc'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Thiết Bị Sản Xuất'),
                        ],
                        [
                            'link' => 'admin/categories_other/materials_equipment',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Thiết Bị Văn Phòng'),
                        ],
                        [
                            'link' => 'admin/work_plan/handling',
                            'key' => 'work_plan',
                            'is_permission' => checkPermission('work_plan', $staff_id, $is_admin),
                            'name' => lang('Kế hoạch công việc'),
                        ],
                        [
                            'link' => 'admin/request_bussiness',
                            'key' => 'request_bussiness',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Công Tác'),
                        ],
                        [
                            'link' => 'admin/request_control_vehicle_bussiness',
                            'key' => 'request_control_vehicle_bussiness',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Điều Xe Công Tác'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Kiểm Soát Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/suggest_educate',
                            'key' => 'suggest_educate',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Yêu Cầu Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/request_improve',
                            'key' => 'request_improve',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/suggest_recruitment',
                            'key' => 'suggest_recruitment',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Tuyển Dụng'),
                        ],
                        [
                            'link' => 'admin/moderation_recruitment',
                            'key' => 'moderation_recruitment',
                            'is_permission' => true,
                            'name' => lang('Phiếu Điều Độ Công Việc Tuyển Dụng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Thử Việc'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Nhân Viên'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Nhân Sự-Tay Nghề'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/paid_holiday_leave',
                            'key' => 'paid_holidays__paid_holiday_leave',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Nghỉ Phép'),
                        ],
                        [
                            'link' => 'admin/entrance_ticket',
                            'key' => 'entrance_ticket',
                            'is_permission' => true,
                            'name' => lang('Phiếu Ra Vào Cổng-Mang Hàng Ra Cổng'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('Phiếu Lương'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá'),
                        ],
                        [
                            'link' => 'suggest_accreditation',
                            'key' => 'accreditation',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Test/Kiểm Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá KPIs'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Hệ Thống'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Quy Trình'),
                        ],
                        [
                            'link' => 'admin/suggest_check',
                            'key' => 'suggest_check',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Kiểm Tra Vệ Sinh ATLĐ - 5S'),
                        ],
                        [
                            'link' => 'admin/suggest_pccc',
                            'key' => 'suggest_pccc',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Kiểm Tra PCCC'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Quy Trình'),
                        ],

                        // [
                        //     'link' => 'admin/evaluate?type=license',
                        //     'key' => 'evaluate',
                        //     'is_permission' => true,
                        //     'name' => lang('Giấy Phép'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=certification',
                        //     'key' => 'certification',
                        //     'is_permission' => true,
                        //     'name' => lang('Chứng Nhận'),
                        // ],
                        // [
                        //     'link' => 'admin/category_tasks',
                        //     'key' => 'category_tasks',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'is_settings' => 1,
                        //     'name' => lang('Mô Tả Công Việc - Vị Trí'),
                        // ],
                        // [
                        //     'link' => 'admin/kpi/list',
                        //     'key' => 'kpi',
                        //     'is_permission' => checkPermission('kpi', $staff_id, $is_admin),
                        //     'name' => lang('KPI'),
                        // ],
                        // [
                        //     'link' => 'admin/branch',
                        //     'key' => 'branch',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang('Chi Nhánh'),
                        // ],
                        // [
                        //     'link' => 'admin/warehouse',
                        //     'key' => 'warehouse',
                        //     'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                        //     'name' => lang('Kho'),
                        // ],
                        // [
                        //     'link' => 'admin/departments',
                        //     'key' => 'departments',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang('Phòng Ban'),
                        // ],
                        // [
                        //     'link' => 'admin/staff',
                        //     'key' => 'staff',
                        //     'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                        //     'name' => lang('Nhân Viên'),
                        // ],
                        // [
                        //     'link' => 'admin/tasks?not_kanban=true',
                        //     'key' => 'tasks',
                        //     'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                        //     'name' => lang('Công Việc'),
                        // ],
                        // [
                        //     'link' => 'admin/payroll/payroll_salary',
                        //     'key' => 'payroll',
                        //     'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                        //     'name' => lang('Bảng Lương'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=educate',
                        //     'key' => 'evaluate',
                        //     'is_permission' => 1,
                        //     'name' => lang('Đào Tạo'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=evaluate',
                        //     'key' => 'evaluate',
                        //     'is_permission' => true,
                        //     'name' => lang('Đánh Giá'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Tuyển Dụng'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('5S'),
                        // ],
                        // [
                        //     'link' => 'admin/maintenance',
                        //     'key' => 'maintenance',
                        //     'name' => lang('Bảo Dưỡng'),
                        //     'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('PCCC'),
                        // ],
                        // [
                        //     'link' => 'admin/decision/list',
                        //     'key' => 'decision',
                        //     'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                        //     'name' => lang('Quyết Định'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Quy Định'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Quy Trình'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Hợp Đồng'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=license',
                        //     'key' => 'evaluate',
                        //     'is_permission' => true,
                        //     'name' => lang('Phép'),
                        // ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories',
                        //     'is_settings' => 1,
                        //     'is_permission' => true,
                        //     'name' => lang('Thiết Bị SX'),
                        // ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories',
                        //     'is_settings' => 1,
                        //     'is_permission' => true,
                        //     'name' => lang('Thiết bị VP'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Khám Sức Khỏe'),
                        // ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories',
                        //     'is_settings' => 1,
                        //     'is_permission' => true,
                        //     'name' => lang('Thiết bị Đo Kiểm'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Hiệu Chuẩn'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Phương Tiện'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Định mức(Năng Suất/Công Việc)'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('Bảo Hiểm'),
                        // ],
                    ],
                ],
                [
                    'key' => 'machines',
                    'name' => lang('Thông Tin Thiết Bị'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/category_machines',
                            'key' => 'category_machines',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Nhóm Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Mã Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Năng Suất Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Danh Sách Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Khấu Hao'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/tools_supplies/category',
                            'key' => 'tools_supplies__category',
                            'is_permission' => checkPermission('tools_supplies_category', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Thay Thế'),
                        ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('Vật Tư Thay Thế'),
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'products',
                    'name' => lang('Thông Tin Sản Phẩm'),
                    'sub' => [
                        [
                            'link' => 'admin/products/category',
                            'key' => 'products__category',
                            'is_permission' => checkPermission('products_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Tính Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/category_stages',
                            'key' => 'category__stages',
                            'name' => lang('Nhóm công đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/bom',
                            'key' => 'products',
                            'is_permission' => checkPermission('products_bom', $staff_id, $is_admin),
                            'name' => lang('Định Mức BOM'),
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn Sản Phẩm'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('Thông Tin Tồn Kho'),
                        ],
                    ],
                ],
                [
                    'key' => 'items',
                    'name' => lang('Thông Tin Nguyên Vật Liệu'),
                    'sub' => [
                        [
                            'link' => 'admin/items/category',
                            'key' => 'items__category',
                            'is_permission' => checkPermission('items_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm Nguyên Vật Liệu'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_settings' => 1,
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại NPL'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Nguyên Vật Liệu'),
                        ],
                        [
                            'link' => 'admin/units?type_title=materials',
                            'key' => 'units',
                            'name' => lang('Đơn Vị Tính Nguyên Vật Liệu'),
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước Nguyên Vật Liệu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('Thông Tin Tồn Kho'),
                        ],
                    ]
                ],
                [
                    'key' => 'maintenace',
                    'name' => lang('Bảo Trì (Maintenace)'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/category_machines',
                            'key' => 'category_machines',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Nhóm Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Mã Thiết Bị'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Bảo Trì'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Lý Lịch Máy'),
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('Loại Bảo Dưỡng'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/category_maintenace',
                            'key' => 'category_maintenace',
                            'is_permission' => true,
                            'name' => lang('Nhóm Bảo Dưỡng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Khu Vực'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Mã Khu Vực'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Danh Mục Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenace/machines_size',
                            'key' => 'machines_size',
                            'is_permission' => true,
                            'name' => lang('Kích Thước Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenace/operating_size',
                            'key' => 'operating_size',
                            'is_permission' => true,
                            'name' => lang('Kích Thước Vận Hành'),
                        ],
                        [
                            'link' => 'admin/units?type_title=machines',
                            'key' => 'units',
                            'name' => lang('Đơn Vị Tính Thiết Bị'),
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('Năng Suất Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=1',
                            'key' => 'category_maintenance_calibration?type=1',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Hiệu Chuẩn'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=2',
                            'key' => 'category_maintenance_calibration?type=2',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Bảo Dưỡng Cơ'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=3',
                            'key' => 'category_maintenance_calibration?type=3',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Bảo Dưỡng Điện'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=4',
                            'key' => 'category_maintenance_calibration?type=4',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Bảo Dưỡng Cơ Sở Hạ Tầng'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=5',
                            'key' => 'category_maintenance_calibration?type=5',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Bảo Dưỡng Hơi Khí Nén'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=6',
                            'key' => 'category_maintenance_calibration?type=6',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Nhớt - Mỡ Bò'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=7',
                            'key' => 'category_maintenance_calibration?type=7',
                            'is_permission' => true,
                            'name' => lang('Danh Mục Vật Tư Thay Thế'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Đơn Vị Sửa Chữa'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Thông Tin Máy'),
                        ],
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => 'suggest_maintenance',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/moderation_maintenance',
                            'key' => 'moderation_maintenance',
                            'is_permission' => true,
                            'name' => lang('Phiếu Điều Độ Công Việc Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/request_calibration',
                            'key' => 'request_calibration',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Hiệu Chuẩn Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Vật Tư Thay Thế'),
                        ],
                    ]
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'other',
                    'name' => lang('Thông tin khác'),
                    'sub' => [
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin),
                            'name' => lang('Loại Bàn Giao'),
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin),
                            'name' => lang('Tiêu Chí Bàn Giao'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Loại Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Nhóm Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin),
                            'name' => lang('Phiếu Kiểm Kê'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Phiếu Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'name' => lang('Loại Hình In'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble',
                            'key' => 'trouble',
                            'name' => lang('Loại Sự Cố'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble/category_problem',
                            'key' => 'category_problem',
                            'is_permission' => true,
                            'name' => lang('Nhóm Sự Cố'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'name' => lang('Loại Chi Phí'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'is_permission' => true,
                            'name' => lang('Nhóm Chi Phí'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/allowance_reduce',
                            'key' => 'allowance_reduce',
                            'name' => lang('Phụ Cấp - Giảm Trừ'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/utilities/media',
                            'key' => 'utilities',
                            'name' => lang('Thư Viện'),
                            'is_permission' => true,
                            'is_settings' => 0,
                        ],
                    ]
                ],
                [
                    'key' => 'it',
                    'name' => lang('IT'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Nhóm Phần Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Mục Linh Kiện Thay Thế'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Mục Bảo Dưỡng Phần Cứng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Mục Bảo Dưỡng Phẩn Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Mục Trang Thiết Bị'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Trang Thiết Bị Sử Dụng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Đơn Vị Sửa Chữa'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Yêu Cầu Sửa Chữa'),
                            'is_permission' => true,
                        ],
                    ]
                ],
                [
                    'key' => 'depart_manufactures',
                    'name' => lang('Phòng Sản Xuất'),
                    'sub' => [
                        [
                            'link' => 'admin/products/category_stages',
                            'key' => 'category__stages',
                            'name' => lang('Nhóm công đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('Công Đoạn Đặc Biệt'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'name' => lang('Loại Hình In'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Mã Lộ Trình'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Đơn Vị Gia Công'),
                        ],
                    ]
                ],
                [
                    'key' => 'depart_quality',
                    'name' => lang('Phòng Chất Lượng'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Đơn Vị Đóng Gói Sản Phẩm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chuẩn Đóng Gói'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Đơn Vị Đóng Gói NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chuẩn Đóng Gói NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phương Tiện'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Thiết bị Đo Kiểm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Đơn Vị Đo Kiểm'),
                        ],
                    ]
                ]
            ]
        ];

        $menu['category']['items']['crm'] = [
            'name' => lang('II. CRM - Quản Lý Khách Hàng'),
            'sub_name' => lang('Customer Relationship Management'),
            'sub_menu_one' => [
                [
                    'key' => 'cs',
                    'name' => lang('Customer Service - Chăm Sóc Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_support/customer_order',
                            'key' => 'coupon_support',
                            'is_permission' => true,
                            'name' => lang('Phiếu Chăm Sóc Khách Hàng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'customer',
                    'name' => lang('Khách hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Công Đoạn Theo Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Sản Phẩm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Loại Khách Hàng'),
                        ],
                    ],
                ],
            ]
        ];

        $menu['category']['items']['scc'] = [
            'name' => lang('III. SCC - Kiểm Soát Chuỗi Cung Ứng'),
            'sub_name' => lang('Supplier Chain Control'),
            'sub_menu_one' => [
                [
                    'key' => 'purchaser',
                    'name' => lang('PM (Purchasing Management)'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_evaluate?type=supplier',
                            'key' => 'suggest_evaluate?type=supplier',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Đánh Giá Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/service',
                            'key' => 'service',
                            'is_permission' => checkPermission('service', $staff_id, $is_admin),
                            'name' => lang('Đơn Đặt Dịch Vụ (SV)'),
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'suppliers',
                    'name' => lang('Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Loại Nhà Cung Cấp'),
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => []
        ];

        $menu['category']['items']['erp'] = [
            'name' => lang('IV. ERP - Hoạch Định Nguồn Lực DN'),
            'sub_name' => lang('Enterprise Resource Planning'),
            'is_not_click' => 1,
        ];

        $menu['category']['items']['office_management'] = [
            'name' => lang('1. Office Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'hr',
                    'name' => lang('HR(Human Resources)'),
                    'sub' => [
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Chi Nhánh'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/category_tasks',
                            'key' => 'category_tasks',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('Danh Sách Mô Tả Vị Trí Công Việc'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('Trang Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/kpi/list',
                            'key' => 'kpi',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách KPI'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Quyết Định'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Hợp Đồng'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Giấy Phép'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Chứng Nhận'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'bgptm',
                    'name' => lang('Báo Giá Phát Triển Mẫu'),
                    'sub' => [
                        [
                            'link' => 'admin/products/category',
                            'key' => 'products__category',
                            'is_permission' => checkPermission('products_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn Sản Phẩm'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Tính Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/quotation_request',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('Phiếu Yêu Cầu Báo Giá'),
                        ],
                        [
                            'link' => 'admin/moderation_quotes',
                            'key' => 'moderation_quotes',
                            'is_permission' => true,
                            'name' => lang('Phiếu Điều Độ Công Việc Báo Giá'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Tính Giá'),
                        ],
                        [
                            'link' => 'admin/request_template',
                            'key' => 'request_template',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Phát Triển Mẫu'),
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => 'moderation_template',
                            'is_permission' => true,
                            'name' => lang('Phiếu Điều Độ Công Việc Phát Triển Mẫu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiếu Phát Triển Mẫu'),
                        ],
                        // [
                        //     'link' => 'admin/products/stages',
                        //     'key' => 'products__stages',
                        //     'name' => lang('Danh sách Công Đoạn'),
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        // ]
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories__machines',
                        //     'name' => lang('Danh Sách Thông Tin Thiết Bị'),
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/print_type',
                        //     'key' => 'print_type',
                        //     'is_permission' => true,
                        //     'name' => lang('Danh Sách Loại Hình In'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/clients',
                        //     'key' => 'clients',
                        //     'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                        //     'name' => lang('Danh Sách Thông Tin Khách Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/products',
                        //     'key' => 'products',
                        //     'is_permission' => checkPermission('products', $staff_id, $is_admin),
                        //     'name' => lang('Danh Sách Thông Tin Sản Phẩm'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang('Danh Sách Thông Tin Nhà Cung Cấp'),
                        // ],
                        // [
                        //     'link' => 'admin/items',
                        //     'key' => 'items',
                        //     'is_permission' => checkPermission('items', $staff_id, $is_admin),
                        //     'name' => lang('Danh Sách Thông Tin NPL'),
                        // ],
                    ],
                ],
                [
                    'key' => 'orders_delivery',
                    'name' => lang('Đơn Hàng'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Nhóm Đơn Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Loại Đơn Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phân Loại Đơn Hàng'),
                        ],
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/purchase_order_request',
                            'key' => 'purchase_order_request',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/moderation_order',
                            'key' => 'moderation_order',
                            'is_permission' => true,
                            'name' => lang('Phiếu Điều Độ Công Việc Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/orders',
                            'key' => 'orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin),
                            'name' => lang('Phiếu Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/request_client_complaints',
                            'key' => 'request_client_complaints',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('Phiều Điều Độ Công Việc Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=customer',
                            'key' => 'suggest_evaluate?type=customer',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá Khách Hàng'),
                        ],
                        // [
                        //     'link' => 'admin/orders/import_orders',
                        //     'key' => 'import_orders',
                        //     'is_permission' => checkPermission('orders', $staff_id, $is_admin, 'create'),
                        //     'name' => lang('Import Tạo Đơn Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/orders/information',
                        //     'key' => 'orders__information',
                        //     'is_permission' => true,
                        //     'name' => lang('Thống Kê Đơn Hàng'),
                        // ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'purchase',
                    'name' => lang('Thu Mua'),
                    'sub' => [
                        [
                            'link' => 'admin/items/category',
                            'key' => 'items__category',
                            'is_permission' => checkPermission('items_category', $staff_id, $is_admin),
                            'name' => lang('Nhóm NPL'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_settings' => 1,
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Chủng Loại NPL'),
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('Tiêu Chuẩn NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Kích Thước NPL'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'name' => lang('Đơn Vị Tính NPL'),
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Công Thức Quy Đổi NPL'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('Nguyên Phụ Liệu'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('Nguyên Phụ Liệu Đặc Biệt'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=supplier',
                            'key' => 'suggest_evaluate?type=supplier',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Yêu Cầu Đánh Giá NCC'),
                        ],
                        [
                            'link' => 'admin/supplier_evaluate',
                            'key' => 'supplier_evaluate',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Đánh Giá NCC'),
                        ],
                        [
                            'link' => 'admin/supplier_evaluate',
                            'key' => 'supplier_evaluate',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Đánh Giá NCC'),
                        ],
                        [
                            'link' => 'admin/purchases/synthetic_purchase',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('Phiếu Yêu Cầu Mua Hàng (PR)'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Điều Độ Công Việc Đơn Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/purchase_order',
                            'key' => 'purchase_order',
                            'is_permission' => checkPermission('purchase_order', $staff_id, $is_admin),
                            'name' => lang('Đơn Mua Hàng (PO)'),
                        ],
                    ],
                ],
                [
                    'key' => 'debt_orders',
                    'name' => lang('Công Nợ Bán'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_invoice/synthetic_coupon_invoice',
                            'key' => 'synthetic_coupon_invoice',
                            'is_permission' => checkPermission('coupon_invoice', $staff_id, $is_admin),
                            'name' => lang('dt_coupon_invoice'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/debt_clients',
                            'key' => 'debt_clients',
                            'is_permission' => checkPermission('debt_clients', $staff_id, $is_admin),
                            'name' => lang('Công Nợ Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips_coupon',
                            'key' => 'other_payslips_coupon',
                            'is_permission' => checkPermission('other_payslips_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thu Khác'),
                        ],
                    ],
                ],
                [
                    'key' => 'debt_purchases',
                    'name' => lang('Công Nợ Mua'),
                    'sub' => [
                        [
                            'link' => 'admin/purchases/synthetic_purchase',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('dt_purchases'),
                        ],
                        [
                            'link' => 'admin/import/synthetic_import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('dt_import'),
                        ],
                        [
                            'link' => 'admin/import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('Nhập Kho'),
                        ],
                        [
                            'link' => 'admin/return_suppliers',
                            'key' => 'return_suppliers',
                            'is_permission' => checkPermission('return_suppliers', $staff_id, $is_admin),
                            'name' => lang('Trả lại hàng mua'),
                        ],
                        [
                            'link' => 'admin/purchase_invoice/synthetic_invoice',
                            'key' => 'purchase_invoice',
                            'is_permission' => checkPermission('purchase_invoice', $staff_id, $is_admin),
                            'name' => lang('dt_purchase_invoice'),
                        ],
                        [
                            'link' => 'admin/pay_slip/synthetic_payslip',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('dt_pay_slip'),
                        ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => 'debt_suppliers',
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                            'name' => lang('Công Nợ Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/suggestion',
                            'key' => 'suggestion',
                            'is_permission' => checkPermission('suggestion', $staff_id, $is_admin),
                            'name' => lang('Phiếu Đề Xuất Tài Chính'),
                        ],
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => true,
                            'name' => lang('Phiếu Tạm Ứng'),
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('ch_other_payslips'),
                        ],
                        [
                            'link' => 'admin/other_payslips/other_payslip_manage',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('dt_other_payslips'),
                        ],
                        [
                            'link' => 'admin/spending_plan',
                            'key' => 'spending_plan',
                            'is_permission' => checkPermission('spending_plan', $staff_id, $is_admin),
                            'name' => lang('spending_plan'),
                        ],
                    ],
                ]
            ],
            'sub_menu_four' => [
                [
                    'key' => 'work',
                    'name' => lang('Quản lý nghiệp vụ - công  việc (Work+)'),
                    'sub' => [
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => 'internal_proposal',
                            'is_permission' => checkPermission('internal_proposal', $staff_id, $is_admin),
                            'name' => lang('Đề Xuất Nội Bộ'),
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'delivery_records',
                            'is_permission' => checkPermission('delivery_records', $staff_id, $is_admin),
                            'name' => lang('Bàn Giao Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Phiếu Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks?kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Công Việc Kanban'),
                        ],
                        [
                            'link' => 'admin/tasks/calendar_pod',
                            'key' => 'tasks__calendar_pod',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('Lịch Công Việc'),
                        ],
                        [
                            'link' => 'admin/gantt',
                            'key' => 'gantt',
                            'is_permission' => checkPermission('gantt', $staff_id, $is_admin),
                            'name' => lang('Sơ Đồ Gantt'),
                        ],
                        [
                            'link' => 'admin/work_plan/handling',
                            'key' => 'work_plan',
                            'is_permission' => checkPermission('work_plan', $staff_id, $is_admin),
                            'name' => lang('Kế hoạch công việc'),
                        ],
                    ]
                ],
                [
                    'key' => 'personnel',
                    'name' => lang('Hồ Sơ Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('Danh sách nhân viên'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                            'name' => lang('Chức Vụ'),
                        ],
                    ]
                ],
                [
                    'key' => 'timekeeping',
                    'name' => lang('Chấm Công'),
                    'sub' => [
                        [
                            'link' => 'admin/paid_holidays/paid_holiday_leave',
                            'key' => 'paid_holidays__paid_holiday_leave',
                            'is_permission' => true,
                            'name' => lang('Đơn xin nghĩ phép'),
                        ],
                        [
                            'link' => 'admin/salary/timekeeping',
                            'key' => 'salary__timekeeping',
                            'is_permission' => checkPermission('timekeeping', $staff_id, $is_admin),
                            'name' => lang('Chi tiết giờ công'),
                        ],
                        [
                            'link' => 'admin/salary/dashboard_timekeeping',
                            'key' => 'salary__dashboard_timekeeping',
                            'is_permission' => checkPermission('dashboard_timekeeping', $staff_id, $is_admin),
                            'name' => lang('Tổng hợp giờ công'),
                        ],
                    ]
                ],
                [
                    'key' => 'overtime',
                    'name' => lang('Tăng ca'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_overtime',
                            'key' => 'suggest_overtime',
                            'is_permission' => true,
                            'name' => lang('Phiếu Đề Xuất Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => true,
                            'name' => lang('Tăng Ca Tháng'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                            'key' => 'business_fee_other__report_business_fee_other_overtime',
                            'name' => lang('Thống Kê Giờ Tăng Ca'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_calculate',
                            'key' => 'business_fee_other__business_fee_other_calculate',
                            'name' => lang('Bảng Tính Tăng Ca'),
                            'is_permission' => true,
                        ],
                    ]
                ],
            ],
        ];

        $menu['category']['items']['production_management'] = [
            'name' => lang('2. Production Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'plan_manufactures',
                    'name' => lang('Kế Hoạch Sản Xuất'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Số Lượng Tồn Kho Cho Phép Sản Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Số Lượng Tồn Kho Cho Phép NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/products/bom',
                            'key' => 'products',
                            'is_permission' => checkPermission('products_bom', $staff_id, $is_admin),
                            'name' => lang('Định Mức NPL - BOM'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Định Mức Thời Gian Canh Bài'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Định Mức NPL Canh Bài'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Nhóm Kế Hoạch'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Loại Kế Hoạch'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_outsource',
                            'key' => 'suggest_outsource',
                            'name' => lang('Phiếu Yêu Cầu Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_outsource',
                            'key' => 'moderation_outsource',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_overtime',
                            'key' => 'request_overtime',
                            'is_permission' => true,
                            'name' => lang('Phiếu Yêu Cầu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => true,
                            'name' => lang('Phiếu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/moderation_overtime',
                            'key' => 'moderation_overtime',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_control_vehicle',
                            'key' => 'suggest_control_vehicle',
                            'name' => lang('Phiếu Yêu Cầu Điều Xe'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admn/request_graft_size',
                            'key' => 'request_graft_size',
                            'name' => lang('Phiếu Yêu Cầu Ghép Size'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_graft_size',
                            'key' => 'moderation_graft_size',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Ghép Size'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_printed_page_layout',
                            'key' => 'request_printed_page_layout',
                            'name' => lang('Phiếu Yêu Cầu Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_printed_page_layout',
                            'key' => 'moderation_printed_page_layout',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/purchase_request_zinc',
                            'key' => 'purchase_request_zinc',
                            'name' => lang('Phiếu Yêu Cầu Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_purchase_zinc',
                            'key' => 'moderation_purchase_zinc',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_place_the_tank_mold',
                            'key' => 'request_place_the_tank_mold',
                            'name' => lang('Phiếu Yêu Cầu Đặt Khuôn Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_place_the_tank_mold',
                            'key' => 'moderation_place_the_tank_mold',
                            'name' => lang('Phiếu Điều Độ Công Đoạn Đặt Khuôn Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Yêu Cầu Mở Lệnh SX'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_production_order',
                            'key' => 'moderation_production_order',
                            'name' => lang('Phiếu Điều Độ Công Việc Mở Lệnh SX'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/purchases',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('Phiếu Yêu Cầu Mua NPL'),
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=1',
                            'key' => 'suggest_plan_purchase?type=1',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Mua NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=1',
                            'key' => 'suggest_plan_purchase?type=2',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Mua Vật Tư'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=3',
                            'key' => 'suggest_plan_purchase?type=3',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Mua Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_evaluate',
                            'key' => 'suggest_plan_evaluate',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Đánh Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_overtime',
                            'key' => 'suggest_plan_overtime',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_outsource',
                            'key' => 'suggest_plan_outsource',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_recruitment',
                            'key' => 'suggest_plan_recruitment',
                            'name' => lang('Phiếu Yêu Cầu Kế Hoạch Tuyển Dụng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Báo Cáo Hàng Phế'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'plan',
                    'name' => lang('PLAN(Production Planning)'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('Tổng Hợp Sản Xuất'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('Kế Hoạch Điều Độ'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Kế Hoạch Điều Động'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('Phiếu Xuất Tồn Kho(NPL/TP)'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/manufacture/index',
                            'key' => 'manufacture__index',
                            'name' => lang('Phiếu Xả Khổ Giấy'),
                            'is_permission' => checkPermission('manufacture', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_zinc',
                            'key' => 'synthetic_zinc',
                            'name' => lang('Tổng Hợp Xuất Kẽm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('DS Đơn Vị Gia Công'),
                        ],
                    ],
                ],
                [
                    'key' => 'plan_npl',
                    'name' => lang('KẾ HOẠCH NPL'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_plan',
                            'key' => 'manufactures__productions_plan',
                            'name' => lang('Kế Hoạch NPL'),
                            'is_permission' => checkPermission('manufactures_productions_plan', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => 'reports__purchase',
                            'name' => lang('Kế Hoạch Điều Động Về NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/manufactures/productions_plan_purchase',
                            'key' => 'manufactures__productions_plan_purchase',
                            'name' => lang('Tổng Hợp Mua Hàng'),
                            'is_permission' => true
                        ]
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'manufactures',
                    'name' => lang('SẢN XUẤT'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('Lệnh Sản Xuất Tổng'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/order_production_details',
                            'key' => 'manufactures__order_production_details',
                            'name' => lang('Lệnh Sản Xuất Chi Tiết'),
                            'is_permission' => checkPermission('manufactures_order_production_details', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_stage',
                            'key' => 'synthetic_stage',
                            'name' => lang('Lệnh Sản Xuất Theo Công Đoạn'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                    ]
                ],
                [
                    'key' => 'qa',
                    'name' => lang('QA(Quality Assurance )'),
                    'sub' => [
                        [
                            'link' => 'admin/quality_control/check_quality',
                            'key' => 'quality_control__check_quality',
                            'name' => lang('Kiểm Tra Chất Lượng'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/category_errors',
                            'key' => 'quality_control__category_errors',
                            'name' => lang('Danh Mục Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/detail_errors',
                            'key' => 'quality_control__detail_errors',
                            'name' => lang('Chi Tiết Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('Báo Cáo Không Phù Hợp'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Sách Thiết Bị Đo Kiểm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Danh Sách Tiêu Chuẩn Chất Lượng SP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Tiêu Chí/Quy Trình Bàn Giao'),
                            'is_permission' => 1,
                        ],
                    ]
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'mro',
                    'name' => lang('MRO(Maintenance Repair Operation)'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'is_permission' => true,
                            'name' => lang('Danh Sách Thiết Bị Máy Móc'),

                        ],
                        [
                            'link' => 'admin/maintenance/category',
                            'key' => 'maintenance__category',
                            'name' => lang('Hạng Mục Bảo Trì'),
                            'is_permission' => checkPermission('category_maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance/calendar',
                            'key' => 'maintenance__calendar',
                            'name' => lang('Lịch Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('Phiếu Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Quy Trình Bảo Trì'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'name' => lang('Danh Sách Khấu Hao'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'name' => lang('Danh Sách Định Mức Thiết Bị'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('DS Hiệu Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('DS Đơn Vị Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'prop',
                    'name' => lang('PROP(Production Process)'),
                    'sub' => [
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'name' => lang('Loại Bàn Giao'),
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'name' => lang('Tiêu Chí Bàn Giao'),
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'hand_over__delivery_records',
                            'name' => lang('Biên Bản Bàn Giao'),
                            'is_permission' => checkPermission('delivery_records', $staff_id, $is_admin)
                        ]
                    ]
                ],
                [
                    'key' => 'releases',
                    'name' => lang('GIAO HÀNG THANH TOÁN'),
                    'sub' => [
                        [
                            'link' => 'admin/releases/synthetic_releases',
                            'key' => 'synthetic_releases',
                            'is_permission' => checkPermission('releases_deliveries', $staff_id, $is_admin),
                            'name' => lang('dt_delivery'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chí Giao Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Tiêu Chuẩn Đóng Gói'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('Phí Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('Phiếu Thanh Toán'),
                        ],
                        [
                            'link' => 'admin/clients/all_shipping',
                            'key' => 'clients__all_shipping',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Địa Chỉ Giao Hàng'),
                        ],
                    ]
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'warehouse',
                    'name' => lang('Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/units',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Vào Kho Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/units',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Đơn Vị Vào Kho NPL'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warning_warehouse',
                            'key' => 'warning_warehouse',
                            'is_permission' => true,
                            'name' => lang('Thời Gian Lưu Kho NPL'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('Kho'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('Thẻ Kho'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Loại Kiểm Kê'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Nhóm Kiểm Kê'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Yêu Cầu Kiểm Kê'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Điều Độ Công Việc Kiểm Kê'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'name' => lang('Phiếu Kiểm Kê'),
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/stock_out_request',
                            'key' => 'stock_out_request',
                            'name' => lang('Phiếu Yêu Cầu Xuất Kho NPL Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/moderation_stock_out',
                            'key' => 'moderation_stock_out',
                            'name' => lang('Phiếu Điều Độ Xuất Kho NPL Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('Phiếu Xuất Kho NPL Tồn'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/request_export_products',
                            'key' => 'request_export_products',
                            'name' => lang('Phiếu Yêu Cầu Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/moderation_export_products',
                            'key' => 'moderation_export_products',
                            'name' => lang('Phiếu Điều Độ Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('Phiếu Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_purchase_npl',
                            'key' => 'suggest_purchase_npl',
                            'name' => lang('Phiếu Yêu Cầu Nhập Kho NPL'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Điều Độ Công Việc Nhập Kho NPL'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('Phiếu Nhập Kho NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Yêu Cầu Nhập Kho TP'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('Phiếu Điều Độ Công Việc Nhập Kho TP'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/purchase_products',
                            'key' => 'stock__purchase_products',
                            'name' => lang('Nhập Kho Thành Phẩm'),
                            'is_permission' => checkPermission('stock_purchase_products', $staff_id, $is_admin)
                        ],
                    ]
                ],
                [
                    'key' => 'warehouse',
                    'name' => lang('KHO HÀNG(Warehouse)'),
                    'sub' => [
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'name' => lang('Kiểm Kê'),
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('DS Kho Hàng'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse/localtion',
                            'key' => 'warehouse__localtion',
                            'name' => lang('Vị Trí Kho'),
                            'is_permission' => checkPermission('warehouse_localtion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer',
                            'key' => 'transfer',
                            'name' => lang('Chuyển Kho'),
                            'is_permission' => checkPermission('transfer', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer_bussiness',
                            'key' => 'transfer_bussiness',
                            'name' => lang('Giữ Kho (Trên Chuyền)'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('Xuất Kho NPL Tồn'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/purchase_products',
                            'key' => 'stock__purchase_products',
                            'name' => lang('Nhập Kho Thành Phẩm'),
                            'is_permission' => checkPermission('stock_purchase_products', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/stock/purchase_internal',
                            'key' => 'stock__purchase_internal',
                            'name' => lang('Thu hồi NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/export_different',
                            'key' => 'stock__export_different',
                            'name' => lang('Xuất Kho Khác(Khẩn/Gấp)'),
                            'is_permission' => checkPermission('export_different', $staff_id, $is_admin)
                        ],
                    ]
                ]
            ]
        ];

        $menu['category']['items']['reports'] = [
            'name' => lang('3. Report'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'report',
                    'name' => lang('Báo Cáo(Report)'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('Báo Cáo Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('Báo Cáo Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => 'reports__sales',
                            'name' => lang('Báo Cáo Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer',
                            'key' => 'reports__debt_customer',
                            'name' => lang('Công Nợ Phải Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => 'reports__fund_balance',
                            'name' => lang('Tồn Quỹ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('Tồn Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('Quản Lý Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/expenses_vs_income',
                            'key' => 'reports__expenses_vs_income',
                            'name' => lang('Quản Lý Lợi Nhuận'),
                            'is_permission' => checkPermission('expenses_vs_income', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'report',
                    'name' => lang('Báo Cáo(Report)'),
                    'sub' => [
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('Phiếu Báo Cáo'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/production_report/incident_tracking',
                            'key' => 'incident_tracking',
                            'name' => lang('Phiếu Theo Dõi Sự Cố'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ],
        ];

        $menu['category']['items']['kpi'] = [
            'name' => lang('V. Đánh Giá KPI Tháng/Năm'),
            'link' => 'kpi/staff_kpi_evaluation',
            // 'sub_menu_one' => [
            //     [
            //         'key' => 'customer',
            //         'name' => lang('Khách hàng'),
            //         'sub' => [
            //             [
            //                 'link' => '',
            //                 'key' => '',
            //                 'name' => lang('Nhóm khách hàng'),
            //             ],
            //             [
            //                 'link' => '',
            //                 'key' => '',
            //                 'name' => lang('Phân loại khách hàng'),
            //             ],
            //         ],
            //     ]
            // ]
        ];

        $menu['category']['items']['power_bi'] = [
            'name' => lang('VI. Dashboard Power BI'),
            'sub_menu_one' => [
                [
                    'key' => 'customer',
                    'name' => lang('Dashbood Power BI'),
                    'sub' => [
                        [
                            'link' => 'admin/report_dashboard/dashboard_quotes',
                            'key' => 'dashboard__quotes',
                            'name' => lang('DASHBOARD Báo Giá Phát Triển Mẫu'),
                            'is_permission' => checkPermission('dashboard_quotes', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_revenue',
                            'key' => 'dashboard__revenue',
                            'name' => lang('DASHBOARD Doanh Thu'),
                            'is_permission' => checkPermission('dashboard_revenue', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_cost',
                            'key' => 'dashboard__cost',
                            'name' => lang('DASHBOARD Chi Phí'),
                            'is_permission' => checkPermission('dashboard_cost', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_stock',
                            'key' => 'dashboard__stock',
                            'name' => lang('DASHBOARD Tồn Kho'),
                            'is_permission' => checkPermission('dashboard_stock', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_manufactures',
                            'key' => 'dashboard__manufactures',
                            'name' => lang('DASHBOARD Sản Xuất'),
                            'is_permission' => checkPermission('dashboard_manufactures', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_task',
                            'key' => 'dashboard__task',
                            'name' => lang('DASHBOARD Công Việc'),
                            'is_permission' => checkPermission('dashboard_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_personnel',
                            'key' => 'dashboard__personnel',
                            'name' => lang('DASHBOARD Hành Chính - Nhân Sự'),
                            'is_permission' => checkPermission('dashboard_personnel', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_purchases',
                            'key' => 'dashboard__purchases',
                            'name' => lang('DASHBOARD Mua Hàng'),
                            'is_permission' => checkPermission('dashboard_purchases', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_business_results',
                            'key' => 'dashboard__business_results',
                            'name' => lang('DASHBOARD Kết Quả Kinh Doanh'),
                            'is_permission' => checkPermission('dashboard_business_results', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ]
        ];

        return $menu;
    }
}

if (!function_exists('getMenuDashboardBK18012024')) {
    function getMenuDashboardBK18012024()
    {
        $CI = &get_instance();
        $staff_id = get_staff_user_id();
        $is_admin = is_admin($staff_id);
        $menu = [];
        $menu['category']['name'] = lang('Hạng mục');

        $menu['category']['items']['created_group'] = [
            'name' => lang('I. Data - Danh Mục'),
            'sub_name' => lang('MASTER DATA'),
            'sub_menu_one' => [
                [
                    'key' => 'customers',
                    'name' => lang('A. Thông Tin Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients/brand',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('1. Mã Brand'),
                        ],
                        [
                            'link' => 'admin/clients/status_client',
                            'key' => 'clients__status_client',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('2. Phân Loại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients__clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('3. Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/categories_other/standard_customer',
                            'key' => 'standard_customer',
                            'is_permission' => true,
                            'name' => lang('4. Tiêu Chuẩn Khách Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('5. Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('6. Chiết Khấu'),
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('7. Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/contracts_sales',
                            'key' => 'contracts_sales',
                            'is_permission' => checkPermission('contracts_sales', $staff_id, $is_admin),
                            'name' => lang('8. Hợp Đồng Bán'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('9. Thuế VAT'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('10. Bảng Giá Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('11. Bảng Giá Sản Phẩm'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'suppliers',
                    'name' => lang('B. Thông Tin Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('1. Nhóm Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/supplier_classify',
                            'key' => 'suppliers',
                            'is_permission' => 1,
                            'name' => lang('2. Phân Loại Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('3. Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/categories_other/standard_supplier',
                            'key' => 'standard_supplier',
                            'is_permission' => true,
                            'name' => lang('4. Tiêu Chuẩn Nhà Cung Cấp'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/certification_supplier',
                            'key' => 'certification_supplier',
                            'is_permission' => true,
                            'name' => lang('5. Chứng Nhận'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/discount_supplier',
                            'key' => 'discount_supplier',
                            'is_permission' => true,
                            'name' => lang('6. Chiết Khấu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/currencies',
                            'key' => 'currencies',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('7. Tiền Tệ'),
                        ],
                        [
                            'link' => 'admin/contracts_supplier',
                            'key' => 'contracts_supplier',
                            'is_permission' => 1,
                            'name' => lang('8. Hợp Đồng Mua'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('9. Thuế VAT'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('10. Bảng Giá NPL'),
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'manager',
                    'name' => lang('C. Quản Lý Văn Phòng<br>1. Phòng Kế Hoạch'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => 'quotes',
                            'is_permission' => true,
                            'name' => lang('1.1. Báo Giá Phát Triển Mẫu'),
                            'backgound' => '#FFEDDD',
                        ],
                        [
                            'link' => 'admin/products/category',
                            'key' => 'products__category',
                            'is_permission' => checkPermission('products_category', $staff_id, $is_admin),
                            'name' => lang('1.1.1. Nhóm Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.1.2. Chủng Loại Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('1.1.3. Tiêu Chuẩn Sản Phẩm'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size?type_title=products',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.1.4. Kích Thước Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.1.5. Đơn Vị Tính Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('1.1.6. Sản Phẩm'),
                        ],
                        [
                            'link' => '',
                            'key' => 'orders',
                            'is_permission' => true,
                            'name' => lang('1.2. Đơn Hàng'),
                            'backgound' => '#FFEDDD',
                        ],
                        [
                            'link' => 'admin/categories_other/type_orders_items',
                            'key' => 'type_orders_items',
                            'is_permission' => true,
                            'name' => lang('1.2.1. Nhóm Đơn Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/type_orders',
                            'key' => 'type_orders',
                            'is_permission' => true,
                            'name' => lang('1.2.2. Loại Đơn Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/classify_orders',
                            'key' => 'classify_orders',
                            'is_permission' => true,
                            'name' => lang('1.2.3. Phân Loại Đơn Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('1.2.4. Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => 'manufactures__list_manufactures',
                            'name' => lang('1.3. Kế Hoạch Sản Xuất'),
                            'is_permission' => true,
                            'backgound' => '#FFEDDD',
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.3.1. Số Lượng Tồn Kho Cho Phép Sản Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.3.2. Số Lượng Tồn Kho Cho Phép NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/products/bom',
                            'key' => 'products',
                            'is_permission' => checkPermission('products_bom', $staff_id, $is_admin),
                            'name' => lang('1.3.3. Định Mức NPL - BOM'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.3.4. Định Mức Thời Gian Canh Bài'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.3.5. Định Mức NPL Canh Bài'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.3.6. Nhóm Đính Mức SP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/planning_group_manu',
                            'key' => 'planning_group_manu',
                            'name' => lang('1.3.7. Nhóm Kế Hoạch'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/type_plan_manu',
                            'key' => 'type_plan_manu',
                            'name' => lang('1.3.8. Loại Kế Hoạch'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.4. Thu Mua'),
                            'is_permission' => 1,
                            'backgound' => '#FFEDDD',
                        ],
                        [
                            'link' => 'admin/items/category',
                            'key' => 'items__category',
                            'is_permission' => checkPermission('items_category', $staff_id, $is_admin),
                            'name' => lang('1.4.1. Nhóm NPL'),
                        ],
                        [
                            'link' => 'admin/species',
                            'key' => 'species',
                            'is_settings' => 1,
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.4.2. Chủng Loại NPL'),
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('1.4.3. Tiêu Chuẩn NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/size',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.4.4. Kích Thước NPL'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=materials',
                            'key' => 'units',
                            'name' => lang('1.4.5. Đơn Vị Tính NPL'),
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/conversion_formula_npl',
                            'key' => 'conversion_formula_npl',
                            'name' => lang('1.4.6. Công Thức Quy Đổi NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('1.4.7. Nguyên Phụ Liệu'),
                        ],
                        [
                            'link' => 'admin/categories_other/materials_special',
                            'key' => 'materials_special',
                            'is_permission' => true,
                            'name' => lang('1.4.8. Nguyên Phụ Liệu Đặc Biệt'),
                        ],
                        [
                            'link' => '',
                            'key' => 'warehouse',
                            'is_permission' => '',
                            'name' => lang('1.5. Kho'),
                            'backgound' => '#FFEDDD',
                        ],
                        [
                            'link' => 'admin/units',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1.5.1. Đơn Vị Vào Kho Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/storage_time_sp',
                            'key' => 'storage_time_sp',
                            'is_permission' => true,
                            'name' => lang('1.5.2. Thời Gian Lưu Kho Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/unit_warehouse_npl',
                            'key' => 'unit_warehouse_npl',
                            'is_permission' => 1,
                            'name' => lang('1.5.3. Đơn Vị Vào Kho NPL'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/storage_time_npl',
                            'key' => 'storage_time_npl',
                            'is_permission' => true,
                            'name' => lang('1.5.4. Thời Gian Lưu Kho NPL'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('1.5.5. Kho'),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('1.5.6. Thẻ Kho'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/categories_other/inventory_type_warehouse',
                            'key' => 'inventory_type_warehouse',
                            'name' => lang('1.5.7. Loại Kiểm Kê'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/inventory_group_warehouse',
                            'key' => 'inventory_group_warehouse',
                            'name' => lang('1.5.8. Nhóm Kiểm Kê'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'manager',
                    'name' => lang('2. Phòng Sale/Marketing'),
                    'sub' => [
                        [
                            'link' => 'admin/categories_other/export_code',
                            'key' => 'export_code',
                            'is_permission' => true,
                            'name' => lang('2.1. Mã Số Xuất Khẩu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/import_code',
                            'key' => 'import_code',
                            'is_permission' => true,
                            'name' => lang('2.2. Mã Số Nhập Khẩu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('2.3. Biểu Thuế'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('2.4. Báo Giá Vận Chuyển'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('2.5. Danh Mục Chứng Từ Xuất Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('2.6. Danh Mục Chứng Từ Nhập Khẩu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('2.7. Mã Số Bưu Điện'),
                        ],
                    ],
                ],
                [
                    'key' => 'manager',
                    'name' => lang('3. Phòng Hành Chính Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over_task',
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin),
                            'name' => lang('3.1. Tiêu Chí Bàn Giao Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/   ',
                            'key' => 'cleaning_5s',
                            'is_permission' => true,
                            'name' => lang('3.2. Khu Vực Vệ Sinh ATLĐ-5S'),
                            'is_settings' => true,
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('3.3. Giấy Phép'),
                        ],
                        [
                            'link' => 'admin/categories_other/certification_group',
                            'key' => 'certification_group',
                            'is_permission' => true,
                            'name' => lang('3.4. Nhóm Chứng Nhận'),
                            'is_settings' => true,
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin),
                            'name' => lang('3.5. Loại Bàn Giao'),
                        ],
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('3.6. Văn Phòng - Chi Nhánh'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.7. Hội Đồng'),
                        ],
                        [
                            'link' => 'admin/board',
                            'key' => 'board',
                            'is_permission' => true,
                            'name' => lang('3.8. Ban'),
                        ],
                        [
                            'link' => 'admin/block',
                            'key' => 'block',
                            'is_permission' => true,
                            'name' => lang('3.9. Khối'),
                        ],
                        [
                            'link' => 'admin/room',
                            'key' => 'room',
                            'is_permission' => true,
                            'name' => lang('3.10. Phòng'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => true,
                            'name' => lang('3.11. Bộ Phận'),
                        ],
                        [
                            'link' => 'admin/nest',
                            'key' => 'nest',
                            'is_permission' => true,
                            'name' => lang('3.12. Tổ'),
                        ],
                        [
                            'link' => 'admin/group',
                            'key' => 'group',
                            'is_permission' => true,
                            'name' => lang('3.13. Nhóm'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                            'name' => lang('3.14. Mã Vị Trí'),
                        ],
                        [
                            'link' => 'admin/kpi/detail_task',
                            'key' => 'detail_task',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('3.16. Mô Tả Công Việc'),
                        ],
                        [
                            'link' => 'admin/category_salary/category_permission',
                            'key' => 'category_permission',
                            'is_permission' => true,
                            'name' => lang('3.17. Nhóm Phép'),
                        ],
                        [
                            'link' => 'admin/category_salary/permission',
                            'key' => 'permission',
                            'is_permission' => true,
                            'name' => lang('3.18. Phép'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.19. Nhóm Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('3.20. Lương'),
                        ],
                        [
                            'link' => 'admin/category_salary/coefficient_salary',
                            'key' => 'coefficient_salary',
                            'is_permission' => true,
                            'name' => lang('3.21. Hệ Số Lương Năng Lực'),
                        ],
                        [
                            'link' => 'admin/category_salary/step_salary',
                            'key' => 'step_salary',
                            'is_permission' => true,
                            'name' => lang('3.22. Hệ Số Lương Công Việc'),
                        ],
                        [
                            'link' => 'admin/kpi/category_kpi',
                            'key' => 'category_kpi',
                            'is_permission' => true,
                            'name' => lang('3.23. KPIs'),
                        ],
                        [
                            'link' => 'admin/categories_other/materials_equipment',
                            'key' => 'materials_equipment',
                            'is_permission' => true,
                            'name' => lang('3.24. Vật tư trang thiết bị'),
                            'is_settings' => true
                        ],
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('3.25. Thông Tin Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('3.26. Hợp Đồng Lao Động'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.27. Khám Sức Khỏe'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.28. Mã Quy Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.29. Nhóm Quy Định Chung'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.30. Quy Định Chung'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.31. Nội Quy'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.32. Nhóm Nội Quy'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('3.33. Quyết Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.34. Bảo Hiểm'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('3.35. Nhóm Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('3.36. Loại Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/type_reports',
                            'key' => 'type_reports',
                            'is_permission' => true,
                            'name' => lang('3.37. Nhóm Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/group_reports',
                            'key' => 'group_reports',
                            'is_permission' => true,
                            'name' => lang('3.38. Loại Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/type_error',
                            'key' => 'type_error',
                            'is_permission' => true,
                            'name' => lang('3.39. Nhóm Lỗi'),
                        ],
                        [
                            'link' => 'admin/group_error',
                            'key' => 'group_error',
                            'is_permission' => true,
                            'name' => lang('3.40. Lỗi'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=educate',
                            'key' => 'evaluate_educate',
                            'is_permission' => true,
                            'name' => lang('3.41. Nhóm Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/type_evaluate?type=educate',
                            'key' => 'type_evaluate_educate',
                            'is_permission' => true,
                            'name' => lang('3.42. Loại Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('3.43. Mã Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/category_evaluate',
                            'key' => 'category_evaluate',
                            'is_permission' => true,
                            'name' => lang('3.44. Nhóm Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/type_evaluate?type=evaluate',
                            'key' => 'type_evaluate_evaluate',
                            'is_permission' => true,
                            'name' => lang('3.45. Loại Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/category_complaints',
                            'key' => 'category_complaints',
                            'is_permission' => true,
                            'name' => lang('3.46. Nhóm Khiếu Nại'),
                        ],
                        [
                            'link' => 'admin/category_improve',
                            'key' => 'category_improve',
                            'is_permission' => true,
                            'name' => lang('3.47. Nhóm Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/type_improve',
                            'key' => 'type_improve',
                            'is_permission' => true,
                            'name' => lang('3.48. Loại Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/type_system',
                            'key' => 'type_system',
                            'is_permission' => true,
                            'name' => lang('3.49. Loại Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/category_system',
                            'key' => 'category_system',
                            'is_permission' => true,
                            'name' => lang('3.50. Nhóm Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/system',
                            'key' => 'system',
                            'is_permission' => true,
                            'name' => lang('3.51. Danh Mục Hệ Thống'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.52. Định Mức Thời Gian'),
                        ],
                        [
                            'link' => 'admin/allowance_reduce',
                            'key' => 'allowance_reduce',
                            'name' => lang('3.53. Phụ Cấp Giảm Trừ'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble',
                            'key' => 'trouble',
                            'name' => lang('3.54. Loại Sự Cố'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/trouble/category_problem',
                            'key' => 'category_problem',
                            'is_permission' => true,
                            'name' => lang('3.55. Nhóm Sự Cố'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.56. Định Mức Năng Suất'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('3.57. Định Mức Công Việc'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('3.58. Thiết Bị Sản Xuất'),
                        ],
                        [
                            'link' => 'admin/categories_other/materials_equipment',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('3.59. Thiết Bị Văn Phòng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.60. Danh Mục Quy Trình'),
                        ],
                    ],
                ],
                [
                    'key' => 'department',
                    'name' => lang('4. Phòng Kế Toán'),
                    'sub' => [
                        [
                            'link' => 'admin/units',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('4.1. Đơn Vị Thanh Toán'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'is_permission' => true,
                            'name' => lang('4.2. Khấu Hao'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('4.3. Thời Gian Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'is_permission' => true,
                            'name' => lang('4.4. Nhóm Chi Phí'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'name' => lang('4.5. Loại Chi Phí'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_payslips',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('4.6. Phiếu Yêu Cầu Chi'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('4.7. Khoản Chi'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('4.8. Mục Chi'),
                        ],
                        [
                            'link' => 'admin/paymentmodes',
                            'key' => 'paymentmodes',
                            'name' => lang('4.9. Hình Thức Thanh Toán'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                    ],
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'department',
                    'name' => lang('D. Quản Lý Sản Xuất<br>1. Phòng Kỹ Thuật<br>1.1. Bảo Trì(Maintenace)'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/category_machines',
                            'key' => 'category_machines',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('1.1.1. Nhóm Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('1.1.2. Mã Thiết Bị'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('1.1.3. Nhóm Bảo Trì'),
                        ],
                        [
                            'link' => 'admin/infomation_machines',
                            'key' => 'infomation_machines',
                            'is_permission' => true,
                            'name' => lang('1.1.4. Lý Lịch Máy'),
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('1.1.5. Loại Bảo Dưỡng'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/category_maintenace',
                            'key' => 'category_maintenace',
                            'is_permission' => true,
                            'name' => lang('1.1.6. Nhóm Bảo Dưỡng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('1.1.7. Nhóm Khu Vực'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('1.1.8. Mã Khu Vực'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('1.1.9. Danh Mục Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenace/machines_size',
                            'key' => 'machines_size',
                            'is_permission' => true,
                            'name' => lang('1.1.10. Kích Thước Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenace/operating_size',
                            'key' => 'operating_size',
                            'is_permission' => true,
                            'name' => lang('1.1.11. Kích Thước Vận Hành'),
                        ],
                        [
                            'link' => 'admin/categories_other/unit_machines',
                            'key' => 'unit_machines',
                            'name' => lang('1.1.12. Đơn Vị Tính Thiết Bị'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('1.1.13. Năng Suất Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=1',
                            'key' => 'category_maintenance_calibration?type=1',
                            'is_permission' => true,
                            'name' => lang('1.1.14. Danh Mục Hiệu Chuẩn'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=2',
                            'key' => 'category_maintenance_calibration?type=2',
                            'is_permission' => true,
                            'name' => lang('1.1.15. Danh Mục Bảo Dưỡng Cơ'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=3',
                            'key' => 'category_maintenance_calibration?type=3',
                            'is_permission' => true,
                            'name' => lang('1.1.16. Danh Mục Bảo Dưỡng Điện'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=4',
                            'key' => 'category_maintenance_calibration?type=4',
                            'is_permission' => true,
                            'name' => lang('1.1.17. Danh Mục Bảo Dưỡng Cơ Sở Hạ Tầng'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=5',
                            'key' => 'category_maintenance_calibration?type=5',
                            'is_permission' => true,
                            'name' => lang('1.1.18. Danh Mục Bảo Dưỡng Hơi Khí Nén'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=6',
                            'key' => 'category_maintenance_calibration?type=6',
                            'is_permission' => true,
                            'name' => lang('1.1.19. Danh Mục Nhớt - Mỡ Bò'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=7',
                            'key' => 'category_maintenance_calibration?type=7',
                            'is_permission' => true,
                            'name' => lang('1.1.20. Danh Mục Vật Tư Thay Thế'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('1.1.21. Đơn Vị Sửa Chữa'),
                        ],
                    ],
                ],
                [
                    'key' => 'it',
                    'name' => lang('1.2. IT'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.1. Nhóm Phần Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.2. Danh Mục Linh Kiện Thay Thế'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.3. Danh Mục Bảo Dưỡng Phần Cứng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.4. Danh Mục Bảo Dưỡng Phẩn Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.5. Danh Mục Trang Thiết Bị'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1.2.6. Trang Thiết Bị Sử Dụng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('1.2.7. Đơn Vị Sửa Chữa'),
                        ],
                    ],
                ],
                [
                    'key' => 'department_manufactures',
                    'name' => lang('2. Phòng Sản Xuất'),
                    'sub' => [
                        [
                            'link' => 'admin/products/category_stages',
                            'key' => 'category__stages',
                            'name' => lang('2.1. Nhóm công đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('2.2. Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('2.3. Công Đoạn Đặc Biệt'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'name' => lang('2.4. Loại Hình In'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2.5. Mã Lộ Trình'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('2.6. Đơn Vị Gia Công'),
                        ],
                    ],
                ],
                [
                    'key' => 'department_qa',
                    'name' => lang('3. Phòng Chất Lượng'),
                    'sub' => [
                        [
                            'link' => 'admin/categories_other/unit_package_sp',
                            'key' => 'unit_package_sp',
                            'is_permission' => 1,
                            'name' => lang('3.1. Đơn Vị Đóng Gói Sản Phẩm'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/packaging_standards_sp',
                            'key' => 'packaging_standards_sp',
                            'is_permission' => 1,
                            'name' => lang('3.2. Tiêu Chuẩn Đóng Gói'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/unit_package_npl',
                            'key' => 'unit_package_npl',
                            'is_permission' => 1,
                            'name' => lang('3.3. Đơn Vị Đóng Gói NPL'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/packaging_standards_npl',
                            'key' => 'packaging_standards_npl',
                            'is_permission' => 1,
                            'name' => lang('3.4. Tiêu Chuẩn Đóng Gói NPL'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/vehicle_cl',
                            'key' => 'vehicle_cl',
                            'is_permission' => true,
                            'name' => lang('3.5. Phương Tiện'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('3.6. Thiết bị Đo Kiểm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('3.7. Đơn Vị Đo Kiểm'),
                        ],
                    ],
                ],
            ]
        ];

        $menu['category']['items']['crm'] = [
            'name' => lang('II. CRM - Quản Lý Khách Hàng'),
            'sub_name' => lang('Customer Relationship Management'),
            'sub_menu_one' => [
                [
                    'key' => 'cs',
                    'name' => lang('Customer Service - Chăm Sóc Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_support/customer_order',
                            'key' => 'coupon_support',
                            'is_permission' => true,
                            'name' => lang('Phiếu Chăm Sóc Khách Hàng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'customer',
                    'name' => lang('Khách hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Công Đoạn Theo Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('Bảng Giá Sản Phẩm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('Danh Sách Loại Khách Hàng'),
                        ],
                    ],
                ],
            ]
        ];

        $menu['category']['items']['scc'] = [
            'name' => lang('III. SCC - Kiểm Soát Chuỗi Cung Ứng'),
            'sub_name' => lang('Supplier Chain Control'),
            'sub_menu_one' => [
                [
                    'key' => 'purchaser',
                    'name' => lang('PM (Purchasing Management)'),
                    'sub' => [
                        [
                            'link' => 'admin/supplier_evaluate',
                            'key' => 'supplier_evaluate',
                            'is_permission' => 1,
                            'name' => lang('Phiếu Đánh Giá Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/service',
                            'key' => 'service',
                            'is_permission' => checkPermission('service', $staff_id, $is_admin),
                            'name' => lang('Đơn Đặt Dịch Vụ (SV)'),
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'suppliers',
                    'name' => lang('Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('Danh Sách Loại Nhà Cung Cấp'),
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => []
        ];

        $menu['category']['items']['erp'] = [
            'name' => lang('IV. ERP - Hoạch Định Nguồn Lực DN'),
            'sub_name' => lang('Enterprise Resource Planning'),
            'is_not_click' => 1,
        ];

        $menu['category']['items']['office_management'] = [
            'name' => lang('1. Office Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'quotes',
                    'name' => lang('I.PLANNING <br>A. Báo Giá - PTM'),
                    'sub' => [
                        [
                            'link' => 'admin/quotes',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('1. Bảng Báo Giá'),
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('2. Danh sách Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('3. Bảng Giá Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'name' => lang('4. Danh Sách Thông Tin Thiết Bị'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'is_permission' => true,
                            'name' => lang('5. Danh Sách Loại Hình In'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('6. Danh Sách Thông Tin Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('7. Danh Sách Thông Tin Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('8. Danh Sách Thông Tin Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('9. Danh Sách Thông Tin NPL'),
                        ],
                        [
                            'link' => 'admin/quotation_request',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('10. Phiếu Yêu Cầu Báo Giá'),
                        ],
                        [
                            'link' => 'admin/moderation_quotes',
                            'key' => 'moderation_quotes',
                            'is_permission' => true,
                            'name' => lang('11. Phiếu Điều Độ Công Việc Báo Giá'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Tính Giá'),
                        ],
                        [
                            'link' => 'admin/request_template',
                            'key' => 'request_template',
                            'is_permission' => true,
                            'name' => lang('13. Phiếu Yêu Cầu Phát Triển Mẫu'),
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => 'moderation_template',
                            'is_permission' => true,
                            'name' => lang('14. Phiếu Điều Độ Công Việc Phát Triển Mẫu'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('15. Phiếu Phát Triển Mẫu'),
                        ],
                    ],
                ],
                [
                    'key' => 'orders',
                    'name' => lang('B. Đơn Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/orders',
                            'key' => 'orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin),
                            'name' => lang('1. Đơn Đặt Hàng Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/orders/import_orders',
                            'key' => 'import_orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin, 'create'),
                            'name' => lang('2. Import Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/orders/information',
                            'key' => 'orders__information',
                            'is_permission' => true,
                            'name' => lang('3. Thống Kê Đơn Hàng'),
                        ],
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('4. Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('5. Phân Loại Đơn Hàng'),
                        ],
                        [
                            'link' => 'admin/returned_goods',
                            'key' => 'returned_goods',
                            'is_permission' => checkPermission('returned_goods', $staff_id, $is_admin),
                            'name' => lang('6. Trả Lại Hàng Bán'),
                        ],
                        [
                            'link' => 'admin/purchase_order_request',
                            'key' => 'purchase_order_request',
                            'is_permission' => true,
                            'name' => lang('7. Phiếu Yêu Cầu Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/moderation_order',
                            'key' => 'moderation_order',
                            'is_permission' => true,
                            'name' => lang('8. Phiếu Điều Độ Công Việc Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/orders',
                            'key' => 'orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin),
                            'name' => lang('9. Phiếu Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/request_client_complaints',
                            'key' => 'request_client_complaints',
                            'is_permission' => true,
                            'name' => lang('10. Phiếu Yêu Cầu Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('11. Phiều Điều Độ Công Việc Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=customer',
                            'key' => 'suggest_evaluate?type=customer',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Yêu Cầu Đánh Giá Khách Hàng'),
                        ],
                    ],
                ],
                [
                    'key' => 'planing',
                    'name' => lang('C. Kế Hoạch Điều Độ<br>C.1. PLANNING'),
                    'sub' => [
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('1. Kế Hoạch Điều Độ Công Đoạn'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/moderation_quotes',
                            'key' => 'moderation_quotes',
                            'name' => lang('2. Kế Hoạch Điều Độ Báo Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => 'moderation_template',
                            'name' => lang('3. Kế Hoạch Điều Độ Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_order',
                            'key' => 'moderation_order',
                            'name' => lang('4. Kế Hoạch Điều Độ Đơn Hàng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Kế Hoạch Điều Độ Xuất Kho Tồn Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Kế Hoạch Điều Độ Xuất Kho Tồn NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Kế Hoạch Điều Độ Nhập Tồn Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Kế Hoạch Điều Độ Nhập Kho Tồn NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('9. Kế Hoạch Điều Độ Xuất Bìa Mẫu Sản Xuất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('10. Kế Hoạch Điều Độ Xuất Khuôn Bế Tồn Sản Xuất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('11. Kế Hoạch Điều Độ Mở Lệnh Sản Xuất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('12. Kế Hoạch Điều Độ Ghép In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('13. Kế Hoạch Điều Độ Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('14. Kế Hoạch Điều Độ Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_purchase_material',
                            'key' => 'moderation_purchase_material',
                            'name' => lang('15. Kế Hoạch Điều Độ Mua NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('16. Kế Hoạch Điều Độ Nhập Kho NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('17. Kế Hoạch Điều Độ Cắt Giấy'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('18. Kế Hoạch Điều Độ In Offset'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('19. Kế Hoạch Điều Độ In Flexo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('20. Kế Hoạch Điều Độ In HP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('21. Kế Hoạch Điều Độ In BarCode'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('22. Kế Hoạch Điều Độ In Lụa'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=7',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('23. Kế Hoạch Điều Độ Phun Bóng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=6',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('24. Kế Hoạch Điều Độ Cán Màng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=8',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('25. Kế Hoạch Điều Độ Bồi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=17',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('26. Kế Hoạch Điều Độ Ép Nhũ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=9',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('27. Kế Hoạch Điều Độ Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=18',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('28. Kế Hoạch Điều Độ Cắt Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=14',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('29. Kế Hoạch Điều Độ Khoan Lỗ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('30. Kế Hoạch Điều Độ Cắt Demi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('31. Kế Hoạch Điều Độ Gấp Sổ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage/moderation_plan_stage_phan_don',
                            'key' => 'moderation_plan_stage_phan_don',
                            'name' => lang('32. Phiếu Điều Độ Công Đoạn Phân Đơn-Dán Tem'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('33. Kế Hoạch Điều Độ Chọt Lỗ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('34. Kế Hoạch Điều Độ Nhóm Kiểm Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('35. Kế Hoạch Điều Độ Nhóm Kiểm Cố Định'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage/moderation_plan_stage_giao_hang',
                            'key' => 'moderation_plan_stage_giao_hang',
                            'name' => lang('36. Kế Hoạch Điều Độ Điều Xe'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('37. Kế Hoạch Điều Độ Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('38. Kế Hoạch Điều Độ Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('39. Kế Hoạch Điều Độ Bàn Giao Kế Hoạch-Hàng Hóa-Sản Xuất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=19',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('40. Kế Hoạch Công Đoạn Cắt Kiểm Kim Loại'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'planing',
                    'name' => lang('C.2. Kỹ Thuật'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Kế Hoạch Điều Độ Bảo Dưỡng Cơ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Kế Hoạch Điều Độ Bảo Dưõng Điện'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Kế Hoạch Điều Độ Bảo Dưõng Điện, Nước, Hạ Tầng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Kế Hoạch Điều Độ Bảo Dưõng Hơi-Khí Nén'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Kế Hoạch Điều Độ Bảo Dưõng Điện Lạnh'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Kế Hoạch Điều Độ Bảo Dưỡng Thiết Bị PCCC'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Kế Hoạch Điều Độ Bảo Dưỡng Camera-Mạng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Kế Hoạch Điều Độ Bảo Dưỡng Server'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('9. Kế Hoạch Điều Độ Bảo Dưỡng Máy Tính VP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('10. Kế Hoạch Điều Độ Bảo Dưỡng Máy In VP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('11. Kế Hoạch Điều Độ VSAT-5S Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('12. Kiểm Soát Vật Tư Thay Thế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('13. Kiểm Soát Vật Tư Bảo Dưỡng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('14. Kiểm Soát Báo Cáo Không Phù Hợp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/repair_plan',
                            'key' => 'repair_plan',
                            'name' => lang('15. Phiếu Kế Hoạch Yêu Cầu Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_repair',
                            'key' => 'request_repair',
                            'name' => lang('16. Phiếu Yêu Cầu Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'staff',
                    'name' => lang('C.3. Hành Chính Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/moderation/moderation_check',
                            'key' => 'moderation_check',
                            'name' => lang('1. Kế Hoạch Điều Độ VSAT-5S Nhà Xưởng, VP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_task',
                            'key' => 'moderation_task',
                            'name' => lang('2. Kế Hoạch Điều Độ Công Việc'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Kế Hoạch Điều Độ Tuyển Dụng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_educate',
                            'key' => 'moderation_educate',
                            'name' => lang('4. Kế Hoạch Điều Độ Đào Tạo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=1',
                            'key' => 'probationary_evaluate?type=1',
                            'name' => lang('5. Kế Hoạch Điều Độ Đánh Giá Thử Việc'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=2',
                            'key' => 'probationary_evaluate?type=2',
                            'name' => lang('6. Kế Hoạch Điều Độ Đánh Giá Nhân Sự'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=3',
                            'key' => 'probationary_evaluate?type=3',
                            'name' => lang('7. Kế Hoạch Điều Độ Đánh Giá NS-Tay Nghề'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Kế Hoạch Điều Độ Sao Y'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('9. Kế Hoạch Điều Độ Công Chứng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('10. Kế Hoạch Điều Độ Chi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('11. Kế Hoạch Điều Độ Tái Ký'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('12. Kế Hoạch Điều Độ Gia Hạn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_evaluate_skill',
                            'key' => 'moderation_evaluate_skill',
                            'name' => lang('13. Kế Hoạch Điều Độ Thi Đánh Giá Tay Nghề'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('14. Kế Hoạch Điều Độ Thi Đánh Giá KPI'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_evaluate_supplier',
                            'key' => 'moderation_evaluate_supplier',
                            'name' => lang('15. Kế Hoạch Điều Độ Tìm Kiếm Đánh Giá Nhà Cung Cấp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('16. Kiểm Soát Chấm Công-Phép'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'qa',
                    'name' => lang('C.4. Chất Lượng(QA)'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Kế Hoạch Điều Độ Kiểm Tra Chất Lượng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Kế Hoạch Điều Độ Đánh Giá Qui Trình Vận Hành'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Kế Hoạch Điều Độ Đánh Giá Nhà Cung Cấp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Kế Hoạch Điều Độ Đánh Giá Hệ Thống'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Kế Hoạch Điều Độ Đánh Giá Khách Hàng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Kiểm Soát Báo Cáo Không Phù Hợp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Kiểm Soát Chăm Sóc Khách Hàng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Kiểm Soát Bàn Giao Công Đoạn ( Hệ Thống)'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'accountant',
                    'name' => lang('C.5. Tài Chính Kế Toán'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Kế Hoạch Điều Độ Giao Hàng-Thu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Kế Hoạch Điều Độ Nhập Kho-Chi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Kiểm Soát Giá Mua-Bán'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Kiểm Soát Tồn Kho TP-Tồn Cho Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5 Kiểm Soát Tồn Kho NPL-Tồn Cho Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Kiểm Soát Tồn Kho NPL-Tồn Quá Hạn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Kiểm Soát Tồn Kho TP-Tồn Quá Hạn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Kiểm Soát Phế Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('9. Kiểm Soát Báo Cáo Không Phù Hợp'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'warehouse',
                    'name' => lang('C.6. Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'name' => lang('1. Kế Hoạch Điều Độ Kiểm Kho'),
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Kế Hoạch Điều Độ Xuất-Nhập-Tồn Kho'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Kiểm Soát Tồn Kho TP-Tồn Cho Phép'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Kiểm Soát Tồn Kho NPL-Tồn Cho Phép'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Kiểm SoátTồn Kho NPL-Tồn Quá Hạn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Kiểm Soát Tồn Kho TP-Tồn Quá Hạn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_outsource',
                            'key' => 'suggest_outsource',
                            'name' => lang('7. Phiếu Yêu Cầu Gia Công'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => 'reports__purchase',
                            'name' => lang('8. Kế Hoạch Điều Động Về NPL'),
                            'is_permission' => checkPermission('purchase', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/manufactures/productions_plan_purchase',
                            'key' => 'manufactures__productions_plan_purchase',
                            'name' => lang('9. Tổng Hợp Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('10. Điều Độ Sản Xuất'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('11. Điều Động Sản Xuất'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('12. Lệnh Sản Xuất Tổng'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/order_production_details',
                            'key' => 'manufactures__order_production_details',
                            'name' => lang('13. Lệnh Sản Xuất Chi Tiết'),
                            'is_permission' => checkPermission('manufactures_order_production_details', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufacture/index',
                            'key' => 'manufacture__index',
                            'name' => lang('14. Phiếu Xả Khổ'),
                            'is_permission' => checkPermission('manufacture', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_zinc',
                            'key' => 'synthetic_zinc',
                            'name' => lang('15. Tổng Hợp Xuất Kẽm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/synthetic_stage',
                            'key' => 'synthetic_stage',
                            'name' => lang('16. Lệnh Sản Xuất Theo Công Đoạn'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('17. Thống Kê Giữ Hàng'),
                            'is_permission' => 1
                        ],
                    ],
                ],
                [
                    'key' => 'purchase',
                    'name' => lang('D. Thu Mua'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('1. Danh Sách Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('2. Nhóm Nhà Cung Cấp'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('3. Bảng Giá Nhà Cung Cấp'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('4. Mặt Hàng Chủ Đạo'),
                        ],
                        [
                            'link' => 'admin/items',
                            'key' => 'items',
                            'is_permission' => checkPermission('items', $staff_id, $is_admin),
                            'name' => lang('5. Danh Sách Nguyên Vật Liệu'),
                        ],
                        [
                            'link' => 'admin/return_suppliers',
                            'key' => 'return_suppliers',
                            'is_permission' => checkPermission('return_suppliers', $staff_id, $is_admin),
                            'name' => lang('6. Trả hàng'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('7. Danh Sách Đơn Vị Sửa Chữa'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('8. Danh Sách Đơn Vị Nhà Gia Công'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=supplier',
                            'key' => 'suggest_evaluate?type=supplier',
                            'is_permission' => 1,
                            'name' => lang('9. Phiếu Yêu Cầu Đánh Giá NCC'),
                        ],
                        [
                            'link' => 'admin/supplier_evaluate',
                            'key' => 'supplier_evaluate',
                            'is_permission' => 1,
                            'name' => lang('10. Phiếu Đánh Giá NCC'),
                        ],
                        [
                            'link' => 'admin/purchases/synthetic_purchase',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('11. Phiếu Yêu Cầu Mua Hàng (PR)'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('12. Phiếu Điều Độ Công Việc Đơn Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/purchase_order',
                            'key' => 'purchase_order',
                            'is_permission' => checkPermission('purchase_order', $staff_id, $is_admin),
                            'name' => lang('13. Đơn Mua Hàng (PO)'),
                        ],
                    ],
                ],
                [
                    'key' => 'warehouse',
                    'name' => lang('E. Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('1. Danh Sách Kho Hàng'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/warehouse/localtion',
                            'key' => 'warehouse__localtion',
                            'name' => lang('2. Vị Trí Kho'),
                            'is_permission' => checkPermission('warehouse_localtion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer',
                            'key' => 'transfer',
                            'name' => lang('3. Chuyển Kho'),
                            'is_permission' => checkPermission('transfer', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/transfer_bussiness',
                            'key' => 'transfer_bussiness',
                            'name' => lang('4. Giữ Kho (Trên Chuyền)'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/stock/purchase_internal',
                            'key' => 'stock__purchase_internal',
                            'name' => lang('5. Thu hồi NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/export_different',
                            'key' => 'stock__export_different',
                            'name' => lang('6. Xuất Kho Khác'),
                            'is_permission' => checkPermission('export_different', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin),
                            'name' => lang('7. Phiếu Yêu Cầu Kiểm Kê'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('8. Phiếu Điều Độ Công Việc Kiểm Kê'),
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin),
                            'name' => lang('9. Phiếu Kiểm Kê'),
                        ],
                        [
                            'link' => 'admin/stock_out_request',
                            'key' => 'stock_out_request',
                            'name' => lang('10. Phiếu Yêu Cầu Xuất Kho NPL Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/moderation_stock_out',
                            'key' => 'moderation_stock_out',
                            'name' => lang('11. Phiếu Điều Độ Xuất Kho NPL Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/exporting_producion',
                            'key' => 'stock__exporting_producion',
                            'name' => lang('12. Phiếu Xuất Kho NPL Tồn'),
                            'is_permission' => checkPermission('stock_exporting_producion', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/request_export_products',
                            'key' => 'request_export_products',
                            'name' => lang('13. Phiếu Yêu Cầu Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/moderation_export_products',
                            'key' => 'moderation_export_products',
                            'name' => lang('14. Phiếu Điều Độ Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('15. Phiếu Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_purchase_npl',
                            'key' => 'suggest_purchase_npl',
                            'name' => lang('16. Phiếu Yêu Cầu Nhập Kho NPL'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('17. Phiếu Điều Độ Công Việc Nhập Kho NPL'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/import',
                            'key' => 'import',
                            'is_permission' => checkPermission('import', $staff_id, $is_admin),
                            'name' => lang('18. Phiếu Nhập Kho NPL'),
                        ],
                        [
                            'link' => 'admin/suggest_ticket_purchase_products',
                            'key' => 'suggest_ticket_purchase_products',
                            'name' => lang('19. Phiếu Yêu Cầu Nhập Kho TP'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('20. Phiếu Điều Độ Công Việc Nhập Kho TP'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock/purchase_products',
                            'key' => 'stock__purchase_products',
                            'name' => lang('21. Phiếu Nhập Kho TP'),
                            'is_permission' => checkPermission('stock_purchase_products', $staff_id, $is_admin)
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'orders_delivery',
                    'name' => lang('II. SALES/MARKETTING<br>A. Xuất Nhập Khẩu'),
                    'sub' => [],
                ],
                [
                    'key' => 'orders_delivery',
                    'name' => lang('B. Tư Vấn Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients__clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('1. Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients__groups',
                            'is_permission' => checkPermission('groups', $staff_id, $is_admin),
                            'name' => lang('2. Nhóm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('3. Bảng Giá Công Đoạn Theo Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('4. Bảng Giá Sản Phẩm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/all_shipping',
                            'key' => 'clients__all_shipping',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('5. Danh Sách Địa Chỉ Giao Hàng'),
                        ],
                    ],
                ],
                [
                    'key' => 'support',
                    'name' => lang('C. Chăm Sóc Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_support/customer_order',
                            'key' => 'coupon_support',
                            'is_permission' => true,
                            'name' => lang('1. Phiếu Chăm Sóc Khách Hàng'),
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'hr',
                    'name' => lang('III. Hành Chính Nhân Sự(HR)<br>A. Công Việc'),
                    'sub' => [
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => 'internal_proposal',
                            'is_permission' => checkPermission('internal_proposal', $staff_id, $is_admin),
                            'name' => lang('1. Đề Xuất Nội Bộ'),
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'delivery_records',
                            'is_permission' =>  checkPermission('delivery_records', $staff_id, $is_admin),
                            'name' => lang('2. Phiếu Yêu Cầu Bàn Giao Hoàn Thành'),
                        ],
                        [
                            'link' => 'admin/suggest_task',
                            'key' => 'suggest_task',
                            'is_permission' => true,
                            'name' => lang('3. Phiếu Yêu Cầu Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('5. Danh Sách Công Việc'),
                        ],
                        [
                            'link' => 'admin/tasks?kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('6. Công Việc Kanban'),
                        ],
                        [
                            'link' => 'admin/tasks/calendar_pod',
                            'key' => 'tasks__calendar_pod',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('7. Lịch Công Việc'),
                        ],
                        [
                            'link' => 'admin/gantt',
                            'key' => 'gantt',
                            'is_permission' => checkPermission('gantt', $staff_id, $is_admin),
                            'name' => lang('8. Sơ Đồ Gantt'),
                        ],
                        [
                            'link' => 'admin/work_plan/handling',
                            'key' => 'work_plan',
                            'is_permission' => checkPermission('work_plan', $staff_id, $is_admin),
                            'name' => lang('9. Kế hoạch công việc'),
                        ],
                        [
                            'link' => 'admin/request_bussiness',
                            'key' => 'request_bussiness',
                            'is_permission' => true,
                            'name' => lang('10. Phiếu Yêu Cầu Công Tác'),
                        ],
                        [
                            'link' => 'admin/request_control_vehicle_bussiness',
                            'key' => 'request_control_vehicle_bussiness',
                            'is_permission' => true,
                            'name' => lang('11. Phiếu Yêu Cầu Điều Xe Công Tác'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Kiểm Soát Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/suggest_educate',
                            'key' => 'suggest_educate',
                            'is_permission' => 1,
                            'name' => lang('13. Phiếu Yêu Cầu Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/request_improve',
                            'key' => 'request_improve',
                            'is_permission' => true,
                            'name' => lang('14. Phiếu Yêu Cầu Cải Tiến'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('15. Danh Mục Quy Trình'),
                        ],
                    ]
                ],
                [
                    'key' => 'edu',
                    'name' => lang('B. Tuyễn Dụng Đào Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('1. Danh Sách Chi Nhánh'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('2. Danh Sách Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('3. Danh Sách Bộ Phận'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('4. Danh Sách Chức Vụ'),
                        ],
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('5. Danh sách nhân viên'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('6. Trang Thiết Bị Công Việc'),
                        ],
                        [
                            'link' => 'admin/kpi/list',
                            'key' => 'kpi',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('7. Danh Sách KPI'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('8. Danh Sách Quyết Định'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('9. Danh Sách Hợp Đồng Lao Động'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('10. Danh Sách Giấy Phép'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('11. Danh Sách Chứng Nhận'),
                        ],
                        [
                            'link' => 'admin/suggest_recruitment',
                            'key' => 'suggest_recruitment',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Yêu Cầu Tuyển Dụng'),
                        ],
                        [
                            'link' => 'admin/moderation_recruitment',
                            'key' => 'moderation_recruitment',
                            'is_permission' => true,
                            'name' => lang('13. Phiếu Điều Độ Công Việc Tuyển Dụng'),
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=1',
                            'key' => 'suggest_probationary_evaluate?type=1',
                            'is_permission' => true,
                            'name' => lang('14. Phiếu Yêu Cầu Đánh Giá Thử Việc'),
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=2',
                            'key' => 'suggest_probationary_evaluate?type=2',
                            'is_permission' => true,
                            'name' => lang('15. Phiếu Yêu Cầu Đánh Giá Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=3',
                            'key' => 'suggest_probationary_evaluate?type=3',
                            'is_permission' => true,
                            'name' => lang('16. Phiếu Yêu Cầu Đánh Giá Nhân Sự-Tay Nghề'),
                        ],
                    ]
                ],
                [
                    'key' => 'salary',
                    'name' => lang('C. Tiền Lương'),
                    'sub' => [
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('1. Thông Tin Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/paid_holiday_leave',
                            'key' => 'paid_holidays__paid_holiday_leave',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('2. Phép Năm'),
                        ],
                        [
                            'link' => 'admin/suggest_paid_holidays',
                            'key' => 'suggest_paid_holidays',
                            'is_permission' => 1,
                            'name' => lang('2.1. Phiếu Yêu Cầu Nghỉ Phép'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/report_paid_holiday_leave',
                            'key' => 'report_paid_holiday_leave',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('2.2. Thống Kê Ngày Phép'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/report_paid_holiday_leave',
                            'key' => 'setup_paid_holidays',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('2.3. Loại Phép'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/setup_paid_holidays',
                            'key' => 'setup_paid_holidays',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('2.4. Theo Dõi Phép Năm'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/paid_holidays_follow',
                            'key' => 'paid_holidays_follow',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('2.5. Theo Dõi Phép Năm'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/paid_holiday_leave',
                            'key' => 'timekeeping',
                            'is_permission' => checkPermission('timekeeping', $staff_id, $is_admin),
                            'name' => lang('3. Chấm Công'),
                        ],
                        [
                            'link' => 'admin/salary/timekeeping',
                            'key' => 'salary__timekeeping',
                            'is_permission' => checkPermission('timekeeping', $staff_id, $is_admin),
                            'name' => lang('3.1. Chi tiết giờ công'),
                        ],
                        [
                            'link' => 'admin/salary/dashboard_timekeeping',
                            'key' => 'salary__dashboard_timekeeping',
                            'is_permission' => checkPermission('dashboard_timekeeping', $staff_id, $is_admin),
                            'name' => lang('3.2. Thống Kê Ngày Công'),
                        ],
                        [
                            'link' => 'admin/entrance_ticket',
                            'key' => 'entrance_ticket',
                            'is_permission' => 1,
                            'name' => lang('3.3. Phiếu Ra Vào Cổng-Mang Hàng Ra Cổng'),
                        ],
                        [
                            'link' => 'admin/suggest_overtime',
                            'key' => 'suggest_overtime',
                            'is_permission' => checkPermission('suggest_overtime', $staff_id, $is_admin),
                            'name' => lang('4. Tăng ca'),
                        ],
                        [
                            'link' => 'admin/request_overtime',
                            'key' => 'request_overtime',
                            'is_permission' => true,
                            'name' => lang('4.1. Phiếu Yêu Cầu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                            'name' => lang('4.2. Tăng Ca Tháng'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                            'key' => 'business_fee_other__report_business_fee_other_overtime',
                            'name' => lang('4.3. Thống Kê Giờ Tăng Ca'),
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_calculate',
                            'key' => 'business_fee_other__business_fee_other_calculate',
                            'name' => lang('4.4. Bảng Tính Tăng Ca'),
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('5. Bảng Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_payment',
                            'key' => 'payroll__payroll_payment',
                            'is_permission' => checkPermission('payroll_payment', $staff_id, $is_admin),
                            'name' => lang('5.1. Phiếu Tạm Ứng Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('5.2. Phiếu Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('5.3. Bảng Lương'),
                        ],
                    ]
                ],
                [
                    'key' => 'ehs',
                    'name' => lang('D. An Toàn Lao Động'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('1. Khu Vực Vệ Sinh ATLĐ-5S'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('2. Nội quy'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate',
                            'key' => 'suggest_evaluate',
                            'is_permission' => 1,
                            'name' => lang('3. Phiếu Yêu Cầu Đánh Giá'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('4. Phiếu Yêu Cầu Test/Kiểm Định'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('5. Phiếu Yêu Cầu Đánh Giá KPIs'),
                        ],
                        [
                            'link' => 'admin/suggest_rating_system',
                            'key' => 'suggest_rating_system',
                            'is_permission' => 1,
                            'name' => lang('6. Phiếu Yêu Cầu Đánh Giá Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/suggest_rating_process',
                            'key' => 'suggest_rating_process',
                            'is_permission' => 1,
                            'name' => lang('7. Phiếu Yêu Cầu Đánh Giá Quy Trình'),
                        ],
                        [
                            'link' => 'admin/suggest_check',
                            'key' => 'suggest_check',
                            'is_permission' => 1,
                            'name' => lang('8. Phiếu Yêu Cầu Kiểm Tra Vệ Sinh ATLĐ - 5S'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('9. Phiếu Yêu Cầu Kiểm Tra PCCC'),
                        ],
                    ]
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'accouting',
                    'name' => lang('IV. Kế Toán(Accouting)<br>A. Kế Toán Thuế'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('1. Doanh Thu Bán '),
                        ],
                        [
                            'link' => 'admin/costs?type=3',
                            'key' => 'costs',
                            'is_permission' => checkPermission('costs', $staff_id, $is_admin),
                            'name' => lang('2. Chi Phí Hợp Lý'),
                        ],
                        [
                            'link' => 'admin/costs?type=4',
                            'key' => 'costs',
                            'is_permission' => checkPermission('costs', $staff_id, $is_admin),
                            'name' => lang('3. Chi Phí Ngoài'),
                        ],
                        [
                            'link' => 'admin/costs?type=5',
                            'key' => 'costs',
                            'is_permission' => checkPermission('costs', $staff_id, $is_admin),
                            'name' => lang('4. Chi Phí Khấu Trừ'),
                        ],
                        [
                            'link' => 'admin/costs?type=6',
                            'key' => 'costs',
                            'is_permission' => checkPermission('costs', $staff_id, $is_admin),
                            'name' => lang('5. Chi Phí Giảm Trừ'),
                        ],
                    ],
                ],
                [
                    'key' => 'accouting',
                    'name' => lang('B. Kế Toán Thu'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_invoice/synthetic_coupon_invoice',
                            'key' => 'synthetic_coupon_invoice',
                            'is_permission' => checkPermission('coupon_invoice', $staff_id, $is_admin),
                            'name' => lang('1. Hóa Đơn Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('2. Phiếu Thu Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/debt_clients',
                            'key' => 'debt_clients',
                            'is_permission' => checkPermission('debt_clients', $staff_id, $is_admin),
                            'name' => lang('3. Công Nợ Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips_coupon',
                            'key' => 'other_payslips_coupon',
                            'is_permission' => checkPermission('other_payslips_coupon', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Thu Khác'),
                        ],
                    ],
                ],
                [
                    'key' => 'accouting',
                    'name' => lang('C. Kế Toán Chi'),
                    'sub' => [
                        [
                            'link' => 'admin/purchase_invoice/synthetic_invoice',
                            'key' => 'purchase_invoice',
                            'is_permission' => checkPermission('purchase_invoice', $staff_id, $is_admin),
                            'name' => lang('1. Hóa Đơn Mua Hàng'),
                        ],
                        [
                            'link' => 'admin/pay_slip/synthetic_payslip',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('2. Phiếu Chi'),
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('2.1. Phiếu Chi Phí Ngoài'),
                        ],
                        [
                            'link' => 'admin/pay_slip/synthetic_payslip',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('2.2. Phiếu Chi Mua Hàng (theo YCMH)'),
                        ],
                        [
                            'link' => 'admin/other_payslips/other_payslip_manage',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('2.3. Phiếu Chi Quản Lý'),
                        ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => 'debt_suppliers',
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                            'name' => lang('3. Công Nợ Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suggest_payslips',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Yêu Cầu Chi'),
                        ],
                        [
                            'link' => 'admin/spending_plan',
                            'key' => 'spending_plan',
                            'is_permission' => true,
                            'name' => lang('5. Phiếu Kế Hoạch Chi'),
                        ],
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => checkPermission('advance', $staff_id, $is_admin),
                            'name' => lang('6. Phiếu Chi Tạm Ứng'),
                        ],
                    ],
                ],
                [
                    'key' => 'accouting',
                    'name' => lang('D. Thủ Quỷ'),
                    'sub' => [
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => checkPermission('advance', $staff_id, $is_admin),
                            'name' => lang('1. Phiếu Chi Tạm Ứng'),
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('2. Phiếu Chi Phí Ngoài'),
                        ],
                        [
                            'link' => 'admin/pay_slip/synthetic_payslip',
                            'key' => 'pay_slip',
                            'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                            'name' => lang('3. Phiếu Chi Mua Hàng (theo YCMH)'),
                        ],
                        [
                            'link' => 'admin/other_payslips/other_payslip_manage',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Chi Quản Lý'),
                        ],
                    ],
                ]
            ],
        ];

        $menu['category']['items']['production_management'] = [
            'name' => lang('2. Production Management'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'qa',
                    'name' => lang('I. Chất Lượng<br>A. QA'),
                    'sub' => [
                        [
                            'link' => 'admin/quality_control/category_errors',
                            'key' => 'quality_control__category_errors',
                            'name' => lang('1. Danh Mục Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/detail_errors',
                            'key' => 'quality_control__detail_errors',
                            'name' => lang('2. Chi Tiết Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('3. Báo Cáo Không Phù Hợp'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Danh Sách Thiết Bị Đo Kiểm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Danh Sách Tiêu Chuẩn Chất Lượng SP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Tiêu Chí/Quy Trình Bàn Giao'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('7. Phiếu Yêu Cầu Kiểm Tra Chất Lượng Sản Phẩm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('8. Phiếu Kiểm Tra Chất Lượng Sản Phẩm'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('9. Phiếu Yêu Cầu Kiểm Tra Chất Lượng NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('10. Phiếu Kiểm Tra Chất Lượng NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('11. Phiếu Yêu Cầu Đánh Giá Chất Lượng NPL'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Yêu Cầu Đánh Giá Chất Lượng Sản Phẩm'),
                        ],
                    ],
                ],
                [
                    'key' => 'qa',
                    'name' => lang('B. Giao Hàng Thanh Toán'),
                    'sub' => [
                        [
                            'link' => 'admin/releases',
                            'key' => 'releases',
                            'is_permission' => checkPermission('releases_deliveries', $staff_id, $is_admin),
                            'name' => lang('1. Phiếu Giao Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('2. Tiêu Chí Giao Hàng'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('3. Phí Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Thanh Toán'),
                        ],
                        [
                            'link' => 'admin/clients/all_shipping',
                            'key' => 'clients__all_shipping',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('5. Danh Sách Địa Chỉ Giao Hàng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'production',
                    'name' => lang('II. Sản Xuất(Production)'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('1. Lệnh Sản Xuất Tổng'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/order_production_details',
                            'key' => 'manufactures__order_production_details',
                            'name' => lang('2. Lệnh Sản Xuất Chi Tiết'),
                            'is_permission' => checkPermission('manufactures_order_production_details', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_stage',
                            'key' => 'synthetic_stage',
                            'name' => lang('3. Lệnh Sản Xuất Theo Công Đoạn'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/list_manufactures',
                            'key' => 'manufactures__list_manufactures',
                            'name' => lang('4. Kế Hoạch Sản Xuất'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('5. Kế Hoạch Điều Độ'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/plan_propose?group=train',
                            'key' => 'plan_propose',
                            'name' => lang('6. Kế Hoạch Điều Động'),
                            'is_permission' => checkPermission('plan_propose', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'name' => lang('7. Loại Bàn Giao'),
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'name' => lang('8. Tiêu Chí Bàn Giao'),
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/suggest_outsource',
                            'key' => 'suggest_outsource',
                            'name' => lang('9. Phiếu Yêu Cầu Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_outsource',
                            'key' => 'moderation_outsource',
                            'name' => lang('10. Phiếu Điều Độ Công Đoạn Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('11. Phiếu Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_overtime',
                            'key' => 'request_overtime',
                            'is_permission' => true,
                            'name' => lang('12. Phiếu Yêu Cầu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => true,
                            'name' => lang('13. Phiếu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/moderation_overtime',
                            'key' => 'moderation_overtime',
                            'name' => lang('14. Phiếu Điều Độ Công Đoạn Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_control_vehicle',
                            'key' => 'suggest_control_vehicle',
                            'name' => lang('15. Phiếu Yêu Cầu Điều Xe'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_graft_size',
                            'key' => 'request_graft_size',
                            'name' => lang('16. Phiếu Yêu Cầu Ghép Size'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_graft_size',
                            'key' => 'moderation_graft_size',
                            'name' => lang('17. Phiếu Điều Độ Công Đoạn Ghép Size'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_printed_page_layout',
                            'key' => 'request_printed_page_layout',
                            'name' => lang('18. Phiếu Yêu Cầu Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_printed_page_layout',
                            'key' => 'moderation_printed_page_layout',
                            'name' => lang('19. Phiếu Điều Độ Công Đoạn Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/purchase_request_zinc',
                            'key' => 'purchase_request_zinc',
                            'name' => lang('20. Phiếu Yêu Cầu Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_purchase_zinc',
                            'key' => 'moderation_purchase_zinc',
                            'name' => lang('21. Phiếu Điều Độ Công Đoạn Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_place_the_tank_mold',
                            'key' => 'request_place_the_tank_mold',
                            'name' => lang('22. Phiếu Yêu Cầu Đặt Khuôn Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_place_the_tank_mold',
                            'key' => 'moderation_place_the_tank_mold',
                            'name' => lang('23. Phiếu Điều Độ Công Đoạn Đặt Khuôn Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_order_request',
                            'key' => 'production_order_request',
                            'name' => lang('24. Phiếu Yêu Cầu Mở Lệnh SX'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_production_order',
                            'key' => 'moderation_production_order',
                            'name' => lang('25. Phiếu Điều Độ Công Việc Mở Lệnh SX'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/purchase_request_material',
                            'key' => 'purchase_request_material',
                            'is_permission' => 1,
                            'name' => lang('26. Phiếu Yêu Cầu Mua NPL'),
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=1',
                            'key' => 'suggest_plan_purchase?type=1',
                            'name' => lang('27. Phiếu Yêu Cầu Kế Hoạch Mua NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=2',
                            'key' => 'suggest_plan_purchase?type=2',
                            'name' => lang('28. Phiếu Yêu Cầu Kế Hoạch Mua Vật Tư'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=3',
                            'key' => 'suggest_plan_purchase?type=3',
                            'name' => lang('29. Phiếu Yêu Cầu Kế Hoạch Mua Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_evaluate',
                            'key' => 'suggest_plan_evaluate',
                            'name' => lang('30. Phiếu Yêu Cầu Kế Hoạch Đánh Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_overtime',
                            'key' => 'suggest_plan_overtime',
                            'name' => lang('31. Phiếu Yêu Cầu Kế Hoạch Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_outsource',
                            'key' => 'suggest_plan_outsource',
                            'name' => lang('32. Phiếu Yêu Cầu Kế Hoạch Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_educate',
                            'key' => 'suggest_plan_educate',
                            'name' => lang('33. Phiếu Yêu Cầu Kế Hoạch Đào Tạo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_recruitment',
                            'key' => 'suggest_plan_recruitment',
                            'name' => lang('34. Phiếu Yêu Cầu Kế Hoạch Tuyển Dụng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('35. Phiếu Báo Cáo Hàng Phế'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'qa',
                    'name' => lang('III. Phòng Kỹ Thuật<br>A. Bảo Trì (Maintenace)'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'is_permission' => true,
                            'name' => lang('1. Danh Sách Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('2. Nhóm Bảo Trì'),
                        ],
                        [
                            'link' => 'admin/maintenance/calendar',
                            'key' => 'maintenance__calendar',
                            'name' => lang('3. Lịch Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('4. Phiếu Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Quy Trình Bảo Trì'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'name' => lang('6. Danh Sách Khấu Hao'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Danh Sách Hiệu Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('8. Danh Sách Vật Tư Thay Thế'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('9. Danh Mục Bảo Dưỡng Cơ'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('10. Danh Mục Bảo Dưỡng Điện'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('11. Danh Mục Bảo Dưỡng Điện Lạnh'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('12. Danh Mục Bảo Dưỡng Hơi Khí Nén'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('13. Danh Mục Trang Thiết Bị'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('14. Trang Thiết Bị Sử Dụng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('15. Lý Lịch Máy'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('16. Đơn Vị Sửa Chữa'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('17. Phiếu Thông Tin Máy'),
                        ],
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => 'suggest_maintenance',
                            'is_permission' => true,
                            'name' => lang('18. Phiếu Yêu Cầu Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/moderation_maintenance',
                            'key' => 'moderation_maintenance',
                            'is_permission' => true,
                            'name' => lang('19. Phiếu Điều Độ Công Việc Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/request_calibration',
                            'key' => 'request_calibration',
                            'is_permission' => true,
                            'name' => lang('20. Phiếu Yêu Cầu Hiệu Chuẩn Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => 'admin/suggest_rating_machines',
                            'key' => 'suggest_rating_machines',
                            'is_permission' => true,
                            'name' => lang('21. Phiếu Yêu Cầu Đánh Giá Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => 'admin/suggest_repalce',
                            'key' => 'suggest_repalce',
                            'is_permission' => true,
                            'name' => lang('22. Phiếu Yêu Cầu Vật Tư Thay Thế'),
                        ],
                    ],
                ],
                [
                    'key' => 'it',
                    'name' => lang('B. IT'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'is_permission' => true,
                            'name' => lang('1. Danh Sách Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Danh Sách Danh Mục Nhóm Phần Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/maintenance/category',
                            'key' => 'maintenance__category',
                            'name' => lang('3. Hạng Mục Bảo Trì'),
                            'is_permission' => checkPermission('category_maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance/calendar',
                            'key' => 'maintenance__calendar',
                            'name' => lang('4. Lịch Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Phiếu Yêu Cầu Sửa Chữa'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Quy Trình Bảo Trì'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Danh Sách Linh Kiện Thay Thế'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Danh Mục Bảo Dưỡng Phần Cứng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('9. Danh Mục Bảo Dưỡng Phẩn Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('10. Danh Mục Trang Thiết Bị'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('11. Trang Thiết Bị Sử Dụng'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('12. Lý Lịch Máy'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('13. Danh Sách Đơn Vị Sửa Chữa'),
                        ],
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => 'suggest_maintenance',
                            'is_permission' => true,
                            'name' => lang('14. Phiếu Yêu Cầu Bảo Dưỡng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_four' => []
        ];

        $menu['category']['items']['reports'] = [
            'name' => lang('3. Report'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'report',
                    'name' => lang('I. Báo Cáo Sản Xuất'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('1. Báo Cáo Sản Xuất Tổng Hợp'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('2. Báo Cáo LSX Hoàn Thành Theo Công Đoạn'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('3. Báo Cáo Hàng Lỗi Theo Lệnh Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('4. Báo Cáo Nhập Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('5. Báo Cáo Tồn Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => 'reports__warehouse',
                            'name' => lang('6. Báo Cáo Tồn Kho NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('7. Báo Cáo Sản Xuất Thừa'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('8. Báo Cáo Tiến độ Đơn Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('9. Báo Cáo Định Mức NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions',
                            'key' => 'reports__productions',
                            'name' => lang('10. Báo Cáo NPL Sử Dụng'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('V. Báo Cáo Công Nợ Phải Trả'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/purchase?is_type=general-synthetic-purchase-report',
                            'key' => '',
                            'name' => lang('1. Tổng Hợp Nợ Phải Trả Theo Phiếu YCMH'),
                            'is_permission' => checkPermission('synthetic_purchase', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
                            'key' => '',
                            'name' => lang('2. Theo Dõi Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('3. Chi Tiết Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Chi Tiết Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => '',
                            'name' => lang('5. Bảng Đối Chiếu Công Nợ Phải Trả'),
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Chi Tiết Thanh Toán Theo Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Chi Tiết Phiếu YCMH'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Chi Phí Giá Thành'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('9. Chi Phí Ngoài'),
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('IX. Phiếu Báo Cáo Sự Cố'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Phiếu Báo Cáo Không Phù Hợp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Phiếu Theo Dõi Báo Cáo Không Phù Hợp'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XII. Báo Cáo Chất Lượng'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Tổng Năng Suất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Năng Xuất Nhóm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Đơn Hàng Gấp, Khẩn, Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Complain Khách Hàng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Chất Lượng'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'report',
                    'name' => lang('II. Báo Cáo Bán Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Giá Trị Theo Đơn Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Hàng Tái Sản Xuất Lại'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('3. Tiến Độ Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Đơn Hàng Theo Lô'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('5. Tổng Hợp Giao Hàng Hàng Ngày'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('6. Phân Tích Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('7. Bảng Kê Giá Bán Gần Nhất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer',
                            'key' => '',
                            'name' => lang('8. Theo Dõi Nợ Phải Thu Ngày Tháng'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VI. Tồn Quỹ'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('1. Nhật Ký Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('2. Nhật Ký Chi'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('3. Nhật Ký Thu Chi'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('4. Tổng Hợp Tồn Quỹ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('5. Số Quỹ Tiền Mặt'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('6. Số Quỹ Ngân Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance',
                            'key' => '',
                            'name' => lang('7. Chi Phí'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('X. Báo Cáo Báo Giá'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Báo Giá Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Tổng Số Báo Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Chi Tiết Báo Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Báo Giá Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Báo Giá Không Đạt'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XIII. Báo Cáo Kỹ Thuật'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Bảo Trì Bảo Dưỡng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Năng Suất Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_repair',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_calibration',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Hiệu Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=muscle_group',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Bảo Dưỡng Cơ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'moderation/moderation_maintenance?type=muscle_group',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Bảo Dưỡng Điện'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=refrigeration_group',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Bảo Dưỡng Điện Lạnh'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=compressed_air_group',
                            'key' => '',
                            'name' => lang('8. Báo Cáo Bảo Dưỡng Hơi Khí-Nén'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/device',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tổng Hợp Thiết Bị/Tháng'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'report',
                    'name' => lang('III. Báo Cáo Mua Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Chi Tiết YCMH'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=general-purchase-report',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Tổng Hợp Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail-purchase-report',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Sổ Chi Tiết Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail-purchase_order-report',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Theo Dõi Đặt Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Tổng Hợp Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail_debt-report',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Chi Tiết Nợ Phải Trả Theo Mặt Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => '',
                            'name' => lang('7. Bảng Kê Giá Mua Gần Nhất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => '',
                            'name' => lang('8. Theo Dõi Nợ Phải Trả Ngày Tháng'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VII. Tồn Kho<br>A. Báo Cáo Tồn Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('1. Tổng Xuất Nhập Tồn Kho Hàng Ngày'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('2. Chi Tiết Xuất Nhập Tồn Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('3. Chi Nhánh Kho, Vị Trí Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('4. Thẻ Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('5. Nhóm Kho, Vị Trí'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Xuất Nhập Tồn'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Tồn Kho NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('8. Báo Cáo Tồn Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tồn Kho Bán Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('10. Báo Cáo Tồn Kho NPL cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('11. Báo Cáo Tồn Kho Thành Phẩm cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('12. Báo Cáo Tồn Kho Bán Thành Phẩm cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('13. Báo Cáo Tồn Kho NPL Cho phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('14. Báo Cáo Tồn Kho Thành Phẩm Cho Phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('15. Báo Cáo Tồn Kho Bán Thành Phẩm Cho Phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('16. Cảnh Báo Ngày Sử Dụng NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('17. Cảnh Báo Ngày Sử Dụng BTP-TP'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('B. Báo Cáo Chi Tiết Các Phiếu'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Chi Tiết Nhập Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Chi Tiết Nhập Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Chi Tiết Xuất Kho Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Chi Tiết Xuất Kho Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Chi Tiết Xuất Kho Khác'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Chi Tiết Chuyển Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Chi Tiết Điều Chỉnh (Kiểm Kê)'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XI. Báo Cáo Phát Triển Mẫu'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Báo Giá Tính Lô'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Tổng Số YC Phát Triên Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Chi Tiết Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Tổng Số Mẫu Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Tổng Số Mẫu K Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Tổng Số Mẫu Đánh Lần 2 3'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
            'sub_menu_four' => [
                [
                    'key' => 'report',
                    'name' => lang('IV. Báo Cáo Nợ Phải Thu'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result',
                            'key' => '',
                            'name' => lang('1. Tổng Hợp Nợ Phải Thu Theo Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result-detail',
                            'key' => '',
                            'name' => lang('2. Theo Dõi Nợ Phải Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('3. Chi tiết phiếu giao hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result-detail',
                            'key' => '',
                            'name' => lang('4. Chi Tiết Nợ Phải Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer?is_type=sale_listing',
                            'key' => '',
                            'name' => lang('5. Bảng Đối Chiếu Công Nợ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Chi Tiết Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('7. Chi Tiết Thanh Toán Theo Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VIII. Báo Cáo Lợi Nhuận'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/expenses_vs_income',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Lợi Nhuận'),
                            'is_permission' => checkPermission('expenses_vs_income', $staff_id, $is_admin),
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XII. Báo Cáo Kế Hoạch'),
                    'sub' => [
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Dàn Trang'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Báo Báo Ghép Size'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Ghi Kẽm, Đặt Khuôn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Kế Hoạch Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Hàng Phế'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
        ];

        $menu['category']['items']['kpi'] = [
            'name' => lang('V. Đánh Giá KPI Tháng/Năm'),
        ];

        $menu['category']['items']['power_bi'] = [
            'name' => lang('VI. Dashboard Power BI'),
            'sub_menu_one' => [
                [
                    'key' => 'customer',
                    'name' => lang('Dashbood Power BI'),
                    'sub' => [
                        [
                            'link' => 'admin/report_dashboard/dashboard_quotes',
                            'key' => 'dashboard__quotes',
                            'name' => lang('DASHBOARD Báo Giá Phát Triển Mẫu'),
                            'is_permission' => checkPermission('dashboard_quotes', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_revenue',
                            'key' => 'dashboard__revenue',
                            'name' => lang('DASHBOARD Doanh Thu'),
                            'is_permission' => checkPermission('dashboard_revenue', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_cost',
                            'key' => 'dashboard__cost',
                            'name' => lang('DASHBOARD Chi Phí'),
                            'is_permission' => checkPermission('dashboard_cost', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_stock',
                            'key' => 'dashboard__stock',
                            'name' => lang('DASHBOARD Tồn Kho'),
                            'is_permission' => checkPermission('dashboard_stock', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_manufactures',
                            'key' => 'dashboard__manufactures',
                            'name' => lang('DASHBOARD Sản Xuất'),
                            'is_permission' => checkPermission('dashboard_manufactures', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_task',
                            'key' => 'dashboard__task',
                            'name' => lang('DASHBOARD Công Việc'),
                            'is_permission' => checkPermission('dashboard_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_personnel',
                            'key' => 'dashboard__personnel',
                            'name' => lang('DASHBOARD Hành Chính - Nhân Sự'),
                            'is_permission' => checkPermission('dashboard_personnel', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_purchases',
                            'key' => 'dashboard__purchases',
                            'name' => lang('DASHBOARD Mua Hàng'),
                            'is_permission' => checkPermission('dashboard_purchases', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_business_results',
                            'key' => 'dashboard__business_results',
                            'name' => lang('DASHBOARD Kết Quả Kinh Doanh'),
                            'is_permission' => checkPermission('dashboard_business_results', $staff_id, $is_admin)
                        ],
                    ],
                ]
            ]
        ];

        return $menu;
    }
}

if (!function_exists('getMenuDashboard')) {
    function getMenuDashboard()
    {
        $CI = &get_instance();
        $staff_id = get_staff_user_id();
        $is_admin = is_admin($staff_id);
        $menu = [];
        $menu['category']['name'] = lang('Hạng mục');
        $numberRoman = 0;

        $numberRoman++;
        $strNumberRoman = convertToRoman($numberRoman);
        $numberM = 0;
        $menu['category']['items']['crm'] = [
            'name' => lang('' . $strNumberRoman . '. CRM - Quản Lý Khách Hàng'),
            'sub_name' => lang('Customer Relationship Management'),
            // 'sub_menu_one' => [
            //     [
            //         'key' => 'cs',
            //         'name' => lang('Customer Service - Chăm Sóc Khách Hàng'),
            //         'sub' => [
            //             [
            //                 'link' => 'admin/coupon_support/customer_order',
            //                 'key' => 'coupon_support',
            //                 'is_permission' => true,
            //                 'name' => lang('Phiếu Chăm Sóc Khách Hàng'),
            //             ],
            //         ],
            //     ],
            // ],
            'sub_menu_one' => [
                [
                    'key' => 'customer',
                    'name' => lang('Khách hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/clients/brand',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Mã Brand'),
                        ],
                        [
                            'link' => 'admin/clients/groups',
                            'key' => 'clients__groups',
                            'is_permission' => checkPermission('groups', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberM) . '. Danh Sách Nhóm Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/status_client',
                            'key' => 'clients__status_client',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberM) . '. Danh Sách Phân Loại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/clients',
                            'key' => 'clients',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberM) . '. Danh Sách Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/categories_other/standard_customer',
                            'key' => 'standard_customer',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Tiêu Chuẩn Khách Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/evaluate?type=customers',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Chứng Nhận Khách Hàng'),
                        ],
                        // [
                        //     'link' => 'admin/import_price_group',
                        //     'key' => 'import_price_group',
                        //     'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberM).'. Bảng Giá Theo Khách Hàng'),
                        // ],
                        [
                            'link' => 'admin/categories_other/discount_customer',
                            'key' => 'discount_customer',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Chiết Khấu Khách Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/other/quota_delivery_package',
                            'key' => 'quota_delivery_package',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Định Mức Kiện Hàng Giao'),
                        ],
                        [
                            'link' => 'admin/list_other/other/code_file_products',
                            'key' => 'code_file_products',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Mã File Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/products/category',
                            'key' => 'products__category',
                            'is_permission' => checkPermission('products_category', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberM) . '. Danh Sách Nhóm Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/species?type_title=products',
                            'key' => 'species',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Chủng Loại Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/products',
                            'key' => 'products',
                            'is_permission' => checkPermission('products', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberM) . '. Danh Sách Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/size?type_title=products',
                            'key' => 'size',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberM) . '. Danh Sách Kích Thước Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/units?type_title=products',
                            'key' => 'units',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberM) . '. Đơn Vị Tính Sản Phẩm'),
                            'is_settings' => 1,
                        ],
                        // [
                        //     'link' => 'admin/quote_stage',
                        //     'key' => 'quote_stage',
                        //     'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                        //     'name' => lang('Bảng Giá Công Đoạn Theo Khách Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/import_price_group',
                        //     'key' => 'import_price_group',
                        //     'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                        //     'name' => lang('Bảng Giá Sản Phẩm Khách Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/clients/groups',
                        //     'key' => 'clients',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang('Danh Sách Loại Khách Hàng'),
                        // ],
                    ],
                ],
            ]
        ];

        $numberRoman++;
        $strNumberRoman = convertToRoman($numberRoman);
        $numberS = 0;
        $menu['category']['items']['scc'] = [
            'name' => lang('' . $strNumberRoman . '. SCC - Kiểm Soát Chuỗi Cung Ứng'),
            'sub_name' => lang('Supplier Chain Control'),
            'sub_menu_one' => [
                [
                    'key' => 'suppliers',
                    'name' => lang('Nhà Cung Cấp'),
                    'sub' => [
                        [
                            'link' => 'admin/suppliers/groups',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberS) . '. Danh Sách Nhóm Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/supplier_classify',
                            'key' => 'suppliers',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Phân Loại Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers',
                            'key' => 'suppliers',
                            'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberS) . '. Danh Sách Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/suppliers/evaluation_criteria',
                            'key' => 'suppliers__evaluation_criteria',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Tiêu Chuẩn Nhà Cung Cấp'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=suppliers',
                            'key' => 'certification',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Chứng Nhận Nhà Cung Cấp'),
                        ],
                        // [
                        //     'link' => 'admin/import_price',
                        //     'key' => 'import_price',
                        //     'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberS).'. Bảng Giá Theo Nhà Cung Cấp'),
                        // ],
                        [
                            'link' => 'admin/categories_other/discount_supplier_new',
                            'key' => 'discount_supplier_new',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Chiết Khấu Nhà Cung Cấp'),
                            'is_settings' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Đánh Giá-Chứng Nhận'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Đào Tạo'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Đo Kiểm'),
                        // ],
                        [
                            'link' => 'admin/categories_other/unit_package_npl',
                            'key' => 'unit_package_npl',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Đơn Vị Đóng Gói NPL'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/unit_package_sp',
                            'key' => 'unit_package_sp',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberS) . '. Danh Sách Đơn Vị Đóng Gói Sản Phẩm'),
                            'is_settings' => 1
                        ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Gia Công'),
                        // ],
                    ],
                ]
            ],
        ];

        // $CI->db->select('tblsuppliers_groups.id, tblsuppliers_groups.name, tblsuppliers_groups.code');
        // $CI->db->from('tblsuppliers_groups');
        // $categoryProducts = $CI->db->get()->result_array();
        // $arrCategoryProducts = [];
        // if (!empty($categoryProducts)) {
        //     foreach ($categoryProducts as $key => $value) {
        //         $arrCategoryProducts = [
        //             'link' => 'admin/suppliers?category_id='.$value['id'],
        //             'key' => 'suppliers',
        //             'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
        //             'name' => lang(''.(++$numberS).'. Danh Sách Nhóm '.$value['name']),
        //         ];
        //         $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = $arrCategoryProducts;
        //     }
        // }
        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/items/category',
            'key' => 'items__category',
            'is_permission' => checkPermission('items_category', $staff_id, $is_admin),
            'name' => lang('' . (++$numberS) . '. Danh Sách Nhóm NPL'),
        ];

        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/species?type_title=materials',
            'key' => 'species',
            'is_settings' => 1,
            'is_permission' => $is_admin ? true : false,
            'name' => lang('' . (++$numberS) . '. Danh Sách Chủng Loại NPL'),
        ];

        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/units?type_title=materials',
            'key' => 'units',
            'name' => lang('' . (++$numberS) . '. Danh Sách Đơn Vị Tính NPL'),
            'is_permission' => $is_admin ? true : false,
            'is_settings' => 1,
        ];

        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/size?type_title=materials',
            'key' => 'size',
            'is_permission' => $is_admin ? true : false,
            'name' => lang('' . (++$numberS) . '. Danh Sách Kích Thước NPL'),
            'is_settings' => 1,
        ];

        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/items',
            'key' => 'items',
            'is_permission' => checkPermission('items', $staff_id, $is_admin),
            'name' => lang('' . (++$numberS) . '. Danh Sách Nguyên Phụ Liệu'),
        ];

        // $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
        //     'link' => '',
        //     'key' => '',
        //     'is_permission' => true,
        //     'name' => lang(''.(++$numberS).'. Danh Sách Mã Khuân Bế'),
        // ];

        // $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
        //     'link' => 'admin/units',
        //     'key' => 'units',
        //     'is_permission' => $is_admin ? true : false,
        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Thanh Toán'),
        //     'is_settings' => 1,
        // ];

        $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
            'link' => 'admin/categories_other/unit_warehouse_npl',
            'key' => 'unit_warehouse_npl',
            'is_permission' => 1,
            'name' => lang('' . (++$numberS) . '. Danh Sách Đơn Vị Vào Kho NPL'),
            'is_settings' => 1,
        ];

        // $menu['category']['items']['scc']['sub_menu_one'][0]['sub'][] = [
        //     'link' => 'admin/units',
        //     'key' => 'units',
        //     'is_permission' => $is_admin ? true : false,
        //     'name' => lang(''.(++$numberS).'. Danh Sách Đơn Vị Vào Kho Sản Phẩm'),
        //     'is_settings' => 1,
        // ];

        //

        $numberRoman++;
        $strNumberRoman = convertToRoman($numberRoman);
        $menu['category']['items']['erp'] = [
            'name' => lang('' . $strNumberRoman . '. ERP - Hoạch Định Nguồn Lực DN'),
            'sub_name' => lang('Enterprise Resource Planning'),
            'is_not_click' => 1,
        ];

        $number = 0;
        $numberC = 0;
        $numberOne = 0;
        $numberOneB = 0;
        $numberOneC = 0;
        $numberOneD = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['office_management'] = [
            'name' => lang('' . (++$number) . '. HCNS'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'hr',
                    'name' => lang('I. Hành Chính Nhân Sự(HR)<br>A. Công Việc'),
                    'sub' => [
                        [
                            'link' => 'admin/tasks?kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Công Việc Kanban'),
                        ],
                        [
                            'link' => 'admin/tasks/calendar_pod',
                            'key' => 'tasks__calendar_pod',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Lịch Công Việc'),
                        ],
                        [
                            'link' => 'admin/gantt',
                            'key' => 'gantt',
                            'is_permission' => checkPermission('gantt', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Sơ Đồ Gantt'),
                        ],
                        [
                            'link' => 'admin/request_bussiness',
                            'key' => 'request_bussiness',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Công Tác'),
                        ],
                        [
                            'link' => 'admin/request_control_vehicle_bussiness',
                            'key' => 'request_control_vehicle_bussiness',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Điều Xe Công Tác'),
                        ],
                        [
                            'link' => 'admin/suggest_educate',
                            'key' => 'suggest_educate',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/request_improve',
                            'key' => 'request_improve',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Cải Tiến'),
                        ],
                    ]
                ],
                [
                    'key' => 'edu',
                    'name' => lang('B. Tuyễn Dụng Đào Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_recruitment',
                            'key' => 'suggest_recruitment',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Tuyển Dụng'),
                        ],
                        [
                            'link' => 'admin/moderation_recruitment',
                            'key' => 'moderation_recruitment',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Điều Độ Công Việc Tuyển Dụng'),
                        ],
                    ]
                ],
                [
                    'key' => 'salary',
                    'name' => lang('C. Tiền Lương'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_paid_holidays',
                            'key' => 'suggest_paid_holidays',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOneC) . '. Phiếu Yêu Cầu Nghỉ Phép'),
                        ],
                        [
                            'link' => 'admin/paid_holidays/report_paid_holiday_leave',
                            'key' => 'report_paid_holiday_leave',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Thống Kê Ngày Phép'),
                        ],
                        // [
                        //     'link' => 'admin/paid_holidays/report_paid_holiday_leave',
                        //     'key' => 'setup_paid_holidays',
                        //     'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberOneC).'. Loại Phép'),
                        // ],
                        [
                            'link' => 'admin/paid_holidays/setup_paid_holidays',
                            'key' => 'setup_paid_holidays',
                            'is_permission' => checkPermission('paid_holidays', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Theo Dõi Phép Năm'),
                        ],
                        [
                            'link' => 'admin/salary/timekeeping',
                            'key' => 'salary__timekeeping',
                            'is_permission' => checkPermission('timekeeping', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Chi tiết giờ công'),
                        ],
                        [
                            'link' => 'admin/salary/dashboard_timekeeping',
                            'key' => 'salary__dashboard_timekeeping',
                            'is_permission' => checkPermission('dashboard_timekeeping', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Thống Kê Ngày Công'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_overtime',
                            'key' => 'business_fee_other__business_fee_other_overtime',
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Tăng Ca Tháng'),
                        ],
                        [
                            'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                            'key' => 'business_fee_other__report_business_fee_other_overtime',
                            'name' => lang('' . (++$numberOneC) . '. Thống Kê Giờ Tăng Ca'),
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/business_fee_other/business_fee_other_calculate',
                            'key' => 'business_fee_other__business_fee_other_calculate',
                            'name' => lang('' . (++$numberOneC) . '. Bảng Tính Tăng Ca'),
                            'is_permission' => checkPermission('business_overtime', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_payment',
                            'key' => 'payroll__payroll_payment',
                            'is_permission' => checkPermission('payroll_payment', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Phiếu Tạm Ứng Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Phiếu Lương'),
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneC) . '. Bảng Lương'),
                        ],
                    ]
                ],
                [
                    'key' => 'ehs',
                    'name' => lang('D. An Toàn Lao Động'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_evaluate',
                            'key' => 'suggest_evaluate',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOneD) . '. Phiếu Yêu Cầu Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/suggest_accreditation',
                            'key' => 'suggest_accreditation',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOneD) . '. Phiếu Yêu Cầu Test/Kiểm Định'),
                        ],
                        [
                            'link' => 'admin/suggest_check',
                            'key' => 'suggest_check',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOneD) . '. Phiếu Yêu Cầu Kiểm Tra Vệ Sinh ATLĐ - 5S'),
                        ],
                        [
                            'link' => 'admin/suggest_pccc',
                            'key' => 'suggest_pccc',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOneD) . '. Phiếu Yêu Cầu Kiểm Tra PCCC'),
                        ],
                    ]
                ],
                [
                    'key' => 'utilities',
                    'name' => lang('E. Tiện Ích'),
                    'sub' => [
                        [
                            'link' => 'admin/utilities/media',
                            'key' => 'utilities_media',
                            'is_permission' => 1,
                            'name' => lang('1. Biểu mẫu dùng chung'),
                        ],
                    ]
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'staff',
                    'name' => lang('II. Hành Chính Nhân Sự'),
                    'sub' => [
                        [
                            'link' => 'admin/moderation/moderation_check',
                            'key' => 'moderation_check',
                            'name' => lang('1. Kế Hoạch Điều Độ VSAT-5S Nhà Xưởng, VP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_task',
                            'key' => 'moderation_task',
                            'name' => lang('2. Kế Hoạch Điều Độ Công Việc'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('3. Kế Hoạch Điều Độ Tuyển Dụng'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation/moderation_educate',
                            'key' => 'moderation_educate',
                            'name' => lang('3. Kế Hoạch Điều Độ Đào Tạo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=1',
                            'key' => 'probationary_evaluate?type=1',
                            'name' => lang('4. Kế Hoạch Điều Độ Đánh Giá Thử Việc'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=2',
                            'key' => 'probationary_evaluate?type=2',
                            'name' => lang('5. Kế Hoạch Điều Độ Đánh Giá Nhân Sự'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/probationary_evaluate?type=3',
                            'key' => 'probationary_evaluate?type=3',
                            'name' => lang('6. Kế Hoạch Điều Độ Đánh Giá NS-Tay Nghề'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('7. Kế Hoạch Điều Độ Sao Y'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('8. Kế Hoạch Điều Độ Công Chứng'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/plan_propose?group=pay_slip',
                            'key' => '',
                            'name' => lang('7. Kế Hoạch Điều Độ Chi'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('10. Kế Hoạch Điều Độ Tái Ký'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('11. Kế Hoạch Điều Độ Gia Hạn'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation/moderation_evaluate_skill',
                            'key' => 'moderation_evaluate_skill',
                            'name' => lang('8. Kế Hoạch Điều Độ Thi Đánh Giá Tay Nghề'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('13. Kế Hoạch Điều Độ Thi Đánh Giá KPI'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation/moderation_evaluate_supplier',
                            'key' => 'moderation_evaluate_supplier',
                            'name' => lang('9. Kế Hoạch Điều Độ Tìm Kiếm Đánh Giá Nhà Cung Cấp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_evaluate',
                            'key' => 'moderation_evaluate',
                            'name' => lang('10. Kế Hoạch Điều Độ Đánh Giá'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'created',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/branch',
                            'key' => 'branch',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Văn Phòng - Chi Nhánh'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Giấy Phép'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Sơ Đồ Tổ Chức'),
                        // ],
                        [
                            'link' => 'admin/regulations_active',
                            'key' => 'regulations_active',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Quy Chế Hoạt Động Phòng Ban'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Quy Chế Khác'),
                        // ],
                        [
                            'link' => 'admin/rules_group',
                            'key' => 'rules_group',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Nội Quy'),
                        ],
                        [
                            'link' => 'admin/regulations/rules',
                            'key' => 'rules',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nội Quy'),
                        ],
                        [
                            'link' => 'admin/regulations_group',
                            'key' => 'regulations_group',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Quy Định Chung'),
                        ],
                        [
                            'link' => 'admin/regulations_code',
                            'key' => 'regulations_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Quy Định'),
                        ],
                        [
                            'link' => 'admin/regulations/fixation',
                            'key' => 'fixation',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Quy Định Chung'),
                        ],
                        [
                            'link' => 'admin/decision/list',
                            'key' => 'decision',
                            'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Quyết Định'),
                        ],
                        [
                            'link' => 'admin/board',
                            'key' => 'board',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hội Đồng'),
                        ],
                        [
                            'link' => 'admin/board',
                            'key' => 'board',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Ban'),
                        ],
                        [
                            'link' => 'admin/room',
                            'key' => 'room',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Phòng (Nhân Sự)'),
                        ],
                        [
                            'link' => 'admin/block',
                            'key' => 'block',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Khối'),
                        ],
                        [
                            'link' => 'admin/departments',
                            'key' => 'departments',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bộ Phận (Nhân Sự)'),
                        ],
                        [
                            'link' => 'admin/nest',
                            'key' => 'nest',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tổ (Nhân Sự)'),
                        ],
                        [
                            'link' => 'admin/group',
                            'key' => 'group',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm (Nhân Sự)'),
                        ],
                        [
                            'link' => 'admin/roles',
                            'key' => 'roles',
                            'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Vị Trí'),
                        ],
                        [
                            'link' => 'admin/email',
                            'key' => 'email',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Email Vị Trí'),
                        ],
                        [
                            'link' => 'admin/kpi/detail_task',
                            'key' => 'detail_task',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mô Tả Công Việc'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Trang Thiết Bị Vị Trí'),
                        ],
                        // [
                        //     'link' => 'admin/roles',
                        //     'key' => 'roles',
                        //     'is_permission' => checkPermission('roles', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Chức Vụ'),
                        // ],
                        [
                            'link' => 'admin/category_salary/step_salary',
                            'key' => 'step_salary',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hệ Lương Công Việc'),
                        ],
                        // [
                        //     'link' => 'admin/salary_group',
                        //     'key' => 'salary_group',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Nhóm Lương'),
                        // ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => 'payroll',
                            'is_permission' => checkPermission('payroll_salary', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Lương'),
                        ],
                        [
                            'link' => 'admin/category_salary/category_permission',
                            'key' => 'category_permission',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Phép'),
                        ],
                        [
                            'link' => 'admin/category_salary/permission',
                            'key' => 'permission',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Phép'),
                        ],
                        [
                            'link' => 'admin/category_salary/coefficient_salary',
                            'key' => 'coefficient_salary',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hệ Số Lương Năng Lực'),
                        ],
                        [
                            'link' => 'admin/kpi/category_kpi',
                            'key' => 'category_kpi',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm KPIs'),
                        ],
                        [
                            'link' => 'admin/kpi/category_kpi',
                            'key' => 'category_kpi',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách KPIs'),
                        ],
                        [
                            'link' => 'admin/salary_deadline',
                            'key' => 'salary_deadline',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thời Hạn Xét Tăng Lương'),
                        ],
                        [
                            'link' => 'admin/contract_code',
                            'key' => 'contract_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Hợp Đồng Lao Động'),
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => 'contract_labor',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hợp Đồng Lao Động'),
                        ],
                        [
                            'link' => 'admin/staff',
                            'key' => 'staff',
                            'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thông Tin Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/allowance_reduce',
                            'key' => 'allowance_reduce',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Phụ Cấp Giảm Trừ'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/physical_deadline',
                            'key' => 'physical_deadline',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thời Hạn Khám Sức Khỏe'),
                        ],
                        [
                            'link' => 'admin/recipe_kpis',
                            'key' => 'recipe_kpis',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Công Thức Quy Đổi KPIs'),
                        ],
                        [
                            'link' => 'admin/departmental_procedures/department_system',
                            'key' => 'department_system',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Quy Trình Liên Phòng Ban (Hệ Thống)'),
                        ],
                        [
                            'link' => 'admin/tasks_group',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Công Việc Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/tasks?not_kanban=true',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Công Việc Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/departmental_procedures/department',
                            'key' => 'departmental_procedures',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Quy Trình Công Việc Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/department_work_norms',
                            'key' => 'department_work_norms',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Công Việc Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/categories_other/cleaning_5s',
                            'key' => 'cleaning_5s',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Khu Vực Vệ Sinh ATLĐ-5S'),
                            'is_settings' => true,
                        ],
                        [
                            'link' => 'admin/quota_bonus_discipline?type=1',
                            'key' => 'quota_bonus_discipline',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Khen Thưởng KPIs'),
                        ],
                        [
                            'link' => 'admin/quota_bonus_discipline?type=2',
                            'key' => 'quota_bonus_discipline',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Kỷ Luật KPIs'),
                        ],
                        [
                            'link' => 'admin/type_improve',
                            'key' => 'type_improve',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/category_evaluate',
                            'key' => 'category_evaluate',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/evaluate',
                            'key' => 'evaluate',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Đánh Giá'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=educate',
                            'key' => 'evaluate_educate',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/evaluate?type=educate',
                            'key' => 'train_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Đào Tạo'),
                        ],
                        [
                            'link' => 'admin/recommended_list',
                            'key' => 'recommended_list',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/propose_code',
                            'key' => 'propose_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Đề Xuất'),
                        ],
                        [
                            'link' => 'admin/area_group',
                            'key' => 'area_group',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Khu Vực'),
                        ],
                        [
                            'link' => 'admin/area_code',
                            'key' => 'area_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Khu Vực'),
                        ],
                        [
                            'link' => 'admin/post_office_code',
                            'key' => 'post_office_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Số Bưu Điện'),
                        ],
                        [
                            'link' => 'admin/category_salary_list',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Bảng Danh Mục Lương'),
                        ],
                        [
                            'link' => 'admin/category_eloquence',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Bảng Khoản Phép'),
                        ],
                        [
                            'link' => 'admin/list_subsidize',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Bảng Danh Sách Hỗ Trợ'),
                        ],
                        [
                            'link' => 'admin/allowance_staff/allowance_toxic',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh sách trợ cấp độc hại'),
                        ],
                        [
                            'link' => 'admin/allowance_staff/allowance_pccc',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh sách trợ cấp PCCC'),
                        ],
                        [
                            'link' => 'admin/allowance_staff/allowance_fsc',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh sách trợ cấp FSC'),
                        ],
                        [
                            'link' => 'admin/allowance_staff/seniority',
                            'key' => '',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Danh sách theo dõi thâm niên'),
                        ],
                        [
                            'link' => 'admin/tasks',
                            'key' => 'tasks',
                            'is_permission' => checkPermission('tasks', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Công Việc'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Quy Trình'),
                        // ],
                        // [
                        //     'link' => 'admin/branch',
                        //     'key' => 'branch',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Chi Nhánh'),
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/departments',
                        //     'key' => 'departments',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Phòng Ban'),
                        // ],
                        // [
                        //     'link' => 'admin/departments',
                        //     'key' => 'departments',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Bộ Phận'),
                        // ],
                        // [
                        //     'link' => 'admin/roles',
                        //     'key' => 'roles',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Chức Vụ'),
                        // ],
                        // [
                        //     'link' => 'admin/staff',
                        //     'key' => 'staff',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh sách nhân viên'),
                        // ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories',
                        //     'is_settings' => 1,
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Trang Thiết Bị'),
                        // ],
                        // [
                        //     'link' => 'admin/kpi/list',
                        //     'key' => 'kpi',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách KPI'),
                        // ],
                        // [
                        //     'link' => 'admin/decision/list',
                        //     'key' => 'decision',
                        //     'is_permission' => checkPermission('decision_list', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Quyết Định'),
                        // ],
                        // [
                        //     'link' => 'admin/category_salary/contract_labor',
                        //     'key' => 'contract_labor',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Hợp Đồng Lao Động'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=license',
                        //     'key' => 'evaluate',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Giấy Phép'),
                        // ],
                        // [
                        //     'link' => 'admin/evaluate?type=certification',
                        //     'key' => 'evaluate',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Chứng Nhận'),
                        // ],
                        // [
                        //     'link' => 'admin/staff',
                        //     'key' => 'staff',
                        //     'is_permission' => checkPermission('staff', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberC).'. Thông Tin Nhân Viên'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Vệ Sinh ATLĐ-5S'),
                        // ],
                        // [
                        //     'link' => 'admin/regulations/rules',
                        //     'key' => 'regulations_rules',
                        //     'is_permission' => $is_admin ? true : false,
                        //     'name' => lang(''.(++$numberC).'. Nội Quy'),
                        // ],
                        [
                            'link' => 'admin/regulations/fixation',
                            'key' => 'regulations_fixation',
                            'is_permission' => $is_admin ? true : false,
                            'name' => lang('' . (++$numberC) . '. Quy Định'),
                        ],
                        //						[
                        //                            'link' => 'admin/measuring_equipment',
                        //                            'key' => 'measuring_equipment',
                        //                            'is_permission' => $is_admin ? true : false,
                        //                            'name' => lang(''.(++$numberC).'. Danh Sách Thiết Bị Đo Kiểm'),
                        //                        ],
                    ]
                ]
            ],
        ];

        $numberOne = 0;
        $numberOneA = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['plan'] = [
            'name' => lang('' . (++$number) . '. Kế Hoạch'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'planing',
                    'name' => lang('A.1. PLANNING'),
                    'sub' => [
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Công Đoạn'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/moderation_quotes',
                            'key' => 'moderation_quotes',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Báo Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => 'moderation_template',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_order',
                            'key' => 'moderation_order',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Đơn Hàng'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Xuất Kho Tồn Thành Phẩm'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Xuất Kho Tồn NPL'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Nhập Tồn Thành Phẩm'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Nhập Kho Tồn NPL'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Xuất Bìa Mẫu Sản Xuất'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Xuất Khuôn Bế Tồn Sản Xuất'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Mở Lệnh Sản Xuất'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Ghép In'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Dàn Trang In'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Ghi Kẽm'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation_purchase_material',
                            'key' => 'moderation_purchase_material',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Mua NPL'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Nhập Kho NPL'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Cắt Giấy'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ In Offset'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=254',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ In Flexo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=255',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ In HP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=257',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ In BarCode'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=256',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ In Lụa'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=260',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Phun Bóng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=261',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Cán Màng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=263',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Bồi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=264',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Ép Nhũ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=265',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Bế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=331',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Cắt Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=330',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Khoan Lỗ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=332',
                            'key' => '',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Cắt Demi'),
                            'is_permission' => 1,
                        ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Gấp Sổ'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation_plan_stage/moderation_plan_stage_phan_don',
                            'key' => 'moderation_plan_stage_phan_don',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Điều Độ Công Đoạn Phân Đơn-Dán Tem'),
                            'is_permission' => 1,
                        ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Chọt Lỗ'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=314',
                            'key' => '',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Nhóm Kiểm Thành Phẩm'),
                            'is_permission' => 1,
                        ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Nhóm Kiểm Cố Định'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation_plan_stage/moderation_plan_stage_giao_hang',
                            'key' => 'moderation_plan_stage_giao_hang',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Điều Độ Điều Xe'),
                            'is_permission' => 1,
                        ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Gia Công'),
                        //     'is_permission' => 1,
                        // ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Tăng Ca'),
                        //     'is_permission' => 1,
                        // ],
                        // [   
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Bàn Giao Kế Hoạch-Hàng Hóa-Sản Xuất'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=19',
                            'key' => 'moderation_plan_stage',
                            'name' => lang('' . (++$numberOneA) . '. Kế Hoạch Công Đoạn Cắt Kiểm Kim Loại'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=2',
                            'key' => 'suggest_plan_purchase?type=2',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Mua Vật Tư'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=3',
                            'key' => 'suggest_plan_purchase?type=3',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Mua Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_evaluate',
                            'key' => 'suggest_plan_evaluate',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Đánh Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_overtime',
                            'key' => 'suggest_plan_overtime',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_outsource',
                            'key' => 'suggest_plan_outsource',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Gia Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_educate',
                            'key' => 'suggest_plan_educate',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Đào Tạo'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_recruitment',
                            'key' => 'suggest_plan_recruitment',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Tuyển Dụng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_plan_deparment',
                            'key' => 'suggest_plan_deparment',
                            'name' => lang('' . (++$numberOneA) . '. Phiếu Yêu Cầu Kế Hoạch Phòng Ban'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Giao Hàng-Thu'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOneA).'. Kế Hoạch Điều Độ Nhập Kho-Chi'),
                        //     'is_permission' => 1,
                        // ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'purchase',
                    'name' => lang('II. Thu Mua'),
                    'sub' => [
                        [
                            'link' => 'admin/return_suppliers',
                            'key' => 'return_suppliers',
                            'is_permission' => checkPermission('return_suppliers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwo) . '. Trả hàng'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=supplier',
                            'key' => 'suggest_evaluate?type=supplier',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Đánh Giá NCC'),
                        ],
                        // [
                        //     'link' => 'admin/supplier_evaluate',
                        //     'key' => 'supplier_evaluate',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberTwo).'. Phiếu Đánh Giá NCC'),
                        // ],
                        [
                            'link' => 'admin/purchases/synthetic_purchase',
                            'key' => 'purchases',
                            'is_permission' => checkPermission('purchases', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Mua Hàng (PR)'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberTwo).'. Phiếu Điều Độ Công Việc Đơn Mua Hàng'),
                        // ],
                        [
                            'link' => 'admin/purchase_order',
                            'key' => 'purchase_order',
                            'is_permission' => checkPermission('purchase_order', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwo) . '. Đơn Mua Hàng (PO)'),
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'create',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberThree).'. Danh Sách Nhà Cung Cấp'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers/groups',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers_group', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberThree).'. Nhóm Nhà Cung Cấp'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberThree).'. Bảng Giá Nhà Cung Cấp'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberThree).'. Mặt Hàng Chủ Đạo'),
                        // ],
                        // [
                        //     'link' => 'admin/items',
                        //     'key' => 'items',
                        //     'is_permission' => checkPermission('items', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberThree).'. Danh Sách Nguyên Vật Liệu'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberThree).'. Danh Sách Đơn Vị Sửa Chữa'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberThree).'. Danh Sách Đơn Vị Nhà Gia Công'),
                        // ],
                    ],
                ]
            ]
        ];

        $numberOne = 0;
        $numberOneB = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['business'] = [
            'name' => lang('' . (++$number) . '. Kinh Doanh'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'quotes',
                    'name' => lang('A. Báo Giá - PTM'),
                    'sub' => [
                        [
                            'link' => 'admin/quotation_request',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Báo Giá'),
                        ],
                        [
                            'link' => 'admin/moderation_quotes',
                            'key' => 'moderation_quotes',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Điều Độ Công Việc Báo Giá'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Tính Giá'),
                        // ],
                        [
                            'link' => 'admin/request_template',
                            'key' => 'request_template',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Phát Triển Mẫu'),
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => 'moderation_template',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Điều Độ Công Việc Phát Triển Mẫu'),
                        ],
                        [
                            'link' => 'admin/ptm',
                            'key' => 'ptm',
                            'is_permission' => true,
                            'name' => '' . (++$numberOne) . '. Phiếu Yêu Cầu Phát Triển Mẫu (PTM)',
                        ],
                    ],
                ],
                [
                    'key' => 'orders',
                    'name' => lang('B. Đơn Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/orders',
                            'key' => 'orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneB) . '. Đơn Đặt Hàng Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/orders/import_orders',
                            'key' => 'import_orders',
                            'is_permission' => checkPermission('orders', $staff_id, $is_admin, 'create'),
                            'name' => lang('' . (++$numberOneB) . '. Import Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/orders/information',
                            'key' => 'orders__information',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Thống Kê Đơn Hàng'),
                        ],
                        [
                            'link' => 'admin/returned_goods',
                            'key' => 'returned_goods',
                            'is_permission' => checkPermission('returned_goods', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOneB) . '. Trả Lại Hàng Bán'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberOneB).'. Phiếu Thống Kê Mã Thành Phẩm'),
                        // ],
                        [
                            'link' => 'admin/purchase_order_request',
                            'key' => 'purchase_order_request',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/moderation_order',
                            'key' => 'moderation_order',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Kế Hoạch Điều Độ Đơn Đặt Hàng'),
                        ],
                        [
                            'link' => 'admin/request_client_complaints',
                            'key' => 'request_client_complaints',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/moderation_request_client_complaints',
                            'key' => 'moderation_request_client_complaints',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiều Điều Độ Công Việc Xử Lý Khiếu Nại Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=customer',
                            'key' => 'suggest_evaluate?type=customer',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Đánh Giá Khách Hàng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'create',
                    'color' => '#2196f3',
                    'name' => lang('II. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/quotes',
                            'key' => 'quotes',
                            'is_permission' => checkPermission('quotes', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwo) . '. Bảng Báo Giá'),
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('' . (++$numberTwo) . '. Danh sách Công Đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwo) . '. Bảng Giá Công Đoạn'),
                        ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories__machines',
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Thông Tin Thiết Bị'),
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        // ],
                        [
                            'link' => 'admin/print_type',
                            'key' => 'print_type',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Danh Sách Loại Hình In'),
                            'is_settings' => 1,
                        ],
                        // [
                        //     'link' => 'admin/clients',
                        //     'key' => 'clients',
                        //     'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Thông Tin Khách Hàng'),
                        // ],
                        // [
                        //     'link' => 'admin/products',
                        //     'key' => 'products',
                        //     'is_permission' => checkPermission('products', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Thông Tin Sản Phẩm'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Thông Tin Nhà Cung Cấp'),
                        // ],
                        // [
                        //     'link' => 'admin/items',
                        //     'key' => 'items',
                        //     'is_permission' => checkPermission('items', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Thông Tin NPL'),
                        // ],
                        // [
                        //     'link' => 'admin/status_orders',
                        //     'key' => 'status_orders',
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Trạng Thái Đơn Hàng'),
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberTwo).'. Danh Sách Phân Loại Đơn Hàng'),
                        // ],
                        [
                            'link' => 'admin/products/category_stages',
                            'key' => 'category__stages',
                            'name' => lang('' . (++$numberTwo) . '. Danh Sách Nhóm công đoạn'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                    ]
                ]
            ]
        ];

        $numberOne = 0;
        $numberOneB = 0;
        $numberOneC = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['marketing'] = [
            'name' => lang('' . (++$number) . '. Marketing'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'orders_delivery',
                    'name' => lang('I. SALES/MARKETTING<br>A. Xuất Nhập Khẩu'),
                    'sub' => [],
                ],
                [
                    'key' => 'orders_delivery',
                    'name' => lang('B. Tư Vấn Khách Hàng'),
                    'sub' => [],
                ],
                [
                    'key' => 'support',
                    'name' => lang('C. Chăm Sóc Khách Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_support/customer_order',
                            'key' => 'coupon_support',
                            'is_permission' => true,
                            'name' => lang('1. Phiếu Chăm Sóc Khách Hàng'),
                        ],
                    ],
                ]
            ],
            // 'sub_menu_two' => [
            //     [
            //         'key' => 'create',
            //         'color' => '#2196f3',
            //         'name' => lang('II. Dữ Liệu Tạo'),
            //         'sub' => [
            //             // [
            //             //     'link' => 'admin/clients',
            //             //     'key' => 'clients__clients',
            //             //     'is_permission' => checkPermission('customers', $staff_id, $is_admin),
            //             //     'name' => lang(''.(++$numberTwo).'. Danh Sách Khách Hàng'),
            //             // ],
            //             // [
            //             //     'link' => 'admin/clients/groups',
            //             //     'key' => 'clients__groups',
            //             //     'is_permission' => checkPermission('groups', $staff_id, $is_admin),
            //             //     'name' => lang(''.(++$numberTwo).'. Nhóm Khách Hàng'),
            //             // ],
            //             // [
            //             //     'link' => 'admin/quote_stage',
            //             //     'key' => 'quote_stage',
            //             //     'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
            //             //     'name' => lang(''.(++$numberTwo).'. Bảng Giá Công Đoạn Theo Khách Hàng'),
            //             // ],
            //             // [
            //             //     'link' => 'admin/import_price_group',
            //             //     'key' => 'import_price_group',
            //             //     'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
            //             //     'name' => lang(''.(++$numberTwo).'. Bảng Giá Sản Phẩm Khách Hàng'),
            //             // ],
            //             // [
            //             //     'link' => 'admin/clients/all_shipping',
            //             //     'key' => 'clients__all_shipping',
            //             //     'is_permission' => checkPermission('customers', $staff_id, $is_admin),
            //             //     'name' => lang(''.(++$numberTwo).'. Danh Sách Địa Chỉ Giao Hàng'),
            //             // ],
            //         ],
            //     ],
            // ]
        ];

        $numberC = 0;
        $numberOne = 0;
        $numberOneB = 0;
        $numberOneC = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['technique'] = [
            'name' => lang('' . (++$number) . '. Kỹ Thuật'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'planing',
                    'name' => lang('I. Kỹ Thuật'),
                    'sub' => [
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=muscle_group',
                            'key' => '',
                            'name' => lang('1. Kế Hoạch Điều Độ Bảo Dưỡng Cơ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=electrical_group',
                            'key' => '',
                            'name' => lang('2. Kế Hoạch Điều Độ Bảo Dưõng Điện'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=infrastructure_group',
                            'key' => '',
                            'name' => lang('3. Kế Hoạch Điều Độ Bảo Dưõng Điện, Nước, Hạ Tầng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=compressed_air_group',
                            'key' => '',
                            'name' => lang('4. Kế Hoạch Điều Độ Bảo Dưõng Hơi-Khí Nén'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=refrigeration_group',
                            'key' => '',
                            'name' => lang('5. Kế Hoạch Điều Độ Bảo Dưõng Điện Lạnh'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=pccc_group',
                            'key' => '',
                            'name' => lang('6. Kế Hoạch Điều Độ Bảo Dưỡng Thiết Bị PCCC'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=network_group',
                            'key' => '',
                            'name' => lang('7. Kế Hoạch Điều Độ Bảo Dưỡng Camera-Mạng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=server_group',
                            'key' => '',
                            'name' => lang('8. Kế Hoạch Điều Độ Bảo Dưỡng Server'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=computer_group',
                            'key' => '',
                            'name' => lang('9. Kế Hoạch Điều Độ Bảo Dưỡng Máy Tính VP'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=computer_group',
                            'key' => '',
                            'name' => lang('10. Kế Hoạch Điều Độ Bảo Dưỡng Máy In VP'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('11. Kế Hoạch Điều Độ VSAT-5S Thiết Bị'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/tools_supplies',
                        //     'key' => 'tools_supplies',
                        //     'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                        //     'name' => lang('12. Kiểm Soát Vật Tư Thay Thế'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/tools_supplies',
                        //     'key' => 'tools_supplies',
                        //     'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                        //     'name' => lang('13. Kiểm Soát Vật Tư Bảo Dưỡng'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang('14. Kiểm Soát Báo Cáo Không Phù Hợp'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/repair_plan',
                            'key' => 'repair_plan',
                            'name' => lang('11. Phiếu Kế Hoạch Yêu Cầu Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                        // [
                        // 	'link' => 'admin/request_repair',
                        // 	'key' => 'request_repair',
                        // 	'name' => lang('12. Phiếu Yêu Cầu Sửa Chữa'),
                        // 	'is_permission' => 1,
                        // ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'qa',
                    'name' => lang('II. Phòng Kỹ Thuật'),
                    'sub' => [
                        [
                            'link' => 'admin/maintenance/calendar',
                            'key' => 'maintenance__calendar',
                            'name' => lang('' . (++$numberTwo) . '. Lịch Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Bảo Trì'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/machine_information',
                            'key' => '',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Thông Tin Máy'),
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'created',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Thiết Bị Sản Xuất'),
                        ],
                        [
                            'link' => 'admin/categories_other/materials_equipment',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Thiết Bị Văn Phòng'),
                        ],
                        [
                            'link' => 'admin/categories/category_machines',
                            'key' => 'category_machines',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=7',
                            'key' => 'category_maintenance_calibration?type=7',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Vật Tư Thay Thế'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=2',
                            'key' => 'category_maintenance_calibration?type=2',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Cơ'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=4',
                            'key' => 'category_maintenance_calibration?type=4',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Cơ Sở Hạ Tầng'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=3',
                            'key' => 'category_maintenance_calibration?type=3',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Điện'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/refrigeration',
                            'key' => 'refrigeration',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Điện Lạnh'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/electricitywater',
                            'key' => 'electricitywater',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Điện Nước Gia Dụng'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/camera',
                            'key' => 'camera',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Hệ Thống Camera'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/ctp',
                            'key' => 'ctp',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Hệ Thống CTP'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/wastewater',
                            'key' => 'wastewater',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Hệ Thống Nước Thải'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=5',
                            'key' => 'category_maintenance_calibration?type=5',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Hơi Khí Nén'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/hardware',
                            'key' => 'hardware',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Phần Cứng'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/software',
                            'key' => 'software',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Phẩn Mềm'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/pccc',
                            'key' => 'pccc',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Phòng Cháy Chữa Cháy'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/transportation',
                            'key' => 'transportation',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Phương Tiện Vận Chuyển'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/sever',
                            'key' => 'sever',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Sever'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/laborsafety',
                            'key' => 'laborsafety',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Thiết Bị An Toàn Lao Động'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/testingequipment',
                            'key' => 'testingequipment',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Thiết Bị Đo Kiểm'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/office',
                            'key' => 'office',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Bảo Dưỡng Thiết Bị Văn Phòng'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/imported_documents',
                            'key' => 'imported_documents',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Chứng Từ Nhập Khẩu'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/exportd_documents',
                            'key' => 'exportd_documents',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Chứng Từ Xuất Khẩu'),
                        ],
                        [
                            'link' => 'admin/system',
                            'key' => 'system',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=1',
                            'key' => 'category_maintenance_calibration?type=1',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Hiệu Chuẩn'),
                        ],
                        [
                            'link' => 'admin/category_maintenance_calibration?type=6',
                            'key' => 'category_maintenance_calibration?type=6',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Danh Mục Nhớt - Mỡ Bò'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/equipmentproductivity',
                            'key' => 'equipmentproductivity',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Năng Suất Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/equipment_consumption',
                            'key' => 'equipment_consumption',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Tiêu Hao Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/categories_maintenance/replacement_supplies',
                            'key' => 'replacement_supplies',
                            'is_permission' => true,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Vật Tư Thay Thế'),
                        ],
                        // [
                        //     'link' => 'admin/categories/machines',
                        //     'key' => 'categories',
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Năng Suất Thiết Bị'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'is_settings' => 1,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Đơn Vị Bảo Dưỡng'),
                        // ],
                        [
                            'link' => 'admin/category_maintenace/machines_size',
                            'key' => 'machines_size',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Kích Thước Thiết Bị'),
                        ],
                        [
                            'link' => 'admin/category_maintenace/operating_size',
                            'key' => 'operating_size',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Kích Thước Vận Hành'),
                        ],
                        [
                            'link' => 'admin/maintenance',
                            'key' => 'maintenance',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Bảo Dưỡng'),
                            'is_permission' => checkPermission('maintenance', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/type_system',
                            'key' => 'type_system',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/infomation_machines',
                            'key' => 'infomation_machines',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Lý Lịch Máy'),
                        ],
                        [
                            'link' => 'admin/category_maintenace',
                            'key' => 'category_maintenace',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh SáchNhóm Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/list_other/other/equipment_maintenance_group',
                            'key' => 'equipment_maintenance_group',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Bảo Trì'),
                        ],
                        [
                            'link' => 'admin/category_system',
                            'key' => 'category_system',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Hệ Thống'),
                        ],
                        [
                            'link' => 'admin/list_other/other/list_program',
                            'key' => 'list_program',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Phần Mềm'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/categories_other/vehicle_cl',
                            'key' => 'vehicle_cl',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Phương Tiện'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories/machines/testingequipment',
                            'key' => 'categories',
                            'is_settings' => 1,
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thiết bị Đo Kiểm'),
                        ],
                        [
                            'link' => 'admin/categories_other/unit_machines',
                            'key' => 'unit_machines',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Đơn Vị Tính Thiết Bị'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/machines',
                            'key' => 'categories__machines',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thiết Bị Máy Móc'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Nhóm Bảo Trì'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Quy Trình Bảo Trì'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/depreciation/depreciation',
                            'key' => 'depreciation',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Khấu Hao'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Hiệu Chuẩn'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/tools_supplies',
                            'key' => 'tools_supplies',
                            'is_permission' => checkPermission('tools_supplies', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Vật Tư Thay Thế'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Cơ'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Điện'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Điện Lạnh'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Hơi Khí Nén'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Lý Lịch Máy'),
                        // ],
                        // [
                        //     'link' => 'admin/suppliers',
                        //     'key' => 'suppliers',
                        //     'is_permission' => checkPermission('suppliers', $staff_id, $is_admin),
                        //     'name' => lang(''.(++$numberC).'. Đơn Vị Sửa Chữa'),
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Danh Mục Nhóm Phần Mềm'),
                        //     'is_permission' => true,
                        // ],
                        [
                            'link' => 'admin/maintenance/category',
                            'key' => 'maintenance__category',
                            'name' => lang('' . (++$numberC) . '. Hạng Mục Bảo Trì'),
                            'is_permission' => checkPermission('category_maintenance', $staff_id, $is_admin)
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Linh Kiện Thay Thế'),
                        //     'is_permission' => true,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Phần Cứng'),
                        //     'is_permission' => true,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Mục Bảo Dưỡng Phẩn Mềm'),
                        //     'is_permission' => true,
                        // ],
                    ]
                ]
            ]
        ];

        $numberC = 0;
        $numberOne = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $menu['category']['items']['quality'] = [
            'name' => lang('' . (++$number) . '. Chất Lượng'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'qa',
                    'name' => lang('I. Chất Lượng(QA)'),
                    'sub' => [
                        [
                            'link' => 'admin/moderation_evaluate?type=quality',
                            'key' => 'moderation_evaluate',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Kiểm Tra Chất Lượng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_rating_process',
                            'key' => 'moderation_rating_process',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Đánh Giá Qui Trình Vận Hành'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_evaluate?type=supplier',
                            'key' => 'moderation_evaluate',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Đánh Giá Nhà Cung Cấp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_rating_system',
                            'key' => 'moderation_rating_system',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Đánh Giá Hệ Thống'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_evaluate?type=customer',
                            'key' => 'moderation_evaluate',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Đánh Giá Khách Hàng'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Báo Cáo Không Phù Hợp'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Chăm Sóc Khách Hàng'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Bàn Giao Công Đoạn (Hệ Thống)'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/suggest_outsource',
                            'key' => 'suggest_outsource',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Gia Công'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_outsource',
                        //     'key' => 'moderation_outsource',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Gia Công'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => 'admin/moderation_overtime',
                        //     'key' => 'moderation_overtime',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Tăng Ca'),
                        //     'is_permission' => 1,
                        // ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'qa',
                    'name' => lang('II. Chất Lượng<br>A. QA'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_test_item_quality/check/products',
                            'key' => 'check_products',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Kiểm Tra Chất Lượng Sản Phẩm'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberTwo).'. Phiếu Kiểm Tra Chất Lượng Sản Phẩm'),
                        // ],
                        [
                            'link' => 'admin/suggest_test_item_quality/check/materials',
                            'key' => 'check_materials',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Kiểm Tra Chất Lượng NPL'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberTwo).'. Phiếu Kiểm Tra Chất Lượng NPL'),
                        // ],
                        [
                            'link' => 'admin/suggest_test_item_quality/evaluate/products',
                            'key' => 'evaluate_products',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Đánh Giá Chất Lượng Sản Phẩm'),
                        ],
                        [
                            'link' => 'admin/suggest_test_item_quality/evaluate/materials',
                            'key' => 'evaluate_materials',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu Yêu Cầu Đánh Giá Chất Lượng NPL'),
                        ],
                        [
                            'link' => 'admin/suggest_evaluate?type=quality',
                            'key' => 'suggest_evaluate?type=quality',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberTwo) . '. Phiếu yêu cầu Kiểm Tra Chất Lượng'),
                        ],
                    ],
                ],
            ],
            'sub_menu_three' => [
                [
                    'key' => 'created',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/list_other/standard/standard_carry',
                            'key' => 'standard_carry',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Bế Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_sample_cover',
                            'key' => 'standard_sample_cover',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Bìa Mẫu KH Duyệt'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_smooth_shine',
                            'key' => 'standard_smooth_shine',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Bóng Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/categories_other/packaging_standards_npl',
                            'key' => 'packaging_standards_npl',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberC) . '. Tiêu Chuẩn Đóng Gói NPL'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/categories_other/packaging_standards_sp',
                            'key' => 'packaging_standards_sp',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Đóng Gói Sản Phẩm'),
                            'is_settings' => 1
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_fsc',
                            'key' => 'standard_fsc',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn FSC Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_delivery_package',
                            'key' => 'standard_delivery_package',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Kiện Hàng Giao Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_membrane',
                            'key' => 'standard_membrane',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Màng Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_template',
                            'key' => 'standard_template',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Mẫu (Y Mẫu, Mẫu TP Tồn Kho, Mẫu Theo SP)'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_condition_color',
                            'key' => 'standard_condition_color',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Điều Kiện Xem Màu( Mắt Thường, Light Box., Ngoài Trời)'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_color',
                            'key' => 'standard_color',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Màu Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories/packaging',
                            'key' => 'categories',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Sản Phẩm'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_bin_carton',
                            'key' => 'standard_bin_carton',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Thùng Carton Của KH'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_trame',
                            'key' => 'standard_trame',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Trame Của KH'),
                            'is_permission' => true,
                        ],
                        // [
                        //     'link' => 'admin/categories_other/packaging_standards_sp',
                        //     'key' => 'packaging_standards_sp',
                        //     'is_permission' => 1,
                        //     'name' => lang(''.(++$numberC).'. Tiêu Chuẩn Đóng Gói'),
                        //     'is_settings' => 1
                        // ],
                        [
                            'link' => 'admin/list_other/standard/standard_sample_code',
                            'key' => 'standard_sample_code',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Bìa Mẫu'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/products/stages',
                            'key' => 'products__stages',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Công Đoạn Đặc Biệt'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/materials_special',
                            'key' => 'materials_special',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nguyên Phụ Liệu Đặc Biệt'),
                        ],
                        [
                            'link' => 'admin/categories/machines/special',
                            'key' => 'machines_special',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thiết Bị Đặc Biệt'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/standard/standard_methods',
                            'key' => 'standard_methods',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Phương Pháp Đo (Đúng Điểm Đo, Đều Màu/Tờ In)'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Bàn Giao'),
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over_task',
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chí Bàn Giao Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/type_reports',
                            'key' => 'type_reports',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/group_reports',
                            'key' => 'group_reports',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Báo Cáo'),
                        ],
                        [
                            'link' => 'admin/type_error',
                            'key' => 'type_error',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Lỗi'),
                        ],
                        [
                            'link' => 'admin/group_error',
                            'key' => 'group_error',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Lỗi'),
                        ],
                        [
                            'link' => 'admin/category_improve',
                            'key' => 'category_improve',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Cải Tiến'),
                        ],
                        [
                            'link' => 'admin/category_complaints',
                            'key' => 'category_complaints',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Khiếu Nại'),
                        ],
                        [
                            'link' => 'admin/request_overtime',
                            'key' => 'request_overtime',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Phiếu Yêu Cầu Tăng Ca'),
                        ],
                        [
                            'link' => 'admin/quality_control/category_errors',
                            'key' => 'quality_control__category_errors',
                            'name' => lang('' . (++$numberC) . '. Danh Mục Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/quality_control/detail_errors',
                            'key' => 'quality_control__detail_errors',
                            'name' => lang('' . (++$numberC) . '. Chi Tiết Lỗi'),
                            'is_permission' => checkPermission('quality_control', $staff_id, $is_admin),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Thiết Bị Đo Kiểm'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/list_other/standard/standard_quality_standards',
                            'key' => '',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tiêu Chuẩn Chất Lượng SP'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC).'. Tiêu Chí/Quy Trình Bàn Giao'),
                        //     'is_permission' => 1,
                        // ],
                    ]
                ]
            ]
        ];

        $numberC = 0;
        $numberOne = 0;
        $numberOneB = 0;
        $numberTwo = 0;
        $numberThree = 0;
        $numberC1 = 0;
        $menu['category']['items']['production_management'] = [
            'name' => lang('' . (++$number) . '. Sản Xuất'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'production',
                    'name' => lang('I. Sản Xuất(Production)'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => 'manufactures__productions_orders',
                            'name' => lang('' . (++$numberOne) . '. Lệnh Sản Xuất Tổng'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/order_production_details',
                            'key' => 'manufactures__order_production_details',
                            'name' => lang('' . (++$numberOne) . '. Lệnh Sản Xuất Chi Tiết'),
                            'is_permission' => checkPermission('manufactures_order_production_details', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/synthetic_stage',
                            'key' => 'synthetic_stage',
                            'name' => lang('' . (++$numberOne) . '. Lệnh Sản Xuất Theo Công Đoạn'),
                            'is_permission' => checkPermission('manufactures_productions_orders', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/manufactures/list_manufactures',
                            'key' => 'manufactures__list_manufactures',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Sản Xuất'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/production_list/moderation_plan?group=253',
                            'key' => 'production_smoothing',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ'),
                            'is_permission' => checkPermission('production_list', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/plan_propose?group=train',
                            'key' => 'plan_propose',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Động'),
                            'is_permission' => checkPermission('plan_propose', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/request_graft_size',
                            'key' => 'request_graft_size',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Ghép Size'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_graft_size',
                        //     'key' => 'moderation_graft_size',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Ghép Size'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/request_printed_page_layout',
                            'key' => 'request_printed_page_layout',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Dàn Trang In'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_printed_page_layout',
                        //     'key' => 'moderation_printed_page_layout',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Dàn Trang In'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/purchase_request_zinc',
                            'key' => 'purchase_request_zinc',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Ghi Kẽm'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_purchase_zinc',
                        //     'key' => 'moderation_purchase_zinc',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Ghi Kẽm'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/request_place_the_tank_mold',
                            'key' => 'request_place_the_tank_mold',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đặt Khuôn Bế'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_place_the_tank_mold',
                        //     'key' => 'moderation_place_the_tank_mold',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Đoạn Đặt Khuôn Bế'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/production_order_request',
                            'key' => 'production_order_request',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Mở Lệnh SX'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => 'admin/moderation_production_order',
                        //     'key' => 'moderation_production_order',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Điều Độ Công Việc Mở Lệnh SX'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/purchase_request_material',
                            'key' => 'purchase_request_material',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Mua NPL'),
                        ],
                        [
                            'link' => 'admin/suggest_plan_purchase?type=1',
                            'key' => 'suggest_plan_purchase?type=1',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Kế Hoạch Mua NPL'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_sample_cover',
                            'key' => 'request_sample_cover',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Bìa Mẫu Sản Xuất'),
                            'is_permission' => 1,
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Ra Vào Cổng-Mang Hàng Ra Cổng'),
                        //     'is_permission' => 1,
                        // ],
                    ],
                ],
                [
                    'key' => 'warehouse',
                    'name' => lang('II. Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/request_export_products',
                            'key' => 'request_export_products',
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Xuất Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/stock_out_request',
                            'key' => 'stock_out_request',
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Xuất Kho NPL Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_purchase_npl',
                            'key' => 'suggest_purchase_npl',
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Nhập Kho NPL'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_ticket_purchase_products',
                            'key' => 'suggest_ticket_purchase_products',
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Nhập Kho TP Tồn'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/suggest_overcome_products',
                            'key' => 'suggest_overcome_products',
                            'name' => lang('' . (++$numberOneB) . '. Phiếu Yêu Cầu Nhập Kho TP Vượt'),
                            'is_permission' => 1
                        ],
                        [
                            'link' => 'admin/transfer',
                            'key' => 'transfer',
                            'name' => lang('' . (++$numberOneB) . '. Chuyển Kho'),
                            'is_permission' => checkPermission('transfer', $staff_id, $is_admin)
                        ],
                    ]
                ]
            ],
            // 'sub_menu_two' => [
            //     [
            //         'key' => 'stages',
            //         'name' => lang('II. Công Đoạn'),
            //         'sub' => [
            //         ]
            //     ]
            // ],
            'sub_menu_two' => [
                [
                    'key' => 'delivery',
                    'name' => lang('II. Giao Hàng Thanh Toán'),
                    'sub' => [
                        [
                            'link' => 'admin/releases',
                            'key' => 'releases',
                            'is_permission' => checkPermission('releases_deliveries', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberThree) . '. Phiếu Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberThree) . '. Phiếu Thanh Toán'),
                        ],
                        [
                            'link' => 'admin/suggest_control_vehicle',
                            'key' => 'suggest_control_vehicle',
                            'name' => lang('' . (++$numberThree) . '. Phiếu Yêu Cầu Điều Xe'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'accountant',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/list_other/list_join/product_standard_group',
                            'key' => 'product_standard_group',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Nhóm Định Mức SP'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/type_orders',
                            'key' => 'type_orders',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Loại Đơn Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/type_orders_items',
                            'key' => 'type_orders_items',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Nhóm Đơn Hàng'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/status_orders',
                            'key' => 'status_orders',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Trạng Thái Đơn Hàng'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/categories_other/type_plan_manu',
                            'key' => 'type_plan_manu',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Loại Kế Hoạch'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/other/group_plan',
                            'key' => 'group_plan',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Mã Kế Hoạch'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/list_join/npl_allowable',
                            'key' => 'npl_allowable',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách NPL Tồn Kho Cho Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/list_join/product_allowable',
                            'key' => 'product_allowable',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Thành Phẩm Tồn Kho Cho Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/warehouse/group',
                            'key' => 'warehouse',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Nhóm Kho'),
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'is_permission' => checkPermission('warehouse', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Mã Kho'),
                        ],
                        [
                            'link' => 'admin/list_other/other/list_area_warehouse',
                            'key' => 'list_area_warehouse',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Khu Vực Kho'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/warehouse',
                            'key' => 'warehouse',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Thẻ Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/list_other/list_join/product_time_stock',
                            'key' => 'product_time_stock',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Thời Gian Lưu Kho'),
                        ],
                        [
                            'link' => 'admin/entrance_ticket',
                            'key' => 'entrance_ticket',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberC1) . '. Phiếu Ra Vào Cổng-Mang Hàng Ra Cổng'),
                        ],
                        [
                            'link' => 'admin/hand_over/category',
                            'key' => 'hand_over__category',
                            'name' => lang('' . (++$numberC1) . '. Loại Bàn Giao'),
                            'is_permission' => checkPermission('category_hand_over', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/hand_over/task',
                            'key' => 'hand_over__task',
                            'name' => lang('' . (++$numberC1) . '. Tiêu Chí Bàn Giao'),
                            'is_permission' => checkPermission('handover_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/quota_info/stage',
                            'key' => 'quota_info_stage',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Định Mức Tiêu Hao Công Đoạn'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/quota_info/use_bom',
                            'key' => 'quota_info_use_bom',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Định Mức NPL Sử Dụng BOM'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/list_other/list_join/barrel_size',
                            'key' => 'barrel_size',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Định Mức Thùng Đóng Gói'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/categories_other/conversion_formula_npl',
                            'key' => 'conversion_formula_npl',
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Công Thức Quy Đổi NPL'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/delivery_criteria',
                            'key' => '',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberC1) . '. Tiêu Chí Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/clients/all_shipping',
                            'key' => 'clients__all_shipping',
                            'is_permission' => checkPermission('customers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC1) . '. Danh Sách Địa Chỉ Giao Hàng'),
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberC1) . '. Chi Nhánh Kho, Vị Trí Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberC1) . '. Thẻ Kho'),
                            'is_permission' => true
                        ],
                        // [
                        //     'link' => 'admin/reports/warehouse',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberC1).'. Nhóm Kho, Vị Trí'),
                        //     'is_permission' => true
                        // ],
                    ]
                ]
            ],
        ];

        $numberOne = 0;
        $numberC = 0;
        $numberTwoB = 0;
        $numberTwoC = 0;
        $menu['category']['items']['finance'] = [
            'name' => lang('' . (++$number) . '. Tài Chính Kế Toán'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'accountant',
                    'name' => lang('I. Tài Chính Kế Toán'),
                    'sub' => [
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Giá Mua-Bán'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Tồn Kho TP-Tồn Cho Phép'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).' Kiểm Soát Tồn Kho NPL-Tồn Cho Phép'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Tồn Kho NPL-Tồn Quá Hạn'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Tồn Kho TP-Tồn Quá Hạn'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Phế Phẩm'),
                        //     'is_permission' => 1,
                        // ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'name' => lang(''.(++$numberOne).'. Kiểm Soát Báo Cáo Không Phù Hợp'),
                        //     'is_permission' => 1,
                        // ],
                        [
                            'link' => 'admin/plan_propose?group=pay_slip',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Kế Hoạch Điều Độ Chi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/target_kpi/synthetic_target_cost',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Mục tiêu ngân sách chi phí'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
            ],
            'sub_menu_two' => [
                [
                    'key' => 'accouting',
                    'name' => lang('II. Kế Toán(Accouting)<br>A. Kế Toán Thuế'),
                    'sub' => [
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang('1. Doanh Thu Bán '),
                        // ],
                        [
                            'link' => 'admin/detail_payslips?type=1',
                            'key' => 'detail_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('1. Chi Phí Hợp Lý'),
                        ],
                        [
                            'link' => 'admin/detail_payslips?type=2',
                            'key' => 'detail_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('2. Chi Phí Ngoài'),
                        ],
                        [
                            'link' => 'admin/detail_payslips?type=3',
                            'key' => 'detail_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('3. Chi Phí Khấu Trừ'),
                        ],
                        [
                            'link' => 'admin/detail_payslips?type=4',
                            'key' => 'detail_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('4. Chi Phí Giảm Trừ'),
                        ],
                    ],
                ],
                [
                    'key' => 'accouting',
                    'name' => lang('B. Kế Toán Thu'),
                    'sub' => [
                        [
                            'link' => 'admin/coupon_invoice/synthetic_coupon_invoice',
                            'key' => 'synthetic_coupon_invoice',
                            'is_permission' => checkPermission('coupon_invoice', $staff_id, $is_admin),
                            'name' => lang('1. Hóa Đơn Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/vouchers_coupon',
                            'key' => 'vouchers_coupon',
                            'is_permission' => checkPermission('vouchers_coupon', $staff_id, $is_admin),
                            'name' => lang('2. Phiếu Thu Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/debt_clients',
                            'key' => 'debt_clients',
                            'is_permission' => checkPermission('debt_clients', $staff_id, $is_admin),
                            'name' => lang('3. Công Nợ Bán Hàng'),
                        ],
                        [
                            'link' => 'admin/other_payslips_coupon',
                            'key' => 'other_payslips_coupon',
                            'is_permission' => checkPermission('other_payslips_coupon', $staff_id, $is_admin),
                            'name' => lang('4. Phiếu Thu Khác'),
                        ],
                    ],
                ],
                [
                    'key' => 'accouting',
                    'name' => lang('C. Kế Toán Chi'),
                    'sub' => [
                        [
                            'link' => 'admin/purchase_invoice/synthetic_invoice',
                            'key' => 'purchase_invoice',
                            'is_permission' => checkPermission('purchase_invoice', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwoC) . '. Hóa Đơn Mua Hàng'),
                        ],
                        // [
                        //     'link' => 'admin/pay_slip/synthetic_payslip',
                        //     'key' => 'pay_slip',
                        //     'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                        //     'name' => lang('2. Phiếu Chi'),
                        // ],
                        // [
                        //     'link' => 'admin/other_payslips',
                        //     'key' => 'other_payslips',
                        //     'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                        //     'name' => lang('2.1. Phiếu Chi Phí Ngoài'),
                        // ],
                        // [
                        //     'link' => 'admin/pay_slip/synthetic_payslip',
                        //     'key' => 'pay_slip',
                        //     'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                        //     'name' => lang('2.2. Phiếu Chi Mua Hàng (theo YCMH)'),
                        // ],
                        // [
                        //     'link' => 'admin/other_payslips/other_payslip_manage',
                        //     'key' => 'other_payslips',
                        //     'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                        //     'name' => lang('2.3. Phiếu Chi Quản Lý'),
                        // ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => 'debt_suppliers',
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwoC) . '. Công Nợ Nhà Cung Cấp'),
                        ],
                        // [
                        //     'link' => 'admin/pay_slip/synthetic_payslip',
                        //     'key' => 'pay_slip',
                        //     'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                        //     'name' => lang('4. Phiếu Yêu Cầu Chi'),
                        // ],
                        // [
                        //     'link' => 'admin/spending_plan',
                        //     'key' => 'spending_plan',
                        //     'is_permission' => true,
                        //     'name' => lang('5. Phiếu Kế Hoạch Chi'),
                        // ],
                        [
                            'link' => 'admin/advance',
                            'key' => 'advance',
                            'is_permission' => checkPermission('advance', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwoC) . '. Phiếu Chi Tạm Ứng'),
                        ],
                        //                        [
                        //                            'link' => 'admin/service',
                        //                            'key' => 'service',
                        //                            'is_permission' => checkPermission('service', $staff_id, $is_admin),
                        //                            'name' => lang(''.(++$numberTwoC).'. Phiếu Dịch Vụ'),
                        //                        ],
                        [
                            'link' => 'admin/suggest_payslips',
                            'key' => 'suggest_payslips',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberTwoC) . '. Phiếu Yêu Cầu Chi'),
                        ],
                        [
                            'link' => 'admin/other_payslips?type=import',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            //                            'name' => lang(''.(++$numberTwoC).'. Phiếu Chi'),
                            'name' => lang('' . (++$numberTwoC) . '. ') . lang('ch_other_payslips'),
                        ],
                        [
                            'link' => 'admin/other_payslips?type=service',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            //                            'name' => lang(''.(++$numberTwoC).'. Phiếu Chi'),
                            'name' => lang('' . (++$numberTwoC) . '. ') . lang('dt_other_payslips1'),
                        ],
                        [
                            'link' => 'admin/suggestion',
                            'key' => 'suggestion',
                            'is_permission' => checkPermission('suggestion', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberTwoC) . '. Phiếu Đề Xuất Tài Chính'),
                        ],
                    ],
                ],
                // [
                //     'key' => 'accouting',
                //     'name' => lang('D. Thủ Quỷ'),
                //     'sub' => [
                //         [
                //             'link' => 'admin/advance',
                //             'key' => 'advance',
                //             'is_permission' => checkPermission('advance', $staff_id, $is_admin),
                //             'name' => lang('1. Phiếu Chi Tạm Ứng'),
                //         ],
                //         [
                //             'link' => 'admin/other_payslips',
                //             'key' => 'other_payslips',
                //             'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                //             'name' => lang('2. Phiếu Chi Phí Ngoài'),
                //         ],
                //         [
                //             'link' => 'admin/pay_slip/synthetic_payslip',
                //             'key' => 'pay_slip',
                //             'is_permission' => checkPermission('pay_slip', $staff_id, $is_admin),
                //             'name' => lang('3. Phiếu Chi Mua Hàng (theo YCMH)'),
                //         ],
                //         [
                //             'link' => 'admin/other_payslips/other_payslip_manage',
                //             'key' => 'other_payslips',
                //             'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                //             'name' => lang('4. Phiếu Chi Quản Lý'),
                //         ],
                //     ],
                // ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'created',
                    'color' => '#2196f3',
                    'name' => lang('III. Dữ Liệu Tạo'),
                    'sub' => [
                        [
                            'link' => 'admin/costs_lever',
                            'key' => 'costs_lever',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Chi'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/costs',
                            'key' => 'costs',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mục Chi'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/list_join/budget_room',
                            'key' => 'budget_room',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Ngân Sách Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/list_other/list_join/depreciation_rates',
                            'key' => 'depreciation_rates',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Khấu Hao Tài Sản-Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => 'admin/list_other/list_join/depreciation_period',
                            'key' => 'depreciation_period',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Thời Gian Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/list_other/list_join/used_time',
                            'key' => 'used_time',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Định Mức Thời Gian Sử Dụng'),
                        ],
                        [
                            'link' => 'admin/paymentmodes',
                            'key' => 'paymentmodes',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hình Thức Thanh Toán'),
                            'is_permission' => true,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/list_muti/time_payment',
                            'key' => 'time_payment',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thời Hạn Thanh Toán'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Thuế'),
                        ],
                        [
                            'link' => 'admin/taxes',
                            'key' => 'taxes',
                            'is_permission' => $is_admin ? true : false,
                            'is_settings' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Thuế VAT'),
                        ],
                        [
                            'link' => 'admin/categories_other/inventory_group_warehouse',
                            'key' => 'inventory_group_warehouse',
                            'name' => lang('' . (++$numberC) . '. Danh Sách Nhóm Kiểm Kê'),
                            'is_permission' => 1,
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/inventory',
                            'key' => 'inventory',
                            'is_permission' => checkPermission('inventory', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Kiểm Kê'),
                        ],
                        [
                            'link' => 'admin/categories_other/import_code',
                            'key' => 'import_code',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Mã Số Nhập Khẩu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/list_other/other/list_imported_documents',
                            'key' => 'list_imported_documents',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Chứng Từ Nhập Khẩu'),
                        ],
                        [
                            'link' => 'admin/depreciable_assets',
                            'key' => 'depreciable_assets',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Tài Sản Khấu Hao'),
                        ],
                        [
                            'link' => 'admin/list_other/other/list_insurance',
                            'key' => 'list_insurance',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảo Hiểm'),
                        ],
                        [
                            'link' => 'admin/import_price',
                            'key' => 'import_price',
                            'is_permission' => checkPermission('import_price', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảng Giá Nhà Cung Cấp'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberC).'. Danh Sách Bảng Giá Nhà Cung Cấp'),
                        // ],
                        [
                            'link' => 'admin/import_price_group',
                            'key' => 'import_price_group',
                            'is_permission' => checkPermission('import_price_group', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảng Giá Khách Hàng'),
                        ],
                        [
                            'link' => 'admin/quote_stage',
                            'key' => 'quote_stage',
                            'is_permission' => checkPermission('quote_stage', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảng Giá Công Đoạn'),
                        ],
                        [
                            'link' => 'admin/list_vehicle',
                            'key' => 'list_vehicle',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảng Giá Phương Tiện-Lộ Trình'),
                        ],
                        [
                            'link' => 'admin/stage_price_list',
                            'key' => 'stage_price_list',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Bảng Giá Đơn Vị Gia Công'),
                        ],
                        [
                            'link' => 'admin/quote_shipping_unit',
                            'key' => 'quote_shipping_unit',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Báo Giá Vận Chuyển'),
                        ],
                        [
                            'link' => 'admin/categories_other/discount_supplier',
                            'key' => 'discount_supplier',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Chiết Khấu'),
                            'is_settings' => 1,
                        ],
                        [
                            'link' => 'admin/contracts_supplier',
                            'key' => 'contracts_supplier',
                            'is_permission' => 1,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hợp Đồng Mua'),
                        ],
                        [
                            'link' => 'admin/contracts_sales',
                            'key' => 'contracts_sales',
                            'is_permission' => checkPermission('contracts_sales', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberC) . '. Danh Sách Hợp Đồng Bán'),
                        ],
                        [
                            'link' => 'admin/list_other/other/list_type_quote',
                            'key' => 'list_type_quote',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Danh Sách Loại Báo Giá'),
                        ],
                        [
                            'link' => 'admin/target_kpi/target_room',
                            'key' => 'target_room',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberC) . '. Định mức ngân sách'),
                        ],
                        //                        [
                        //                            'link' => '',
                        //                            'key' => '',
                        //                            'name' => lang(''.(++$numberC).'. Danh Sách Mã Lộ Trình'),
                        //                            'is_permission' => true,
                        //                        ],
                    ]
                ]
            ]
        ];

        $numberOne = 0;
        $menu['category']['items']['internal'] = [
            'name' => lang('' . (++$number) . '. Kiểm Soát Nội Bộ'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'internal',
                    'name' => lang('I. Kiểm Soát Nội Bộ'),
                    'sub' => [
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => 'internal_proposal',
                            'is_permission' => checkPermission('internal_proposal', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Đề Xuất Nội Bộ'),
                        ],
                        [
                            'link' => 'admin/hand_over/delivery_records',
                            'key' => 'delivery_records',
                            'is_permission' =>  checkPermission('delivery_records', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Bàn Giao Hoàn Thành'),
                        ],
                        [
                            'link' => 'admin/suggest_task',
                            'key' => 'suggest_task',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Công Việc'),
                        ],
                        [
                            'link' => 'admin/work_plan/handling',
                            'key' => 'work_plan',
                            'is_permission' => checkPermission('work_plan', $staff_id, $is_admin),
                            'name' => lang('' . (++$numberOne) . '. Kế hoạch công việc'),
                        ],
                        // [
                        //     'link' => '',
                        //     'key' => '',
                        //     'is_permission' => true,
                        //     'name' => lang(''.(++$numberOne).'. Phiếu Kiểm Soát Hệ Thống'),
                        // ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=1',
                            'key' => 'suggest_probationary_evaluate?type=1',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đánh Giá Thử Việc'),
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=2',
                            'key' => 'suggest_probationary_evaluate?type=2',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đánh Giá Nhân Viên'),
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=3',
                            'key' => 'suggest_probationary_evaluate?type=3',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đánh Giá Nhân Sự-Tay Nghề'),
                        ],
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => 'suggest_maintenance',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/moderation_maintenance',
                            'key' => 'moderation_maintenance',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Điều Độ Công Việc Bảo Dưỡng'),
                        ],
                        [
                            'link' => 'admin/request_calibration',
                            'key' => 'request_calibration',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Hiệu Chuẩn Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => 'admin/suggest_rating_machines',
                            'key' => 'suggest_rating_machines',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Đánh Giá Thiết Bị Máy Móc'),
                        ],
                        [
                            'link' => 'admin/suggest_repalce',
                            'key' => 'suggest_repalce',
                            'is_permission' => true,
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Vật Tư Thay Thế'),
                        ],
                        [
                            'link' => 'admin/request_repair',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Yêu Cầu Sửa Chữa'),
                            'is_permission' => true,
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => 'production_report',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Vi Phạm'),
                            'is_permission' => checkPermission('production_report', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/production_report/incident_tracking',
                            'key' => 'incident_tracking',
                            'name' => lang('' . (++$numberOne) . '. Phiếu Theo Dõi Báo Cáo Vi Phạm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/machine_productivity',
                            'key' => 'machine_productivity',
                            'name' => lang('' . (++$numberOne) . '. Năng Suất Máy'),
                            'is_permission' => 1,
                        ],
                    ]
                ]
            ]
        ];

        $numberOne = 0;
        $numberRomanOne = 0;
        $menu['category']['items']['reports'] = [
            'name' => lang('' . (++$number) . '. Báo Cáo'),
            'is_sub' => 1,
            'sub_menu_one' => [
                [
                    'key' => 'report',
                    'name' => lang('I. Báo Cáo Báo Giá'),
                    'sub' => [
                        [
                            'link' => 'admin/report_quotes/quotes?type=quotes_sample',
                            'key' => '',
                            'name' => lang('1. Báo Giá Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=total_quotes',
                            'key' => '',
                            'name' => lang('2. Tổng Số Báo Giá'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=quotes_pass',
                            'key' => '',
                            'name' => lang('3. Báo Giá Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=quotes_fail',
                            'key' => '',
                            'name' => lang('4. Báo Giá Không Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=total_request_sample',
                            'key' => '',
                            'name' => lang('5. Tổng Số YC Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=total_sample_pass',
                            'key' => '',
                            'name' => lang('6. Tổng Số Mẫu Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=total_sample_fail',
                            'key' => '',
                            'name' => lang('7. Tổng Số Mẫu Không Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/report_quotes/quotes?type=total_sample_orders',
                            'key' => '',
                            'name' => lang('8. Tổng Số Mẫu Đánh Lần 2 3'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/quotes',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tổng Hợp Báo Giá/Tháng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/sample_development',
                            'key' => '',
                            'name' => lang('10. Báo Cáo Tổng Hợp PTM/Tháng'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('V. Báo Cáo Công Nợ Phải Trả'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/purchase?is_type=general-synthetic-purchase-report',
                            'key' => '',
                            'name' => lang('1. Tổng Hợp Nợ Phải Trả Theo Phiếu YCMH'),
                            'is_permission' => checkPermission('synthetic_purchase', $staff_id, $is_admin),
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
                            'key' => '',
                            'name' => lang('2. Theo Dõi Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=general-detail-import-report',
                            'key' => '',
                            'name' => lang('3. Chi Tiết Phiếu Nhập Hàng'),
                            'is_permission' => checkPermission('detail_import_report', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('4. Chi Tiết Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/debt_suppliers',
                            'key' => '',
                            'name' => lang('5. Bảng Đối Chiếu Công Nợ'),
                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('6. Chi Tiết Thanh Toán Theo Phiếu Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Chi Tiết Phiếu YCMH'),
                            'is_permission' => true
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('8. Chi Phí Giá Thành'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/other_payslips',
                            'key' => 'other_payslips',
                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
                            'name' => lang('9. ') . lang('ch_other_payslips'),
                        ],
                        [
                            'link' => 'admin/reports_purchase/synthetic_plan_payslip_department_month',
                            'key' => 'synthetic_plan_payslip_department_month',
                            'is_permission' => true,
                            'name' => lang('10. ') . lang('Báo Cáo Tổng Hợp Kế Hoạch Chi Phòng Ban'),
                        ],
                        [
                            'link' => 'admin/reports_purchase/synthetic_plan_department',
                            'key' => 'synthetic_plan_department',
                            'is_permission' => true,
                            'name' => lang('11. ') . lang('Báo Cáo Tổng Hợp Kế Hoạch Phòng Ban'),
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('IX. Báo Cáo Kỹ Thuật'),
                    'sub' => [
                        [
                            'link' => 'admin/suggest_maintenance',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Bảo Trì Bảo Dưỡng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => '',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Năng Suất Thiết Bị'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_repair',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Sửa Chữa'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/request_calibration',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Hiệu Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=muscle_group',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Bảo Dưỡng Cơ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=electrical_group',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Bảo Dưỡng Điện'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=refrigeration_group',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Bảo Dưỡng Điện Lạnh'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation/moderation_maintenance?type=compressed_air_group',
                            'key' => '',
                            'name' => lang('8. Báo Cáo Bảo Dưỡng Hơi Khí-Nén'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/device',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tổng Hợp Thiết Bị/Tháng'),
                            'is_permission' => 1,
                        ],

                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XIII. TỒN QUỸ'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/fund_balance?is_type=diary-of-collecting-money',
                            'key' => '',
                            'name' => lang('1. Nhật Ký Thu'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=diary-of-spending-money',
                            'key' => '',
                            'name' => lang('2. Nhật Ký Chi'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=diary-of-revenue-and-expenditure',
                            'key' => '',
                            'name' => lang('3. Nhật Ký Thu Chi'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=aggregate-fund-balance',
                            'key' => '',
                            'name' => lang('4. Tổng Hợp Tồn Quỹ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=cash-book',
                            'key' => '',
                            'name' => lang('5. Sổ Quỹ Tiền Mặt'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=cash-book-bank',
                            'key' => '',
                            'name' => lang('6. Sổ Quỹ Ngân Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/fund_balance?is_type=cash-flow',
                            'key' => '',
                            'name' => lang('7. Chi Phí'),
                            'is_permission' => true
                        ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'report',
                    'name' => lang('II. Báo Cáo Phát Triển Mẫu'),
                    'sub' => [
                        [
                            'link' => 'admin/moderation_template',
                            'key' => '',
                            'name' => lang('1. Tổng Số YC Phát Triên Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => '',
                            'name' => lang('2. Chi Tiết Phát Triển Mẫu'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => '',
                            'name' => lang('3. Tổng Số Mẫu Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => '',
                            'name' => lang('4. Tổng Số Mẫu K Đạt'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_template',
                            'key' => '',
                            'name' => lang('5. Tổng Số Mẫu Đánh Lần 2 3'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VI. Tồn Kho<br>A. Báo Cáo Tồn Kho'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Tổng Xuất Nhập Tồn Kho Hàng Ngày'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Chi Tiết Xuất Nhập Tồn Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-inventory-report',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Xuất Nhập Tồn'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock_product',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock_semi_product',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Bán Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho NPL cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Thành Phẩm cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Bán Thành Phẩm cũ'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=inventory_nvl_hs',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho NPL Cho phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=inventory_tp_hs',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Thành Phẩm Cho Phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=inventory_btp_hs',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tồn Kho Bán Thành Phẩm Cho Phép'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=limit_user_date',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Cảnh Báo Ngày Sử Dụng NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=limit_user_date_btp',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Cảnh Báo Ngày Sử Dụng BTP-TP'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_warehouse/syntheticProductWarehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tổng Hợp Tồn Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_warehouse/syntheticMaterialWarehouse',
                            'key' => '',
                            'name' => lang('' . (++$numberOne) . '. Báo Cáo Tổng Hợp Tồn Kho NPL'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('X. BÁO CÁO HÀNH CHÍNH NHÂN SỰ'),
                    'sub' => [
                        [
                            'link' => 'admin/salary/timekeeping',
                            'key' => '',
                            'name' => lang('1. Chấm Công'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/paid_holidays/paid_holidays_follow',
                            'key' => '',
                            'name' => lang('2. Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/payroll/payroll_salary',
                            'key' => '',
                            'name' => lang('3. Lương'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_social_insurance',
                            'key' => '',
                            'name' => lang('4. Bảo Hiểm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_social_welfare',
                            'key' => '',
                            'name' => lang('5. Phúc Lợi Xã Hội'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_union',
                            'key' => '',
                            'name' => lang('6. Công Đoàn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_personal_income_tax',
                            'key' => '',
                            'name' => lang('7. Thuế TNCN'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/business_fee_other/report_business_fee_other_overtime',
                            'key' => '',
                            'name' => lang('8. Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/suggest_probationary_evaluate?type=1',
                            'key' => '',
                            'name' => lang('9. Thử Việc'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/violation_records',
                            'key' => '',
                            'name' => lang('10. Vi Phạm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/kpi/suggest_kpi',
                            'key' => '',
                            'name' => lang('11. Đánh Giá KPI'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/decision_bonus_discipline',
                            'key' => '',
                            'name' => lang('12. Khen Thưởng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/decision_bonus_discipline',
                            'key' => '',
                            'name' => lang('13. Kỷ Luật'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/evaluate?type=certification',
                            'key' => '',
                            'name' => lang('14. Chứng Nhận, Chứng Chỉ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/evaluate?type=license',
                            'key' => '',
                            'name' => lang('15. Giấy Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/category_salary/contract_labor',
                            'key' => '',
                            'name' => lang('16. Hợp Đồng Lao Động'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/regulations/rules',
                            'key' => '',
                            'name' => lang('17. Qui Chế'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/regulations/fixation',
                            'key' => '',
                            'name' => lang('18. Qui Định Nội Bộ'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/human',
                            'key' => '',
                            'name' => lang('19. Tổng Hợp Nhân Sự/Tháng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/evaluate',
                            'key' => '',
                            'name' => lang('20. Tổng Hợp Giấy Phép'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/fixation',
                            'key' => '',
                            'name' => lang('21. Tổng Hợp Qui Định - Qui Chế'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XIV. B. BÁO CÁO CHI TIẾT CÁC PHIẾU'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-import-report',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Chi Tiết Nhập Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-import-report_mh',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Chi Tiết Nhập Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-export-report',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Chi Tiết Xuất Kho Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-exporting_producion-report',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Chi Tiết Xuất Kho Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-other-report',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Chi Tiết Xuất Kho Khác'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-transfer-report',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Chi Tiết Chuyển Kho'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=warehouse-adjusted-report',
                            'key' => '',
                            'name' => lang('7. Báo Cáo Chi Tiết Điều Chỉnh (Kiểm Kê)'),
                            'is_permission' => true
                        ],
                    ],
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'report',
                    'name' => lang('III. Báo Cáo Bán Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/sales?type=sales_of_order',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Giá Trị Theo Đơn Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Hàng Tái Sản Xuất Lại'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales?type=detail_delivery',
                            'key' => '',
                            'name' => lang('3. Tiến Độ Giao Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Đơn Hàng Theo Lô'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales?type=order_status',
                            'key' => '',
                            'name' => lang('5. Tổng Hợp Giao Hàng Hàng Ngày'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales?type=sales_analysis',
                            'key' => '',
                            'name' => lang('6. Phân Tích Bán Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/sales?type=nearest_selling_price',
                            'key' => '',
                            'name' => lang('7. Bảng Kê Giá Bán Gần Nhất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result',
                            'key' => '',
                            'name' => lang('8. Theo Dõi Nợ Phải Thu Ngày Tháng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_summary/orders',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tổng Hợp Thu/Tháng'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VII. Báo Cáo Sản Xuất'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/productions?type=general_production_new',
                            'key' => 'reports__productions',
                            'name' => lang('1. Báo Cáo Sản Xuất Tổng Hợp'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=complete_stage',
                            'key' => 'reports__productions',
                            'name' => lang('2. Báo Cáo LSX Hoàn Thành Theo Công Đoạn'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=product_error',
                            'key' => 'reports__productions',
                            'name' => lang('3. Báo Cáo Hàng Lỗi Theo Lệnh Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=production_detailed',
                            'key' => 'reports__productions',
                            'name' => lang('4. Báo Cáo Nhập Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=inventory_tp_hs',
                            'key' => 'reports__warehouse',
                            'name' => lang('5. Báo Cáo Tồn Kho Thành Phẩm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/warehouse?is_type=inventory_nvl_hs',
                            'key' => 'reports__warehouse',
                            'name' => lang('6. Báo Cáo Tồn Kho NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=report_over_production',
                            'key' => 'reports__productions',
                            'name' => lang('7. Báo Cáo Sản Xuất Thừa'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=order_progress',
                            'key' => 'reports__productions',
                            'name' => lang('8. Báo Cáo Tiến độ Đơn Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=material_norms',
                            'key' => 'reports__productions',
                            'name' => lang('9. Báo Cáo Định Mức NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/productions?type=usage_material',
                            'key' => 'reports__productions',
                            'name' => lang('10. Báo Cáo NPL Sử Dụng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_manu/syntheticPlanNPL',
                            'key' => 'reports__productions',
                            'name' => lang('11. Báo Cáo Tổng Hợp Kế Hoạch NPL'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_summary/planning',
                            'key' => 'reports__productions',
                            'name' => lang('12. Báo Cáo Tổng Hợp KH Sản Xuất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_summary/productivity',
                            'key' => 'reports__productions',
                            'name' => lang('13. Báo Cáo Tổng Hợp Năng Suất Công Đoạn'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XI. BÁO CÁO KHÔNG PHÙ HỢP'),
                    'sub' => [
                        [
                            'link' => 'admin/production_report',
                            'key' => '',
                            'name' => lang('1. Vi Phạm'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => '',
                            'name' => lang('2. Không Phù Hợp'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_manu/syntheticProductionReport',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Tổng Hợp BCKPH'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'statistic',
                    'name' => lang('XV. THỐNG KÊ DANH MỤC'),
                    'sub' => [
                        [
                            'link' => 'admin/statistic/customer',
                            'key' => '',
                            'name' => lang('1. Thống Kê Khách Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/statistic/supplier',
                            'key' => '',
                            'name' => lang('2. Thống Kê Nhà Cung Cấp'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/statistic/staff',
                            'key' => '',
                            'name' => lang('3. Thống Kê Nhân Viên'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/statistic/machines',
                            'key' => '',
                            'name' => lang('4. Thống Kê Máy Móc'),
                            'is_permission' => true
                        ],
                    ],
                ],
            ],
            'sub_menu_four' => [
                [
                    'key' => 'report',
                    'name' => lang('IV. Báo Cáo Mua Hàng'),
                    'sub' => [
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => '',
                            'name' => lang('1. Báo Cáo Chi Tiết YCMH'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=general-purchase-report',
                            'key' => '',
                            'name' => lang('2. Báo Cáo Tổng Hợp Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail-purchase-report',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Sổ Chi Tiết Mua Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail-purchase_order-report',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Theo Dõi Đặt Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
                            'key' => '',
                            'name' => lang('5. Báo Cáo Tổng Hợp Nợ Phải Trả'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=detail_debt-report',
                            'key' => '',
                            'name' => lang('6. Báo Cáo Chi Tiết Nợ Phải Trả Theo Mặt Hàng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase',
                            'key' => '',
                            'name' => lang('7. Bảng Kê Giá Mua Gần Nhất'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
                            'key' => '',
                            'name' => lang('8. Theo Dõi Nợ Phải Trả Ngày Tháng'),
                            'is_permission' => true
                        ],
                        [
                            'link' => 'admin/reports_purchase/syntheticPurchaseCostMonth',
                            'key' => '',
                            'name' => lang('9. Báo Cáo Tổng Hợp Chi Mua Hàng /Tháng'),
                            'is_permission' => true
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('VIII. Báo Cáo Chất Lượng'),
                    'sub' => [
                        [
                            'link' => 'admin/manufactures/productions_orders',
                            'key' => '',
                            'name' => lang('1. Năng Xuất Nhóm'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/orders',
                            'key' => '',
                            'name' => lang('2. Đơn Hàng Gấp, Khẩn, Tăng Ca'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/production_report',
                            'key' => '',
                            'name' => lang('3. Báo Cáo Complain Khách Hàng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/moderation_plan_stage?type=1',
                            'key' => '',
                            'name' => lang('4. Báo Cáo Chất Lượng'),
                            'is_permission' => 1,
                        ],
                    ],
                ],
                [
                    'key' => 'report',
                    'name' => lang('XII. BÁO CÁO ĐỀ XUẤT'),
                    'sub' => [
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('1. Đề Xuất Báo Cáo Khẩn - Gấp'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('2. Đề Xuất Báo Cáo Ngày'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('3. Đề Xuất Báo Cáo Đánh Giá KPI'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('4. Đề Xuất Kế Hoạch Mua'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('5. Đề Xuất Kế Hoạch Chi'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('6. Đề Xuất Chi Khẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('7. Đề Xuất Mua Khẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('8. Đề Xuất Foso'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('9. Đề Xuất Trình Ký Qui Định'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('10. Đề Xuất Trình Ký Hợp Đồng'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('11. Đề Xuất Trình Ký Qui Trình, Qui Chuẩn'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/internal_proposal',
                            'key' => '',
                            'name' => lang('12. Đề Xuất Trình Ký Khen Thưởng Kỷ Luật'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/tasks',
                            'key' => '',
                            'name' => lang('13. Báo Cáo Tổng Hợp Công Việc-Qui Trình'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/InternalProposal',
                            'key' => '',
                            'name' => lang('14. Báo Cáo Tổng Hợp Đề Xuất'),
                            'is_permission' => 1,
                        ],
                        [
                            'link' => 'admin/reports_summary/InternalProposalUrgent',
                            'key' => '',
                            'name' => lang('15. Báo Cáo Tổng Hợp Đề Xuất Gấp Khẩn'),
                            'is_permission' => 1,
                        ],
                    ],
                ]
            ],
        ];
        //        $menu['category']['items']['reports_old'] = [
        //            'name' => lang(''.(++$number).'. Báo Cáo Cũ'),
        //            'is_sub' => 1,
        //            'sub_menu_one' => [
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('I. Báo Cáo Sản Xuất'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/productions?type=general_production_new',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('1. Báo Cáo Sản Xuất Tổng Hợp'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=complete_stage',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('2. Báo Cáo LSX Hoàn Thành Theo Công Đoạn'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=product_error',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('3. Báo Cáo Hàng Lỗi Theo Lệnh Sản Xuất'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=production_detailed',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('4. Báo Cáo Nhập Kho Thành Phẩm'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=inventory_tp_hs',
        //                            'key' => 'reports__warehouse',
        //                            'name' => lang('5. Báo Cáo Tồn Kho Thành Phẩm'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=inventory_nvl_hs',
        //                            'key' => 'reports__warehouse',
        //                            'name' => lang('6. Báo Cáo Tồn Kho NPL'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=report_over_production',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('7. Báo Cáo Sản Xuất Thừa'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=order_progress',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('8. Báo Cáo Tiến độ Đơn Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=material_norms',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('9. Báo Cáo Định Mức NPL'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/productions?type=usage_material',
        //                            'key' => 'reports__productions',
        //                            'name' => lang('10. Báo Cáo NPL Sử Dụng'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('V. Báo Cáo Công Nợ Phải Trả'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=general-synthetic-purchase-report',
        //                            'key' => '',
        //                            'name' => lang('1. Tổng Hợp Nợ Phải Trả Theo Phiếu YCMH'),
        //                            'is_permission' => checkPermission('synthetic_purchase', $staff_id, $is_admin),
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
        //                            'key' => '',
        //                            'name' => lang('2. Theo Dõi Nợ Phải Trả'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=general-detail-import-report',
        //                            'key' => '',
        //                            'name' => lang('3. Chi Tiết Phiếu Nhập Hàng'),
        //                            'is_permission' => checkPermission('detail_import_report', $staff_id, $is_admin),
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('4. Chi Tiết Nợ Phải Trả'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/debt_suppliers',
        //                            'key' => '',
        //                            'name' => lang('5. Bảng Đối Chiếu Công Nợ'),
        //                            'is_permission' => checkPermission('debt_suppliers', $staff_id, $is_admin),
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('6. Chi Tiết Thanh Toán Theo Phiếu Giao Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('7. Báo Cáo Chi Tiết Phiếu YCMH'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('8. Chi Phí Giá Thành'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/other_payslips',
        //                            'key' => 'other_payslips',
        //                            'is_permission' => checkPermission('other_payslips', $staff_id, $is_admin),
        ////                            'name' => lang('9. Chi Phí Ngoài'),
        //                            'name' => lang('9. ') . lang('ch_other_payslips'),
        //                        ],
        //                    ],
        //                ],
        //                // [
        //                //     'key' => 'report',
        //                //     'name' => lang('IX. Phiếu Báo Cáo Sự Cố'),
        //                //     'sub' => [
        //                //         [
        //                //             'link' => '',
        //                //             'key' => '',
        //                //             'name' => lang('1. Phiếu Báo Cáo Không Phù Hợp'),
        //                //             'is_permission' => 1,
        //                //         ],
        //                //         [
        //                //             'link' => '',
        //                //             'key' => '',
        //                //             'name' => lang('2. Phiếu Theo Dõi Báo Cáo Không Phù Hợp'),
        //                //             'is_permission' => 1,
        //                //         ],
        //                //     ],
        //                // ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('XIII. Báo Cáo Chất Lượng'),
        //                    'sub' => [
        //                        // [
        //                        //     'link' => '',
        //                        //     'key' => '',
        //                        //     'name' => lang('1. Tổng Năng Suất'),
        //                        //     'is_permission' => 1,
        //                        // ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('1. Năng Xuất Nhóm'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('2. Đơn Hàng Gấp, Khẩn, Tăng Ca'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Cáo Complain Khách Hàng'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/moderation_plan_stage?type=1',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Chất Lượng'),
        //                            'is_permission' => 1,
        //                        ],
        //                    ],
        //                ]
        //            ],
        //            'sub_menu_two' => [
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('II. Báo Cáo Bán Hàng'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/sales?type=sales_of_order',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Giá Trị Theo Đơn Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales',
        //                            'key' => '',
        //                            'name' => lang('2. Báo Cáo Hàng Tái Sản Xuất Lại'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales?type=detail_delivery',
        //                            'key' => '',
        //                            'name' => lang('3. Tiến Độ Giao Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Đơn Hàng Theo Lô'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales?type=order_status',
        //                            'key' => '',
        //                            'name' => lang('5. Tổng Hợp Giao Hàng Hàng Ngày'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales?type=sales_analysis',
        //                            'key' => '',
        //                            'name' => lang('6. Phân Tích Bán Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales?type=nearest_selling_price',
        //                            'key' => '',
        //                            'name' => lang('7. Bảng Kê Giá Bán Gần Nhất'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result',
        //                            'key' => '',
        //                            'name' => lang('8. Theo Dõi Nợ Phải Thu Ngày Tháng'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('VI. Tồn Quỹ'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=diary-of-collecting-money',
        //                            'key' => '',
        //                            'name' => lang('1. Nhật Ký Thu'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=diary-of-spending-money',
        //                            'key' => '',
        //                            'name' => lang('2. Nhật Ký Chi'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=diary-of-revenue-and-expenditure',
        //                            'key' => '',
        //                            'name' => lang('3. Nhật Ký Thu Chi'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=aggregate-fund-balance',
        //                            'key' => '',
        //                            'name' => lang('4. Tổng Hợp Tồn Quỹ'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=cash-book',
        //                            'key' => '',
        //                            'name' => lang('5. Sổ Quỹ Tiền Mặt'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=cash-book-bank',
        //                            'key' => '',
        //                            'name' => lang('6. Sổ Quỹ Ngân Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/fund_balance?is_type=cash-flow',
        //                            'key' => '',
        //                            'name' => lang('7. Chi Phí'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('IX. Báo Cáo Báo Giá'),
        //                    'sub' => [
        //                        // [
        //                        //     'link' => 'admin/report_quotes/quotes',
        //                        //     'key' => '',
        //                        //     'name' => lang('1. Báo Cáo Báo Giá'),
        //                        //     'is_permission' => 1,
        //                        // ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=quotes_sample',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Giá Phát Triển Mẫu'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=total_quotes',
        //                            'key' => '',
        //                            'name' => lang('2. Tổng Số Báo Giá'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=quotes_pass',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Giá Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=quotes_fail',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Giá Không Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=total_request_sample',
        //                            'key' => '',
        //                            'name' => lang('5. Tổng Số YC Phát Triển Mẫu'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=total_sample_pass',
        //                            'key' => '',
        //                            'name' => lang('6. Tổng Số Mẫu Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=total_sample_fail',
        //                            'key' => '',
        //                            'name' => lang('7. Tổng Số Mẫu Không Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => 'admin/report_quotes/quotes?type=total_sample_orders',
        //                            'key' => '',
        //                            'name' => lang('8. Tổng Số Mẫu Đánh Lần 2 3'),
        //                            'is_permission' => 1,
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('XIV. Báo Cáo Kỹ Thuật'),
        //                    'sub' => [
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Bảo Trì Bảo Dưỡng'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('2. Báo Cáo Năng Suất Thiết Bị'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Cáo Sửa Chữa'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Hiệu Chuẩn'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('5. Báo Cáo Bảo Dưỡng Cơ'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('6. Báo Cáo Bảo Dưỡng Điện'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('7. Báo Cáo Bảo Dưỡng Điện Lạnh'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('8. Báo Cáo Bảo Dưỡng Hơi Khí-Nén'),
        //                            'is_permission' => 1,
        //                        ],
        //                    ],
        //                ]
        //            ],
        //            'sub_menu_three' => [
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('III. Báo Cáo Mua Hàng'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/purchase',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Chi Tiết YCMH'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=general-purchase-report',
        //                            'key' => '',
        //                            'name' => lang('2. Báo Cáo Tổng Hợp Mua Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=detail-purchase-report',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Cáo Sổ Chi Tiết Mua Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=detail-purchase_order-report',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Theo Dõi Đặt Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
        //                            'key' => '',
        //                            'name' => lang('5. Báo Cáo Tổng Hợp Nợ Phải Trả'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=detail_debt-report',
        //                            'key' => '',
        //                            'name' => lang('6. Báo Cáo Chi Tiết Nợ Phải Trả Theo Mặt Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase',
        //                            'key' => '',
        //                            'name' => lang('7. Bảng Kê Giá Mua Gần Nhất'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/purchase?is_type=to_pay_debt-report',
        //                            'key' => '',
        //                            'name' => lang('8. Theo Dõi Nợ Phải Trả Ngày Tháng'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('VII. Tồn Kho<br>A. Báo Cáo Tồn Kho'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/warehouse',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Tổng Xuất Nhập Tồn Kho Hàng Ngày'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Chi Tiết Xuất Nhập Tồn Kho'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-inventory-report',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Xuất Nhập Tồn'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho NPL'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock_product',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Thành Phẩm'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=report_all_of_stock_semi_product',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Bán Thành Phẩm'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho NPL cũ'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Thành Phẩm cũ'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Bán Thành Phẩm cũ'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=inventory_nvl_hs',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho NPL Cho phép'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=inventory_tp_hs',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Thành Phẩm Cho Phép'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=inventory_btp_hs',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Báo Cáo Tồn Kho Bán Thành Phẩm Cho Phép'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=limit_user_date',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Cảnh Báo Ngày Sử Dụng NPL'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=limit_user_date_btp',
        //                            'key' => '',
        //                            'name' => lang(''.(++$numberOne).'. Cảnh Báo Ngày Sử Dụng BTP-TP'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('B. Báo Cáo Chi Tiết Các Phiếu'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-import-report',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Chi Tiết Nhập Thành Phẩm'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-import-report_mh',
        //                            'key' => '',
        //                            'name' => lang('2. Báo Cáo Chi Tiết Nhập Mua Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-export-report',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Cáo Chi Tiết Xuất Kho Bán Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-exporting_producion-report',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Chi Tiết Xuất Kho Sản Xuất'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-other-report',
        //                            'key' => '',
        //                            'name' => lang('5. Báo Cáo Chi Tiết Xuất Kho Khác'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-transfer-report',
        //                            'key' => '',
        //                            'name' => lang('6. Báo Cáo Chi Tiết Chuyển Kho'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/warehouse?is_type=warehouse-adjusted-report',
        //                            'key' => '',
        //                            'name' => lang('7. Báo Cáo Chi Tiết Điều Chỉnh (Kiểm Kê)'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('X. Báo Cáo Phát Triển Mẫu'),
        //                    'sub' => [
        //                        // [
        //                        //     'link' => '',
        //                        //     'key' => '',
        //                        //     'name' => lang('1. Báo Giá Tính Lô'),
        //                        //     'is_permission' => 1,
        //                        // ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('1. Tổng Số YC Phát Triên Mẫu'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('2. Chi Tiết Phát Triển Mẫu'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('3. Tổng Số Mẫu Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('4. Tổng Số Mẫu K Đạt'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('5. Tổng Số Mẫu Đánh Lần 2 3'),
        //                            'is_permission' => 1,
        //                        ],
        //                    ],
        //                ]
        //            ],
        //            'sub_menu_four' => [
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('IV. Báo Cáo Nợ Phải Thu'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result',
        //                            'key' => '',
        //                            'name' => lang('1. Tổng Hợp Nợ Phải Thu Theo Phiếu Giao Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result-detail',
        //                            'key' => '',
        //                            'name' => lang('2. Theo Dõi Nợ Phải Thu'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales',
        //                            'key' => '',
        //                            'name' => lang('3. Chi tiết phiếu giao hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/debt_customer?is_type=debt-all-result-detail',
        //                            'key' => '',
        //                            'name' => lang('4. Chi Tiết Nợ Phải Thu'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/debt_customer?is_type=sale_listing',
        //                            'key' => '',
        //                            'name' => lang('5. Bảng Đối Chiếu Công Nợ Phải Thu'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/debt_customer',
        //                            'key' => '',
        //                            'name' => lang('6. Báo Cáo Chi Tiết Phiếu Giao Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                        [
        //                            'link' => 'admin/reports/sales',
        //                            'key' => '',
        //                            'name' => lang('7. Chi Tiết Thanh Toán Theo Phiếu Giao Hàng'),
        //                            'is_permission' => true
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('VIII. Báo Cáo Lợi Nhuận'),
        //                    'sub' => [
        //                        [
        //                            'link' => 'admin/reports/expenses_vs_income',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Lợi Nhuận'),
        //                            'is_permission' => checkPermission('expenses_vs_income', $staff_id, $is_admin),
        //                        ],
        //                    ],
        //                ],
        //                [
        //                    'key' => 'report',
        //                    'name' => lang('XI. Báo Cáo Kế Hoạch'),
        //                    'sub' => [
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('1. Báo Cáo Dàn Trang'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('2. Báo Báo Ghép Size'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('3. Báo Cáo Ghi Kẽm, Đặt Khuôn'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('4. Báo Cáo Kế Hoạch Tăng Ca'),
        //                            'is_permission' => 1,
        //                        ],
        //                        [
        //                            'link' => '',
        //                            'key' => '',
        //                            'name' => lang('5. Báo Cáo Hàng Phế'),
        //                            'is_permission' => 1,
        //                        ],
        //                    ],
        //                ]
        //            ],
        //        ];

        $numberRoman++;
        $strNumberRoman = convertToRoman($numberRoman);
        $menu['category']['items']['kpi'] = [
            'name' => lang('' . $strNumberRoman . '. Đánh Giá KPI Tháng/Năm'),
            'link' => 'kpi/staff_kpi_evaluation',
            'check' => 1
        ];

        $numberRoman++;
        $strNumberRoman = convertToRoman($numberRoman);
        // $menu['category']['items']['power_bi'] = [
        //     'name' => lang(''.$strNumberRoman.'. Dashboard Power BI'),
        //     'sub_menu_one' => [
        //         [
        //             'key' => 'customer',
        //             'name' => lang('Dashbood'),
        //             'sub' => [
        //                 [
        //                     'link' => 'admin/report_dashboards/manufactures',
        //                     'key' => 'dashboard__manufactures',
        //                     'name' => lang('Báo cáo sản xuất'),
        //                     'is_permission' => true
        //                 ],
        //             ],
        //         ]
        //     ],
        //     'sub_menu_two' => [
        //         [
        //             'key' => 'customer',
        //             'name' => lang('Dashbood Power BI'),
        //             'sub' => [
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_quotes',
        //                     'key' => 'dashboard__quotes',
        //                     'name' => lang('DASHBOARD Báo Giá Phát Triển Mẫu'),
        //                     'is_permission' => checkPermission('dashboard_quotes', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_revenue',
        //                     'key' => 'dashboard__revenue',
        //                     'name' => lang('DASHBOARD Doanh Thu'),
        //                     'is_permission' => checkPermission('dashboard_revenue', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_cost',
        //                     'key' => 'dashboard__cost',
        //                     'name' => lang('DASHBOARD Chi Phí'),
        //                     'is_permission' => checkPermission('dashboard_cost', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_stock',
        //                     'key' => 'dashboard__stock',
        //                     'name' => lang('DASHBOARD Tồn Kho'),
        //                     'is_permission' => checkPermission('dashboard_stock', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_manufactures',
        //                     'key' => 'dashboard__manufactures',
        //                     'name' => lang('DASHBOARD Sản Xuất'),
        //                     'is_permission' => checkPermission('dashboard_manufactures', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_task',
        //                     'key' => 'dashboard__task',
        //                     'name' => lang('DASHBOARD Công Việc'),
        //                     'is_permission' => checkPermission('dashboard_task', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_personnel',
        //                     'key' => 'dashboard__personnel',
        //                     'name' => lang('DASHBOARD Hành Chính - Nhân Sự'),
        //                     'is_permission' => checkPermission('dashboard_personnel', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_purchases',
        //                     'key' => 'dashboard__purchases',
        //                     'name' => lang('DASHBOARD Mua Hàng'),
        //                     'is_permission' => checkPermission('dashboard_purchases', $staff_id, $is_admin)
        //                 ],
        //                 [
        //                     'link' => 'admin/report_dashboard/dashboard_business_results',
        //                     'key' => 'dashboard__business_results',
        //                     'name' => lang('DASHBOARD Kết Quả Kinh Doanh'),
        //                     'is_permission' => checkPermission('dashboard_business_results', $staff_id, $is_admin)
        //                 ],
        //             ],
        //         ]
        //     ]
        // ];

        $menu['category']['items']['power_bi'] = [
            'name' => lang('' . $strNumberRoman . '. Dashboard'),
            'sub_menu_one' => [
                [
                    'key' => 'customer',
                    'name' => lang('Dashboard'),
                    'sub' => [
                        [
                            'link' => 'admin/report_dashboard/dashboard_quotes',
                            'key' => 'dashboard__quotes',
                            'name' => lang('I.DASHBOARD CRM-QUẢN LÝ KHÁCH HÀNG'),
                            'is_permission' => checkPermission('dashboard_quotes', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_revenue',
                            'key' => 'dashboard__revenue',
                            'name' => lang('II.DASHBOARD KINH DOANH'),
                            'is_permission' => checkPermission('dashboard_revenue', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_cost',
                            'key' => 'dashboard__cost',
                            'name' => lang('III.DASHBOARD TÀI CHÍNH KẾ TOÁN'),
                            'is_permission' => checkPermission('dashboard_cost', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_stock',
                            'key' => 'dashboard__stock',
                            'name' => lang('IV.DASHBOARD TỒN KHO'),
                            'is_permission' => checkPermission('dashboard_stock', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboards/manufactures',
                            'key' => 'dashboard__manufactures',
                            'name' => lang('V.DASHBOARD SẢN XUẤT'),
                            'is_permission' => checkPermission('dashboard_manufactures', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_task',
                            'key' => 'dashboard__task',
                            'name' => lang('VI.DASHBOARD CÔNG VIỆC'),
                            'is_permission' => checkPermission('dashboard_task', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_personnel',
                            'key' => 'dashboard__personnel',
                            'name' => lang('VII.DASHBOARD HÀNH CHÍNH NHÂN SỰ'),
                            'is_permission' => checkPermission('dashboard_personnel', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboard/dashboard_purchases',
                            'key' => 'dashboard__purchases',
                            'name' => lang('VIII.DASHBOARD SCC-KIỂM SOÁT CHUỖI CUNG ỨNG'),
                            'is_permission' => checkPermission('dashboard_purchases', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboards/quality',
                            'key' => 'dashboard__quality',
                            'name' => lang('IX.DASHBOARD CHẤT LƯỢNG'),
                            'is_permission' => checkPermission('dashboard_quality', $staff_id, $is_admin)
                        ],
                        // [
                        //     'link' => 'admin/report_dashboard/dashboard_business_results',
                        //     'key' => 'dashboard__business_results',
                        //     'name' => lang('DASHBOARD Kết Quả Kinh Doanh'),
                        //     'is_permission' => checkPermission('dashboard_business_results', $staff_id, $is_admin)
                        // ],
                    ],
                ]
            ],
            'sub_menu_two' => [
                [
                    'key' => 'dashboarđ_two',
                    'name' => lang('Dashboard'),
                    'sub' => [
                        [
                            'link' => 'admin/report_dashboards/dashboard_technique',
                            'key' => 'dashboard__technique',
                            'name' => lang('X.DASHBOARD KỸ THUẬT'),
                            'is_permission' => checkPermission('dashboard_technique', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboards/dashboard_internal_control',
                            'key' => 'dashboard_internal_control',
                            'name' => lang('XI.DASHBOARD KIỂM SOÁT NỘI BỘ'),
                            'is_permission' => checkPermission('dashboard_internal_control', $staff_id, $is_admin)
                        ],
                        [
                            'link' => 'admin/report_dashboards/dashboard_kpi_room',
                            'key' => 'dashboard__kpi_room',
                            'name' => lang('XII.DASHBOARD BÁO CÁO KPIS PHÒNG BAN'),
                            'is_permission' => checkPermission('dashboard__kpi_room', $staff_id, $is_admin)
                        ],
                    ]
                ]
            ],
            'sub_menu_three' => [
                [
                    'key' => 'dashboarđ_three',
                    'name' => lang('Dashboard'),
                    'sub' => [
                        [
                            'link' => 'admin/RiskDashboard/index',
                            'key' => 'RiskDashboard',
                            'name' => lang('DASHBOARD TỔNG QUAN'),
                            'is_permission' => checkPermission('RiskDashboard', $staff_id, $is_admin)
                        ],
                        [
                            'link' => (function() use ($staff_id, $is_admin) {
                                if (checkPermission('DashboardKpi', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/dashboard';
                                if (checkPermission('DashboardKpi_Import', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/import_phong_ban';
                                if (checkPermission('DashboardKpi_CongViec', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/cong_viec';
                                if (checkPermission('DashboardKpi_ProductionReport', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/production_report';
                                if (checkPermission('DashboardKpi_KyDanhGia', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/ky_danh_gia';
                                if (checkPermission('DashboardKpi_PhieuDanhGia', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/phieu_danh_gia';
                                if (checkPermission('DashboardKpi_ViPham', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/vi_pham';
                                if (checkPermission('DashboardKpi_PheDuyet', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/phe_duyet';
                                if (checkPermission('DashboardKpi_FormIn', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/form_in';
                                if (checkPermission('DashboardKpi_TongHop', $staff_id, $is_admin)) return 'admin/dashboardKpi/index/tong_hop';
                                return 'admin/dashboardKpi/index/dashboard';
                            })(),
                            'key' => 'dashboardKpi',
                            'name' => lang('DASHBOARD KPI'),
                            'is_permission' => (
                                checkPermission('DashboardKpi', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_Import', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_CongViec', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_ProductionReport', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_KyDanhGia', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_PhieuDanhGia', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_ViPham', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_PheDuyet', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_FormIn', $staff_id, $is_admin) ||
                                checkPermission('DashboardKpi_TongHop', $staff_id, $is_admin)
                            )
                        ],
                    ]
                ]
            ]
        ];

        return $menu;
    }
}
