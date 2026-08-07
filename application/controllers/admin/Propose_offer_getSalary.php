    /**
     * Get salary P1/P2 by role_id and seniority (months)
     * Thêm vào cuối Propose_offer.php trước dấu }
     */
    public function getSalaryByRole()
    {
        $role_id = $this->input->get('role_id');
        $seniority_months = (int)$this->input->get('seniority_months') ?: 0;

        if (!$role_id) {
            echo json_encode(['success' => false, 'message' => 'role_id is required']);
            return;
        }

        $this->db->select('tbl_salary_3p.*, tbl_grade.code as grade_code, tbl_grade.seniority_from_month, tbl_grade.seniority_to_month');
        $this->db->from('tbl_salary_3p');
        $this->db->join('tbl_grade', 'tbl_grade.id = tbl_salary_3p.grade_id', 'inner');
        $this->db->where('tbl_salary_3p.role_id', $role_id);
        $this->db->where('tbl_salary_3p.status', 1);
        $this->db->where('tbl_salary_3p.effective_from <=', date('Y-m-d'));
        $this->db->group_start();
        $this->db->where('tbl_salary_3p.effective_to >=', date('Y-m-d'));
        $this->db->or_where('tbl_salary_3p.effective_to IS NULL');
        $this->db->group_end();
        $this->db->where('tbl_grade.seniority_from_month <=', $seniority_months);
        $this->db->where('tbl_grade.seniority_to_month >=', $seniority_months);
        $this->db->order_by('tbl_salary_3p.version', 'DESC');
        $this->db->limit(1);
        
        $salary = $this->db->get()->row();

        if ($salary) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'salary_p1' => (float)$salary->salary_p1,
                    'salary_p2' => (float)$salary->salary_p2,
                    'p2_min' => (float)$salary->salary_p2 * 0.8,
                    'p2_max' => (float)$salary->salary_p2 * 1.2,
                    'grade_code' => $salary->grade_code
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy khung lương']);
        }
    }
