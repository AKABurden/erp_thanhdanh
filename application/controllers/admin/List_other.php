<?php
defined('BASEPATH') or exit('No direct script access allowed');

class List_other extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->preView = true;
        $this->preViewOwn = true;
        $this->preAdd = true;
        $this->preEdit = true;
        $this->preApprove = true;
        $this->preDelete = true;
        /*list_type*/
        /*Chỉ cần  khai báo các trường cơ bản (do sửa đổi nên hạn chế khai báo thêm vào cái này)*/
		$this->list_type = [
			'standard_carry' => [
				'id' => 'standard_carry',
				'name' => 'Tiêu Chuẩn Bế Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Bế'
				]
			],
			'standard_sample_cover' => [
				'id' => 'standard_sample_cover',
				'name' => 'Tiêu Chuẩn Bìa Mẫu KH Duyệt',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Bìa Mẫu'
				]
			],
			'standard_smooth_shine' => [
				'id' => 'standard_smooth_shine',
				'name' => 'Tiêu Chuẩn Bóng Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Bóng'
				]
			],
			'standard_fsc' => [
				'id' => 'standard_fsc',
				'name' => 'Tiêu Chuẩn FSC Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn FSC'
				]
			],
			'standard_delivery_package' => [
				'id' => 'standard_delivery_package',
				'name' => 'Tiêu Chuẩn chuẩn Kiện Hàng Giao Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Kiện Hàng'
				]
			],
			'standard_membrane' => [
				'id' => 'standard_membrane',
				'name' => 'Tiêu Chuẩn Màng Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Màng'
				]
			],
			'standard_template' => [
				'id' => 'standard_template',
				'name' => 'Tiêu Chuẩn Mẫu (Y Mẫu, Mẫu TP Tồn Kho, Mẫu Theo SP)',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Mẫu'
				]
			],
			'standard_condition_color' => [
				'id' => 'standard_condition_color',
				'name' => 'Điều Kiện Xem Màu( Mắt Thường, Light Box., Ngoài Trời)',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Điều Kiện Xem Màu'
				]
			],
			'standard_color' => [
				'id' => 'standard_color',
				'name' => 'Tiêu Chuẩn Màu Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Màu'
				]
			],
			'standard_bin_carton' => [
				'id' => 'standard_bin_carton',
				'name' => 'Tiêu Chuẩn Thùng Carton Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Thùng Carton'
				]
			],
			'standard_trame' => [
				'id' => 'standard_trame',
				'name' => 'Tiêu Chuẩn Trame Của KH',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Trame'
				]
			],
			'standard_sample_code' => [
				'id' => 'standard_sample_code',
				'name' => 'Mã Bìa Mẫu',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Mã Bìa Mẫu'
				]
			],
			'standard_methods' => [
				'id' => 'standard_methods',
				'name' => 'Phương Pháp Đo (Đúng Điểm Đo, Đều Màu/Tờ In)',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Phương Pháp Đo'
				]
			],
			'standard_quality_standards' => [
				'id' => 'standard_quality_standards',
				'name' => 'Tiêu Chuẩn Chất Lượng SP',
				'colums' => [
//					'code' => 'Mã Sản Phẩm',
//					'name' => 'Tên Sản Phẩm',
					'standard' => 'Tiêu Chuẩn Chất Lượng'
				]
			],
		];

        /*list_other*/
        /*Nếu muốn cập nhật, khai báo 1 bảng mới cơ bản chỉ cần thêm như format*/
		$this->list_other = [
			'quota_delivery_package' => [
				'id' => 'quota_delivery_package',
				'name' => 'Danh Sách Định Mức Kiện Hàng Giao',
				'table' => 'tblquota_delivery_package',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã Định Mức',
					'name' => 'Tên Định Mức',
					'package_quantity' => 'Số Kiện',
					'kg' => 'Số Kg',
				]
			],
			'code_file_products' => [
				'id' => 'code_file_products',
				'name' => 'Danh Sách Mã File Sản Phẩm',
				'table' => 'tbl_code_file_products',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã File Sản Phẩm',
					'name' => 'Tên File Sản Phẩm',
					'is_url' => 'Đường Link'
				]
			],
            'equipment_maintenance_group' => [
				'id' => 'equipment_maintenance_group',
				'name' => 'Danh Sách Nhóm Bảo Trì',
				'table' => 'tbl_equipment_maintenance_group',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã Nhóm Bảo Trì',
					'name' => 'Tên Nhóm Bảo Trì',
				]
			],
            'list_program' => [
				'id' => 'list_program',
				'name' => 'Danh Sách Nhóm Phần Mềm',
				'table' => 'tbl_list_program',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã Nhóm Phần Mềm',
					'name' => 'Tên Nhóm Phần Mềm',
				]
			],
            'group_plan' => [
				'id' => 'group_plan',
				'name' => 'Danh Sách Mã Kế Hoạch',
				'table' => 'tbl_group_plan',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã Kế Hoạch',
					'name' => 'Tên Kế Hoạch',
				]
			],
            'list_area_warehouse' => [
				'id' => 'list_area_warehouse',
				'name' => 'Danh Sách Khu Vực Kho',
				'table' => 'tbl_list_area_warehouse',
				'colums' => [
					'id' => 'ID',
					'code' => 'Mã Khu Vực',
					'name' => 'Tên Khu Vực',
				]
			],
            'list_imported_documents' => [
                'id' => 'list_imported_documents',
                'name' => 'Danh Sách Chứng Từ Nhập Khẩu',
                'table' => 'tbl_list_imported_documents',
                'colums' => [
                    'id' => 'ID',
                    'code' => 'Mã Chứng Từ Nhập Khẩu',
                    'name' => 'Tên Chứng Từ Nhập Khẩu',
                ]
            ],
            'list_insurance' => [
                'id' => 'list_insurance',
                'name' => 'Danh Sách Bảo Hiểm',
                'table' => 'tbl_list_insurance',
                'colums' => [
                    'id' => 'ID',
                    'code' => 'Mã Bảo Hiểm',
                    'name' => 'Tên Bảo Hiểm',
                ]
            ],
            'list_type_quote' => [
                'id' => 'list_type_quote',
                'name' => 'Danh Sách Loại Báo Giá',
                'table' => 'tbl_list_type_quote',
                'colums' => [
                    'id' => 'ID',
                    'code' => 'Mã Loại Báo Giá',
                    'name' => 'Tên Loại Báo Giá',
                ]
            ],
		];


        /*list_join*/
        /*Dùng cho các bảng đã có dữ liệu và muốn thống kê riêng và cập nhật 1 trường*/
        $this->list_join = [
            'npl_allowable' => [
                'id' => 'npl_allowable',
                'name' => 'Danh Sách NPL Tồn Kho Cho Phép',
                'table' => 'tbl_materials',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã NPL',
                    'name' => 'Tên NPL',
                    'allowable' => 'Tồn Cho Phép',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Nguyên Phụ Liệu',
                        'type' => 'input_select',
                        'url_select' => 'admin/items/searchSelect2Materials',
                    ],
                    'allowable' => [
                        'label' => 'Tồn Cho Phép',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'allowable IS NOT NULL'
                ]
            ],
            'product_allowable' => [
                'id' => 'product_allowable',
                'name' => 'Danh Sách Thành Phẩm Tồn Kho Cho Phép',
                'table' => 'tbl_products',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Thành Phẩm',
                    'name' => 'Tên Thành Phẩm',
                    'allowable' => 'Tồn Cho Phép',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thành Phẩm',
                        'type' => 'input_select',
                        'url_select' => 'admin/products/searchProductsSelect2'
                    ],
                    'allowable' => [
                        'label' => 'Tồn Cho Phép',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'allowable IS NOT NULL',
                    'type_products = "products"'
                ]
            ],
            'product_time_stock' => [
                'id' => 'product_time_stock',
                'name' => 'Danh Sách Thời Gian Lưu Kho',
                'table' => 'tbl_products',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Thành Phẩm',
                    'name' => 'Tên Thành Phẩm',
                    'time_inventory' => 'Thời gian tồn kho',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thành Phẩm',
                        'type' => 'input_select',
                        'url_select' => 'admin/products/searchProductsSelect2'
                    ],
                    'time_inventory' => [
                        'label' => 'Thời gian tồn kho',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'time_inventory IS NOT NULL',
                    'type_products = "products"'
                ]
            ],
            'product_standard_group' => [
                'id' => 'product_standard_group',
                'name' => 'Danh Sách Nhóm Định Mức SP',
                'table' => 'tbl_products',
                'colums' => [
                    'id' => 'ID',
                    'code' => 'Mã Sản Phẩm',
                    'name' => 'Tên Sản Phẩm',
                    'quota' => 'Định mức',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thành Phẩm',
                        'type' => 'input_select',
                        'url_select' => 'admin/products/searchProductsSelect2'
                    ],
                    'quota' => [
                        'label' => 'Định Mức',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'quota IS NOT NULL',
                    'type_products = "products"'
                ]
            ],
            'budget_room' => [
                'id' => 'budget_room',
                'name' => 'Danh Sách Định Mức Ngân Sách Phòng Ban',
                'table' => 'tbl_room',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Phòng ban',
                    'name' => 'Tên Phòng ban',
                    'budget' => 'Định Mức Ngân Sách',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Phòng ban',
                        'type' => 'select',
                        'table' => 'tbl_room',
                        'option' => ['id', 'name', 'code']
                    ],
                    'budget' => [
                        'label' => 'Thời gian tồn kho',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'budget IS NOT NULL'
                ]
            ],
            'barrel_size' => [
                'id' => 'barrel_size',
                'name' => 'Danh Sách Định Mức Thùng Đóng Gói',
                'table' => 'tbl_products',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Sản Phẩm',
                    'name' => 'Tên Sản Phẩm',
                    'zcode' => 'Mã Khách Hàng',
                    'company' => 'Tên Khách Hàng',
                    'barrel_size' => 'Định Mức Thùng',
                ],
                'colums_join' => [
                    'tbl_products.id as id',
                    'tbl_products.code as code',
                    'tbl_products.name as name',
                    'tblclients.zcode as zcode',
                    'tblclients.company as company',
                    'tbl_products.barrel_size as barrel_size',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thành Phẩm',
                        'type' => 'input_select',
                        'url_select' => 'admin/list_other/searchProductsSelect2',
                        'product_customer' => true
                    ],
                    'barrel_size' => [
                        'label' => 'Định Mức Thùng',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'barrel_size IS NOT NULL'
                ],
                'join' => [
                    'tblclients' => 'tblclients.userid = tbl_products.customer'
                ]
            ],
            'depreciation_rates' => [
                'id' => 'depreciation_rates',
                'name' => 'Định Mức Khấu Hao Tài Sản - Thiết Bị Máy Móc',
                'table' => 'tbl_machines',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Thiết Bị',
                    'name' => 'Tên Thiết Bị',
                    'depreciation_rates' => 'Định Mức Khấu Hao',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thiết Bị',
                        'type' => 'input_select',
                        'url_select' => 'admin/list_other/searchMachinesSelect2',
                    ],
                    'depreciation_rates' => [
                        'label' => 'Định Mức Khấu Hao',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'depreciation_rates IS NOT NULL'
                ],
            ],
            'depreciation_period' => [
                'id' => 'depreciation_period',
                'name' => 'Định Mức Thời Gian Khấu Hao',
                'table' => 'tbl_machines',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Thiết Bị',
                    'name' => 'Tên Thiết Bị',
                    'depreciation_period' => 'Định Mức Thời Gian Khấu Hao',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thiết Bị',
                        'type' => 'input_select',
                        'url_select' => 'admin/list_other/searchMachinesSelect2',
                    ],
                    'depreciation_period' => [
                        'label' => 'Định Mức Thời Gian Khấu Hao',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'depreciation_period IS NOT NULL'
                ],
            ],
            'used_time' => [
                'id' => 'used_time',
                'name' => 'Định Mức Thời Gian Sử Dụng',
                'table' => 'tbl_machines',
                'colums' => [
                    'id' => '#',
                    'code' => 'Mã Thiết Bị',
                    'name' => 'Tên Thiết Bị',
                    'used_time' => 'Định Mức Thời Gian Sử Dụng',
                ],
                'colums_edit' => [
                    'id' => [
                        'label' => 'Thiết Bị',
                        'type' => 'input_select',
                        'url_select' => 'admin/list_other/searchMachinesSelect2',
                    ],
                    'used_time' => [
                        'label' => 'Định Mức Thời Gian Sử Dụng',
                        'type' => 'text',
                        'class' => 'text-center'
                    ],
                ],
                'where' => [
                    'used_time IS NOT NULL'
                ],
            ]
        ];

        $this->list_muti = [
            'time_payment' => [
                'id' => 'time_payment',
                'name' => 'Danh Sách Thời Hạn Thanh Toán',
                'default' => 'client',
                'data' => [
                    'client' => [
                        'table' => 'tblclients',
                        'name_table' => 'Khách Hàng',
                        'columKey' => 'userid',
                        'columView' => 'zcode',
                        'colums_th' => [
                            'id' => '#',
                            'code' => 'Mã Khách Hàng',
                            'company' => 'Tên Khách Hàng',
                            'time_payment' => 'Thời Gian Thanh Toán',
                        ],
                        'colums' => [
                            'userid' => '#',
                            'zcode' => 'Mã Khách Hàng',
                            'company' => 'Tên Khách Hàng',
                            'time_payment' => 'Thời Gian Thanh Toán',
                        ],
                        'colums_edit' => [
                            'userid' => [
                                'label' => 'Khách Hàng',
                                'type' => 'input_select',
                                'url_select' => 'admin/clients/searchOnlyCustomers',
                            ],
                            'time_payment' => [
                                'label' => 'Thời gian thanh toán',
                                'type' => 'text',
                                'class' => 'text-center'
                            ],
                        ],
                        'where' => [
                            'time_payment IS NOT NULL'
                        ]
                    ],
                    'supplier' => [
                        'table' => 'tblsuppliers',
                        'name_table' => 'Nhà Cung Cấp',
                        'columKey' => 'id',
                        'columView' => 'code',
                        'colums_th' => [
                            'id' => '#',
                            'code' => 'Mã NCC',
                            'company' => 'Tên NCC',
                            'time_payment' => 'Thời Gian Thanh Toán',
                        ],
                        'colums' => [
                            'id' => '#',
                            'code' => 'Mã NCC',
                            'company' => 'Tên NCC',
                            'time_payment' => 'Thời Gian Thanh Toán',
                        ],
                        'colums_edit' => [
                            'id' => [
                                'label' => 'Nhà Cung Cấp',
                                'type' => 'input_select',
                                'url_select' => 'admin/list_other/searchSuppliersSelect2',
                            ],
                            'time_payment' => [
                                'label' => 'Thời gian thanh toán',
                                'type' => 'text',
                                'class' => 'text-center'
                            ],
                        ],
                        'where' => [
                            'time_payment IS NOT NULL'
                        ]
                    ]
                ],
//                'table' => [
//                    'client' => 'tblclients',
//                    'supplier' => 'tblsuppliers'
//                ],
//                'name_table' => [
//                    'client' => 'Khách Hàng',
//                    'supplier' => 'Nhà Cung Cấp'
//                ],
//                'columKey' => [
//                    'client' => 'userid',
//                    'supplier' => 'id',
//                ],
//                'colums_th' => [
//                    'client' => [
//                        'id' => '#',
//                        'code' => 'Mã Khách Hàng',
//                        'company' => 'Tên Khách Hàng',
//                        'time_payment' => 'Thời Gian Thanh Toán',
//                    ],
//                    'supplier' => [
//                        'id' => '#',
//                        'code' => 'Mã Nhà Cung Cấp',
//                        'company' => 'Tên Nhà Cung Cấp',
//                        'time_payment' => 'Thời Gian Thanh Toán',
//                    ],
//                ],
//                'colums' => [
//                    'client' => [
//                        'userid' => '#',
//                        'zcode' => 'Mã Khách Hàng',
//                        'company' => 'Tên Khách Hàng',
//                        'time_payment' => 'Thời Gian Thanh Toán',
//                    ],
//                    'supplier' => [
//                        'id' => '#',
//                        'code' => 'Mã Nhà Cung Cấp',
//                        'company' => 'Tên Nhà Cung Cấp',
//                        'time_payment' => 'Thời Gian Thanh Toán',
//                    ],
//                ],
//                'colums_edit' => [
//                    'client' => [
//                        'userid' => [
//                            'label' => 'Khách Hàng',
//                            'type' => 'input_select',
//                            'url_select' => 'admin/clients/searchOnlyCustomers',
//                        ],
//                        'time_payment' => [
//                            'label' => 'Thời gian thanh toán',
//                            'type' => 'text',
//                            'class' => 'text-center'
//                        ],
//                    ],
//                    'supplier' => [
//                        'id' => [
//                            'label' => 'Nhà Cung Cấp',
//                            'type' => 'input_select',
//                            'url_select' => 'admin/list_other/searchSuppliersSelect2',
//                        ],
//                        'time_payment' => [
//                            'label' => 'Thời gian thanh toán',
//                            'type' => 'text',
//                            'class' => 'text-center'
//                        ],
//                    ],
//                ],
//                'where' => [
//                    'client' => [
//                        'time_payment IS NOT NULL'
//                    ],
//                    'supplier' => [
//                        'time_payment IS NOT NULL'
//                    ]
//                ],
            ]
        ];

    }

    /*Quản lý nhanh các tiêu chuẩn*/
    public function standard($type = 'standard_carry') {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
		$data_type = !empty($this->list_type[$type]) ? $this->list_type[$type] : [];
		if(empty($data_type)) {
			show_404();
		}
		if(!empty($data_type)) {
			$data['title'] = 'Danh sách ' . $data_type['name'];
			$data['type'] = $data_type['id'];
			$data['name_colums'] = $data_type['colums'];
		}
        $this->load->view('admin/list_other/manage', $data);
    }

    /*Chỉnh sửa nhanh các tiêu chuẩn*/
	public function detail($type = 'standard_carry', $id = 0){
		$data_type = $this->list_type[$type];
		if(empty($data_type)) {
			show_404();
		}
		
		if ($this->input->post()){
			$dataResut = [];
			$data = $this->input->post();
			if (empty($id)){
				$this->db->where('standard', $data['standard']);
				$this->db->where('type', $type);
				$ktCode = $this->db->get('tbllist_other')->row();
				if(!empty($ktCode)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Tiêu chuẩn đã tồn tại vui lòng nhập tiêu chuẩn khác'
					]);die();
				}
				
				$fields = [
//					'code' => $data['code'],
//					'name' =>$data['name'],
					'standard' =>$data['standard'],
					'type' => $type,
					'create_by' => get_staff_user_id(),
					'date_create' => date('Y-m-d H:i:s')
				];
				$success = $this->db->insert('tbllist_other', $fields);
				if (!empty($success)){
					$id = $this->db->insert_id();
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Thêm mới thành công');
				} else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Thêm mới không thành công');
				}
				
				echo json_encode($dataResut);return;
			}
			else {
				$this->db->where('standard', $data['standard']);
				$this->db->where('type', $type);
				$this->db->where('id != "'.$id.'"', false, false);
				$ktCode = $this->db->get('tbllist_other')->row();
				if(!empty($ktCode)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Tiêu chuẩn đã tồn tại vui lòng nhập mã khác'
					]);die();
				}
				
				$this->db->where('id', $id);
				$list_data_other = $this->db->get('tbllist_other')->row();
				$fields = [
//					'code' => $data['code'],
//					'name' =>$data['name'],
					'standard' => $data['standard'],
					'type' => $type,
				];
				
				$this->db->where('id', $id);
				$success = $this->db->update('tbllist_other', $fields);
				if (!empty($success)){
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Cập nhật thành công');
				}
				else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Cập nhật không thành công');
				}
				echo json_encode($dataResut);return;
			}
		}
		else {
			if (empty($id)){
				if (!$this->preAdd){
					accessDenied(true);
				}
				
				$data['title'] = 'Thêm ' . $data_type['name'];
			} else {
				if (!$this->preEdit){
					accessDenied(true);
				}
				$data['list_other'] = $this->db->get_where('tbllist_other', ['type' => $type, 'id' => $id])->row();
				$data['title'] = 'Sửa ' . $data_type['name'];
			}
		}
		$data['name_colums'] = $data_type['colums'];
		$data['type'] = $type;
		$data['id'] = $id;
		$this->load->view('admin/list_other/detail',$data);
	}
	
	public function table($type = 'standard_carry') {
		$data_type = $this->list_type[$type];
		$aColumns = [
			'tbllist_other.id as id',
			'tbl_products.code as code',
			'tbl_products.name as name',
			'tbllist_other.standard as standard',
			'tbllist_other.create_by as create_by',
		];
		$sWhere = [
			'AND tbllist_other.type = "'.$type.'"',
		];
		$join = [
			'LEFT JOIN tbl_products ON tbl_products.id = tbllist_other.id_product'
		];
		$sIndexColumn = 'id';
		$sTable       = 'tbllist_other';
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, []);
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['id'];
			$row[] = $aRow['code'];
			$row[] = $aRow['name'];
			$row[] = $aRow['standard'];
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '</a>';
			$row[] = $profile_CREATE . ' ' . $fullname_CREATE;
			$options = '';
			$options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('list_other/detail/' . $type . '/' . $aRow['id'])).'" ><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-danger c_delete" href="'.(admin_url('list_other/remove/' . $type)).'" data-id="'.$aRow['id'].'" ><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}
	
	public function remove($type = 'standard_carry') {
		$id = $this->input->post('id');
		if(!empty($id)) {
			$this->db->where('id', $id);
			$this->db->where('type', $type);
			$ktList_other = $this->db->get('tbllist_other')->row();
			if(!empty($ktList_other)) {
				$this->db->where('id', $id);
				$success = $this->db->delete('tbllist_other');
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa dữ liệu thành công'
					]);die();
				}
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa Phiếu không thành công']);die();
	}

    /*Quản lý nhanh các danh mục cơ bản và tiêu chuẩn*/
	public function other($type = 'quota_delivery_package') {
		if (!$this->preView && !$this->preViewOwn) {
			access_denied();
		}
		$data_list_other = !empty($this->list_other[$type]) ? $this->list_other[$type] : [];
		if(empty($data_list_other)) {
			show_404();
		}
		if(!empty($data_list_other)) {
			// $data['title'] = 'Danh sách ' . $data_list_other['name'];
            $data['title'] = $data_list_other['name'];
			$data['type'] = $data_list_other['id'];
			$data['name_colums'] = $data_list_other['colums'];
		}
		$this->load->view('admin/list_other/other', $data);
	}

    /*Chỉnh sửa nhanh các danh mục cơ bản và tiêu chuẩn*/
	public function detail_other($type = 'quota_delivery_package', $id = 0){
		$data_type = $this->list_other[$type];
		if(empty($data_type)) {
			show_404();
		}
		
		if ($this->input->post()){
			$dataResut = [];
			$data = $this->input->post();
			if (empty($id)){
				$this->db->where('code', $data['code']);
				$ktCode = $this->db->get($data_type['table'])->row();
				if(!empty($ktCode)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Mã đã tồn tại vui lòng nhập mã khác'
					]);die();
				}
				
				$fields = [
					'create_by' => get_staff_user_id(),
					'date_create' => date('Y-m-d H:i:s')
				];
				
				foreach($data_type['colums'] as $k => $v) {
					if(isset($data[$k])) {
						$fields[$k] = $data[$k];
					}
				}
//				$fields = [
//					'code' => $data['code'],
//					'name' =>$data['name'],
//					'standard' =>$data['standard'],
//					'type' => $type,
//					'create_by' => get_staff_user_id(),
//					'date_create' => date('Y-m-d H:i:s')
//				];
				$success = $this->db->insert($data_type['table'], $fields);
				if (!empty($success))
				{
					$id = $this->db->insert_id();
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Thêm mới thành công');
				} else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Thêm mới không thành công');
				}
				
				echo json_encode($dataResut);return;
			}
			else {
				$this->db->where('code', $data['code']);
				$this->db->where('id != "'.$id.'"', false, false);
				$ktCode = $this->db->get($data_type['table'])->row();
				if(!empty($ktCode)) {
					echo json_encode([
						'success' => false,
						'alert_type' => 'danger',
						'message' => 'Mã đã tồn tại vui lòng nhập mã khác'
					]);die();
				}
				
				
				$this->db->where('id', $id);
				$list_data_other = $this->db->get($data_type['table'])->row();
				
				$fields = [];
				foreach($data_type['colums'] as $k => $v) {
					if(isset($data[$k])) {
						$fields[$k] = $data[$k];
					}
				}
				
				$this->db->where('id', $id);
				$success = $this->db->update($data_type['table'], $fields);
				if (!empty($success)){
					$dataResut['success'] = true;
					$dataResut['alert_type'] = 'success';
					$dataResut['message'] = lang('Cập nhật thành công');
				}
				else {
					$dataResut['success'] = false;
					$dataResut['alert_type'] = 'danger';
					$dataResut['message'] = lang('Cập nhật không thành công');
				}
				
				echo json_encode($dataResut);return;
			}
		}
		else {
			if (empty($id)){
				if (!$this->preAdd){
					accessDenied(true);
				}
				
				$data['title'] = 'Thêm ' . $data_type['name'];
			} else {
				if (!$this->preEdit){
					accessDenied(true);
				}
				$data['list_other'] = $this->db->get_where('tbllist_other', ['type' => $type, 'id' => $id])->row();
				$data['title'] = 'Sửa ' . $data_type['name'];
			}
		}
		$data['name_colums'] = $data_type['colums'];
		$data['type'] = $type;
		$data['id'] = $id;
		$this->load->view('admin/list_other/detail_other', $data);
	}

    /*show ra các danh mục cơ bản và tiêu chuẩn*/
	public function table_other($type = 'quota_delivery_package') {
		$data_type = $this->list_other[$type];
		$aColumns = [];
		foreach($data_type['colums'] as $k => $v) {
			$aColumns[] = $data_type['table'].'.'.$k.' as ' . $k;
		}
		$aColumns[] = $data_type['table'] . '.create_by as create_by';
		$sWhere = [
//			'AND tbllist_other.type = "'.$type.'"',
		];
		$join = [];
		$sIndexColumn = 'id';
		$sTable       = $data_type['table'];
		$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, []);
		$output       = $result['output'];
		$rResult      = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			foreach($data_type['colums'] as $k => $v) {
				$row[] = $aRow[$k];
			}
			$fullname_CREATE = get_staff_full_name($aRow['create_by']);
			$profile_CREATE = '<a data-toggle="tooltip" data-title="' . $fullname_CREATE . '" href="' . admin_url('profile/' . $aRow['create_by']) . '">' . staff_profile_image($aRow['create_by'], [
					'staff-profile-image-small',
				]) . '</a>';
			$row[] = $profile_CREATE . ' ' . $fullname_CREATE;
			$options = '';
			$options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('list_other/detail_other/' . $type . '/' . $aRow['id'])).'" ><i class="fa fa-edit"></i></a>';
			$options .= '<a class="btn btn-icon btn-danger c_delete" href="'.(admin_url('list_other/remove_other/' . $type)).'" data-id="'.$aRow['id'].'" ><i class="fa fa-remove"></i></a>';
			$row[] = $options;
			$output['aaData'][] = $row;
		}
		echo json_encode($output);die();
	}
	
	public function remove_other($type = 'quota_delivery_package') {
		$data_type = $this->list_other[$type];
		$id = $this->input->post('id');
		if(!empty($id)) {
			$this->db->where('id', $id);
			$ktList_other = $this->db->get($data_type['table'])->row();
			if(!empty($ktList_other)) {
				$this->db->where('id', $id);
				$success = $this->db->delete($data_type['table']);
				if(!empty($success)) {
					echo json_encode([
						'success' => true,
						'alert_type' => 'success',
						'message' => 'Xóa dữ liệu thành công'
					]);die();
				}
			}
		}
		echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa Phiếu không thành công']);die();
	}


    /*Quản lý nhanh các danh mục có các trường cập nhật dữ liệu*/
    public function list_join($type = 'npl_allowable') {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data_list_join = !empty($this->list_join[$type]) ? $this->list_join[$type] : [];
        if(empty($data_list_join)) {
            show_404();
        }
        if(!empty($data_list_join)) {
            $data['title'] = $data_list_join['name'];
            $data['type'] = $type;
            $data['name_colums'] = $data_list_join['colums'];
        }
        $this->load->view('admin/list_other/list_join/manage', $data);
    }

    /*Thêm sửa xóa nhanh các danh mục có các trường cập nhật dữ liệu*/
    public function detail_join($type = '', $id = '') {
        $data_type = $this->list_join[$type];
        if($this->input->post()) {
            $data = [];
            $colums_edit = $data_type['colums_edit'];
            foreach($colums_edit as $key => $value) {
                $data[$key] = $this->input->post($key);
            }
            $_id = $id;
            if(!empty($data['id']) && empty($id)) {
                $_id = $data['id'];
            }
            if(!empty($_id)) {
                unset($data['id']);
                $this->db->where('id', $_id);
                $success = $this->db->update($data_type['table'], $data);
            }
            if(!empty($success)) {
                if(empty($id)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm dữ liệu thành công']);
                    die();
                }
                else {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật dữ liệu thành công']);
                    die();
                }
            }
            else {
                if(empty($id)) {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);
                    die();
                }
                else {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);
                    die();
                }
            }
        }
        else {
            if (empty($id)){
                if (!$this->preAdd){
                    accessDenied(true);
                }
                $data['title'] = 'Thêm ' . $data_type['name'];
            } else {
                if (!$this->preEdit){
                    accessDenied(true);
                }
                $data['list_join'] = $this->db->get_where($data_type['table'], ['id' => $id])->row();
                $data['title'] = 'Sửa ' . $data_type['name'] . ' <br/>(' . ($data['list_join']->code . ' - '. $data['list_join']->name).')';
                unset($data_type['colums_edit']['id']);
            }
        }

        $data['type'] = $type;
        $data['name_colums'] = $data_type['colums'];
        $data['colums_edit'] = $data_type['colums_edit'];
        $this->load->view('admin/list_other/list_join/detail', $data);
    }

    public function table_join($type = '') {
        $data_type = $this->list_join[$type];
        $aColumns = [];
        if(empty($data_type['colums_join'])) {
            foreach ($data_type['colums'] as $k => $v) {
                $aColumns[] = $data_type['table'] . '.' . $k . ' as ' . $k;
            }
        }
        else {
            foreach ($data_type['colums_join'] as $k => $v) {
                $aColumns[] = $v;
            }
        }
        $sWhere = [];
        if(!empty($data_type['where'])){
            foreach($data_type['where'] as $key => $value) {
                $sWhere[] = 'AND '. $value;
            }
        }
        $join = [];
        if(!empty($data_type['join'])) {
            foreach($data_type['join'] as $keyTable => $valueWhere) {
                $join[] = 'LEFT JOIN '.$keyTable.' ON ' . $valueWhere;
            }
        }
        $sIndexColumn = 'id';
        $sTable       = $data_type['table'];
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $aRow) {
            $row = [];
            foreach($data_type['colums'] as $k => $v) {
                $row[] = '<div class="'.(@$data_type['colums_edit'][$k]['class']).'">' . $aRow[$k] . '</div>';
            }
            $options = '';
            $options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('list_other/detail_join/' . $type . '/' . $aRow['id'])).'" ><i class="fa fa-edit"></i></a>';
            $options .= '<a class="btn btn-icon btn-danger c_delete" href="'.(admin_url('list_other/remove_join/' . $type)).'" data-id="'.$aRow['id'].'" ><i class="fa fa-remove"></i></a>';
            $row[] = $options;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    //làm rỗng trường cần cập nhật
    public function remove_join($type = '')
    {
        $data_type = $this->list_join[$type];
        $id = $this->input->post('id');
        $colums_edit = $data_type['colums_edit'];
        $dataUpdate = [];
        foreach($colums_edit as $key => $value) {
            if($key != 'id') {
                $dataUpdate[$key] = NULL;
            }
        }

        if(!empty($dataUpdate) && !empty($id)) {
            $this->db->where('id', $id);
            $success = $this->db->update($data_type['table'], $dataUpdate);
        }

        if(!empty($success)) {
            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa thành công']);die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa không thành công']);die();
    }


    /*Quản lý nhanh các danh mục có các trường cập nhật dữ liệu*/
    public function list_muti($type = '') {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data_list_muti = !empty($this->list_muti[$type]) ? $this->list_muti[$type] : [];
        if(empty($data_list_muti)) {
            show_404();
        }
        if(!empty($data_list_muti)) {
            $data['title'] = $data_list_muti['name'];
            $data['type'] = $type;
//            $data['name_colums'] = $data_list_muti['colums_th'];
//            $data['list_table'] = $data_list_muti['data']['table'];
//            $data['name_table'] = $data_list_muti['name_table'];
            $data['list_data'] = $data_list_muti['data'];
            $data['default'] = $data_list_muti['default'];
        }
//        print_arrays($data['list_data']);
        $this->load->view('admin/list_other/list_muti/manage', $data);
    }

    /*Thêm sửa xóa nhanh các danh mục có các trường cập nhật dữ liệu*/
    public function detail_muti($type = '', $filterType = '', $id = '') {
        $list_type_data = $this->list_muti[$type];
        $data_type = $list_type_data['data'][$filterType];
        if($this->input->post()) {
            $data = [];
            $colums_edit = $data_type['colums_edit'];
            foreach($colums_edit as $key => $value) {
                $data[$key] = $this->input->post($key);
            }
            $_id = $id;
            if(!empty($data[$data_type['columKey']]) && empty($id)) {
                $_id = $data[$data_type['columKey']];
            }
            if(!empty($_id)) {
                unset($data[$data_type['columKey']]);
                $this->db->where($data_type['columKey'], $_id);
                $success = $this->db->update($data_type['table'], $data);
            }
            if(!empty($success)) {
                if(empty($id)) {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Thêm dữ liệu thành công']);
                    die();
                }
                else {
                    echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Cập nhật dữ liệu thành công']);
                    die();
                }
            }
            else {
                if(empty($id)) {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);
                    die();
                }
                else {
                    echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Thêm dữ liệu không thành công']);
                    die();
                }
            }
        }
        else {
            if (empty($id)){
                if (!$this->preAdd){
                    accessDenied(true);
                }
                $data['title'] = 'Thêm ' . $list_type_data['name'];
            }
            else {
                if (!$this->preEdit){
                    accessDenied(true);
                }
                $data['list_muti'] = $this->db->get_where($data_type['table'], [$data_type['columKey'] => $id])->row();
                $data['title'] = 'Sửa ' . $list_type_data['name'] . ' <br/>(' . ($data['list_muti']->{$data_type['columView']}).')';
                $data['columKey'] = $data_type['columKey'];
                unset($data_type['colums_edit'][$data_type['columKey']]);
            }
        }

        $data['type'] = $type;
        $data['filterType'] = $filterType;
        $data['name_colums'] = $data_type['colums'];
        $data['colums_edit'] = $data_type['colums_edit'];
        $this->load->view('admin/list_other/list_muti/detail', $data);
    }

    public function table_muti($type = '') {
        $data_type = $this->list_muti[$type]['data'];

        $filterType = $this->input->post('filterType');

        $sTable = $data_type[$filterType]['table'];
        $colums = $data_type[$filterType]['colums'];
        $KWhere = !empty($data_type[$filterType]['where']) ? $data_type[$filterType]['where'] : [];
        $KJoin = !empty($data_type[$filterType]['join']) ? $data_type[$filterType]['join'] : [];
        $columKey = $data_type[$filterType]['columKey'];


        $aColumns = [];

        foreach ($colums as $k => $v) {
            $aColumns[] = $k;
        }

        $sWhere = [];
        if(!empty($KWhere)){
            foreach($KWhere as $key => $value) {
                $sWhere[] = 'AND '. $value;
            }
        }
        $join = [];
        if(!empty($KJoin)) {
            foreach($KJoin as $keyTable => $valueWhere) {
                $join[] = 'LEFT JOIN '.$keyTable.' ON ' . $valueWhere;
            }
        }

        $sIndexColumn = $columKey;
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, []);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        foreach ($rResult as $aRow) {
            $row = [];
            foreach($colums as $k => $v) {
                $row[] = '<div class="'.(@$data_type[$filterType]['colums_edit'][$k]['class']).'">' . $aRow[$k] . '</div>';
            }
            $options = '';
            $options .= '<a class="btn btn-icon btn-default c_modal" href="'.(admin_url('list_other/detail_muti/' . $type . '/' . $filterType . '/' . $aRow[$columKey])).'" ><i class="fa fa-edit"></i></a>';
            $options .= '<a class="btn btn-icon btn-danger c_delete" href="'.(admin_url('list_other/remove_muti/' . $type . '/' . $filterType)).'" data-id="'.$aRow[$columKey].'" ><i class="fa fa-remove"></i></a>';
            $row[] = $options;
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    //làm rỗng trường cần cập nhật
    public function remove_muti($type = '', $filterType = '')
    {
        $data_type = $this->list_muti[$type]['data'][$filterType];
        $id = $this->input->post('id');
        $colums_edit = $data_type['colums_edit'];
        $dataUpdate = [];
        foreach($colums_edit as $key => $value) {
            if($key != $data_type['columKey']) {
                $dataUpdate[$key] = NULL;
            }
        }

        if(!empty($dataUpdate) && !empty($id)) {
            $this->db->where($data_type['columKey'], $id);
            $success = $this->db->update($data_type['table'], $dataUpdate);
        }

        if(!empty($success)) {
            echo json_encode(['success' => true, 'alert_type' => 'success', 'message' => 'Xóa thành công']);die();
        }
        echo json_encode(['success' => false, 'alert_type' => 'danger', 'message' => 'Xóa không thành công']);die();
    }

    //COMBOBOX Thành phẩm có tên khách hàng
    function searchProductsSelect2($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->searchProductsClientSelect2($term, $limit);
        if ($id) {
            $product = $this->products_model->rowProduct($id);
            $data['row'] = ['id' => $product['id'], 'text' => $product['code']];
        }
        echo json_encode($data);
    }


    public function searchProductsClientSelect2($q, $limit = 50)
    {
        $this->db->select('
            CONCAT(tbl_products.id, "__products") as id, 
            CONCAT(COALESCE(tbl_products.name, "")) as text, 
            tbl_products.name as item_name, 
            IF(tbl_products.images IS NOT NULL && tbl_products.images != "", CONCAT("uploads/products/", "", tbl_products.images, ""), "") as images, 
            tblunits.unit as unit_name, 
            tbl_products.price_sell as price_sell, 
            tbl_products.info as info, 
            COALESCE(tbl_products.code, "") as code, 
            tbl_products.code as item_code, 
            CONCAT(tbl_products.category_id, "__products") as category_id,
            tblsize.name as size_name,
            tbl_products.loss as loss,
            tbl_products.quantity_child_sheet as quantity_child_sheet,
            tbl_products.quantity_sheet_bale as quantity_sheet_bale,
            tbl_products.mode_product as mode_product,
            tbl_products.product_name_customer as name_customer,
            tbl_products.height as height,
            tbl_products.wide as wide,
            tbl_products.wide as wide,
            tbl_products.packing as packing,
            tbl_products.quantity_max as quantity_max,
            tbl_products.time_inventory as time_inventory,
            tbl_products.quota_time_change_one as quota_time_change_one,
            unit_stock.unit as unit_stock,
            tbl_species.name as specie_name,
            tbl_category_products.name as category_name,
            tb_unit_measure.unit as unit_measure,
            tbl_brand.code as brand_code,
            COALESCE(tblclients.company, "") as company,
        ', false);
        $this->db->from('tbl_products');
        $this->db->join('tblunits', 'tbl_products.unit_id = tblunits.unitid', 'left');
        $this->db->join('tblunits unit_stock', 'tbl_products.conversion_unit = unit_stock.unitid', 'left');
        $this->db->join('tblunits tb_unit_measure', 'tbl_products.unit_measure = tb_unit_measure.unitid', 'left');
        $this->db->join('tblsize', 'tblsize.id = tbl_products.size', 'left');
        $this->db->join('tbl_species', 'tbl_species.id = tbl_products.species', 'left');
        $this->db->join('tbl_category_products', 'tbl_category_products.id = tbl_products.category_id', 'left');
        $this->db->join('tbl_brand', 'tbl_brand.id = tbl_products.brand_id', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbl_products.customer', 'left');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_products.code', $q);
            $this->db->or_like('tbl_products.name', $q);
            $this->db->group_end();
        }
        $this->db->where('type_products !=', 'semi_products_outside');
        $this->db->where('tbl_products.status', 1);
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    //COMBOBOX danh sách thiết bị
    function searchSuppliersSelect2($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $this->db->select('
                tblsuppliers.id as id, 
                CONCAT(COALESCE(tblsuppliers.code, ""), " (", tblsuppliers.company, ")") as text, 
                tblsuppliers.company as company, 
                tblsuppliers.code as code', false);
        $this->db->from('tblsuppliers');
        if(!empty($term)) {
            $this->db->group_start();
            $this->db->like('code', $term);
            $this->db->or_like('company', $term);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();
        if ($id) {
            $this->db->select('
                tblsuppliers.id as id, 
                CONCAT(COALESCE(tblsuppliers.code, ""), " (", tblsuppliers.company, ")") as text, 
                tblsuppliers.company as company, 
                tblsuppliers.code as code', false);
            $this->db->from('tblsuppliers');
            $this->db->where('id', $id);
            $this->db->limit($limit);
            $suppliers = $this->db->get()->row_array();

            $data['row'] = ['id' => $suppliers['id'], 'text' => $suppliers['text']];
        }
        echo json_encode($data);return;
    }

    //COMBOBOX danh sách thiết bị
    function searchMachinesSelect2($id = false)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $data['results'] = $this->modelMachinesSelect2($term, $limit);
        if ($id) {
            $this->db->select('
            tbl_machines.id as id, 
            CONCAT(COALESCE(tbl_machines.code, ""), " (", tbl_machines.name,")") as text, 
            tbl_machines.name as name, 
            tbl_machines.code as code, 
        ', false);
            $this->db->from('tbl_machines');
            $this->db->where('id', $id);
            $this->db->limit($limit);
            $machines = $this->db->get()->row_array();

            $data['row'] = ['id' => $machines['id'], 'text' => $machines['text']];
        }
        echo json_encode($data);return;
    }

    public function modelMachinesSelect2($q, $limit = 50)
    {
        $this->db->select('
            tbl_machines.id as id, 
            CONCAT(COALESCE(tbl_machines.code, ""), " (", tbl_machines.name,")") as text, 
            tbl_machines.name as name, 
            tbl_machines.code as code, 
        ', false);
        $this->db->from('tbl_machines');
        if (!empty($q))
        {
            $this->db->group_start();
            $this->db->like('tbl_machines.code', $q);
            $this->db->or_like('tbl_machines.name', $q);
            $this->db->group_end();
        }
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }


}