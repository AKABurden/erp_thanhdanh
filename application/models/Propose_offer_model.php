<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Propose_offer_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert new offer
     * @param array $data
     * @return int Insert ID
     */
    public function insertProposeOffer($data)
    {
        $this->db->insert(db_prefix() . '_propose_offer', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Offer Created [ID: ' . $insert_id . ', Code: ' . $data['ma_offer'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update existing offer
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProposeOffer($id, $data)
    {
        $this->db->where('id', $id);
        $updated = $this->db->update(db_prefix() . '_propose_offer', $data);

        if ($updated) {
            log_activity('Offer Updated [ID: ' . $id . ']');
        }

        return $updated;
    }

    /**
     * Delete offer
     * @param int $id
     * @return bool
     */
    public function deleteProposeOffer($id)
    {
        // Get offer info before delete for logging
        $offer = $this->getProposeOfferById($id);

        $this->db->where('id', $id);
        $deleted = $this->db->delete(db_prefix() . '_propose_offer');

        if ($deleted && $offer) {
            log_activity('Offer Deleted [ID: ' . $id . ', Code: ' . $offer->ma_offer . ']');
        }

        return $deleted;
    }

    /**
     * Get offer by ID
     * @param int $id
     * @return object|null
     */
    public function getProposeOfferById($id)
    {
        $this->db->select('tbl_propose_offer.*, tbl_hr_eprofile.full_name as ten_ung_vien,tbl_role_level.name as name_lever_offer');
        $this->db->where('tbl_propose_offer.id', $id);
        $this->db->join('tbl_hr_eprofile', 'tbl_hr_eprofile.id = tbl_propose_offer.kqpv_id', 'left');
        $this->db->join('tbl_hr_requirements', 'tbl_hr_requirements.id = tbl_propose_offer.id_yctd', 'left');
        $this->db->join('tbl_role_level', 'tbl_role_level.id = tbl_hr_requirements.role_level', 'left');
        return $this->db->get(db_prefix() . '_propose_offer')->row();
    }

    /**
     * Get offer by code
     * @param string $ma_offer
     * @return object|null
     */
    public function getProposeOfferByCode($ma_offer)
    {
        $this->db->where('ma_offer', $ma_offer);
        return $this->db->get(db_prefix() . '_propose_offer')->row();
    }

    /**
     * Get all offers with optional filters
     * @param array $where
     * @param string $order_by
     * @return array
     */
    public function getAllProposeOffers($where = [], $order_by = 'ngay_tao DESC')
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        }

        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }

    /**
     * Get offers by status
     * @param string $status
     * @return array
     */
    public function getOffersByStatus($status)
    {
        $this->db->where('trang_thai', $status);
        $this->db->order_by('ngay_tao', 'DESC');
        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }

    /**
     * Get offers by department
     * @param string $department
     * @return array
     */
    public function getOffersByDepartment($department)
    {
        $this->db->where('phong_ban_offer', $department);
        $this->db->order_by('ngay_tao', 'DESC');
        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }

    /**
     * Get offers created by staff
     * @param int $staff_id
     * @return array
     */
    public function getOffersByStaff($staff_id)
    {
        $this->db->where('staff_create', $staff_id);
        $this->db->order_by('ngay_tao', 'DESC');
        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }

    /**
     * Count offers by status
     * @param string $status
     * @return int
     */
    public function countOffersByStatus($status = null)
    {
        if ($status) {
            $this->db->where('trang_thai', $status);
        }
        return $this->db->count_all_results(db_prefix() . '_propose_offer');
    }

    /**
     * Search offers
     * @param string $search_term
     * @return array
     */
    public function searchOffers($search_term)
    {
        $this->db->group_start();
        $this->db->like('ma_offer', $search_term);
        $this->db->or_like('ma_yctd', $search_term);
        $this->db->or_like('ten_ung_vien', $search_term);
        $this->db->or_like('vi_tri_offer', $search_term);
        $this->db->or_like('phong_ban_offer', $search_term);
        $this->db->group_end();

        $this->db->order_by('ngay_tao', 'DESC');
        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }

    /**
     * Get statistics
     * @return array
     */
    public function getStatistics()
    {
        $stats = [];

        // Total offers
        $stats['total'] = $this->db->count_all_results(db_prefix() . '_propose_offer');

        // By status
        $stats['draft'] = $this->countOffersByStatus('DRAFT');
        $stats['pending'] = $this->countOffersByStatus('DANG_CHO_DUYET');
        $stats['sent'] = $this->countOffersByStatus('DA_GUI');
        $stats['accepted'] = $this->countOffersByStatus('CHAP_NHAN');
        $stats['rejected'] = $this->countOffersByStatus('TU_CHOI');

        // This month
        $this->db->where('MONTH(ngay_tao)', date('m'));
        $this->db->where('YEAR(ngay_tao)', date('Y'));
        $stats['this_month'] = $this->db->count_all_results(db_prefix() . '_propose_offer');

        // This week
        $this->db->where('WEEK(ngay_tao)', date('W'));
        $this->db->where('YEAR(ngay_tao)', date('Y'));
        $stats['this_week'] = $this->db->count_all_results(db_prefix() . '_propose_offer');

        return $stats;
    }

    /**
     * Check if offer code exists
     * @param string $ma_offer
     * @param int $exclude_id (optional, for update check)
     * @return bool
     */
    public function isOfferCodeExists($ma_offer, $exclude_id = null)
    {
        $this->db->where('ma_offer', $ma_offer);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->count_all_results(db_prefix() . '_propose_offer') > 0;
    }

    /**
     * Generate unique offer code
     * @return string
     */
    public function generateOfferCode()
    {
        do {
            $code = 'OFR' . date('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while ($this->isOfferCodeExists($code));

        return $code;
    }

    /**
     * Update offer status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        return $this->updateProposeOffer($id, [
            'trang_thai' => $status,
            'staff_update' => get_staff_user_id(),
            'date_update' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get recent offers
     * @param int $limit
     * @return array
     */
    public function getRecentOffers($limit = 10)
    {
        $this->db->order_by('ngay_tao', 'DESC');
        $this->db->limit($limit);
        return $this->db->get(db_prefix() . '_propose_offer')->result();
    }
    public function getStaffRoles($staff_id)
    {
        $this->db->select('tblroles.name as role_name');
        $this->db->where('staffid', $staff_id);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaff.role', 'left');
        return $this->db->get('tblroles')->row_array();
    }
    function getYCTDById($id = '')
    {
        $this->db->select('*');
        $this->db->from('tbl_hr_requirements');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }
}
