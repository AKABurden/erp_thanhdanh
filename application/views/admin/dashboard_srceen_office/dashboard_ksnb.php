<style>
    .box .value_ksnb{
        font-size: 45px !important;
        cursor: pointer;
    }
    .box .label_ksnb{
        font-size: 20px !important;
    }

    .box_vs1 .value_ksnb{
        font-size: 35px !important;
        color: #cd8c37;
        cursor: pointer;
    }
    .box_vs1 .label_ksnb{
        font-size: 25px !important;
        min-height: 50px;
    }

</style>
<div id="dashboard_ksnb" class="dashboard-ksnb hide">
    <div class="page_ksnb page_ksnb_1">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_ksnb">TỔNG ĐỀ XUẤT NỘI BỘ</div>
                        <div class="value value_ksnb count_all_internal" onclick="detailKsnbInternal(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">TỔNG BÁO CÁO KHÔNG PHÙ HỢP</div>
                        <div class="value value_ksnb count_all_production" onclick="detailKsnbKPH(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">TỔNG BÁO CÁO VI PHẠM</div>
                        <div class="value value_ksnb count_all_vi_pham" onclick="detailKsnbKPH(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">TỔNG PHIẾU YÊU CẦU TĂNG CA</div>
                        <div class="value value_ksnb count_all_overtime" onclick="detailKsnbOvertime(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">TỔNG PHIẾU KẾ HOẠCH GIA CÔNG</div>
                        <div class="value value_ksnb count_all_outsource" onclick="detailKsnbOutsource(this,1)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_ksnb red">ĐỀ XUẤT NỘI BỘ CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_internal" onclick="detailKsnbInternal(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb red">BÁO CÁO KHÔNG PHÙ HỢP CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_production" onclick="detailKsnbKPH(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb red">BÁO CÁO VI PHẠM CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_vi_pham" onclick="detailKsnbKPH(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb red">PHIẾU YÊU CẦU TĂNG CA CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_overtime" onclick="detailKsnbOvertime(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb red">PHIẾU KẾ HOẠCH GIA CÔNG CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_outsource" onclick="detailKsnbOutsource(this,2)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_ksnb">ĐỀ XUẤT NỘI BỘ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_internal" onclick="detailKsnbInternal(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">BÁO CÁO KHÔNG PHÙ HỢP HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_production" onclick="detailKsnbKPH(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">BÁO CÁO VI PHẠM HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_vi_pham" onclick="detailKsnbKPH(this,6)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">PHIẾU YÊU CẦU TĂNG CA ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_overtime" onclick="detailKsnbOvertime(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_ksnb">PHIẾU KẾ HOẠCH GIA CÔNG ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_outsource" onclick="detailKsnbOutsource(this,3)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 66px">
                    <div class="box">
                        <div class="label label_ksnb">KPI DƯỚI 100 ĐIỂM</div>
                        <div class="value value_ksnb count_staff_kpi">-</div>
                    </div>
                    <div id="container_ksnb" style="margin-top: 50px"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_ksnb page_ksnb_2">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);margin:20px 0;width: 100%">
               <div class="box_vs1_wrap">
                   <?php if(!empty($categoryStage)){ ?>
                       <?php foreach ($categoryStage as $key => $value){ ?>
                           <div class="box_vs1">
                               <div class="label label_ksnb"><?= $value['name'] ?></div>
                               <div class="value value_ksnb total_<?= $value['id'] ?>" onclick="detailKsnbNangSuat(this,<?= $value['id'] ?>)">-</div>
                           </div>
                       <?php } ?>
                   <?php } ?>
               </div>
            </section>
        </div>
    </div>
    <div class="page_ksnb page_ksnb_3">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 33.33%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_new red">TỔNG SỐ NHÂN VIÊN CHƯA CHECK IN</div>
                        <div class="value value_new red count_not_checkin_ksnb" onclick="detailHcns(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new red">TỔNG SỐ NHÂN VIÊN CHƯA CHECK OUT</div>
                        <div class="value value_new red count_not_checkout_ksnb" onclick="detailHcns(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN ĐÃ CHECK IN</div>
                        <div class="value value_new count_checkin_ksnb" onclick="detailHcns(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN ĐÃ CHECK OUT</div>
                        <div class="value value_new count_checkout_ksnb" onclick="detailHcns(this,4)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 33.33%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ NHÂN VIÊN TĂNG CA</div>
                        <div class="value value_new green count_overtime_ksnb" onclick="detailHcns(this,6)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ ĐÁNH GIÁ CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new count_evaluate_ksnb" onclick="detailEvaluateHcns(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ CHỨNG NHẬN CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new count_certification_ksnb" onclick="detailEvaluateHcns(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div class="label label_new">TỔNG SỐ CHỨNG CHỈ CẦN TÁI ĐÁNH GIÁ</div>
                        <div class="value value_new count_certificate_ksnb" onclick="detailEvaluateHcns(this,4)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div id="container_cham_cong_ksnb"></div>
                    <div id="container_evaluate_ksnb"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_ksnb page_ksnb_4">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 16.66%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG SỐ ĐÁNH GIÁ THIẾT BỊ</div>
                        <div class="value value_ksnb count_all_rating_ksnb" onclick="detailModalTechnialRatingMachines(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG SỐ BẢO DƯỠNG MÁY MÓC THIẾT BỊ</div>
                        <div class="value value_ksnb count_all_maintenance_ksnb" onclick="detailModalTechnialMaintenance(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG SỐ SỬA CHỮA MÁY MÓC THIẾT BỊ</div>
                        <div class="value value_ksnb count_all_repair_ksnb" onclick="detailModalTechnialRepair(this,1)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial red">ĐÁNH GIÁ THIẾT BỊ CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_rating_ksnb" onclick="detailModalTechnialRatingMachines(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial red">BẢO DƯỠNG MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_maintenance_ksnb" onclick="detailModalTechnialMaintenance(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_repair_ksnb" onclick="detailModalTechnialRepair(this,2)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">ĐÁNH GIÁ THIẾT BỊ ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_rating_ksnb" onclick="detailModalTechnialRatingMachines(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">BẢO DƯỠNG MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_maintenance_ksnb" onclick="detailModalTechnialMaintenance(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">SỬA CHỮA MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_repair_ksnb" onclick="detailModalTechnialRepair(this,3)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial red">ĐÁNH GIÁ THIẾT BỊ CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_finish_rating_ksnb" onclick="detailModalTechnialRatingMachines(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial red">BẢO DƯỠNG THIẾT BỊ CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_finish_maintenance_ksnb" onclick="detailModalTechnialMaintenance(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_finish_repair_ksnb" onclick="detailModalTechnialRepair(this,4)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 16.66%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">ĐÁNH GIÁ THIẾT BỊ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_finish_rating_ksnb" onclick="detailModalTechnialRatingMachines(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">BẢO DƯỠNG THIẾT BỊ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_finish_maintenance_ksnb" onclick="detailModalTechnialMaintenance(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">SỬA CHỮA MÁY MÓC THIẾT BỊ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_finish_repair_ksnb" onclick="detailModalTechnialRepair(this,5)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 16%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 66px">
                    <div class="box">
                        <div class="label label_technial">TỔNG SỐ LỆNH CÓ PHẾ</div>
                        <div class="value value_ksnb total_production_items_errors_ksnb" onclick="detailQaProductionError(this,1)">-</div>
                    </div>
                    <div id="container_technial_ksnb" style="margin-top: 50px"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_ksnb page_ksnb_5">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 210px);width: 25%;margin:20px 0; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG KẾ HOẠCH KIỂM KÊ</div>
                        <div class="value value_ksnb count_inventory_ksnb" onclick="detailInventoryKsnb(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG MÃ NPL TỒN KHO</div>
                        <div class="value value_ksnb count_all_nvl_ksnb" onclick="detailWarehouseKsnb(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG MÃ BTP TỒN KHO</div>
                        <div class="value value_ksnb count_all_btp_ksnb" onclick="detailWarehouseKsnb(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG MÃ TP TỒN KHO</div>
                        <div class="value value_ksnb count_all_tp_ksnb" onclick="detailWarehouseKsnb(this,7)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width:25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG KẾ HOẠCH KIỂM TOÁN NỘI BỘ</div>
                        <div class="value value_ksnb count_internal_proposal_de_xuat_ksnb" onclick="detailKsnbInternalDeXuat(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">NPL TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                        <div class="value value_ksnb count_6_nvl_ksnb" onclick="detailWarehouseKsnb(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">BTP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                        <div class="value value_ksnb count_6_btp_ksnb" onclick="detailWarehouseKsnb(this,5)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TP TỒN KHO QUÁ HẠN TRÊN 6 THÁNG</div>
                        <div class="value value_ksnb count_6_tp_ksnb" onclick="detailWarehouseKsnb(this,8)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 25%; float: left;margin:20px 0">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label label_technial">TỔNG BCKPH CÓ LỖI</div>
                        <div class="value value_ksnb count_error_production" onclick="detailKsnbProductionError(this,1)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">NPL TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                        <div class="value value_ksnb count_12_nvl_ksnb" onclick="detailWarehouseKsnb(this,3)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">BTR TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                        <div class="value value_ksnb count_12_btp_ksnb" onclick="detailWarehouseKsnb(this,6)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label label_technial">TP TỒN KHO QUÁ HẠN TRÊN 12 THÁNG</div>
                        <div class="value value_ksnb count_12_tp_ksnb" onclick="detailWarehouseKsnb(this,9)">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 210px);width: 24.5%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;margin-top: 20px">
                    <div class="box">
                        <div class="label label_technial">TỔNG BC VI PHẠM CÓ LỖI</div>
                        <div class="value value_ksnb count_error_vi_pham" onclick="detailKsnbProductionError(this,2)">-</div>
                    </div>
                    <div id="container_inventoy" style="margin-top: 10px"></div>
                    <div id="container_warehouse" style="margin-top: 10px"></div>
                </div>
            </section>
        </div>
    </div>
    <div class="sub-menu">
        <div class="sub-menu-child-new active child-ksnb" data-value="1" onclick="changeFilterKsnb(this,1)">Trang 1 (d)</div>
        <div class="sub-menu-child-new child-ksnb" data-value="3" onclick="changeFilterKsnb(this,3)">Trang 2 (f)</div>
        <div class="sub-menu-child-new child-ksnb" data-value="4" onclick="changeFilterKsnb(this,4)">Trang 3 (g)</div>
        <div class="sub-menu-child-new child-ksnb" data-value="5" onclick="changeFilterKsnb(this,5)">Trang 4 (h)</div>
        <div class="sub-menu-child-new child-ksnb" data-value="2" onclick="changeFilterKsnb(this,2)">Năng suất (j)</div>
    </div>
</div>
<script>
    function detailKsnbInternal(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_internal/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }
    function detailKsnbKPH(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_kph/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailKsnbOvertime(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_overtime/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailKsnbOutsource(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_outsource/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailInventoryKsnb(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_inventory/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailKsnbInternalDeXuat(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_internal_de_xuat/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailKsnbProductionError(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_production_error/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailWarehouseKsnb(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_warehouse/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function detailKsnbNangSuat(_this,type = 1){
        value = getNumberFromText($(_this));
        if (value == 0) return;
        var $btn = $(this);
        $btn.addClass('loading');
        $.ajax({
            url: `<?= base_url('dashboard_srceen_office/modal_detail_ksnb_nang_suat/') ?>${type}`,
            type: 'GET',
            success: function(html) {
                $('#chModal_dashboard').html(html);
                openModal('chModal_dashboard');
            },
            complete: function() {
                $btn.removeClass('loading');
            }
        });
    }

    function changeFilterKsnb(_this,id){
        $('.child-ksnb').removeClass('active');
        $(_this).addClass('active');

        $('.page_ksnb').addClass('hide');
        $(`.page_ksnb_${id}`).removeClass('hide');
    }
    function getNumberFromText(selector) {
        let text = $(selector).text().trim();
        return text === '-' ? 0 : parseFloat(text) || 0;
    }
    function count_ksnb() {
        dataChartChamCong = [];
        dataChartEvaluate = [];
        dataChartKsnb = [];
        dataChartTechnialKsnb = [];
        dataChartInventory = [];
        let count_not_checkin_ksnb_old = getNumberFromText('.count_not_checkin_ksnb');
        let count_not_checkout_ksnb_old = getNumberFromText('.count_not_checkout_ksnb');
        let count_checkin_ksnb_old = getNumberFromText('.count_checkin_ksnb');
        let count_checkout_ksnb_old = getNumberFromText('.count_checkout_ksnb');
        let count_overtime_ksnb_old = getNumberFromText('.count_overtime_ksnb');
        let count_evaluate_ksnb_old = getNumberFromText('.count_evaluate_ksnb');
        let count_certification_ksnb_old = getNumberFromText('.count_certification_ksnb');
        let count_certificate_ksnb_old = getNumberFromText('.count_certificate_ksnb');

        let count_inventory_ksnb_old = getNumberFromText('.count_inventory_ksnb');
        let count_internal_proposal_de_xuat_ksnb_old = getNumberFromText('.count_internal_proposal_de_xuat_ksnb');
        let count_error_vi_pham_old = getNumberFromText('.count_error_vi_pham');
        let count_error_production_old = getNumberFromText('.count_error_production');


        let count_all_nvl_ksnb_old = getNumberFromText('.count_all_nvl_ksnb');
        let count_all_btp_ksnb_old = getNumberFromText('.count_all_btp_ksnb');
        let count_all_tp_ksnb_old = getNumberFromText('.count_all_tp_ksnb');
        let count_6_nvl_ksnb_old = getNumberFromText('.count_6_nvl_ksnb');
        let count_6_btp_ksnb_old = getNumberFromText('.count_6_btp_ksnb');
        let count_6_tp_ksnb_old = getNumberFromText('.count_6_tp_ksnb');
        let count_12_nvl_ksnb_old = getNumberFromText('.count_12_nvl_ksnb');
        let count_12_btp_ksnb_old = getNumberFromText('.count_12_btp_ksnb');
        let count_12_tp_ksnb_old = getNumberFromText('.count_12_tp_ksnb');

        if ($('#dashboard-ksnb').hasClass('active')) {
            $.getJSON("<?= site_url('dashboard_srceen_office/count_ksnb') ?>", res => {
                dataChartKsnb = [];
                dataChartChamCong = [];
                dataChartEvaluate = [];
                dataChartTechnialKsnb = [];
                dataChartInventory = [];
                dataChartWarehouse = [];
                if (!res || !res.success) return;
                $('.count_all_internal').text(mainFmt(mainNum(res.data.count_all_internal)));
                $('.count_un_approve_internal').text(mainFmt(mainNum(res.data.count_un_approve_internal)));
                $('.count_approved_internal').text(mainFmt(mainNum(res.data.count_approved_internal)));
                $('.count_all_production').text(mainFmt(mainNum(res.data.count_all_production)));
                $('.count_un_approve_production').text(mainFmt(mainNum(res.data.count_un_approve_production)));
                $('.count_approved_production').text(mainFmt(mainNum(res.data.count_approved_production)));
                $('.count_all_vi_pham').text(mainFmt(mainNum(res.data.count_all_vi_pham)));
                $('.count_un_approve_vi_pham').text(mainFmt(mainNum(res.data.count_un_approve_vi_pham)));
                $('.count_approved_vi_pham').text(mainFmt(mainNum(res.data.count_approved_vi_pham)));
                $('.count_all_overtime').text(mainFmt(mainNum(res.data.count_all_overtime)));
                $('.count_un_approve_overtime').text(mainFmt(mainNum(res.data.count_un_approve_overtime)));
                $('.count_approved_overtime').text(mainFmt(mainNum(res.data.count_approved_overtime)));
                $('.count_all_outsource').text(mainFmt(mainNum(res.data.count_all_outsource)));
                $('.count_un_approve_outsource').text(mainFmt(mainNum(res.data.count_un_approve_outsource)));
                $('.count_approved_outsource').text(mainFmt(mainNum(res.data.count_approved_outsource)));
                $('.count_staff_kpi').text(mainFmt(mainNum(res.data.count_staff_kpi)));
                $('.count_not_checkin_ksnb').text(mainFmt(mainNum(res.data.count_not_checkin_ksnb)));
                $('.count_not_checkout_ksnb').text(mainFmt(mainNum(res.data.count_not_checkout_ksnb)));
                $('.count_checkin_ksnb').text(mainFmt(mainNum(res.data.count_checkin_ksnb)));
                $('.count_checkout_ksnb').text(mainFmt(mainNum(res.data.count_checkout_ksnb)));
                $('.count_overtime_ksnb').text(mainFmt(mainNum(res.data.count_overtime_ksnb)));
                $('.count_evaluate_ksnb').text(mainFmt(mainNum(res.data.count_evaluate_ksnb)));
                $('.count_certification_ksnb').text(mainFmt(mainNum(res.data.count_certification_ksnb)));
                $('.count_certificate_ksnb').text(mainFmt(mainNum(res.data.count_certificate_ksnb)));

                $('.count_all_rating_ksnb').text(mainFmt(mainNum(res.data.count_all_rating_ksnb)));
                $('.count_un_approve_rating_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_rating_ksnb)));
                $('.count_approved_rating_ksnb').text(mainFmt(mainNum(res.data.count_approved_rating_ksnb)));
                $('.count_un_approve_finish_rating_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_finish_rating_ksnb)));
                $('.count_approved_finish_rating_ksnb').text(mainFmt(mainNum(res.data.count_approved_finish_rating_ksnb)));
                $('.count_all_maintenance_ksnb').text(mainFmt(mainNum(res.data.count_all_maintenance_ksnb)));
                $('.count_un_approve_maintenance_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_maintenance_ksnb)));
                $('.count_approved_maintenance_ksnb').text(mainFmt(mainNum(res.data.count_approved_maintenance_ksnb)));
                $('.count_un_approve_finish_maintenance_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_finish_maintenance_ksnb)));
                $('.count_approved_finish_maintenance_ksnb').text(mainFmt(mainNum(res.data.count_approved_finish_maintenance_ksnb)));
                $('.count_all_repair_ksnb').text(mainFmt(mainNum(res.data.count_all_repair_ksnb)));
                $('.count_un_approve_repair_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_repair_ksnb)));
                $('.count_approved_repair_ksnb').text(mainFmt(mainNum(res.data.count_approved_repair_ksnb)));
                $('.count_un_approve_finish_repair_ksnb').text(mainFmt(mainNum(res.data.count_un_approve_finish_repair_ksnb)));
                $('.count_approved_finish_repair_ksnb').text(mainFmt(mainNum(res.data.count_approved_finish_repair_ksnb)));
                $('.total_production_items_errors_ksnb').text(mainFmt(mainNum(res.data.total_purchase_errors_ksnb)));
                $('.count_inventory_ksnb').text(mainFmt(mainNum(res.data.count_inventory_ksnb)));
                $('.count_all_nvl_ksnb').text(mainFmt(mainNum(res.data.count_all_nvl_ksnb)));
                $('.count_all_btp_ksnb').text(mainFmt(mainNum(res.data.count_all_btp_ksnb)));
                $('.count_all_tp_ksnb').text(mainFmt(mainNum(res.data.count_all_tp_ksnb)));
                $('.count_internal_proposal_de_xuat_ksnb').text(mainFmt(mainNum(res.data.count_internal_proposal_de_xuat_ksnb)));
                $('.count_6_nvl_ksnb').text(mainFmt(mainNum(res.data.count_6_nvl_ksnb)));
                $('.count_6_btp_ksnb').text(mainFmt(mainNum(res.data.count_6_btp_ksnb)));
                $('.count_6_tp_ksnb').text(mainFmt(mainNum(res.data.count_6_tp_ksnb)));
                $('.count_error_production').text(mainFmt(mainNum(res.data.count_error_production)));
                $('.count_12_nvl_ksnb').text(mainFmt(mainNum(res.data.count_12_nvl_ksnb)));
                $('.count_12_btp_ksnb').text(mainFmt(mainNum(res.data.count_12_btp_ksnb)));
                $('.count_12_tp_ksnb').text(mainFmt(mainNum(res.data.count_12_tp_ksnb)));
                $('.count_error_vi_pham').text(mainFmt(mainNum(res.data.count_error_vi_pham)));

                if (res.data.dtProductionListsItems && res.data.dtProductionListsItems.length > 0) {
                    res.data.dtProductionListsItems.forEach(item => {
                        $(`.total_${item.category_stages_id}`).text(mainFmt(mainNum(item.total)));
                    });
                }

                dataChartKsnb = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_internal,
                            res.data.count_all_production,
                            res.data.count_all_vi_pham,
                            res.data.count_all_overtime,
                            res.data.count_all_outsource,
                        ]
                    },
                    {
                        name: 'Chưa duyệt, hoàn thành',
                        data: [
                            res.data.count_un_approve_internal,
                            res.data.count_un_approve_production,
                            res.data.count_un_approve_vi_pham,
                            res.data.count_un_approve_overtime,
                            res.data.count_un_approve_outsource,
                        ]
                    },
                    {
                        name: 'Đã duyệt, hoàn thành',
                        data: [
                            res.data.count_approved_internal,
                            res.data.count_approved_production,
                            res.data.count_approved_vi_pham,
                            res.data.count_approved_overtime,
                            res.data.count_approved_outsource,
                        ]
                    }
                ];
                loadChartKsnb(dataChartKsnb);


                dataChartChamCong.push({
                    name: `Chưa CheckIn - ${res.data.count_not_checkin_ksnb}`,
                    y: parseInt(res.data.count_not_checkin_ksnb)
                });
                dataChartChamCong.push({
                    name: 'Chưa CheckOut - ' + res.data.count_not_checkout_ksnb,
                    y: parseInt(res.data.count_not_checkout_ksnb)
                });
                dataChartChamCong.push({
                    name: 'Đã CheckIn - ' + res.data.count_checkin_ksnb,
                    y: parseInt(res.data.count_checkin_ksnb)
                });
                dataChartChamCong.push({
                    name: 'Đã CheckOut - ' + res.data.count_checkout_ksnb,
                    y: parseInt(res.data.count_checkout_ksnb)
                });
                dataChartChamCong.push({
                    name: 'Tăng Ca - ' + res.data.count_overtime_ksnb,
                    y: parseInt(res.data.count_overtime_ksnb)
                });

                if (count_not_checkin_ksnb_old !== (res.data.count_not_checkin_ksnb) ||
                    count_not_checkout_ksnb_old !== (res.data.count_not_checkout_ksnb) ||
                    count_checkin_ksnb_old !== (res.data.count_checkin_ksnb) ||
                    count_checkout_ksnb_old !== (res.data.count_checkout_ksnb) ||
                    count_overtime_ksnb_old !== (res.data.count_overtime_ksnb)) {
                    loadChartChamCong(dataChartChamCong);
                }

                dataChartEvaluate.push({
                    name: `Cần tái đánh giá - ${res.data.count_evaluate_ksnb}`,
                    y: parseInt(res.data.count_evaluate_ksnb)
                });
                dataChartEvaluate.push({
                    name: 'Cần tái đánh giá chứng nhận - ' + res.data.count_certification_ksnb,
                    y: parseInt(res.data.count_certification_ksnb)
                });
                dataChartEvaluate.push({
                    name: 'Cần tái đánh giá chứng chỉ - ' + res.data.count_certificate_ksnb,
                    y: parseInt(res.data.count_certificate_ksnb)
                });

                if (count_evaluate_ksnb_old !== (res.data.count_evaluate_ksnb) ||
                    count_certification_ksnb_old !== (res.data.count_certification_ksnb) ||
                    count_certificate_ksnb_old !== (res.data.count_certificate_ksnb)) {
                    loadChartEvaluateKsnb(dataChartEvaluate);
                }

                dataChartTechnialKsnb = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_rating_ksnb,
                            res.data.count_all_maintenance_ksnb,
                            res.data.count_all_repair_ksnb,
                        ]
                    },
                    {
                        name: 'Chưa duyệt, hoàn thành',
                        data: [
                            res.data.count_un_approve_rating_ksnb,
                            res.data.count_un_approve_maintenance_ksnb,
                            res.data.count_un_approve_repair_ksnb,
                        ]
                    },
                    {
                        name: 'Đã duyệt, hoàn thành',
                        data: [
                            res.data.count_approved_rating_ksnb,
                            res.data.count_approved_maintenance_ksnb,
                            res.data.count_approved_repair_ksnb,
                        ]
                    }
                ];

                loadChartTechnialKsnb(dataChartTechnialKsnb);

                dataChartInventory.push({
                    name: `Tổng kiểm kê - ${res.data.count_inventory_ksnb}`,
                    y: parseInt(res.data.count_inventory_ksnb)
                });
                dataChartInventory.push({
                    name: `Tổng kiểm toán nội bộ - ${res.data.count_internal_proposal_de_xuat_ksnb}`,
                    y: parseInt(res.data.count_internal_proposal_de_xuat_ksnb)
                });
                dataChartInventory.push({
                    name: `BCKPH có lỗi - ${res.data.count_error_production}`,
                    y: parseInt(res.data.count_error_production)
                });
                dataChartInventory.push({
                    name: `BC vi phạm có lỗi - ${res.data.count_error_vi_pham}`,
                    y: parseInt(res.data.count_error_vi_pham)
                });

                if (count_inventory_ksnb_old !== (res.data.count_inventory_ksnb) ||
                    count_internal_proposal_de_xuat_ksnb_old !== (res.data.count_internal_proposal_de_xuat_ksnb) ||
                    count_error_production_old !== (res.data.count_error_production) ||
                    count_error_vi_pham_old !== (res.data.count_error_vi_pham)) {
                    loadChartInventory(dataChartInventory);
                }


                dataChartWarehouse = [
                    {
                        name: 'Tất cả',
                        data: [
                            res.data.count_all_nvl_ksnb,
                            res.data.count_all_btp_ksnb,
                            res.data.count_all_tp_ksnb,
                        ]
                    },
                    {
                        name: 'Quá hạn trên 6 tháng',
                        data: [
                            res.data.count_6_nvl_ksnb,
                            res.data.count_6_btp_ksnb,
                            res.data.count_6_tp_ksnb,
                        ]
                    },
                    {
                        name: 'Quá hạn trên 12 tháng',
                        data: [
                            res.data.count_12_nvl_ksnb,
                            res.data.count_12_btp_ksnb,
                            res.data.count_12_tp_ksnb,
                        ]
                    }
                ];

                if (count_all_nvl_ksnb_old !== (res.data.count_all_nvl_ksnb) ||
                    count_all_btp_ksnb_old !== (res.data.count_all_btp_ksnb) ||
                    count_all_tp_ksnb_old !== (res.data.count_all_tp_ksnb) ||
                    count_6_nvl_ksnb_old !== (res.data.count_6_nvl_ksnb) ||
                    count_6_btp_ksnb_old !== (res.data.count_6_btp_ksnb) ||
                    count_6_tp_ksnb_old !== (res.data.count_6_tp_ksnb) ||
                    count_12_nvl_ksnb_old !== (res.data.count_12_nvl_ksnb) ||
                    count_12_btp_ksnb_old !== (res.data.count_12_btp_ksnb) ||
                    count_12_tp_ksnb_old !== (res.data.count_12_tp_ksnb)) {
                    loadChartWarehouse(dataChartWarehouse);
                }

            });
        }
    }
    count_ksnb();
    setInterval(count_ksnb, 20000);

    function loadChartKsnb(dataChart = {}){
        Highcharts.chart('container_ksnb', {
            chart: {
                type: 'bar',
                height: 650, // tăng chiều cao
            },
            title: {
                text: 'Tổng quan kiểm soát nội bộ'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['Đề xuất nội bộ', 'Báo cáo không phù hợp', 'Báo cáo vi phạm', 'Yêu cầu tăng ca', 'Kế hoạch gia công'],
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' số lượng'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: -10,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: dataChart
        });
    }

    function loadChartChamCong(dataChart = {}) {
        Highcharts.chart('container_cham_cong_ksnb', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: 'Tổng quan nhân viên'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        style: {
                            fontSize: '15px',
                        },
                    }
                }
            },
            series: [
                {
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }
            ]
        });
    }

    function loadChartEvaluateKsnb(dataChart = {}) {
        Highcharts.chart('container_evaluate_ksnb', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: 'Tổng quan cần tái đánh giá'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        style: {
                            fontSize: '15px',
                        },
                    }
                }
            },
            series: [
                {
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }
            ]
        });
    }

    function loadChartTechnialKsnb(dataChart = {}){
        Highcharts.chart('container_technial_ksnb', {
            chart: {
                type: 'bar',
                height: 650, // tăng chiều cao
            },
            title: {
                text: 'Tổng quan dữ liệu kỹ thuật'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['Đánh giá thiết bị', 'Hiệu chuẩn máy móc', 'Bảo dưỡng máy móc', 'Sửa chữa máy móc', 'BCKPH thiết bị'],
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' số lượng'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: -10,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: dataChart
        });
    }

    function loadChartInventory(dataChart = {}) {
        Highcharts.chart('container_inventoy', {
            chart: {
                type: 'pie',
                zooming: {
                    type: 'xy'
                },
                panning: {
                    enabled: true,
                    type: 'xy'
                },
                panKey: 'shift'
            },
            title: {
                text: 'Tổng quan kế hoạch'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        style: {
                            fontSize: '15px',
                        },
                    }
                }
            },
            series: [
                {
                    name: 'Số lượng',
                    colorByPoint: true,
                    data: dataChart
                }
            ]
        });
    }

    function loadChartWarehouse(dataChart = {}){
        Highcharts.chart('container_warehouse', {
            chart: {
                type: 'bar',
            },
            title: {
                text: 'Tổng quan tồn kho'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['NPL', 'BTP', 'TP'],
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' số lượng'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: -10,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: dataChart
        });
    }
</script>