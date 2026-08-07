<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quota_info extends AdminController
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
    }

    /*Quản lý nhanh các tiêu chuẩn*/
    public function stage() {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data['title'] = 'Danh Sách Định Mức Tiêu Hao Công Đoạn';
        $this->load->view('admin/quota_info/stage/manage', $data);
    }

    public function table_stage()
    {
        $aColumns = [
            'tbl_stages.id as id',
            'tbl_stages.code as code_stages',
            'tbl_stages.name as name_stages',
            'tbl_materials.code as code',
            'tbl_materials.name as name',
//            'tbl_product_versions.versions as versions',
//            'tbl_products.name as name_product',
            'tbl_element_items.quantity_compensation as quantity_compensation',
        ];
        $sWhere = [];
        $join = [
            'LEFT JOIN tbl_element_items ON tbl_element_items.stage_id = tbl_stages.id',
            'LEFT JOIN tbl_materials ON tbl_materials.id = tbl_element_items.item_id AND tbl_element_items.type = "materials"',
            'JOIN tbl_versions_element ON tbl_versions_element.id = tbl_element_items.element_id',
            'LEFT JOIN tbl_product_versions ON tbl_product_versions.id = tbl_versions_element.version_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_product_versions.product_id',
        ];
//        $sWhere[] = 'AND tbl_stages.type_use = 0';
//        $sWhere[] = 'AND tbl_stages.quota_npl_consumption_one > 0';
        if($this->input->post('materials_search')) {
            $sWhere[] = 'AND tbl_materials.id = "'.$this->input->post('materials_search').'"';
        }
        if($this->input->post('stage_search')) {
            $sWhere[] = 'AND tbl_stages.id = "'.$this->input->post('stage_search').'"';
        }
        $sIndexColumn = 'id';
        $sTable       = 'tbl_stages';
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, [
            'tbl_stages.quota_npl_consumption_one',
            'tbl_element_items.stage_id as stage_isset',
            'tbl_product_versions.versions as versions',
            'tbl_versions_element.element_name as element_name',
            'tbl_products.code as code_product',
        ]);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        $id_view = '';
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            if($id_view != $aRow['id']) {
                $id_view = $aRow['id'];
                $row['DT_RowClass'] = 'bg-title';
                $row[] = '';
                $row[] = '<div style="white-space: nowrap;">' . $aRow['code_stages'] . '</div>';
                $row[] = $aRow['name_stages'] ;
                $row[] = '';
                $row[] = '';
                $row[] = '<div class="text-center">' . $aRow['quota_npl_consumption_one'] . '</div>';
                $output['aaData'][] = $row;
                $row = [];
            }
            if(empty($aRow['stage_isset'])) {
                continue;
            }
            $row = [];
            $row[] = ($key + 1);
            $row[] = '';
            $nameElement = '<div><span class="label label-default" style="border:1px solid "><b>BOM:</b> ' . $aRow['versions'] . '</span></div>';
            $nameElement .= '<div class="mtop10"><span class="label label-default" style="border:1px solid "><b>Thành Phần:</b> ' . $aRow['element_name'] . '</span></div>';
            $nameElement .= '<div class="mtop10"><span class="label label-default" style="border:1px solid "><b>TP:</b> ' . $aRow['code_product'] . '</span></div>';
            $row[] = $nameElement;
            $row[] = $aRow['code'];
            $row[] = $aRow['name'];
            $row[] = '<div class="text-center">' . $aRow['quantity_compensation'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

    public function use_bom() {
        if (!$this->preView && !$this->preViewOwn) {
            access_denied();
        }
        $data['title'] = 'Danh Sách Định Mức NPL Sử Dụng BOM';
        $this->load->view('admin/quota_info/use_bom/manage', $data);
    }

    public function table_use_bom()
    {
        $aColumns = [
            'tbl_materials.id as id',
            'tbl_materials.code as code',
            'tbl_materials.name as name',
//            'tbl_products.name as name_product',
            'tbl_stages.code as code_stages',
            'tbl_stages.name as name_stages',
            'tbl_element_items.quantity_compensation as quantity_compensation',
        ];
        $sWhere = [];
        $join = [
            'LEFT JOIN tbl_element_items ON tbl_element_items.item_id = tbl_materials.id AND tbl_element_items.type = "materials"',
            'LEFT JOIN tbl_versions_element ON tbl_versions_element.id = tbl_element_items.element_id',
            'LEFT JOIN tbl_stages ON tbl_stages.id = tbl_element_items.stage_id',
            'LEFT JOIN tbl_product_versions ON tbl_product_versions.id = tbl_versions_element.version_id',
            'LEFT JOIN tbl_products ON tbl_products.id = tbl_product_versions.product_id',
        ];

//        $sWhere[] = 'AND tbl_stages.type_use = 0';
        $sWhere[] = 'AND tbl_element_items.stage_id != 0';
        if($this->input->post('materials_search')) {
            $sWhere[] = 'AND tbl_materials.id = "'.$this->input->post('materials_search').'"';
        }
        $sIndexColumn = 'id';
        $sTable       = 'tbl_materials';
        $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $sWhere, [
            'tbl_product_versions.versions as versions',
            'tbl_versions_element.element_name as element_name',
            'tbl_stages.quota_npl_consumption_one',
            'tbl_products.code as code_product',
            'tbl_product_versions.id as id_bom'
        ]);
        $output       = $result['output'];
        $rResult      = $result['rResult'];
        $id_view = '';
        foreach ($rResult as $key =>  $aRow) {
            $row = [];
            if($id_view != $aRow['id']) {
                $id_view = $aRow['id'];
                $row['DT_RowClass'] = 'bg-title';
                $row[] = '';
                $row[] = '<div style="white-space: nowrap;">' . $aRow['code'] . '</div>';
                $row[] = $aRow['name'];
                $row[] = '';
                $row[] = '';
                $row[] = '<div class="text-center">' . $aRow['quota_npl_consumption_one'] . '</div>';
//                $row[] = '';
                $output['aaData'][] = $row;
                $row = [];

            }
            $row[] = ($key + 1);
            $row[] = '';
            $nameElement = '<div><span class="label label-default" style="border:1px solid "><b>BOM:</b> ' . $aRow['versions'] . '</span></div>';
            $nameElement .= '<div class="mtop10"><span class="label label-default" style="border:1px solid "><b>Thành Phần:</b> ' . $aRow['element_name'] . '</span></div>';
            $nameElement .= '<div class="mtop10"><span class="label label-default" style="border:1px solid "><b>TP:</b> ' . $aRow['code_product'] . '</span></div>';
            $row[] = $nameElement;
            $row[] = $aRow['code_stages'];
            $row[] = $aRow['name_stages'];
            $row[] = '<div class="text-center">' . $aRow['quantity_compensation'] . '</div>';
            $output['aaData'][] = $row;
        }
        echo json_encode($output);die();
    }

}