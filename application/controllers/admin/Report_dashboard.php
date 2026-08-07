<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Report_dashboard extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();

        $this->perViewDashboardQuotes = has_permission('dashboard_quotes', '', 'view');
        $this->perViewDashboardRevenue = has_permission('dashboard_revenue', '', 'view');
        $this->perViewDashboardCost = has_permission('dashboard_cost', '', 'view');
        $this->perViewDashboardStock = has_permission('dashboard_stock', '', 'view');
        $this->perViewDashboardManufactures = has_permission('dashboard_manufactures', '', 'view');
        $this->perViewDashboardTask = has_permission('dashboard_task', '', 'view');
        $this->perViewDashboardPersonnel = has_permission('dashboard_personnel', '', 'view');
        $this->perViewDashboardPurchases = has_permission('dashboard_purchases', '', 'view');
        $this->perViewDashboardBusinessResults = has_permission('dashboard_business_results', '', 'view');

        if (!$this->perViewDashboardRevenue && !$this->perViewDashboardCost && !$this->perViewDashboardStock && !$this->perViewDashboardManufactures && !$this->perViewDashboardTask && !$this->perViewDashboardPersonnel && !$this->perViewDashboardPurchases && !$this->perViewDashboardQuotes && !$this->perViewDashboardBusinessResults) {
            accessDenied();
        }

        $this->menu = [];

        if ($this->perViewDashboardQuotes) {
            $this->menu[] = [
                'id' => 1,
                'name' => 'DASHBOARD BÁO GIÁ PHÁT TRIỂN MẪU',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/doanh-thu.png'),
                'link' => admin_url('report_dashboard/dashboard_quotes'),
            ];
        }
        
        if ($this->perViewDashboardRevenue) {
            $this->menu[] = [
                'id' => 2,
                // 'name' => 'DASHBOARD DOANH THU - TRẢ LẠI HÀNG BÁN',
                'name' => 'DASHBOARD DOANH THU',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/doanh-thu.png'),
                'link' => admin_url('report_dashboard/dashboard_revenue'),
            ];
        }

        if ($this->perViewDashboardCost) {
            $this->menu[] = [
                'id' => 3,
                'name' => 'DASHBOARD CHI PHÍ',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/chi-phi.png'),
                'link' => 'report_dashboard/dashboard_cost',
            ];
        }

        if ($this->perViewDashboardStock) {
            $this->menu[] = [
                'id' => 4,
                'name' => 'DASHBOARD TỒN KHO',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/ton-kho.png'),
                'link' => admin_url('report_dashboard/dashboard_stock'),
            ];
        }

        if ($this->perViewDashboardManufactures) {
            $this->menu[] = [
                'id' => 5,
                'name' => 'DASHBOARD SẢN XUẤT',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/san-xuat.png'),
                'link' => admin_url('report_dashboard/dashboard_manufactures'),
            ];
        }

        if ($this->perViewDashboardTask) {
            $this->menu[] = [
                'id' => 6,
                'name' => 'DASHBOARD CÔNG VIỆC',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/cong-viec.png'),
                'link' => admin_url('report_dashboard/dashboard_task'),
            ];
        }

        if ($this->perViewDashboardPersonnel) {
            $this->menu[] = [
                'id' => 7,
                'name' => 'DASHBOARD HÀNH CHÁNH - NHÂN SỰ',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/hanh-chinh.png'),
                'link' => admin_url('report_dashboard/dashboard_personnel'),
            ];
        }

        if ($this->perViewDashboardPurchases) {
            $this->menu[] = [
                'id' => 8,
                'name' => 'DASHBOARD MUA HÀNG',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/hanh-chinh.png'),
                'link' => admin_url('report_dashboard/dashboard_purchases'),
            ];
        }

        if ($this->perViewDashboardBusinessResults) {
            $this->menu[] = [
                'id' => 9,
                'name' => 'DASHBOARD KẾT QUẢ KINH DOANH',
                'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/doanh-thu.png'),
                'link' => admin_url('report_dashboard/dashboard_business_results'),
            ];
        }

        // $this->menu = [
        //     [
        //         'id' => 1,
        //         'name' => 'DASHBOOD DOANH THU',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/doanh-thu.png'),
        //         'link' => admin_url('report_dashboard/dashboard_revenue'),
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'DASHBOOD CHI PHÍ',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/chi-phi.png'),
        //         'link' => 'report_dashboard/dashboard_cost',
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'DASHBOOD TỒN KHO',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/ton-kho.png'),
        //         'link' => admin_url('report_dashboard/dashboard_stock'),
        //     ],
        //     [
        //         'id' => 4,
        //         'name' => 'DASHBOOD SẢN XUẤT',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/san-xuat.png'),
        //         'link' => admin_url('report_dashboard/dashboard_manufactures'),
        //     ],
        //     [
        //         'id' => 5,
        //         'name' => 'DASHBOOD CÔNG VIỆC',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/cong-viec.png'),
        //         'link' => admin_url('report_dashboard/dashboard_task'),
        //     ],
        //     [
        //         'id' => 6,
        //         'name' => 'DASHBOOD HÀNH CHÁNH - NHÂN SỰ',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/hanh-chinh.png'),
        //         'link' => admin_url('report_dashboard/dashboard_personnel'),
        //     ],
        //     [
        //         'id' => 7,
        //         'name' => 'DASHBOOD MUA HÀNG',
        //         'logo' => base_url('download/preview_image?path=uploads/icon_dashboard/hanh-chinh.png'),
        //         'link' => admin_url('report_dashboard/dashboard_purchases'),
        //     ]
        // ];
        
    }

    public function index()
    {
        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/index', $data);
    }
    public function dashboard_purchases()
    {
        if (!$this->perViewDashboardPurchases) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_purchases');
    }
    public function dashboard_stock()
    {
        if (!$this->perViewDashboardStock) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_stock');
    }
    public function dashboard_cost()
    {
        if (!$this->perViewDashboardCost) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_cost');
    }
    public function dashboard_revenue()
    {
        if (!$this->perViewDashboardRevenue) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_revenue');
    }

    public function dashboard_manufactures()
    {
        if (!$this->perViewDashboardManufactures) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_manufactures');
    }

    public function dashboard_personnel()
    {
        if (!$this->perViewDashboardPersonnel) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_personnel');
    }

    public function dashboard_task()
    {
        if (!$this->perViewDashboardTask) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_task');
    }

    public function dashboard_quotes()
    {
        if (!$this->perViewDashboardQuotes) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_quotes');
    }

    public function dashboard_business_results()
    {
        if (!$this->perViewDashboardBusinessResults) {
            accessDenied();
        }

        $data['menu'] = $this->menu;
        $this->load->view('admin/report_dashboard/dashboard_business_results');
    }
}
