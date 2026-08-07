<?php
    $this->perViewExpensesIncome = has_permission('expenses_vs_income', '', 'view');
    if (!$this->perViewExpensesIncome) {
        die;
    }
    $arrBranch = [];
    $dtBranch = [];
    $this->db->select("tblbranch.*, '' as tdSalesBranch, 0 as totalSalesBranch");
    $this->db->from('tblbranch');
    if (!empty($branch)) {
        $this->db->where_in('tblbranch.id', $branch);
        $arrBranch = $this->db->get()->result_array();
    } else {
        $dtBranch = $this->db->get()->result_array();
    }
?>
<style>
    .dt-buttons .buttons-html5 {
        background: #ccc;
    }
</style>
<?php
?>
<h2 class="text-center uppercase text-primary"><?= $title ?></h2>
<table id="tb-sale-performance" class="table table table-striped table-diary-of-collecting-money dont-responsive-table">
    <thead>
        <tr style="background:#b9fffc; font-weight: 700;">
            <th class="text-left"><b>Nội dung </b></th>
            <?php
                $thHead = '';

                $tdSales = '';
                $grandTotalSales = 0;

                $tdReduceSales = '';
                $grandTotalReduceSales = 0;

                $tdDiscountSales = '';
                $grandDiscountSales = 0;

                $tdReturnSales = '';
                $grandReturnSales = 0;

                $tdNetSales = '';
                $grandTotalNetSales = 0;

                $tdCostPrice = '';
                $grandTotalCostPrice = 0;

                $tdSalesProfit = '';
                $grandTotalSalesProfit = 0;

                $tdCharge = '';
                $grandTotalCharge = 0;

                $tdExpensesRecorded = '';
                $grandTotalExpensesRecorded = 0;

                $tdBusinessProfit = '';
                $grandTotalBusinessProfit = 0;

                $tdOtherIncome = '';
                $grandTotalOtherIncome = 0;

                $tdOtherExpenses = '';
                $grandTotalOtherExpenses = 0;

                $tdProfit = '';
                $grandTotalProfit = 0;

                $tdGTGT = '';
                $grandTotalGTGT = 0;

                if (!empty($year)) {
                    foreach ($year as $key => $y) {
                        if (!empty($month)) {
                            foreach ($month as $k => $m) {
                                if (!empty($arrBranch)) {
                                    foreach ($arrBranch as $b => $bra) {
                                        $branch_id = $bra['id'];
                                        $thHead.= '<th class="text-right"><b>'.$bra['name'].'.T'.$m.'.'.$y.'</b></th>';
                                        $dtDate = getDateReportSales($y, false, $m);

                                        $getTotalSales = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        //total sales : 1
                                        $totalsales = !empty($getTotalSales['grand_total']) ? $getTotalSales['grand_total'] : 0;
                                        $tdSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalsales).'</a></td>';
                                        $grandTotalSales+= $totalsales;

                                        //returns sales : 2.2
                                        $getReturnsSales = $this->site_model->getReturnsSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalReturnSales = !empty($getReturnsSales['grand_total']) ? $getReturnsSales['grand_total'] : 0;
                                        // $tdReturnSales.= '<td class="text-right">'.formatMoney($totalReturnSales).'</td>';
                                        $tdReturnSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="hang_tra_ve" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalReturnSales).'</a></td>';
                                        $grandReturnSales+= $totalReturnSales;

                                        //discount : 2.1
                                        $totalDiscount = !empty($getTotalSales['total_discount']) ? $getTotalSales['total_discount'] : 0;
                                        $tdDiscountSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chiet_khau_hoa_don" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalDiscount).'</a></td>';
                                        $grandDiscountSales+= $totalDiscount;

                                        //reduce sales : 2
                                        $totalReduceSales = $totalReturnSales + $totalDiscount;
                                        $tdReduceSales.= '<td class="text-right">'.formatMoney($totalReduceSales).'</td>';
                                        $grandTotalReduceSales+= $totalReduceSales;

                                        //net sales : 3 = 1 - 2
                                        $totalNetSales = $totalsales - $totalReduceSales;
                                        $tdNetSales.= '<td class="text-right">'.formatMoney($totalNetSales).'</td>';
                                        $grandTotalNetSales+= $totalNetSales;

                                        //cost price : 4
                                        $getCostPriceSales = $this->site_model->getCostPriceSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalCostPrice = !empty($getCostPriceSales['cost_price']) ? $getCostPriceSales['cost_price'] : 0;
                                        // $tdCostPrice.= '<td class="text-right">'.formatMoney($totalCostPrice).'</td>';
                                        $tdCostPrice.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gia_von_hang_ban" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCostPrice).'</a></td>';
                                        $grandTotalCostPrice+= $totalCostPrice;

                                        //sales profit : 5 = 3 - 4
                                        $totalSalesProfit = $totalNetSales - $totalCostPrice;
                                        $tdSalesProfit.= '<td class="text-right">'.formatMoney($totalSalesProfit).'</td>';
                                        $grandTotalSalesProfit+= $totalSalesProfit;

                                        //charge : 6
                                        $getCharge = $this->site_model->getCharge($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalCharge = $getCharge;
                                        // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                        $tdCharge.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCharge).'</a></td>';
                                        $grandTotalCharge+= $totalCharge;

                                        //sevices : 6.1
                                        $getTotalService = $this->site_model->getTotalService($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalExpensesRecorded = $getTotalService['grand_total'];
                                        // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                        $tdExpensesRecorded.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi_ghi_nhan" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalExpensesRecorded).'</a></td>';
                                        $grandTotalExpensesRecorded+= $totalExpensesRecorded;

                                        // business profit : 7 = 5 - 6 - 6.1
                                        $totalBusinessProfit = $totalSalesProfit - $totalCharge - $totalExpensesRecorded;
                                        $tdBusinessProfit.= '<td class="text-right">'.formatMoney($totalBusinessProfit).'</td>';
                                        $grandTotalBusinessProfit+= $totalBusinessProfit;

                                        //other income : 8
                                        $getOtherPayslipsCoupon = $this->site_model->getOtherPayslipsCoupon($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalOtherIncome = $getOtherPayslipsCoupon['total'];
                                        // $tdOtherIncome.= '<td class="text-right">'.formatMoney($totalOtherIncome).'</td>';
                                        $tdOtherIncome.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="thu_nhap_khac" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalOtherIncome).'</a></td>';
                                        $grandTotalOtherIncome+= $totalOtherIncome;

                                        //GTGT: 9
                                        $totalGTGT = $getTotalSales['grand_total_tax'];
                                        $tdGTGT.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gtgt" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalGTGT).'</a></td>';
                                        $grandTotalGTGT+= $totalGTGT;

                                        //profit : 10 = 7 + 8
                                        $totalProfit = $totalBusinessProfit + $totalOtherIncome + $totalGTGT;
                                        $tdProfit.= '<td class="text-right">'.formatMoney($totalProfit).'</td>';
                                        $grandTotalProfit+= $totalProfit;
                                    }
                                } else {
                                    $thHead.= '<th class="text-right"><b>T'.$m.'.'.$y.'</b></th>';
                                    $dtDate = getDateReportSales($y, false, $m);

                                    $getTotalSales = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date']);
                                    //total sales : 1
                                    $totalsales = !empty($getTotalSales['grand_total']) ? $getTotalSales['grand_total'] : 0;
                                    $tdSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalsales).'</a></td>';
                                    $grandTotalSales+= $totalsales;

                                    if (!empty($dtBranch)) {
                                        foreach ($dtBranch as $kk => $vv) {
                                            $branch_id = $vv['id'];
                                            $getTotalSalesBranch = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                            $totalSalesBranch = !empty($getTotalSalesBranch['grand_total']) ? $getTotalSalesBranch['grand_total'] : 0;
                                            $tdSalesBranch = '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalSalesBranch).'</a></td>';

                                            $dtBranch[$kk]['totalSalesBranch'] = $dtBranch[$kk]['totalSalesBranch'] + $totalSalesBranch;
                                            $dtBranch[$kk]['tdSalesBranch'] = $dtBranch[$kk]['tdSalesBranch'].$tdSalesBranch;
                                        }
                                    }

                                    //returns sales : 2.2
                                    $getReturnsSales = $this->site_model->getReturnsSales($dtDate['start_date'], $dtDate['end_date']);
                                    $totalReturnSales = !empty($getReturnsSales['grand_total']) ? $getReturnsSales['grand_total'] : 0;
                                    // $tdReturnSales.= '<td class="text-right">'.formatMoney($totalReturnSales).'</td>';
                                    $tdReturnSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="hang_tra_ve" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalReturnSales).'</a></td>';
                                    $grandReturnSales+= $totalReturnSales;

                                    //discount : 2.1
                                    $totalDiscount = !empty($getTotalSales['total_discount']) ? $getTotalSales['total_discount'] : 0;
                                    $tdDiscountSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chiet_khau_hoa_don" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalDiscount).'</a></td>';
                                    $grandDiscountSales+= $totalDiscount;

                                    //reduce sales : 2
                                    $totalReduceSales = $totalReturnSales + $totalDiscount;
                                    $tdReduceSales.= '<td class="text-right">'.formatMoney($totalReduceSales).'</td>';
                                    $grandTotalReduceSales+= $totalReduceSales;

                                    //net sales : 3 = 1 - 2
                                    $totalNetSales = $totalsales - $totalReduceSales;
                                    $tdNetSales.= '<td class="text-right">'.formatMoney($totalNetSales).'</td>';
                                    $grandTotalNetSales+= $totalNetSales;

                                    //cost price : 4
                                    $getCostPriceSales = $this->site_model->getCostPriceSales($dtDate['start_date'], $dtDate['end_date']);
                                    $totalCostPrice = !empty($getCostPriceSales['cost_price']) ? $getCostPriceSales['cost_price'] : 0;
                                    // $tdCostPrice.= '<td class="text-right">'.formatMoney($totalCostPrice).'</td>';
                                    $tdCostPrice.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gia_von_hang_ban" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCostPrice).'</a></td>';
                                    $grandTotalCostPrice+= $totalCostPrice;

                                    //sales profit : 5 = 3 - 4
                                    $totalSalesProfit = $totalNetSales - $totalCostPrice;
                                    $tdSalesProfit.= '<td class="text-right">'.formatMoney($totalSalesProfit).'</td>';
                                    $grandTotalSalesProfit+= $totalSalesProfit;

                                    //charge : 6
                                    $getCharge = $this->site_model->getCharge($dtDate['start_date'], $dtDate['end_date']);
                                    $totalCharge = $getCharge;
                                    // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                    $tdCharge.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCharge).'</a></td>';
                                    $grandTotalCharge+= $totalCharge;

                                    //sevices : 6.1
                                    $getTotalService = $this->site_model->getTotalService($dtDate['start_date'], $dtDate['end_date']);
                                    $totalExpensesRecorded = $getTotalService['grand_total'];
                                    // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                    $tdExpensesRecorded.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi_ghi_nhan" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalExpensesRecorded).'</a></td>';
                                    $grandTotalExpensesRecorded+= $totalExpensesRecorded;

                                    // business profit : 7 = 5 - 6
                                    $totalBusinessProfit = $totalSalesProfit - $totalCharge - $totalExpensesRecorded;
                                    $tdBusinessProfit.= '<td class="text-right">'.formatMoney($totalBusinessProfit).'</td>';
                                    $grandTotalBusinessProfit+= $totalBusinessProfit;

                                    //other income : 8
                                    $getOtherPayslipsCoupon = $this->site_model->getOtherPayslipsCoupon($dtDate['start_date'], $dtDate['end_date']);
                                    $totalOtherIncome = $getOtherPayslipsCoupon['total'];
                                    // $tdOtherIncome.= '<td class="text-right">'.formatMoney($totalOtherIncome).'</td>';
                                    $tdOtherIncome.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="thu_nhap_khac" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalOtherIncome).'</a></td>';
                                    $grandTotalOtherIncome+= $totalOtherIncome;

                                    //GTGT: 9
                                    $totalGTGT = $getTotalSales['grand_total_tax'];
                                    $tdGTGT.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gtgt" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalGTGT).'</a></td>';
                                    $grandTotalGTGT+= $totalGTGT;

                                    //profit : 10 = 7 + 8
                                    $totalProfit = $totalBusinessProfit + $totalOtherIncome + $totalGTGT;
                                    $tdProfit.= '<td class="text-right">'.formatMoney($totalProfit).'</td>';
                                    $grandTotalProfit+= $totalProfit;
                                }
                            }
                        } else {
                            if (!empty($arrBranch)) {
                                foreach ($arrBranch as $b => $bra) {
                                    $branch_id = $bra['id'];
                                    //branch
                                    $thHead.= '<th class="text-right"><b>'.$bra['name'].''.$y.'</b></th>';
                                    $dtDate = getDateReportSales($y);

                                    $getTotalSales = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    //total sales
                                    $totalsales = !empty($getTotalSales['grand_total']) ? $getTotalSales['grand_total'] : 0;
                                    // $tdSales.= '<td class="text-right">'.formatMoney($totalsales).'</td>';
                                    $tdSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalsales).'</a></td>';
                                    $grandTotalSales+= $totalsales;

                                    

                                    //returns sales
                                    $getReturnsSales = $this->site_model->getReturnsSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    $totalReturnSales = !empty($getReturnsSales['grand_total']) ? $getReturnsSales['grand_total'] : 0;
                                    // $tdReturnSales.= '<td class="text-right">'.formatMoney($totalReturnSales).'</td>';
                                    $tdReturnSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="hang_tra_ve" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalReturnSales).'</a></td>';
                                    $grandReturnSales+= $totalReturnSales;

                                    //discount
                                    $totalDiscount = !empty($getTotalSales['total_discount']) ? $getTotalSales['total_discount'] : 0;
                                    // $tdDiscountSales.= '<td class="text-right">'.formatMoney($totalDiscount).'</td>';
                                    $tdDiscountSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chiet_khau_hoa_don" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalDiscount).'</a></td>';
                                    $grandDiscountSales+= $totalDiscount;

                                    //reduce sales
                                    $totalReduceSales = $totalReturnSales + $totalDiscount;
                                    $tdReduceSales.= '<td class="text-right">'.formatMoney($totalReduceSales).'</td>';
                                    $grandTotalReduceSales = $totalReduceSales;

                                    //net sales 3 = 1 - 2
                                    $totalNetSales = $totalsales - $totalReduceSales;
                                    $tdNetSales.= '<td class="text-right">'.formatMoney($totalNetSales).'</td>';
                                    $grandTotalNetSales+= $totalNetSales;

                                    //cost price : 4
                                    $getCostPriceSales = $this->site_model->getCostPriceSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    $totalCostPrice = !empty($getCostPriceSales['cost_price']) ? $getCostPriceSales['cost_price'] : 0;
                                    // $tdCostPrice.= '<td class="text-right">'.formatMoney($totalCostPrice).'</td>';
                                    $tdCostPrice.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gia_von_hang_ban" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCostPrice).'</a></td>';
                                    $grandTotalCostPrice+= $totalCostPrice;

                                    //sales profit : 5 = 3 - 4
                                    $totalSalesProfit = $totalNetSales - $totalCostPrice;
                                    $tdSalesProfit.= '<td class="text-right">'.formatMoney($totalSalesProfit).'</td>';
                                    $grandTotalSalesProfit+= $totalSalesProfit;

                                    //charge : 6
                                    $getCharge = $this->site_model->getCharge($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    $totalCharge = $getCharge;
                                    // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                    $tdCharge.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCharge).'</a></td>';
                                    $grandTotalCharge+= $totalCharge;

                                    //sevices : 6.1
                                    $getTotalService = $this->site_model->getTotalService($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    $totalExpensesRecorded = $getTotalService['grand_total'];
                                    // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                    $tdExpensesRecorded.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi_ghi_nhan" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalExpensesRecorded).'</a></td>';
                                    $grandTotalExpensesRecorded+= $totalExpensesRecorded;

                                    // business profit : 7 = 5 - 6
                                    $totalBusinessProfit = $totalSalesProfit - $totalCharge - $totalExpensesRecorded;
                                    $tdBusinessProfit.= '<td class="text-right">'.formatMoney($totalBusinessProfit).'</td>';
                                    $grandTotalBusinessProfit+= $totalBusinessProfit;

                                    //other income : 8
                                    $getOtherPayslipsCoupon = $this->site_model->getOtherPayslipsCoupon($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                    $totalOtherIncome = $getOtherPayslipsCoupon['total'];
                                    // $tdOtherIncome.= '<td class="text-right">'.formatMoney($totalOtherIncome).'</td>';
                                    $tdOtherIncome.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="thu_nhap_khac" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalOtherIncome).'</a></td>';
                                    $grandTotalOtherIncome+= $totalOtherIncome;

                                    //GTGT: 9
                                    $totalGTGT = $getTotalSales['grand_total_tax'];
                                    $tdGTGT.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gtgt" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalGTGT).'</a></td>';
                                    $grandTotalGTGT+= $totalGTGT;

                                    //profit : 10 = 7 + 8 + 9
                                    $totalProfit = $totalBusinessProfit + $totalOtherIncome + $totalGTGT;
                                    $tdProfit.= '<td class="text-right">'.formatMoney($totalProfit).'</td>';
                                    $grandTotalProfit+= $totalProfit;
                                }
                            } else {
                                $thHead.= '<th class="text-right"><b>'.$y.'</b></th>';
                                $dtDate = getDateReportSales($y);

                                $getTotalSales = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date']);
                                //total sales
                                $totalsales = !empty($getTotalSales['grand_total']) ? $getTotalSales['grand_total'] : 0;
                                // $tdSales.= '<td class="text-right">'.formatMoney($totalsales).'</td>';
                                $tdSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalsales).'</a></td>';
                                $grandTotalSales+= $totalsales;

                                if (!empty($dtBranch)) {
                                    foreach ($dtBranch as $kk => $vv) {
                                        $branch_id = $vv['id'];
                                        $getTotalSalesBranch = $this->site_model->getTotalSales($dtDate['start_date'], $dtDate['end_date'], $branch_id);
                                        $totalSalesBranch = !empty($getTotalSalesBranch['grand_total']) ? $getTotalSalesBranch['grand_total'] : 0;
                                        $tdSalesBranch = '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="doanh_thu_ban_hang" id_branch="'.$branch_id.'" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalSalesBranch).'</a></td>';

                                        $dtBranch[$kk]['totalSalesBranch'] = $dtBranch[$kk]['totalSalesBranch']+ $totalSalesBranch;
                                        $dtBranch[$kk]['tdSalesBranch'] = $dtBranch[$kk]['tdSalesBranch'].$tdSalesBranch;
                                    }
                                }

                                //returns sales
                                $getReturnsSales = $this->site_model->getReturnsSales($dtDate['start_date'], $dtDate['end_date']);
                                $totalReturnSales = !empty($getReturnsSales['grand_total']) ? $getReturnsSales['grand_total'] : 0;
                                // $tdReturnSales.= '<td class="text-right">'.formatMoney($totalReturnSales).'</td>';
                                $tdReturnSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="hang_tra_ve" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalReturnSales).'</a></td>';
                                $grandReturnSales+= $totalReturnSales;

                                //discount
                                $totalDiscount = !empty($getTotalSales['total_discount']) ? $getTotalSales['total_discount'] : 0;
                                // $tdDiscountSales.= '<td class="text-right">'.formatMoney($totalDiscount).'</td>';
                                $tdDiscountSales.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chiet_khau_hoa_don" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalDiscount).'</a></td>';
                                $grandDiscountSales+= $totalDiscount;

                                //reduce sales
                                $totalReduceSales = $totalReturnSales + $totalDiscount;
                                $tdReduceSales.= '<td class="text-right">'.formatMoney($totalReduceSales).'</td>';
                                $grandTotalReduceSales = $totalReduceSales;

                                //net sales 3 = 1 - 2
                                $totalNetSales = $totalsales - $totalReduceSales;
                                $tdNetSales.= '<td class="text-right">'.formatMoney($totalNetSales).'</td>';
                                $grandTotalNetSales+= $totalNetSales;

                                //cost price : 4
                                $getCostPriceSales = $this->site_model->getCostPriceSales($dtDate['start_date'], $dtDate['end_date']);
                                $totalCostPrice = !empty($getCostPriceSales['cost_price']) ? $getCostPriceSales['cost_price'] : 0;
                                // $tdCostPrice.= '<td class="text-right">'.formatMoney($totalCostPrice).'</td>';
                                $tdCostPrice.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gia_von_hang_ban" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCostPrice).'</a></td>';
                                $grandTotalCostPrice+= $totalCostPrice;

                                //sales profit : 5 = 3 - 4
                                $totalSalesProfit = $totalNetSales - $totalCostPrice;
                                $tdSalesProfit.= '<td class="text-right">'.formatMoney($totalSalesProfit).'</td>';
                                $grandTotalSalesProfit+= $totalSalesProfit;

                                //charge : 6
                                $getCharge = $this->site_model->getCharge($dtDate['start_date'], $dtDate['end_date']);
                                $totalCharge = $getCharge;
                                // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                $tdCharge.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalCharge).'</a></td>';
                                $grandTotalCharge+= $totalCharge;

                                //sevices : 6.1
                                $getTotalService = $this->site_model->getTotalService($dtDate['start_date'], $dtDate['end_date']);
                                $totalExpensesRecorded = $getTotalService['grand_total'];
                                // $tdCharge.= '<td class="text-right">'.formatMoney($totalCharge).'</td>';
                                $tdExpensesRecorded.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="chi_phi_ghi_nhan" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalExpensesRecorded).'</a></td>';
                                $grandTotalExpensesRecorded+= $totalExpensesRecorded;

                                // business profit : 7 = 5 - 6
                                $totalBusinessProfit = $totalSalesProfit - $totalCharge - $totalExpensesRecorded;
                                $tdBusinessProfit.= '<td class="text-right">'.formatMoney($totalBusinessProfit).'</td>';
                                $grandTotalBusinessProfit+= $totalBusinessProfit;

                                //other income : 8
                                $getOtherPayslipsCoupon = $this->site_model->getOtherPayslipsCoupon($dtDate['start_date'], $dtDate['end_date']);
                                $totalOtherIncome = $getOtherPayslipsCoupon['total'];
                                // $tdOtherIncome.= '<td class="text-right">'.formatMoney($totalOtherIncome).'</td>';
                                $tdOtherIncome.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="thu_nhap_khac" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalOtherIncome).'</a></td>';
                                $grandTotalOtherIncome+= $totalOtherIncome;

                                //GTGT: 9
                                $totalGTGT = $getTotalSales['grand_total_tax'];
                                $tdGTGT.= '<td class="text-right"><a class="tnh-modal-attr" start_date="'.$dtDate['start_date'].'" end_date="'.$dtDate['end_date'].'" type_object="gtgt" id_branch="0" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/reports/infoSalePerformance').'">'.formatMoney($totalGTGT).'</a></td>';
                                $grandTotalGTGT+= $totalGTGT;

                                //profit : 10 = 7 + 8 + 9
                                $totalProfit = $totalBusinessProfit + $totalOtherIncome + $totalGTGT;
                                $tdProfit.= '<td class="text-right">'.formatMoney($totalProfit).'</td>';
                                $grandTotalProfit+= $totalProfit;
                            }
                        }
                    }
                }

                $thHead.= '<th class="text-right">'.lang('tnh_grand_total').'</th>';

                $tdSales.= '<td class="text-right">'.formatMoney($grandTotalSales).'</td>';

                $tdReduceSales.= '<td class="text-right">'.formatMoney($grandTotalReduceSales).'</td>';

                $tdReturnSales.= '<td class="text-right">'.formatMoney($grandReturnSales).'</td>';

                $tdDiscountSales.= '<td class="text-right">'.formatMoney($grandDiscountSales).'</td>';

                $tdNetSales.= '<td class="text-right">'.formatMoney($grandTotalNetSales).'</td>';

                $tdCostPrice.= '<td class="text-right">'.formatMoney($grandTotalCostPrice).'</td>';

                $tdSalesProfit.= '<td class="text-right">'.formatMoney($grandTotalSalesProfit).'</td>';

                $tdCharge.= '<td class="text-right">'.formatMoney($grandTotalCharge).'</td>';

                $tdExpensesRecorded.= '<td class="text-right">'.formatMoney($grandTotalExpensesRecorded).'</td>';

                $tdBusinessProfit.= '<td class="text-right">'.formatMoney($grandTotalBusinessProfit).'</td>';

                $tdOtherIncome.= '<td class="text-right">'.formatMoney($grandTotalOtherIncome).'</td>';

                $tdGTGT.= '<td class="text-right">'.formatMoney($grandTotalGTGT).'</td>';

                $tdProfit.= '<td class="text-right">'.formatMoney($grandTotalProfit).'</td>';

                echo $thHead;
            ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Doanh thu bán hàng (1)</td>
            <?= $tdSales ?>
        </tr>
        <?php if (!empty($dtBranch)): ?>
            <?php foreach($dtBranch as $key => $value): ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $value['name'] ?>(1.<?= $key + 1 ?>)</td>
                    <?= $value['tdSalesBranch'] ?>
                    <td class="text-right"><?= formatMoney($value['totalSalesBranch']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td>Giảm trừ Doanh thu (2 = 2.1+2.2)</td>
            <?= $tdReduceSales ?>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chiết khấu hóa đơn (2.1)</td>
            <?= $tdDiscountSales ?>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Giá trị hàng bán bị trả lại (2.2)</td>
            <?= $tdReturnSales ?>
        </tr>
        <tr>
            <td>Doanh thu thuần (3=1-2)</td>
            <?= $tdNetSales ?>
        </tr>
        <tr>
            <td>Giá vốn hàng bán (4)</td>
            <?= $tdCostPrice ?>
        </tr>
        <tr>
            <td>Lợi nhuận gộp về bán hàng (5=3-4)</td>
            <?= $tdSalesProfit ?>
        </tr>
        <tr>
            <td>Chi phí (6)</td>
            <?= $tdCharge ?>
        </tr>
        <tr>
            <td>Chi phí ghi nhận (6.1)</td>
            <?= $tdExpensesRecorded ?>
        </tr>
        <tr>
            <td>Lợi nhuận từ hoạt động kinh doanh(7=5-6-6.1)</td>
            <?= $tdBusinessProfit ?>
        </tr>
        <tr>
            <td>Thu nhập khác (8)</td>
            <?= $tdOtherIncome ?>
        </tr>
        <tr>
            <td>Thuế GTGT đầu ra (9)</td>
            <?= $tdGTGT ?>
        </tr>
        <tr>
            <td>Lợi nhuận thuần (10=(7+8+9))</td>
            <?= $tdProfit ?>
        </tr>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        $('#tb-sale-performance').DataTable({
            "language": lang_datatables,
            'searching': false,
            'ordering': false,
            'paging': false,
            "info": false,
            'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
            buttons: [
                // 'copy', 'excel', 'csv', 'pdf',
                {
                    text: 'Excel',
                    title: '<?= $title ?>',
                    // extend: 'excelHtml5',
                    // autoFilter: true,
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    // customize: function ( xlsx ){
                    //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    //     $('row c', sheet).attr( 's', '25' );
                    // }
                },
            ],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            }
        });
    });
</script>