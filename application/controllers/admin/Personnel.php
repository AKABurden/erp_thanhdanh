<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Personnel extends AdminController
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('personnel_model');
        $this->load->model('category_model');
        $this->tnh = true;
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
        $this->upload_path = 'uploads/personnel/';
    }

    public function index()
    {
        $data['title'] = lang('tnh_hr');
        $data['all'] = $this->personnel_model->countPersonnel('all');
        $this->load->view('admin/personnel/personnel', $data);
    }

    public function add_personnel()
    {
        if ($this->input->post('add'))
        {
            $data = [];
            $this->form_validation->set_rules('fullname', lang("tnh_fullname"), 'required');
            $this->form_validation->set_rules('birthday', lang("tnh_birthday"), 'required');
            $this->form_validation->set_rules('gender', lang("tnh_gender"), 'required');
            $this->form_validation->set_rules('telephone', lang("tnh_telephone"), 'required');
            $this->form_validation->set_rules('email', lang("Email"), 'required');
            if ($this->form_validation->run() == true)
            {
                $fullname = $this->input->post('fullname');
                $birthday = to_sql_date($this->input->post('birthday'));
                $gender = $this->input->post('gender');
                $birthplace = $this->input->post('birthplace');
                $domicile = $this->input->post('domicile');
                $cmnd_id_passport = $this->input->post('cmnd_id_passport');
                $date_range = $this->input->post('date_range') ? to_sql_date($this->input->post('date_range')) : null;
                $issued_by = $this->input->post('issued_by');
                $marital_status = $this->input->post('marital_status');
                $nationality = $this->input->post('nationality');
                $nation = $this->input->post('nation');
                $account_name = $this->input->post('account_name');
                $bank = $this->input->post('bank');
                $branch = $this->input->post('branch');
                $personal_tax_code = $this->input->post('personal_tax_code');
                $note = $this->input->post('note');
                $telephone = $this->input->post('telephone');
                $email = $this->input->post('email');
                $skype = $this->input->post('skype');
                $facebook = $this->input->post('facebook');
                $resident = $this->input->post('resident');
                $current_accommodation = $this->input->post('current_accommodation');
                $religion = $this->input->post('religion');

                //family
                $counterFamily = $this->input->post('counterFamily');
                $arrFamily = [];
                $countFamily = 0;
                if (!empty($counterFamily)) {
                    foreach ($counterFamily as $key => $value) {
                        $relationship_family = $this->input->post('relationship_family')[$value];
                        $fullname_family = $this->input->post('fullname_family')[$value];
                        $year_birthday_family = $this->input->post('year_birthday_family')[$value];
                        $career_family = $this->input->post('career_family')[$value];
                        $address_family = $this->input->post('address_family')[$value];
                        $telephone_family = $this->input->post('telephone_family')[$value];

                        if (empty($relationship_family) || empty($fullname_family) || empty($telephone_family))
                        {
                            continue;
                        }

                        $arrFamily[] = [
                            'relationship_family' => $relationship_family,
                            'fullname_family' => $fullname_family,
                            'year_birthday_family' => $year_birthday_family,
                            'career_family' => $career_family,
                            'address_family' => $address_family,
                            'telephone_family' => $telephone_family,
                        ];
                        $countFamily++;
                    }
                }
                //end family

                //literacy
                $counterLiteracy = $this->input->post('counterLiteracy');
                $arrLiteracy = [];
                $countLiteracy = 0;
                if (!empty($counterLiteracy)) {
                    foreach ($counterLiteracy as $key => $value) {
                        $from_date_literacy = to_sql_date($this->input->post('from_date_literacy')[$value]);
                        $to_date_literacy = to_sql_date($this->input->post('to_date_literacy')[$value]);
                        $literacy = $this->input->post('literacy')[$value];
                        $training_places_literacy = $this->input->post('training_places_literacy')[$value];
                        $specialized_literacy = $this->input->post('specialized_literacy')[$value];
                        $classification_literacy = $this->input->post('classification_literacy')[$value];

                        if (empty($from_date_literacy) || empty($to_date_literacy) || empty($literacy) || empty($training_places_literacy) || empty($specialized_literacy) || empty($classification_literacy))
                        {
                            continue;
                        }

                        $arrLiteracy[] = [
                            'from_date_literacy' => $from_date_literacy,
                            'to_date_literacy' => $to_date_literacy,
                            'literacy' => $literacy,
                            'training_places_literacy' => $training_places_literacy,
                            'specialized_literacy' => $specialized_literacy,
                            'classification_literacy' => $classification_literacy,
                        ];
                        $countLiteracy++;
                    }
                }
                //

                //job
                $departments = $this->input->post('departments');
                $locations = $this->input->post('locations');
                $role = $this->input->post('role');
                $workplace = $this->input->post('workplace');
                $day_in = to_sql_date($this->input->post('day_in'));
                $day_in_primary = to_sql_date($this->input->post('day_in_primary'));
                //end job

                //concurrently
                $counterConcurrently = $this->input->post('counterConcurrently');
                $arrConcurrently = [];
                $counConcurrently = 0;
                if (!empty($counterConcurrently)) {
                    foreach ($counterConcurrently as $key => $value) {
                        $deparments_concurrently = $this->input->post('deparments_concurrently')[$value];
                        $location_concurrently = $this->input->post('location_concurrently')[$value];
                        $role_concurrently = $this->input->post('role_concurrently')[$value];

                        if (empty($deparments_concurrently) || empty($location_concurrently) || empty($role_concurrently))
                        {
                            continue;
                        }

                        $arrConcurrently[] = [
                            'deparments_concurrently' => $deparments_concurrently,
                            'location_concurrently' => $location_concurrently,
                            'role_concurrently' => $role_concurrently,
                        ];
                        $counConcurrently++;
                    }
                }
                //end concurrently

                //salary
                $counterSalary = $this->input->post('counterSalary');
                $arrSalary = [];
                $countSalary = 0;
                if (!empty($counterSalary)) {
                    foreach ($counterSalary as $key => $value) {
                        $from_date_salary = to_sql_date($this->input->post('from_date_salary')[$value]);
                        $note_salary = $this->input->post('note_salary')[$value];
                        $salary_form = $this->input->post('salary_form')[$value];
                        $money_salary = number_unformat($this->input->post('money_salary')[$value]);

                        if (empty($from_date_salary) || empty($salary_form))
                        {
                            continue;
                        }

                        //handling allowance
                        $arrAllowance = [];
                        // $salary_form_allowance = $this->input->post('salary_form_allowance')[$value];
                        $salary_form_allowance = !empty($this->input->post('salary_form_allowance')[$value]) ? $this->input->post('salary_form_allowance')[$value] : NULL;
                        if (!empty($salary_form_allowance)) {
                            foreach ($salary_form_allowance as $k => $val) {
                                $money_salary_allowance = number_unformat($this->input->post('money_salary_allowance')[$value][$k]);
                                $arrAllowance[] = [
                                    'salary_form_allowance' => $val,
                                    'money_salary_allowance' => $money_salary_allowance
                                ];
                            }
                        }
                        //end handling allowance

                        $arrSalary[] = [
                            'from_date_salary' => $from_date_salary,
                            'note_salary' => $note_salary,
                            'salary_form' => $salary_form,
                            'money_salary' => $money_salary,
                            'allowance' => $arrAllowance,
                        ];
                        $countSalary++;
                    }
                }
                //end salary

                //sign
                $signer = $this->input->post('signer');
                $role_signer = $this->input->post('role_signer');
                $sign_day = $this->input->post('sign_day');
                //end sign

                //insurrance
                $insurrance_book_number = $this->input->post('insurrance_book_number');
                $number_bhty = $this->input->post('number_bhty');
                $province_code = $this->input->post('province_code');
                $hospital_registration = $this->input->post('hospital_registration');
                //end insurrance

                //history insurrance
                $counterInsurrance = $this->input->post('counter_insurrance');
                $arrInsurrance = [];
                $countInsurrance = 0;
                if (!empty($counterInsurrance)) {
                    foreach ($counterInsurrance as $key => $value) {
                        $from_month_insurrance = $this->input->post('from_month_insurrance')[$value];
                        $form_insurrance = $this->input->post('form_insurrance')[$value];
                        $insurrance = $this->input->post('insurrance')[$value];
                        $money_insurrance = number_unformat($this->input->post('money_insurrance')[$value]);

                        $dtInsurrance = $this->category_model->rowInsurrance($insurrance);

                        if (empty($from_month_insurrance) || empty($form_insurrance) || empty($insurrance) || empty($dtInsurrance))
                        {
                            continue;
                        }

                        $rate_company_insurrance = $dtInsurrance['rate_company'];
                        $rate_worker_insurrance = $dtInsurrance['rate_worker'];

                        $moneyRateCompany = formatDecimalMoney($money_insurrance/$rate_company_insurrance);
                        $moneyRateWorker = formatDecimalMoney($money_insurrance/$rate_worker_insurrance);


                        $arrInsurrance[] = [
                            'from_month_insurrance' => $from_month_insurrance,
                            'form_insurrance' => $form_insurrance,
                            'insurrance' => $insurrance,
                            'money_insurrance' => $money_insurrance,
                            'rate_company_insurrance' => $rate_company_insurrance,
                            'rate_worker_insurrance' => $rate_worker_insurrance,
                            'money_company' => $moneyRateCompany,
                            'money_worker' => $moneyRateWorker,
                        ];
                        $countInsurrance++;
                    }
                }
                //end history insurrance

                //receive
                $receive = $this->input->post('receive');
                //end receive
                $code = getReference('personnel');

                //file
                $folder = tnh_vn_to_str($code);
                if (!is_dir('uploads/personnel/'.$folder)) {
                    mkdir('./uploads/personnel/' . $folder, 0777, TRUE);
                }
                $this->load->library('upload');
                if (!empty($_FILES['images']) && $_FILES['images']['size'] > 0) {
                    $upload_path = 'uploads/personnel/'.$folder;
                    $config['upload_path'] = $upload_path;
                    $config['allowed_types'] = $this->image_types;
                    // $config['max_size'] = $this->allowed_file_size;
                    // $config['max_width'] = $this->Settings->iwidth;
                    // $config['max_height'] = $this->Settings->iheight;
                    // $config['overwrite'] = TRUE;
                    //$config['max_filename'] = 25;
                    $config['encrypt_name'] = false;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('images')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        $data['result'] = 0;
                        $data['message'] = $error;
                        echo json_encode($data);
                        return;
                    }
                    $images = $this->upload->file_name;
                } else {
                    $images = NULL;
                }
                //end file
                //attachments
                $uploadData = [];
                if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['size'])) {
                    $fileCount = count($_FILES['attachments']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $_FILES['file']['name'] = $_FILES['attachments']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['attachments']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['attachments']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['attachments']['size'][$i];

                        $config['upload_path'] = 'uploads/personnel/'.$folder;
                        $config['allowed_types'] = '*';

                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i]['name'] = $this->upload->file_name;
                            $uploadData[$i]['extension'] = $_FILES['attachments']['type'][$i];
                            $uploadData[$i]['size'] = $_FILES['attachments']['size'][$i];
                            $uploadData[$i]['update_by'] = get_staff_user_id();
                            $uploadData[$i]['date_updated'] = date('Y-m-d H:i:s');
                        } else {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            $data['result'] = 0;
                            $data['message'] = $error;
                            echo json_encode($data);
                            return;
                        }
                    }
                }
                //

                //personnel history job
                $personnelHistoryJob = [
                    'personnel_id' => 0,
                    'date' => date('Y-m-d'),
                    'status' => 1,
                    'department_id' => $departments,
                    'location_id' => $locations,
                    'role_id' => $role,
                ];
                //

                $option = [
                    'code' => $code,
                    'fullname' => $fullname,
                    'birthday' => $birthday,
                    'gender' => $gender,
                    'birthplace' => $birthplace,
                    'domicile' => $domicile,
                    'cmnd_id_passport' => $cmnd_id_passport,
                    'date_range' => $date_range,
                    'issued_by' => $issued_by,
                    'marital_status' => $marital_status,
                    'nation' => $nation,
                    'nationality' => $nationality,
                    'account_name' => $account_name,
                    'bank' => $bank,
                    'branch' => $branch,
                    'personal_tax_code' => $personal_tax_code,
                    'images' => $images,
                    'note' => $note,
                    'telephone' => $telephone,
                    'email' => $email,
                    'skype' => $skype,
                    'facebook' => $facebook,
                    'resident' => $resident,
                    'current_accommodation' => $current_accommodation,
                    'departments' => $departments,
                    'locations' => $locations,
                    'role' => $role,
                    'workplace' => $workplace,
                    'day_in' => $day_in,
                    'day_in_primary' => $day_in_primary,
                    'signer' => $signer,
                    'role_signer' => $role_signer,
                    'sign_day' => $sign_day,
                    'insurrance_book_number' => $insurrance_book_number,
                    'number_bhty' => $number_bhty,
                    'province_code' => $province_code,
                    'hospital_registration' => $hospital_registration,
                    'folder' => $folder,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id(),
                    'religion' => $religion,
                    'status' => 1,
                ];

                $personnel_id = $this->personnel_model->insertPersonnel($option);
                if ($personnel_id) {
                    updateReference('personnel');

                    //handling Family
                    if (!empty($arrFamily))
                    {
                        foreach ($arrFamily as $key => $value) {
                            $arrFamily[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelFamily($arrFamily);
                    }
                    //end handling family

                    //handling Literacy
                    if (!empty($arrLiteracy))
                    {
                        foreach ($arrLiteracy as $key => $value) {
                            $arrLiteracy[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchLiteracy($arrLiteracy);
                    }
                    //end Literacy

                    //handling concurrently
                    if (!empty($arrConcurrently))
                    {
                        foreach ($arrConcurrently as $key => $value) {
                            $arrConcurrently[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelConcurrently($arrConcurrently);
                    }
                    //end concurrently

                    //handling salary
                    if (!empty($arrSalary))
                    {
                        foreach ($arrSalary as $key => $value) {
                            $value['personnel_id'] = $personnel_id;
                            $arrAllowance = $value['allowance'];
                            unset($value['allowance']);

                            $personnel_salary_id = $this->personnel_model->insertPersonnelSalary($value);
                            if (!empty($personnel_salary_id)) {
                                if (!empty($arrAllowance)) {
                                    foreach ($arrAllowance as $k => $val) {
                                        $arrAllowance[$k]['personnel_salary_id'] = $personnel_salary_id;
                                    }
                                    $this->personnel_model->insertBatchPersonnelSalaryAllowance($arrAllowance);
                                }
                            }
                        }
                    }
                    //end salary

                    //handling Insurrance
                    if (!empty($arrInsurrance)) {
                        foreach ($arrInsurrance as $key => $value) {
                            $arrInsurrance[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelInsurrance($arrInsurrance);
                    }
                    //end insurrance

                    //handling receive
                    if (!empty($receive))
                    {
                        $arrReceive = [];
                        foreach ($receive as $key => $value) {
                            $arrReceive[$key]['personnel_id'] = $personnel_id;
                            $arrReceive[$key]['receive_id'] = $value;
                        }
                        $this->personnel_model->insertBatchPersonnelReceive($arrReceive);
                    }
                    //end receive

                    if (!empty($uploadData))
                    {
                        foreach ($uploadData as $key => $value) {
                            $uploadData[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelAttachments($uploadData);
                    }

                    $personnelHistoryJob['personnel_id'] = $personnel_id;
                    $this->personnel_model->insertPersonnelHistoryJob($personnelHistoryJob);

                    insertActivityLog([
                        'type_parent_obj' => 'personnel',
                        'table_obj' => 'tbl_personnel',
                        'id_obj' => $personnel_id,
                        'name_obj' => $code,
                        'content' => lang('tnh_tmhsnx').' ['.$code.']',
                        'actions' => 'add'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists('uploads/personnel/'.$folder.'/'.$images)) {
                        @unlink('uploads/personnel/'.$folder.'/'.$images);
                    }
                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists('uploads/personnel/'.$folder.'/'.$value)) {
                                @unlink('uploads/personnel/'.$folder.'/'.$value);
                            }
                        }
                    }
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

    	$data['deparments'] = $this->category_model->getDeparments();
    	$data['roles'] = $this->category_model->getRole();
    	$data['locations'] = $this->category_model->getLocations();
        $data['workplace'] = $this->category_model->getWorkplace();
        $data['allowance'] = $this->category_model->getAllowance();
        $data['salaryForm'] = $this->category_model->getSalaryForm();
    	$data['provinceLevel'] = $this->category_model->getProvinceLevel();

    	$data['title'] = lang('tnh_add_personnel');
    	$data['breadcrumb'] = [array('link' => base_url('admin/personnel'), 'page' => lang('tnh_hr')), array('link' => '#', 'page' => lang('tnh_add_personnel'))];
        $this->load->view('admin/personnel/add_personnel', $data);
    }

    public function edit_personnel($id)
    {
        $personnel = $this->personnel_model->getPersonnelById($id);
        if ($this->input->post('edit'))
        {
            $data = [];
            $this->form_validation->set_rules('fullname', lang("tnh_fullname"), 'required');
            $this->form_validation->set_rules('birthday', lang("tnh_birthday"), 'required');
            $this->form_validation->set_rules('gender', lang("tnh_gender"), 'required');
            $this->form_validation->set_rules('telephone', lang("tnh_telephone"), 'required');
            $this->form_validation->set_rules('email', lang("Email"), 'required');
            if ($this->form_validation->run() == true)
            {
                $fullname = $this->input->post('fullname');
                $birthday = to_sql_date($this->input->post('birthday'));
                $gender = $this->input->post('gender');
                $birthplace = $this->input->post('birthplace');
                $domicile = $this->input->post('domicile');
                $cmnd_id_passport = $this->input->post('cmnd_id_passport');
                $date_range = $this->input->post('date_range') ? to_sql_date($this->input->post('date_range')) : null;
                $issued_by = $this->input->post('issued_by');
                $marital_status = $this->input->post('marital_status');
                $nationality = $this->input->post('nationality');
                $nation = $this->input->post('nation');
                $account_name = $this->input->post('account_name');
                $bank = $this->input->post('bank');
                $branch = $this->input->post('branch');
                $personal_tax_code = $this->input->post('personal_tax_code');
                $note = $this->input->post('note');
                $telephone = $this->input->post('telephone');
                $email = $this->input->post('email');
                $skype = $this->input->post('skype');
                $facebook = $this->input->post('facebook');
                $resident = $this->input->post('resident');
                $current_accommodation = $this->input->post('current_accommodation');
                $religion = $this->input->post('religion');

                //family
                $counterFamily = $this->input->post('counterFamily');
                $arrFamily = [];
                $countFamily = 0;
                if (!empty($counterFamily)) {
                    foreach ($counterFamily as $key => $value) {
                        $relationship_family = $this->input->post('relationship_family')[$value];
                        $fullname_family = $this->input->post('fullname_family')[$value];
                        $year_birthday_family = $this->input->post('year_birthday_family')[$value];
                        $career_family = $this->input->post('career_family')[$value];
                        $address_family = $this->input->post('address_family')[$value];
                        $telephone_family = $this->input->post('telephone_family')[$value];

                        if (empty($relationship_family) || empty($fullname_family) || empty($telephone_family))
                        {
                            continue;
                        }

                        $arrFamily[] = [
                            'relationship_family' => $relationship_family,
                            'fullname_family' => $fullname_family,
                            'year_birthday_family' => $year_birthday_family,
                            'career_family' => $career_family,
                            'address_family' => $address_family,
                            'telephone_family' => $telephone_family,
                        ];
                        $countFamily++;
                    }
                }
                //end family

                //literacy
                $counterLiteracy = $this->input->post('counterLiteracy');
                $arrLiteracy = [];
                $countLiteracy = 0;
                if (!empty($counterLiteracy)) {
                    foreach ($counterLiteracy as $key => $value) {
                        $from_date_literacy = to_sql_date($this->input->post('from_date_literacy')[$value]);
                        $to_date_literacy = to_sql_date($this->input->post('to_date_literacy')[$value]);
                        $literacy = $this->input->post('literacy')[$value];
                        $training_places_literacy = $this->input->post('training_places_literacy')[$value];
                        $specialized_literacy = $this->input->post('specialized_literacy')[$value];
                        $classification_literacy = $this->input->post('classification_literacy')[$value];

                        if (empty($from_date_literacy) || empty($to_date_literacy) || empty($literacy) || empty($training_places_literacy) || empty($specialized_literacy) || empty($classification_literacy))
                        {
                            continue;
                        }

                        $arrLiteracy[] = [
                            'from_date_literacy' => $from_date_literacy,
                            'to_date_literacy' => $to_date_literacy,
                            'literacy' => $literacy,
                            'training_places_literacy' => $training_places_literacy,
                            'specialized_literacy' => $specialized_literacy,
                            'classification_literacy' => $classification_literacy,
                        ];
                        $countLiteracy++;
                    }
                }
                //

                //job
                $departments = $this->input->post('departments');
                $locations = $this->input->post('locations');
                $role = $this->input->post('role');
                $workplace = $this->input->post('workplace');
                $day_in = to_sql_date($this->input->post('day_in'));
                $day_in_primary = to_sql_date($this->input->post('day_in_primary'));
                //end job

                //concurrently
                $counterConcurrently = $this->input->post('counterConcurrently');
                $arrConcurrently = [];
                $counConcurrently = 0;
                if (!empty($counterConcurrently)) {
                    foreach ($counterConcurrently as $key => $value) {
                        $deparments_concurrently = $this->input->post('deparments_concurrently')[$value];
                        $location_concurrently = $this->input->post('location_concurrently')[$value];
                        $role_concurrently = $this->input->post('role_concurrently')[$value];

                        if (empty($deparments_concurrently) || empty($location_concurrently) || empty($role_concurrently))
                        {
                            continue;
                        }

                        $arrConcurrently[] = [
                            'deparments_concurrently' => $deparments_concurrently,
                            'location_concurrently' => $location_concurrently,
                            'role_concurrently' => $role_concurrently,
                        ];
                        $counConcurrently++;
                    }
                }
                //end concurrently

                //salary
                $counterSalary = $this->input->post('counterSalary');
                $arrSalary = [];
                $countSalary = 0;
                if (!empty($counterSalary)) {
                    foreach ($counterSalary as $key => $value) {
                        $from_date_salary = to_sql_date($this->input->post('from_date_salary')[$value]);
                        $note_salary = $this->input->post('note_salary')[$value];
                        $salary_form = $this->input->post('salary_form')[$value];
                        $money_salary = number_unformat($this->input->post('money_salary')[$value]);

                        if (empty($from_date_salary) || empty($salary_form))
                        {
                            continue;
                        }

                        //handling allowance
                        $arrAllowance = [];
                        $salary_form_allowance = !empty($this->input->post('salary_form_allowance')[$value]) ? $this->input->post('salary_form_allowance')[$value] : NULL;
                        if (!empty($salary_form_allowance)) {
                            foreach ($salary_form_allowance as $k => $val) {
                                $money_salary_allowance = number_unformat($this->input->post('money_salary_allowance')[$value][$k]);
                                $arrAllowance[] = [
                                    'salary_form_allowance' => $val,
                                    'money_salary_allowance' => $money_salary_allowance
                                ];
                            }
                        }
                        //end handling allowance

                        $arrSalary[] = [
                            'from_date_salary' => $from_date_salary,
                            'note_salary' => $note_salary,
                            'salary_form' => $salary_form,
                            'money_salary' => $money_salary,
                            'allowance' => $arrAllowance,
                        ];
                        $countSalary++;
                    }
                }
                //end salary

                //sign
                $signer = $this->input->post('signer');
                $role_signer = $this->input->post('role_signer');
                $sign_day = $this->input->post('sign_day');
                //end sign

                //insurrance
                $insurrance_book_number = $this->input->post('insurrance_book_number');
                $number_bhty = $this->input->post('number_bhty');
                $province_code = $this->input->post('province_code');
                $hospital_registration = $this->input->post('hospital_registration');
                //end insurrance

                //history insurrance
                $counterInsurrance = $this->input->post('counter_insurrance');
                $arrInsurrance = [];
                $countInsurrance = 0;
                if (!empty($counterInsurrance)) {
                    foreach ($counterInsurrance as $key => $value) {
                        $from_month_insurrance = $this->input->post('from_month_insurrance')[$value];
                        $form_insurrance = $this->input->post('form_insurrance')[$value];
                        $insurrance = $this->input->post('insurrance')[$value];
                        $money_insurrance = number_unformat($this->input->post('money_insurrance')[$value]);

                        $dtInsurrance = $this->category_model->rowInsurrance($insurrance);

                        if (empty($from_month_insurrance) || empty($form_insurrance) || empty($insurrance) || empty($dtInsurrance))
                        {
                            continue;
                        }

                        $rate_company_insurrance = $dtInsurrance['rate_company'];
                        $rate_worker_insurrance = $dtInsurrance['rate_worker'];

                        $moneyRateCompany = formatDecimalMoney($money_insurrance/$rate_company_insurrance);
                        $moneyRateWorker = formatDecimalMoney($money_insurrance/$rate_worker_insurrance);


                        $arrInsurrance[] = [
                            'from_month_insurrance' => $from_month_insurrance,
                            'form_insurrance' => $form_insurrance,
                            'insurrance' => $insurrance,
                            'money_insurrance' => $money_insurrance,
                            'rate_company_insurrance' => $rate_company_insurrance,
                            'rate_worker_insurrance' => $rate_worker_insurrance,
                            'money_company' => $moneyRateCompany,
                            'money_worker' => $moneyRateWorker,
                        ];
                        $countInsurrance++;
                    }
                }
                //end history insurrance

                //receive
                $receive = $this->input->post('receive');
                //end receive
                $code = $personnel['code'];

                //file
                $folder = tnh_vn_to_str($code);
                $imageOld = $personnel['images'];
                $this->load->library('upload');
                if (!empty($_FILES['images']) && $_FILES['images']['size'] > 0) {
                    $upload_path = 'uploads/personnel/'.$folder;
                    $config['upload_path'] = $upload_path;
                    $config['allowed_types'] = $this->image_types;
                    $config['encrypt_name'] = false;
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('images')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        $data['result'] = 0;
                        $data['message'] = $error;
                        echo json_encode($data);
                        return;
                    }
                    $images = $this->upload->file_name;
                } else {
                    $images = NULL;
                }
                //end file
                //attachments
                $uploadData = [];
                if (!empty($_FILES['attachments']) && !empty($_FILES['attachments']['size'])) {
                    $fileCount = count($_FILES['attachments']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $_FILES['file']['name'] = $_FILES['attachments']['name'][$i];
                        $_FILES['file']['type'] = $_FILES['attachments']['type'][$i];
                        $_FILES['file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i];
                        $_FILES['file']['error'] = $_FILES['attachments']['error'][$i];
                        $_FILES['file']['size'] = $_FILES['attachments']['size'][$i];

                        $config['upload_path'] = 'uploads/personnel/'.$folder;
                        $config['allowed_types'] = '*';

                        $this->upload->initialize($config);
                        if ($this->upload->do_upload('file')) {
                            $uploadData[$i]['name'] = $this->upload->file_name;
                            $uploadData[$i]['extension'] = $_FILES['attachments']['type'][$i];
                            $uploadData[$i]['size'] = $_FILES['attachments']['size'][$i];
                            $uploadData[$i]['update_by'] = get_staff_user_id();
                            $uploadData[$i]['date_updated'] = date('Y-m-d H:i:s');
                        } else {
                            $error = $this->upload->display_errors();
                            $this->session->set_flashdata('error', $error);
                            $data['result'] = 0;
                            $data['message'] = $error;
                            echo json_encode($data);
                            return;
                        }
                    }
                }
                //

                //personnel history job
                $personnelHistoryJob = [
                    'personnel_id' => $id,
                    'date' => date('Y-m-d'),
                    'status' => 1,
                    'department_id' => $departments,
                    'location_id' => $locations,
                    'role_id' => $role,
                ];
                //

                $option = [
                    'code' => $code,
                    'fullname' => $fullname,
                    'birthday' => $birthday,
                    'gender' => $gender,
                    'birthplace' => $birthplace,
                    'domicile' => $domicile,
                    'cmnd_id_passport' => $cmnd_id_passport,
                    'date_range' => $date_range,
                    'issued_by' => $issued_by,
                    'marital_status' => $marital_status,
                    'nation' => $nation,
                    'nationality' => $nationality,
                    'account_name' => $account_name,
                    'bank' => $bank,
                    'branch' => $branch,
                    'personal_tax_code' => $personal_tax_code,
                    'note' => $note,
                    'telephone' => $telephone,
                    'email' => $email,
                    'skype' => $skype,
                    'facebook' => $facebook,
                    'resident' => $resident,
                    'current_accommodation' => $current_accommodation,
                    'departments' => $departments,
                    'locations' => $locations,
                    'role' => $role,
                    'workplace' => $workplace,
                    'day_in' => $day_in,
                    'day_in_primary' => $day_in_primary,
                    'signer' => $signer,
                    'role_signer' => $role_signer,
                    'sign_day' => $sign_day,
                    'insurrance_book_number' => $insurrance_book_number,
                    'number_bhty' => $number_bhty,
                    'province_code' => $province_code,
                    'hospital_registration' => $hospital_registration,
                    'folder' => $folder,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'updated_by' => get_staff_user_id(),
                    'religion' => $religion,
                ];

                if (!empty($images)) {
                    $option['images'] = $images;
                }

                $up = $this->personnel_model->updatePersonnel($id, $option);
                $personnel_id = $id;
                if ($up) {
                    $salaryDT = $this->personnel_model->getPersonnelSalary($id);
                    $this->personnel_model->deletePersonnelFamily($id);
                    $this->personnel_model->deleteLiteracy($id);
                    $this->personnel_model->deletePersonnelConcurrently($id);
                    $this->personnel_model->deletePersonnelSalary($id);
                    if (!empty($salaryDT)) {
                        foreach ($salaryDT as $key => $value) {
                            $this->personnel_model->deletePersonnelSalaryAllowance($value['id']);
                        }
                    }
                    $this->personnel_model->deletePersonnelInsurrance($id);
                    $this->personnel_model->deletePersonnelReceive($id);

                    //handling Family
                    if (!empty($arrFamily))
                    {
                        foreach ($arrFamily as $key => $value) {
                            $arrFamily[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelFamily($arrFamily);
                    }
                    //end handling family

                    //handling Literacy
                    if (!empty($arrLiteracy))
                    {
                        foreach ($arrLiteracy as $key => $value) {
                            $arrLiteracy[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchLiteracy($arrLiteracy);
                    }
                    //end Literacy

                    //handling concurrently
                    if (!empty($arrConcurrently))
                    {
                        foreach ($arrConcurrently as $key => $value) {
                            $arrConcurrently[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelConcurrently($arrConcurrently);
                    }
                    //end concurrently

                    //handling salary
                    if (!empty($arrSalary))
                    {
                        foreach ($arrSalary as $key => $value) {
                            $value['personnel_id'] = $personnel_id;
                            $arrAllowance = $value['allowance'];
                            unset($value['allowance']);

                            $personnel_salary_id = $this->personnel_model->insertPersonnelSalary($value);
                            if (!empty($personnel_salary_id)) {
                                if (!empty($arrAllowance)) {
                                    foreach ($arrAllowance as $k => $val) {
                                        $arrAllowance[$k]['personnel_salary_id'] = $personnel_salary_id;
                                    }
                                    $this->personnel_model->insertBatchPersonnelSalaryAllowance($arrAllowance);
                                }
                            }
                        }
                    }
                    //end salary

                    //handling Insurrance
                    if (!empty($arrInsurrance)) {
                        foreach ($arrInsurrance as $key => $value) {
                            $arrInsurrance[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelInsurrance($arrInsurrance);
                    }
                    //end insurrance

                    //handling receive
                    if (!empty($receive))
                    {
                        $arrReceive = [];
                        foreach ($receive as $key => $value) {
                            $arrReceive[$key]['personnel_id'] = $personnel_id;
                            $arrReceive[$key]['receive_id'] = $value;
                        }
                        $this->personnel_model->insertBatchPersonnelReceive($arrReceive);
                    }
                    //end receive

                    if (!empty($uploadData))
                    {
                        foreach ($uploadData as $key => $value) {
                            $uploadData[$key]['personnel_id'] = $personnel_id;
                        }
                        $this->personnel_model->insertBatchPersonnelAttachments($uploadData);
                    }

                    if (!empty($images)) {
                        if (!empty($imageOld)) {
                            if (file_exists('uploads/personnel/'.$folder.'/'.$imageOld)) {
                                @unlink('uploads/personnel/'.$folder.'/'.$imageOld);
                            }
                        }
                    }

                    if (!$this->personnel_model->isPersonnelHistoryJobChange($personnelHistoryJob['personnel_id'], $personnelHistoryJob['status'], $personnelHistoryJob['department_id'], $personnelHistoryJob['location_id'], $personnelHistoryJob['role_id'])) {
                        $this->personnel_model->insertPersonnelHistoryJob($personnelHistoryJob);
                    }

                    insertActivityLog([
                        'type_parent_obj' => 'personnel',
                        'table_obj' => 'tbl_personnel',
                        'id_obj' => $personnel_id,
                        'name_obj' => $code,
                        'content' => lang('tnh_cnhsnx').' ['.$code.']',
                        'actions' => 'edit'
                    ]);

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    if (file_exists('uploads/personnel/'.$folder.'/'.$images)) {
                        @unlink('uploads/personnel/'.$folder.'/'.$images);
                    }
                    if (!empty($uploadData)) {
                        foreach ($uploadData as $key => $value) {
                            if (file_exists('uploads/personnel/'.$folder.'/'.$value)) {
                                @unlink('uploads/personnel/'.$folder.'/'.$value);
                            }
                        }
                    }
                    $data['result'] = 0;
                    $data['message'] = lang('fail');
                }
            } else {
                $data['result'] = 0;
                $data['message'] = validation_errors();
            }
            echo json_encode($data);
            die;
        }

        $family = $this->personnel_model->getPersonnelFamilyById($id);
        $literacy = $this->personnel_model->getPersonnelLiteracyById($id);
        $concurrently = $this->personnel_model->getPersonnelConcurrently($id);
        $salary = $this->personnel_model->getPersonnelSalary($id);
        $insurrance = $this->personnel_model->getPersonnelInsurranceById($id);
        $receive = $this->personnel_model->getPersonnelReceiveById($id);
        $arrReceive = [];
        foreach ($receive as $key => $value) {
            $arrReceive[] = $value['receive_id'];
        }
        $attachments = $this->personnel_model->getPersonnelAttachmentsById($id);

        $data['attachments'] = $attachments;
        $data['arrReceive'] = $arrReceive;
        $data['insurrance'] = $insurrance;
        $data['salary'] = $salary;
        $data['concurrently'] = $concurrently;
        $data['family'] = $family;
        $data['literacy'] = $literacy;
        $data['id'] = $id;
        $data['personnel'] = $personnel;
        $data['deparments'] = $this->category_model->getDeparments();
        $data['roles'] = $this->category_model->getRole();
        $data['locations'] = $this->category_model->getLocations();
        $data['workplace'] = $this->category_model->getWorkplace();
        $data['allowance'] = $this->category_model->getAllowance();
        $data['salaryForm'] = $this->category_model->getSalaryForm();
        $data['provinceLevel'] = $this->category_model->getProvinceLevel();

        $data['title'] = lang('tnh_edit_personnel');
        $data['breadcrumb'] = [array('link' => base_url('admin/personnel'), 'page' => lang('tnh_hr')), array('link' => '#', 'page' => lang('tnh_edit_personnel'))];
        $this->load->view('admin/personnel/edit_personnel', $data);
    }

    public function searchPersonnel($id = 0)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        // $params = $this->input->get('params');
        // $type = $params['type'];
        $personnel = $this->personnel_model->searchPersonnel($term, $limit);

        $results = $personnel;
        $data['results'] = $results;

        if ($id) {
            // $data['row'] = ['id' => $info['id'], 'text' => $info['code']];
        }
        echo json_encode($data);
    }

    public function getPersonnel()
    {

        $this->datatables->select("
            tbl_personnel.id as id,
            tbl_personnel.code as code,
            CONCAT(tbl_personnel.folder, '__', tbl_personnel.images) as images,
            tbl_personnel.fullname as fullname,
            tbl_personnel.birthday as birthday,
            tbl_personnel.gender as gender,
            tbl_personnel.birthplace as birthplace,
            tbl_personnel.domicile as domicile,
            tbl_personnel.cmnd_id_passport as cmnd_id_passport,
            tbl_personnel.date_range as date_range,
            tbl_personnel.issued_by as issued_by,
            tbl_personnel.marital_status as marital_status,
            tbl_personnel.nationality as nationality,
            tbl_personnel.nation as nation,
            tbl_personnel.account_name as account_name,
            tbl_personnel.bank as bank,
            tbl_personnel.branch as branch,
            tbl_personnel.personal_tax_code as personal_tax_code,
            tbl_personnel.status as status,
            tbl_personnel.note as note,
            tbl_personnel.date_created as date_created,
            CONCAT(tblstaff.firstname, ' ', tblstaff.lastname, '') as created_by,
        ", FALSE)

        ->from('tbl_personnel')
        ->join('tblstaff', 'tblstaff.staffid = tbl_personnel.created_by', 'left');

        $view = '<a href="'.base_url('admin/personnel/view/$1').'"><i class="fa fa-file-text-o"></i> '.lang('tnh_view_personnel').'</a>';

        $edit = '<a href="'.base_url('admin/personnel/edit_personnel/$1').'"><i class="fa fa-pencil"></i> '.lang('tnh_edit_personnel').'</a>';

        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
            <button href=\''.base_url('admin/personnel/deletePersonnel/$1').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
        "><i class="fa fa-remove width-icon-actions"></i> '.lang('tnh_delete_personnel').'</a>';

        // <li>'.$view.'</li>
        // <li class="not-outside">'.$delete.'</li>

        $actions = '
        <div class="dropdown text-center">
            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
            '.lang('actions').'
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                <li>'.$view.'</li>
                <li>'.$edit.'</li>
                <li class="not-outside">'.$delete.'</li>
            </ul>
        </div>';

        $this->datatables->add_column('actions', $actions, 'id');
        $data = json_decode($this->datatables->generate());
        echo json_encode($data);
    }

    public function view($id)
    {
        $personnel = $this->personnel_model->getPersonnelById($id);
        $family = $this->personnel_model->getPersonnelFamilyById($id);
        $literacy = $this->personnel_model->getPersonnelLiteracyById($id);
        $deparment = $this->personnel_model->getDepartmentsById($personnel['departments']);
        $location = $this->personnel_model->getLocationsById($personnel['locations']);
        $workplace = $this->personnel_model->getWorkplaceById($personnel['workplace']);
        $role = $this->personnel_model->getRolesById($personnel['role']);
        $concurrently = $this->personnel_model->getPersonnelConcurrently($id);
        $salary = $this->personnel_model->getPersonnelSalary($id);
        $province = $this->personnel_model->getProvinceLevelById($personnel['province_code']);
        $hospital = $this->personnel_model->getHospitalInsurranceById($personnel['hospital_registration']);
        $insurrance = $this->personnel_model->getPersonnelInsurranceById($id);
        $receive = $this->personnel_model->getPersonnelReceiveById($id);
        $attachments = $this->personnel_model->getPersonnelAttachmentsById($id);
        $arrReceive = [];
        foreach ($receive as $key => $value) {
            $arrReceive[] = $value['receive_id'];
        }
        $historyJob = $this->personnel_model->getPersonnelHistoryJob($id);


        $data['historyJob'] = $historyJob;
        $data['attachments'] = $attachments;
        $data['arrReceive'] = $arrReceive;
        $data['insurrance'] = $insurrance;
        $data['province'] = $province;
        $data['hospital'] = $hospital;
        $data['salary'] = $salary;
        $data['concurrently'] = $concurrently;
        $data['deparment'] = $deparment;
        $data['location'] = $location;
        $data['workplace'] = $workplace;
        $data['role'] = $role;
        $data['personnel'] = $personnel;
        $data['family'] = $family;
        $data['literacy'] = $literacy;
        $data['title'] = $personnel['fullname'];
        $data['id'] = $id;
        $data['breadcrumb'] = [array('link' => base_url('admin/personnel'), 'page' => lang('tnh_hr')), array('link' => '#', 'page' => lang('tnh_view_personnel'))];
        $this->load->view('admin/personnel/view_personnel', $data);
    }

    public function deletePersonnel($id)
    {
        $data = [];
        // if (!$this->perDeleteOrders) {
        //     $data['result'] = 0;
        //     $data['message'] = lang('access_denied');
        //     echo json_encode($data); die;
        // }
        if ($id) {
            $personnel = $this->personnel_model->getPersonnelById($id);
            $salary = $this->personnel_model->getPersonnelById($id);
            $attachments = $this->personnel_model->getPersonnelAttachmentsById($id);
            if ($this->personnel_model->deletePersonnel($id)) {

                $this->personnel_model->deletePersonnelFamily($id);
                $this->personnel_model->deleteLiteracy($id);
                $this->personnel_model->deletePersonnelConcurrently($id);
                $this->personnel_model->deletePersonnelSalary($id);
                if (!empty($salaryDT)) {
                    foreach ($salary as $key => $value) {
                        $this->personnel_model->deletePersonnelSalaryAllowance($value['id']);
                    }
                }
                $this->personnel_model->deletePersonnelInsurrance($id);
                $this->personnel_model->deletePersonnelReceive($id);
                $this->personnel_model->deletePersonnelHistoryJob($id);

                $folder = $personnel['folder'];
                if (!empty($personnel['images'])) {
                    $images = $personnel['images'];
                    if (file_exists('uploads/personnel/'.$folder.'/'.$images)) {
                        @unlink('uploads/personnel/'.$folder.'/'.$images);
                    }
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $key => $value) {
                        if (file_exists('uploads/personnel/'.$folder.'/'.$value['name'])) {
                            @unlink('uploads/personnel/'.$folder.'/'.$value['name']);
                        }
                    }
                }

                insertActivityLog([
                    'type_parent_obj' => 'personnel',
                    'table_obj' => 'tbl_personnel',
                    'id_obj' => $id,
                    'name_obj' => $personnel['code'],
                    'content' => lang('tnh_cnhsnx').' ['.$personnel['code'].']',
                    'actions' => 'edit'
                ]);

                $data['result'] = 1;
                $data['message'] = lang('success');
            }
        } else {
            $data['result'] = 0;
            $data['message'] = lang('fail');
        }
        echo json_encode($data);
    }

    public function personnel_contract()
    {
        $data['title'] = lang('personnel_contract');
        $data['all'] = 0;
        $this->load->view('admin/personnel/personnel_contract', $data);
    }

    public function add_personnel_contract()
    {
        $data['title'] = lang('tnh_add_personnel_contract');
        $this->load->view('admin/personnel/add_personnel_contract', $data);
    }
}