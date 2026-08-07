<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Compose extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('compose_model');
    }

    public function index()
    {
        if (!has_permission('compose', '', 'view') && !has_permission('compose', '', 'view_own')) {
            access_denied('compose');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('compose');
        }
        $data['title'] = _l('ch_compose');
        $this->load->view('admin/compose/manage', $data);
    }
    public function table()
    {
        $this->app->get_table_data('compose');
    }
    public function delete($id)
    {
        if (!has_permission('compose', '', 'delete')) {
            $data['alert_type'] = 'danger';
            $data['message'] = lang('Bạn không có quyển xóa');
            echo json_encode($data);
            die;
        }
        if (!$id) {
            $data['alert_type'] = 'danger';
            $data['message'] = lang('Không tìm thấy phiếu');
            echo json_encode($data);
            die;
        }
        $success    = $this->compose_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('ch_no_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('ch_delete');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
    public function detail($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {

                if (!has_permission('compose', '', 'create')) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bạn không có quyển thêm');
                    echo json_encode($data);
                    die;
                }

                $_data = $this->input->post();
                if (isset($_data['items']) && count($_data['items']) > 0) {
                    $errors = '';
                    foreach ($_data['items'] as $key => $value) {
                        $ktr = get_table_where('tblcompose_detail', array('code' => $value['code'], 'style_number' => $value['style_number'], 'color_name' => $value['color_name'], 'primary_size' => $value['primary_size'], 'upc' => $value['upc']), '', 'row');
                        if (!empty($ktr)) {
                            $errors .= '<div>PO [' . $value['code'] . '], Style Number [' . $value['style_number'] . ', COLOR NAME [' . $value['color_name'] . ', PRIMARY SIZE [' . $value['primary_size'] . ', UPC/EAN CODE [' . $value['upc'] . ']. đã tồn tại trên phần mềm</div><br>';
                        }
                    }
                    if (!empty($errors)) {
                        $data['result'] = 0;
                        $data['message'] = $errors;
                        echo json_encode($data);
                        die;
                    }
                    $id = $this->compose_model->add($_data);
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('Không có mặt hàng để lưu');
                    echo json_encode($data);
                    die;
                }

                if ($id) {
                    $data['result'] = 1;
                    $data['message'] = lang('ch_added_successfuly');
                    echo json_encode($data);
                    die;
                }
            } else {
                if (!has_permission('compose', '', 'edit')) {
                    $data['result'] = 0;
                    $data['message'] = lang('Bạn không có quyển sửa');
                    echo json_encode($data);
                    die;
                }
                $errors = '';
                $_data = $this->input->post();
                foreach ($_data['items'] as $key => $value) {
                    $ktr = get_table_where('tblcompose_detail', array('code' => $value['code'], 'style_number' => $value['style_number'], 'color_name' => $value['color_name'], 'primary_size' => $value['primary_size'], 'upc' => $value['upc'], 'id_compose !=' => $id), '', 'row');
                    if (!empty($ktr)) {
                        $errors .= '<div>PO [' . $value['code'] . '], Style Number [' . $value['style_number'] . ', COLOR NAME [' . $value['color_name'] . ', PRIMARY SIZE [' . $value['primary_size'] . ', UPC/EAN CODE [' . $value['upc'] . ']. đã tồn tại trên phần mềm</div><br>';
                    }
                }
                if (!empty($errors)) {
                    $data['result'] = 0;
                    $data['message'] = $errors;
                    echo json_encode($data);
                    die;
                }
                $success = $this->compose_model->update($this->input->post(), $id);
                if ($success == true) {
                    // set_alert('success', _l('ch_updated_successfuly'));
                    $data['result'] = 1;
                    $data['message'] = lang('ch_updated_successfuly');
                    echo json_encode($data);
                    die;
                }
            }
        }
        if ($id != '') {
            if (!has_permission('compose', '', 'edit')) {
                access_denied('compose');
            }
            $data['title'] = _l('ch_edit_compose');
            $data['main'] = get_table_where('tblcompose', array('id' => $id), '', 'row');
            $data['main']->items = get_table_where('tblcompose_detail', array('id_compose' => $id));
            $data['id'] = $id;
        } else {
            if (!has_permission('compose', '', 'create')) {
                ajax_access_denied();
            }
            $data['id'] = '';
            $data['title'] = _l('ch_add_compose');
        }

        $this->load->view('admin/compose/detail', $data);
    }
    public function import_items()
    {
        ob_end_clean();
        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->load->helper('security');
        $data = $this->input->post();
        $row_start = $data['row_start'];
        $row_end = $data['row_end'];
        $code_po = '';
        if (!empty($_FILES['file_csv_po'])) {
            $fullfile = $_FILES['file_csv_po']['tmp_name'];
            $extension = strtoupper(pathinfo($_FILES['file_csv_po']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS' && $extension != 'CSV') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }

            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $code_po = $objWorksheet->getCellByColumnAndRow(0, 4)->getValue();
        }
        $code_mo = '';
        if (!empty($_FILES['file_mo'])) {
            $fullfile = $_FILES['file_mo']['tmp_name'];
            $extension = strtoupper(pathinfo($_FILES['file_mo']['name'], PATHINFO_EXTENSION));
            if ($extension != 'XLSX' && $extension != 'XLS' && $extension != 'CSV') {
                echo json_encode(['success' => false, 'alert_type' => 'success', 'message' => _l('cong_not_type')]);
                die();
            }

            $inputFileType = PHPExcel_IOFactory::identify($fullfile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load("$fullfile");
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $row_start = !empty($row_start) ? $row_start : 1; // read start
            $row_end = !empty($row_end) ? $row_end : $highestRow; // read end
            $code_mo = $objWorksheet->getCellByColumnAndRow(2, 5)->getValue();
            $code_mo_1 = '';
            $code_mo_2 = '';
            if (!empty($code_mo)) {
                $code_mo = explode(' / ', $code_mo);
                if (!empty($code_mo[0])) {
                    $code_mo_1 = $code_mo[0];
                }
                if (!empty($code_mo[1])) {
                    $code_mo_2 = $code_mo[1];
                }
            }
            $list_data = [];
            $dem = 0;
            for ($row = $row_start; $row <= $row_end; ++$row) {
                $list_data[$row]['code'] = $code_mo_1 . '/' . $code_po . '/' . $code_mo_2;
                $list_data[$row]['quantity'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                $list_data[$row]['style_number'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $list_data[$row]['primary_size'] = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $list_data[$row]['color_name'] =  $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                $list_data[$row]['upc'] = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                $list_data[$row]['loss'] = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
                $dem++;
            }
            $i = 0;
            $list_data_orr = [];
            $check_in = array();
            foreach ($list_data as $key => $value) {
                $code_check = $value['code'] . $value['style_number'] . $value['color_name'] . $value['primary_size'] . $value['upc'];
                if (in_array($code_check, $check_in)) {
                    $list_data_orr[$i] = $value;
                    $list_data_orr[$i]['title'] = 'Đã tồn tại mặt hàng';
                    $list_data_orr[$i]['count'] = $key;
                    $i++;
                    unset($list_data[$key]);
                    continue;
                }
                $check_in[] = $code_check;
            }
        }
        echo json_encode([
            'success' => true,
            'alert_type' => 'success',
            'list_data' => $list_data,
            'list_data_orr' => $list_data_orr
        ]);
        die();
    }
    public function update_compose()
    {
        if (!has_permission('compose', '', 'edit')) {
            echo json_encode(array(
                'data' => '',
                'alert_type' => 'danger',
                'message' => 'Bạn không có quyền sửa'
            ));die;
        }
        $alert_type = 'danger';
        $message    = _l('Sửa thông thành công');
        $_data = $this->input->post();
        if (!empty($_data)) {
            if (!empty($_data['id']) && !empty($_data['name'])) {
                $up = array();
                $up[$_data['name']] = $_data['data'];
                $success = $this->db->update('tblcompose_detail', $up, array('id' => $_data['id']));
            }
            if ($success) {
                $alert_type = 'success';
                $message    = _l('Sửa thành công');
            }
        }

        echo json_encode(array(
            'data' => $_data['data'],
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }
}
