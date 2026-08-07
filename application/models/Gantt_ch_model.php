<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gantt_ch_model extends App_Model
{
    public function countProductionsGantt()
    {
        $permission =false;
        
        if(has_permission('gantt','','view_own')&&!is_admin())
        {
            $permission = true;
        }
        $productions_orders = $this->input->post('productions_orders');
        $this->db->select('COUNT(*) as count');
        $this->db->join('tbltask_assigned','tbltask_assigned.staffid = tblstaff.staffid');
        $this->db->join('tbltasks','tbltasks.id = tbltask_assigned.taskid', 'left');
        $this->db->where('tbltasks.status !=',5);
        $this->db->group_by('tblstaff.staffid');
        if($permission ==  true){
              $this->db->where('tblstaff.staffid',get_staff_user_id());
        }
        if (!empty($productions_orders)) {
            $this->db->where("tblstaff.staffid", $productions_orders);
        }
        $this->db->group_by('tblstaff.staffid');
        return $this->db->get('tblstaff')->row()->count;
    }
    public function loadGanttProductions($start, $limit)
    {
        $permission =false;
        
        if(has_permission('gantt','','view_own')&&!is_admin())
        {
            $permission = true;
        }
        $productions_orders = $this->input->post('productions_orders');
        $data = [];
        $this->db->select('
            tblstaff.staffid as staffid,
            CONCAT(firstname," ",lastname) as company', false);
        $this->db->join('tbltask_assigned','tbltask_assigned.staffid = tblstaff.staffid');
        $this->db->join('tbltasks','tbltasks.id = tbltask_assigned.taskid', 'left');
        $this->db->where('tbltasks.status !=',5);
        $this->db->group_by('tblstaff.staffid');
        if($permission ==  true){
              $this->db->where('tblstaff.staffid',get_staff_user_id());
        }
        if (!empty($productions_orders)) {
            $this->db->where("tblstaff.staffid", $productions_orders);
        }
        $this->db->from('tblstaff');
        $staff = $this->db->get()->result_array();
        $warningDuePOD = get_option('warning_due_pod');
        if (!empty($staff)) {
            foreach ($staff as $key => $value) {
                $staff_id = $value['staffid'];
                $name = get_staff_full_name($value['staffid']);
                $productionOrder = [
                    'production_order_id' => $staff_id,
                    'values' => false,
                    'desc' => 'productions_orders',
                    'name' => $name
                ];
                array_push($data, $productionOrder);
                $this->db->select("
                    tbltasks.id as id,
                    tbltasks.startdate as date_create,
                    tbltasks.duedate as delivery_date,
                    tbltasks.name as name,
                    tbltasks.description as description,
                ", false);
                $this->db->join('tbltask_assigned','tbltask_assigned.taskid = tbltasks.id', 'left');
                $this->db->where('tbltask_assigned.staffid',$value['staffid']);
                $this->db->where('tbltasks.status !=',5);
                $this->db->from('tbltasks');

                $order = $this->db->get()->result_array();
                foreach ($order as $k => $v) {
                $productionOrderDetailId = $v['id'];
                $referenceDetailNo = $v['name'];

                $dateStart = strftime('%Y/%m/%d', strtotime($v['date_create']));
                $dateEnd = strftime('%Y/%m/%d', strtotime($v['delivery_date']));

                $descPOD = $v['description'];

                //handling status pod
                $customClassPOD = 'ganttPrimary';
                $dateDuePOD = (strtotime($v['delivery_date']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                    if ($dateDuePOD < 0) {
                        //trễ hạn
                        $customClassPOD = 'ganttRed';
                        //
                    } else if ($dateDuePOD == 0) {
                        //Tới hạn
                        $customClassPOD = 'ganttGreen';
                    } else {
                        //sắp tới hạn
                        if (($dateDuePOD - $warningDuePOD) < 0) {
                            $customClassPOD = 'ganttYellow';
                        }
                    }
                //end handling status pod
                $dataPOD = [
                    'production_order_detail_id' => $productionOrderDetailId,
                    'values' => [
                        [
                            'from' => $dateStart,
                            'to' => $dateEnd,
                            'desc' => $descPOD,
                            'label' => $referenceDetailNo,
                            'customClass' => $customClassPOD,
                            'dataObj' => [
                                'production_order_detail_id' => $productionOrderDetailId
                            ]
                        ]
                    ],
                    'desc' => '<b>'.$referenceDetailNo.'</b>',
                    'name' => '',
                ];
                array_push($data, $dataPOD);
                }
            }
        }
        // print_arrays($data);

        return $data;
    }
}