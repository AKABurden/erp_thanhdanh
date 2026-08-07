<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Themes_mobile extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function table_purchase_order()
    {
        $aColumns = [
            'tblpurchase_order.id',
            '2'
        ];

        $sIndexColumn = 'id';
        $sTable       = 'tblpurchase_order';

        $join         = array(
        );
        $where         = array();

        $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
        ));
        $output  = $result['output'];
        $rResult = $result['rResult'];
        $currentPage = $this->input->post('start');
        $currentall = $output['iTotalRecords'];
        foreach ($rResult as $r => $aRow) {
            $row = [];
            for ($i = 0 ; $i < count($aColumns) ; $i++) {
                $_data = $aRow[$aColumns[$i]];
                $row[] = $_data;
            }
            
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
    }
}