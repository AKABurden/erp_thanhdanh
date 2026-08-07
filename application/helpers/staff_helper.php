<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @param array $data additional data passed from view role.php and member.php
 * @return array
 * @since  2.3.3
 * Get available staff permissions, modules can use the filter too to hook permissions
 */
function get_available_staff_permissions($data = [])
{
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';
    $allPermissionsArray = [
        'view_own' => _l('permission_view_own'),
        'view' => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'print' => _l('print'),
        'approve' => _l('approve'),
        'delete' => _l('permission_delete'),
    ];
    $withoutViewOwnPermissionsArray = [
        'view' => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    $withNotApplicableViewOwn = array_merge(['view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')]], $withoutViewOwnPermissionsArray);
    $permissions = [
        // ___________________CÁC QUYỀN_____________________
        // view : Xem toàn cầu
        // view_own: Xem sở hữu
        // create : Khởi tạo
        // edit: Chỉnh sửa
        // print : IN
        // approve: Phê duyệt
        // import: Import
        // export: Export
        // delete: Xóa
        // _________________________________________________
        //Nhớ thêm parent vào menu option vd client thì "parent": "parent_customers";
        'parent_customers' => [
            'name' => _l('clients'),
            'child' => [
                'dashboard_client' => [
                    'name' => _l('Dashboard'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                    ],
                ],
                'leads' => [
                    'name' => _l('leads'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'import' => _l('Import'),
                        'export' => _l('Export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'customers' => [
                    'name' => _l('clients'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'import' => _l('Import'),
                        'export' => _l('Export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'all_contacts' => [
                    'name' => _l('customer_contacts'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'edit' => _l('permission_edit'),
                        'export' => _l('Export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'groups' => [
                    'name' => _l('customer_groups'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'export' => _l('Export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'info_client' => [
                    'name' => _l('group_info_client'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'export' => _l('Export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'care_ofs' => [
                    'name' => _l('Sinh nhật khách hàng'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                    ],
                ],
                'procedure_client' => [
                    'name' => _l('procedure_support'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'import_price_group' => [
                    'name' => _l('Bảng giá nhóm khách hàng'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'parent_purchase' => [
            'name' => _l('purchase'),
            'child' => [
                'purchases' => [
                    'name' => _l('ch_purchases'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'purchase_order' => [
                    'name' => _l('ch_order'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'view_price' => _l('view_price'),
                        'print' => _l('print')
                    ],
                ],
                'import' => [
                    'name' => _l('ch_import'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'view_price' => _l('view_price'),
                        'approve_qc' => _l('Duyệt QC'),
                        'approve_warehouse' => _l('approve_warehouse'),
                    ],
                ],
                'return_suppliers' => [
                    'name' => _l('ch_return'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'approve_warehouse' => _l('approve_warehouse'),
                    ],
                ],
                'service' => [
                    'name' => _l('Dịch vụ khác'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'contracts_supplier' => [
                    'name' => _l('Hợp Đồng Mua'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'parent_pay_slip' => [
            'name' => _l('debt_purchase'),
            'child' => [
                'purchase_invoice' => [
                    'name' => _l('ch_purchase_invoice'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'pay_slip' => [
                    'name' => _l('ch_vouchers_for_purchase'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'debt_suppliers' => [
                    'name' => _l('debt_suppliers'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                    ],
                ],
                'other_payslips' => [
                    'name' => _l('ch_other_payslips'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'suggestion' => [
                    'name' => _l('ch_suggestion'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve_accept' => _l('approve') . ': ' . _l('trưởng phòng'),
                    ],
                ],
                'suggest_payslips' => [
                    'name' => _l('ch_suggest_payslips'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'depreciable_assets' => [
                    'name' => _l('Tài sản khấu hao'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => false,
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
            ],
        ],
        'parent_warehouse' => [
            'name' => _l('warehouse'),
            'child' => [
                'warehouse_group' => [
                    'name' => _l('kb_article_add_edit_group'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'warehouse' => [
                    'name' => _l('warehouse'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('Export'),
                    ],
                ],
                'warehouse_localtion' => [
                    'name' => _l('warehouse_localtion'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'transfer' => [
                    'name' => _l('ch_transfer_warehouse'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'approve_warehouse' => _l('approve_warehouse'),
                    ],
                ],
                'stock_exporting_producion' => [
                    'name' => _l('tnh_exporting_stock_producion'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'approve_warehouse' => _l('approve_warehouse'),
                    ],
                ],
                'stock_purchase_products' => [
                    'name' => _l('purchase_products'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'print' => _l('print'),
                        'approve_warehouse' => _l('approve_warehouse'),
                        'approve' => _l('approve_warehouse') . ' trên chuyền',
                        'import' => _l('approve_warehouse') . ' sản xuất lỗi',
                    ],
                ],
                'export_different' => [
                    'name' => _l('ch_export_different'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'approve_warehouse' => _l('approve_warehouse'),
                    ],
                ],
            ],
        ],
        'parent_inventory' => [
            'name' => _l('inventory'),
            'child' => [
                'inventory' => [
                    'name' => _l('ch_inventory_warehouse'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('Duyệt kiểm kê'),
                    ],
                ],
                'adjusted' => [
                    'name' => _l('ch_adjusted_warehouse'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'parent_suppliers' => [
            'name' => _l('ch_suppliers'),
            'child' => [
                'suppliers_group' => [
                    'name' => _l('ch_groups_suppliers'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'suppliers' => [
                    'name' => _l('ch_suppliers'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'import_price' => [
                    'name' => _l('Bảng giá nhà cung cấp'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'export' => _l('export'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'parent_invoice_items' => [
            'name' => _l('items'),
            'child' => [
                'categories' => [
                    'name' => _l('ch_categories'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'invoice_items' => [
                    'name' => _l('ch_items_s'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'items_category' => [
                    'name' => _l('tnh_item_materials_category'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
                'items' => [
                    'name' => _l('tnh_item_materials_list'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
                'products_category' => [
                    'name' => _l('tnh_category_product'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
                'products' => [
                    'name' => _l('tnh_products'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
                'products_list_bom' => [
                    'name' => _l('tnh_list_bom'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'products_bom' => [
                    'name' => _l('tnh_list_bom'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'print' => _l('print'),
                    ],
                ],
                'tools_supplies_category' => [
                    'name' => _l('tnh_category_tools_supplies'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
                'tools_supplies' => [
                    'name' => _l('tnh_tools_supplies_list'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                    ],
                ],
            ],
        ],
        'parent_vouchers_coupon' => [
            'name' => _l('debt_selling'),
            'child' => [
                'vouchers_coupon' => [
                    'name' => _l('vouchers_for_coupon'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'debt_clients' => [
                    'name' => _l('debt_clients'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                    ],
                ],
                'other_payslips_coupon' => [
                    'name' => _l('other_payslips_coupon'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'coupon_invoice' => [
                    'name' => _l('coupon_invoice'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'manufactures' => [
            'name' => _l('manufactures'),
            'child' => [
                'manufactures_productions_plan' => [
                    'name' => _l('productions_plan'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('export'),
                        'approve' => _l('approve'),
                    ],
                ],
                'manufactures_productions_capacity' => [
                    'name' => _l('productions_capacity'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'manufactures_productions_orders' => [
                    'name' => _l('productions_orders'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'qc' => _l('Duyệt hoàn Thành'),
                        'approve_cancel' => _l('approve') . ': ' . _l('ch_cancel'),
                    ],
                ],
                'manufactures_order_production_details' => [
                    'name' => _l('tnh_productions_order_details'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'approve' => _l('approve'),
                        'qc' => _l('qc'),
                        'notifications' => _l('tnh_notifications'),
                    ],
                ],
                'manufactures_calendar_pod' => [
                    'name' => _l('Lịch sản xuất'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                    ],
                ],
                'manufactures_gantt_pod' => [
                    'name' => _l('tnh_diagram_gantt_lsx'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                    ],
                ],
                'manufactures_list_suggest_exporting' => [
                    'name' => _l('list_suggest_exporting'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'manufacture' => [
                    'name' => _l('manufacture'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
            ],
        ],
        'parent_orders' => [
            'name' => _l('tnh_orders'),
            'child' => [
                'orders_gannt_orders' => [
                    'name' => _l('tnh_diagram_gantt_orders'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                    ],
                ],
                'quote_stage' => [
                    'name' => _l('Bảng giá công đoạn'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'quotes' => [
                    'name' => _l('tnh_quotes'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'orders' => [
                    'name' => _l('tnh_orders'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'cost' => _l('cost_price'),
                        'profit' => _l('profit'),
                    ],
                ],
                'compose' => [
                    'name' => _l('ch_compose'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete')
                    ],
                ],
                'contracts_sales' => [
                    'name' => _l('contracts_sales'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'business_plan' => [
                    'name' => _l('tnh_business_plan'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                    ],
                ],
                'set_prices' => [
                    'name' => _l('set_prices'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'ptm' => [
                    'name' => _l('tnh_ptm'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'export' => _l('export'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'parent_releases' => [
            'name' => _l('releases'),
            'child' => [
                'releases_deliveries' => [
                    'name' => _l('deliveries'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'approve_warehouse' => _l('approve_warehouse'),
                        'cost' => _l('price'),
                        'delete' => _l('permission_delete'),
                        'approve_accept' => _l('Đã nhận chứng từ'),
                    ],
                ],
            ],
        ],
        'parent_tasks' => [
            'name' => _l('assigned'),
            'child' => [
                'tasks' => [
                    'name' => _l('ch_work_list'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'gantt' => [
                    'name' => _l('tnh_diagram_gantt'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                    ],
                ],
                'work_plan' => [
                    'name' => _l('tnh_work_plan'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'edit' => _l('permission_edit'),
                    ],
                ],
            ],
        ],
        'parent_reports' => [
            'name' => _l('reports'),
            'child' => [
                'orders_of_quotes' => [
                    'name' => _l('tnh_report_orders_of_quotes'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'delivery_schedules' => [
                    'name' => _l('tnh_report_delivery_schedules'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'sales_of_order' => [
                    'name' => _l('tnh_sales_of_order'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'nearest_selling_price' => [
                    'name' => _l('tnh_nearest_selling_price'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'returned_goods' => [
                    'name' => _l('tnh_report_returned_goods'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'order_status' => [
                    'name' => _l('tnh_report_order_status'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'sales_analysis' => [
                    'name' => _l('sales_analysis'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'selling_diary' => [
                    'name' => _l('selling_diary'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'material_norms' => [
                    'name' => _l('report_of_material_norms'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'usage_material' => [
                    'name' => _l('report_the_usage_material'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'production_detailed' => [
                    'name' => _l('report_production_detailed'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'situation_order_execution' => [
                    'name' => _l('report_situation_order_execution'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'status_production' => [
                    'name' => _l('report_status_production'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'use_ml_ac_production_orders' => [
                    'name' => _l('report_use_material_according_production_orders'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'general_production' => [
                    'name' => _l('report_general_production'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'production_schedule_by_order' => [
                    'name' => _l('tnh_production_schedule_by_order'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'expenses_vs_income' => [
                    'name' => _l('expenses_vs_income'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
            ],
        ],
        'parent_reports_purchase' => [
            'name' => _l('expenses_reports'),
            'child' => [
                'purchase_details' => [
                    'name' => _l('general_purchase_detail_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'consolidated_purchase_report' => [
                    'name' => _l('ch_consolidated_purchase_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'purchase_details_book' => [
                    'name' => _l('ch_purchase_details_book'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'summary_of_liabilities' => [
                    'name' => _l('ch_summary_of_liabilities'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'details_of_liabilities_by_item' => [
                    'name' => _l('ch_details_of_liabilities_by_item'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
            ],
        ],
        'parent_fund_balance' => [
            'name' => _l('ch_fund_balance'),
            'child' => [
                'diary_of_collecting_money' => [
                    'name' => _l('diary_of_collecting_money'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'diary_of_spending_money' => [
                    'name' => _l('diary_of_spending_money'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'diary_of_revenue_and_expenditure' => [
                    'name' => _l('diary_of_revenue_and_expenditure'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'aggregate_fund_balance' => [
                    'name' => _l('aggregate_fund_balance'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'cash_book_bank' => [
                    'name' => _l('cash_book_bank'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'cash_flow' => [
                    'name' => _l('ch_cash_flow'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
            ],
        ],
        'parent_warehouse_reports' => [
            'name' => _l('ch_warehouse_reports'),
            'child' => [
                'stock_card' => [
                    'name' => _l('ch_stock_card'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'import_export_report' => [
                    'name' => _l('import_export_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'import_report' => [
                    'name' => _l('import_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'export_report' => [
                    'name' => _l('export_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'export_sx_report' => [
                    'name' => _l('export_sx_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'transfer_report' => [
                    'name' => _l('transfer_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'export_orther_report' => [
                    'name' => _l('export_other_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'adjusted_report' => [
                    'name' => _l('adjusted_report'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
            ],
        ],
        'parent_debt_customer' => [
            'name' => _l('debt_customer'),
            'child' => [
                'debt_all_result' => [
                    'name' => _l('debt_all_result'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'debt_all_result_detail' => [
                    'name' => _l('debt_all_result_detail'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'debt_all_result_by_staff' => [
                    'name' => _l('debt_all_result_by_staff'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
            ],
        ],
        'parent_quality_control' => [
            'name' => _l('tnh_qc'),
            'child' => [
                'quality_control' => [
                    'name' => _l('QC + Danh mục qc'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'print' => _l('print'),
                        'approve' => 'Duyệt QA-Nhập Xuất TP',
                        'approve_warehouse' => 'Duyệt QA-Tồn Kho TP',
                        'notifications' => 'Duyệt Thủ Kho',
                    ],
                ],
            ],
        ],
        'parent_outsource' => [
            'name' => _l('Gia công'),
            'child' => [
                'import_outsource' => [
                    'name' => _l('Nhập gia công'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'delete' => _l('permission_delete'),
                        'approve_warehouse' => _l('Duyệt QA'),
                        'notifications' => _l('tnh_notifications'),
                    ],
                ],
                'outsource' => [
                    'name' => _l('Gia công'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'approve' => _l('approve'),
                        'export_outsource' => _l('Bổ sung NVL/BTP'),
                    ],
                ],
            ],
        ],
        'parent_warning_warehouse' => [
            'name' => _l('Cảnh báo tồn kho'),
            'child' => [
                'warning_warehouse' => [
                    'name' => _l('Cảnh báo tồn kho'),
                    'permissions' => [
                        'notifications' => _l('Cảnh báo tồn kho'),
                    ],
                ],
            ],
        ],
        'parent_notification' => [
            'name' => _l('tnh_notifications'),
            'child' => [
                'notification_orders' => [
                    'name' => _l('orders'),
                    'permissions' => [
                        'agree_notifications' => _l('tnh_agree'),
                    ],
                ],
                'notification_purchases' => [
                    'name' => _l('tnh_pruchases_items'),
                    'permissions' => [
                        'add_notifications' => _l('add'),
                    ],
                ],
            ],
        ],
        'list_decision' => [
            'name' => _l('Quyết định'),
            'child' => [
                'decision_category' => [
                    'name' => _l('Danh mục'),
                    'permissions' => $withoutViewOwnPermissionsArray,
                ],
                'decision_list' => [
                    'name' => _l('Biên bản quyết định'),
                    'permissions' => $allPermissionsArray,
                ],
            ],
        ],
        'parent_paid_holiday_leave' => [
            'name' => _l('Đơn xin nghỉ phép'),
            'child' => [
                'paid_holidays' => [
                    'name' => _l('Đơn xin nghỉ phép'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve' => _l('Duyệt phiếu'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'has_personnel' => [
            'name' => _l('Hành chính nhân sự'),
            'child' => [
                'staff' => [
                    'name' => _l('Nhân viên'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                        'export' => _l('Xuất excel'),
                    ],
                ],
                'timekeeping' => [
                    'name' => _l('Chi tiết giờ công'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_edit'),
                    ],
                ],
                'dashboard_timekeeping' => [
                    'name' => _l('Thống kê giờ công'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ],
                ],
                'suggest_overtime' => [
                    'name' => _l('Phiếu đề xuất tăng ca'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve' => _l('Duyệt phiếu'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'business_overtime' => [
                    'name' => _l('Tăng ca tháng'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'approve' => _l('Duyệt phiếu'),
                    ],
                ],
                'business_report_overtime' => [
                    'name' => _l('Thống kê tăng ca'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ],
                ],
                'business_calculate' => [
                    'name' => _l('Bảng tính tăng ca'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'payroll_payment' => [
                    'name' => _l('Phiếu tạm ứng'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'payroll_salary' => [
                    'name' => _l('Bảng lương'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
            ],
        ],
        'production_report' => [
            'name' => _l('Phiếu báo cáo'),
            'child' => [
                'production_report' => [
                    'name' => _l('Phiếu báo cáo'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'approve_lbc' => _l('Người lập báo cáo'),
                        'approve_ncnxl' => _l('Người chứng nhận xử lý'),
                        'approve_gspn' => _l('Người giám sát phòng ngừa'),
                        'approve_dg' => _l('Người đánh giá'),
                        'delete' => _l('permission_delete'),
                    ],
                ]
            ]
        ],
        'maintenance' => [
            'name' => _l('Bảo trì'),
            'child' => [
                'maintenance' => [
                    'name' => _l('Bảo trì'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        //						'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'category_maintenance' => [
                    'name' => _l('Hạng mục bảo trì'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'import' => _l('Import'),
                        'delete' => _l('permission_delete'),
                    ],
                ]
            ],
        ],
        'menu_hand_over' => [
            'name' => _l('tnh_hand_over'),
            'child' => [
                'category_hand_over' => [
                    'name' => _l('tnh_category_hand_over'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'handover_task' => [
                    'name' => _l('tnh_handover_task'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ],
                ],
                'delivery_records' => [
                    'name' => _l('tnh_delivery_records'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ],
                ]
            ],
        ],
        'internal_proposal' => [
            'name' => _l('Phiếu đề xuất nội bộ'),
            'child' => [
                'internal_proposal' => [
                    'name' => _l('Phiếu đề xuất nội bộ'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve_accept' => _l('approve'),
                        'approve' => 'Thêm Cột Duyệt Thực Thi',
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'plan_propose' => [
            'name' => _l('c_plan_propose'),
            'child' => [
                'plan_propose' => [
                    'name' => _l('c_plan_propose'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve_accept' => _l('approve'),
                        'approve' => 'Thêm Cột Duyệt Thực Thi',
                        'print' => _l('print'),
                        'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'production_list' => [
            'name' => _l('tnh_production_list'),
            'child' => [
                'production_list' => [
                    'name' => _l('tnh_production_list'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'create' => _l('permission_create'),
                        'approve' => lang('Cập nhật'),
                        'edit' => _l('permission_edit'),
                        // 'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'kpi' => [
            'name' => _l('kpi'),
            'child' => [
                'kpi_criteria' => [
                    'name' => _l('tnh_kpi_criteria'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        // 'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'kpi' => [
                    'name' => _l('tnh_kpi_list'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'kpi_equipment_stage' => [
                    'name' => lang('KPI Thiết Bị Công Đoạn'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'parent_report_dashboard' => [
            'name' => _l('report_dashboard'),
            'child' => [
                'dashboard_quotes' => [
                    'name' => _l('Dashboard báo giá'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_revenue' => [
                    'name' => _l('Dashboard doanh thu'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_cost' => [
                    'name' => _l('Dashboard chi phí'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_stock' => [
                    'name' => _l('Dashboard tồn kho'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_manufactures' => [
                    'name' => _l('Dashboard sản xuất'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_task' => [
                    'name' => _l('Dashboard công việc'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_personnel' => [
                    'name' => _l('Dashboard hành chánh - nhân sự'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_purchases' => [
                    'name' => _l('Dashboard mua hàng'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_business_results' => [
                    'name' => _l('Dashboard kế hoạch kinh doanh'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
            ]
        ],
        'parent_5s' => [
            'name' => _l('Vệ Sinh 5s'),
            'child' => [
                'cleaning_5s' => [
                    'name' => _l('Khu vực vệ sinh 5s'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'suggest_check' => [
                    'name' => _l('Phiếu yêu cầu kiểm tra vệ sinh ATLĐ-5S'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'parent_suggest' => [
            'name' => _l('Phiếu yêu cầu'),
            'child' => [
                'suggest_recruitment' => [
                    'name' => _l('Phiếu yêu cầu tuyển dụng'),
                    'permissions' => [
                        'view' => _l('permission_view') . ' (' . _l('permission_global') . ')',
                        'view_own' => _l('permission_view_own'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve' => _l('approve'),
                        'delete' => _l('permission_delete'),
                    ]
                ]
            ]
        ],
        'parent_kpi' => [
            'name' => _l('KPI'),
            'child' => [
                'quota_bonus' => [
                    'name' => _l('Định mức khen thưởng'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ]
                ],
                'quota_discipline' => [
                    'name' => _l('Định mức kỷ luật'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ]
                ]
            ]
        ],
        'parent_category' => [
            'name' => _l('Danh mục'),
            'child' => [
                'type_improve' => [
                    'name' => _l('Loại cải tiến'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'category_evaluate' => [
                    'name' => _l('Nhóm đánh giá'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'evaluate' => [
                    'name' => _l('Đánh giá'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve' => _l('approve'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'educate' => [
                    'name' => _l('Đào tạo'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'approve' => _l('approve'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'category_salary' => [
                    'name' => _l('Danh mục bảng lương'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'category_eloquence' => [
                    'name' => _l('Bảng khoản phép'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'list_subsidize' => [
                    'name' => _l('Danh sách trợ cấp'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
                'allowance_toxic' => [
                    'name' => _l('Trợ cấp độc hại'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ]
                ],
                'allowance_pccc' => [
                    'name' => _l('Trợ cấp PCCC'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ]
                ],
                'allowance_fsc' => [
                    'name' => _l('Trợ cấp FSC'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'edit' => _l('permission_edit'),
                    ]
                ],
                'seniority' => [
                    'name' => _l('Theo dõi thâm niên'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'species' => [
                    'name' => _l('tnh_species'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
            ]
        ],
        'parent_category_salary' => [
            'name' => _l('Danh mục lương'),
            'child' => [
                'dashboard_contract' => [
                    'name' => _l('Dashboard hợp đồng lao động'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'dashboard_contract_appendix' => [
                    'name' => _l('Dashboard phụ lục hợp đồng'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'contract_appendix' => [
                    'name' => _l('Phụ lục hợp đồng'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'create' => _l('permission_create'),
                        'edit' => _l('permission_edit'),
                        'delete' => _l('permission_delete'),
                    ]
                ],
            ]
        ],
        'dashboard' => [
            'name' => _l('Dashboard'),
            'child' => [
                'RiskDashboard' => [
                    'name' => _l('DASHBOARD TỔNG QUAN'),
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
            ]
        ],
        'dashboardKpi' => [
            'name' => _l('Dashboard KPI'),
            'child' => [
                'DashboardKpi' => [
                    'name' => 'KPI - Tổng quan',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_Import' => [
                    'name' => 'KPI - Import',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_CongViec' => [
                    'name' => 'KPI - Công việc',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_ProductionReport' => [
                    'name' => 'KPI - Báo cáo vi phạm',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_KyDanhGia' => [
                    'name' => 'KPI - Kỳ đánh giá',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_PhieuDanhGia' => [
                    'name' => 'KPI - Phiếu đánh giá',
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'approve_lbc' => _l('Duyệt HCNS'),
                        'approve_ncnxl' => _l('Duyệt KTNB'),
                        'approve_gspn' => _l('Duyệt KSRR'),
                        'approve_dg' => _l('Duyệt BOD'),
                    ]
                ],
                'DashboardKpi_ViPham' => [
                    'name' => 'KPI - Vi phạm',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_PheDuyet' => [
                    'name' => 'KPI - Phê duyệt',
                    'permissions' => [
                        'view' => _l('permission_view'),
                        'approve_lbc' => _l('Duyệt HCNS'),
                        'approve_ncnxl' => _l('Duyệt KTNB'),
                        'approve_gspn' => _l('Duyệt KSRR'),
                        'approve_dg' => _l('Duyệt BOD'),
                    ]
                ],
                'DashboardKpi_FormIn' => [
                    'name' => 'KPI - In phiếu',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
                'DashboardKpi_TongHop' => [
                    'name' => 'KPI - Tổng hợp',
                    'permissions' => [
                        'view' => _l('permission_view'),
                    ]
                ],
            ]
        ]
    ];
    return hooks()->apply_filters('staff_permissions', $permissions, $data);
}

/**
 * Get staff by ID or current logged in staff
 * @param mixed $id staff id
 * @return mixed
 */
function get_staff($id = null)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }
    // Staff not logged in
    if (empty($id)) {
        return null;
    }
    if (!class_exists('staff_model', false)) {
        get_instance()->load->model('staff_model');
    }
    return get_instance()->staff_model->get($id);
}

/**
 * Return staff profile image url
 * @param mixed $staff_id
 * @param string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');
    if ((string)$staff_id === (string)get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $staff = $GLOBALS['current_user'];
    } else {
        $CI = &get_instance();
        $CI->db->select('profile_image')
            ->where('staffid', $staff_id);
        $staff = $CI->db->get(db_prefix() . 'staff')->row();
    }
    if ($staff) {
        if (!empty($staff->profile_image)) {
            $profileImagePath = 'uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image;
            if (file_exists($profileImagePath)) {
                $url = base_url($profileImagePath);
            }
        }
    }
    return $url;
}

/**
 * Staff profile image with href
 * @param boolean $id staff id
 * @param array $classes image classes
 * @param string $type
 * @param array $img_attrs additional <img /> attributes
 * @return string
 */
function staff_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
    $url = base_url('assets/images/user-placeholder.jpg');
    $id = trim($id);
    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }
    $blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    if ((string)$id === (string)get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $result = $GLOBALS['current_user'];
    } else {
        $CI = &get_instance();
        $result = $CI->app_object_cache->get('staff-profile-image-data-' . $id);
        if (!$result) {
            $CI->db->select('profile_image,firstname,lastname');
            $CI->db->where('staffid', $id);
            $result = $CI->db->get(db_prefix() . 'staff')->row();
            $CI->app_object_cache->add('staff-profile-image-data-' . $id, $result);
        }
    }
    if (!$result) {
        return $blankImageFormatted;
    }
    if ($result && $result->profile_image !== null) {
        $profileImagePath = 'uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image;
        if (file_exists($profileImagePath)) {
            $profile_image = '<img ' . $_attributes . ' src="' . base_url($profileImagePath) . '" class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
        } else {
            return $blankImageFormatted;
        }
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    }
    return $profile_image;
}

/**
 * Get staff full name
 * @param string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $tmpStaffUserId = get_staff_user_id();
    if ($userid == '' || $userid == $tmpStaffUserId) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname;
        }
        $userid = $tmpStaffUserId;
    }
    $CI = &get_instance();
    $staff = $CI->app_object_cache->get('staff-full-name-data-' . $userid);
    if (!$staff) {
        $CI->db->where('staffid', $userid);
        $staff = $CI->db->select('firstname,lastname')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-full-name-data-' . $userid, $staff);
    }
    return $staff ? $staff->firstname . ' ' . $staff->lastname : '';
}

/**
 * Get staff default language
 * @param mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        // checking for current user if is admin
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->default_language;
        }
        $staffid = get_staff_user_id();
    }
    $CI = &get_instance();
    $CI->db->select('default_language');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }
    return '';
}

function get_staff_recent_search_history($staff_id = null)
{
    $recentSearches = get_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches');
    if ($recentSearches == '') {
        $recentSearches = [];
    } else {
        $recentSearches = json_decode($recentSearches);
    }
    return $recentSearches;
}

function update_staff_recent_search_history($history, $staff_id = null)
{
    $totalRecentSearches = hooks()->apply_filters('total_recent_searches', 5);
    $history = array_reverse($history);
    $history = array_unique($history);
    $history = array_splice($history, 0, $totalRecentSearches);
    update_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches', json_encode($history));
    return $history;
}

/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param string $staff_id staff id
 * @return boolean
 */
function is_staff_member($staff_id = '')
{
    $CI = &get_instance();
    if ($staff_id == '') {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staff_id = get_staff_user_id();
    }
    $CI->db->where('staffid', $staff_id)
        ->where('is_not_staff', 0);
    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}
