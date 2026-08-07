<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Departments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('departments_model');

        if (!is_admin()) {
            access_denied('Departments');
        }
    }

    /* List all departments */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('departments');
        }
        $data['email_exist_as_staff'] = $this->email_exist_as_staff();
        $data['title']                = _l('Bộ phận');
        $this->load->view('admin/departments/manage', $data);
    }

    /* Edit or add new department */
    public function department($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

			$data['code'] = !empty($data['code']) ? $data['code'] : NULL;

            if (isset($data['fakeusernameremembered']) || isset($data['fakepasswordremembered'])) {
                unset($data['fakeusernameremembered']);
                unset($data['fakepasswordremembered']);
            }

            if (!$this->input->post('id')) {
                $id = $this->departments_model->add($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('department'));
                }
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                    'email_exist_as_staff' => $this->email_exist_as_staff(),
                ]);
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->departments_model->update($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('department'));
                }
                echo json_encode([
                    'success'              => $success,
                    'message'              => $message,
                    'email_exist_as_staff' => $this->email_exist_as_staff(),
                ]);
            }
            die;
        }
    }

    /* Delete department from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('departments'));
        }
        $response = $this->departments_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('department_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('department')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('department_lowercase')));
        }
        redirect(admin_url('departments'));
    }

    public function email_exists()
    {
        // First we need to check if the email is the same
        $departmentid = $this->input->post('departmentid');
        if ($departmentid) {
            $this->db->where('departmentid', $departmentid);
            $_current_email = $this->db->get(db_prefix().'departments')->row();
            if ($_current_email->email == $this->input->post('email')) {
                echo json_encode(true);
                die();
            }
        }
        $exists = total_rows(db_prefix().'departments', [
            'email' => $this->input->post('email'),
        ]);
        if ($exists > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function test_imap_connection()
    {
        app_check_imap_open_function();

        $email         = $this->input->post('email');
        $password      = $this->input->post('password', false);
        $host          = $this->input->post('host');
        $imap_username = $this->input->post('username');
        if ($this->input->post('encryption')) {
            $encryption = $this->input->post('encryption');
        } else {
            $encryption = '';
        }

        require_once(APPPATH . 'third_party/php-imap/Imap.php');

        $mailbox = $host;

        if ($imap_username != '') {
            $username = $imap_username;
        } else {
            $username = $email;
        }

        $password   = $password;
        $encryption = $encryption;
        // open connection
        $imap = new Imap($mailbox, $username, $password, $encryption);
        if ($imap->isConnected() === true) {
            echo json_encode([
                'alert_type' => 'success',
                'message'    => _l('lead_email_connection_ok'),
            ]);
        } else {
            echo json_encode([
                'alert_type' => 'warning',
                'message'    => $imap->getError(),
            ]);
        }
    }

    private function email_exist_as_staff()
    {
        return total_rows(db_prefix().'departments', 'email IN (SELECT email FROM '.db_prefix().'staff)') > 0;
    }

	public function code_exists() {
		if ($this->input->is_ajax_request() && $this->input->post('code') != '') {
			if ($this->input->post()) {
				$id = $this->input->post('departmentid');
				if ($id != '') {
					$this->db->where('departmentid', $id);
					$_current_email = $this->db->get('tbldepartments')->row();
					if ($_current_email->code == $this->input->post('code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('code', $this->input->post('code'));
				$total_rows = $this->db->count_all_results('tbldepartments');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

    public function print_pdf_html()
    {
        ob_start();
        $data = [];
        $ids = $this->input->get('ids');
        $arrId = explode(',', $ids);
        $title = lang('IN QR BỘ PHẬN');
        $items = null;
        if (!empty($arrId)) {
            $this->db->select("
                tbldepartments.*,
                tbldepartments.departmentid as id
            ");
            $this->db->from('tbldepartments');
            $this->db->where_in('tbldepartments.departmentid', $arrId);
            $items = $this->db->get()->result_array();
        }
        $data['items'] = $items;

        $content = ob_get_contents();

        $data['object'] = "departments";
        $data['hide'] = 'hide';
        $data['title'] = $title;
        $data['content'] = $content;
        ob_end_clean();
        $pdf = print_pdf_qr_dtmv($data);
        $type = 'I';
        if ($type == "S") {
            return $pdf->Output(slug_it('qr') . '.pdf', $type);
        } else {
            $pdf->Output(slug_it('qr') . '.pdf', $type);
        }
    }
}
