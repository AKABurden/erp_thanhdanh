<style>
    .value-accouting {
        font-size: 60px !important;
    }

    .sub-menu-accounting {
        width: 100%;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        position: absolute;
        bottom: 60px;
        padding: 10px;
    }
</style>
<div id="dashboard_accounting" class="dashboard-accounting hide">
    <div class="page_1">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG ĐỀ XUẤT NỘI BỘ CHƯA DUYỆT</div>
                        <div class="value value-accouting red js-accounting-pending">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG ĐỀ XUẤT TÀI CHÍNH CHƯA CHI</div>
                        <div class="value value-accouting red js-accounting-no-payed">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">ĐỀ XUẤT TÀI CHÍNH CHƯA TẠO HÓA ĐƠN</div>
                        <div class="value value-accouting red js-accounting-dxtc-invoice-not-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG NHẬP KHO CHƯA TẠO HÓA ĐƠN</div>
                        <div class="value value-accouting red js-accounting-invoice-not-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG HỢP ĐỒNG KHÁCH HÀNG CẦN TÁI KÝ</div>
                        <div class="value value-accouting red js-contract_clients_ctk">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG HỢP ĐỒNG NCC CẦN TÁI KÝ</div>
                        <div class="value value-accouting red js-contract_ncc_ctk">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG ĐỀ XUẤT NỘI BỘ ĐÃ DUYỆT</div>
                        <div class="value value-accouting green js-accounting-approved">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG ĐỀ XUẤT TÀI CHÍNH ĐÃ CHI</div>
                        <div class="value value-accouting green js-accounting-payed">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">ĐỀ XUẤT TÀI CHÍNH ĐÃ TẠO HÓA ĐƠN</div>
                        <div class="value value-accouting green js-accounting-dxtc-invoice-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label ">TỔNG NHẬP KHO ĐÃ TẠO HÓA ĐƠN</div>
                        <div class="value value-accouting green js-accounting-invoice-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG HỢP ĐỒNG KHÁCH HÀNG ĐÃ TÁI KÝ</div>
                        <div class="value value-accouting green js-contract_clients_dtk">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG HỢP ĐỒNG NCC ĐÃ TÁI KÝ</div>
                        <div class="value value-accouting green js-contract_ncc_dtk">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box" style="margin: -8px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-accounting"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_2 hide">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">

                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG GIAO HÀNG CHƯA NHẬN CHỨNG TỪ</div>
                        <div class="value value-accouting red js-accounting-delivery-no-docs">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG GIAO HÀNG CHƯA TẠO HÓA ĐƠN BÁN HÀNG</div>
                        <div class="value value-accouting red js-accounting-delivery-invoice-not-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG GIAO HÀNG CHƯA KHAI HẢI QUAN</div>
                        <div class="value value-accouting red js-accounting-delivery-not-cleared">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 50px; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label red">TỔNG HÓA ĐƠN CHƯA THU TIỀN</div>
                        <div class="value value-accouting red js-accounting-invoice-not-collected">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG GIAO HÀNG ĐÃ NHẬN CHỨNG TỪ</div>
                        <div class="value value-accouting green js-accounting-delivery-has-docs">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label ">TỔNG GIAO HÀNG ĐÃ TẠO HÓA ĐƠN BÁN HÀNG</div>
                        <div class="value value-accouting green js-accounting-delivery-invoice-created">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG GIAO HÀNG ĐÃ KHAI HẢI QUAN</div>
                        <div class="value value-accouting green js-accounting-delivery-cleared">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box">
                        <div style="font-size: 25px;" class="label">TỔNG HÓA ĐƠN ĐÃ THU TIỀN</div>
                        <div class="value value-accouting green js-accounting-invoice-collected">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box" style="margin: -8px;height: 388px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-delivery-pending"></canvas>
                        </div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box" style="margin: -8px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-delivery-khq"></canvas>
                        </div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box" style="margin: -8px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-invoice-payment"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_3 hide">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA DUYỆT</div>
                        <div class="value value_ksnb red count_un_approve_repair_ac" onclick="detailModalTechnialRepair(this,2)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label red">SỬA CHỮA MÁY MÓC THIẾT BỊ CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_un_approve_finish_repair_ac" onclick="detailModalTechnialRepair(this,4)">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label red">KẾ HOẠCH CHI CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_plan_propose_chi_chh">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label red">KẾ HOẠCH THU CHƯA HOÀN THÀNH</div>
                        <div class="value value_ksnb red count_plan_propose_thu_chh">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label">SỬA CHỮA MÁY MÓC THIẾT BỊ ĐÃ DUYỆT</div>
                        <div class="value value_ksnb count_approved_repair_ac">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">SỬA CHỮA MÁY MÓC THIẾT BỊ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_approved_finish_repair_ac">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">KẾ HOẠCH CHI ĐÃ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_plan_propose_chi_hh">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">KẾ HOẠCH THU ĐÃ HOÀN THÀNH</div>
                        <div class="value value_ksnb count_plan_propose_thu_hh">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box" style="margin: -8px;height: 388px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-repair-accounting"></canvas>
                        </div>
                    </div>
                    <div class="box" style="margin: -8px;height: 388px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-plan_propose-accounting"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="page_4 hide">
        <div class="container-detail">
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label">Tổng Đề Xuất Mua Theo Kế Hoạch</div>
                        <div class="value value_ksnb count_recommended_list_group_dxmtkht">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">Tổng Đề Xuất Mua Ngoài Kế Hoạch</div>
                        <div class="value value_ksnb count_recommended_list_group_dxmnkht">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">Tổng Đề Xuất Mua Vượt Kế Hoạch</div>
                        <div class="value value_ksnb count_recommended_list_group_dxdgmvkh">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33.33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box box_new">
                        <div class="label">Tổng Đề Xuất Mua Thiếu Kế Hoạch</div>
                        <div class="value value_ksnb count_recommended_list_group_dxdgmtkh">-</div>
                    </div>
                    <hr style="margin: 10px 0 0 0; border: none; border-top: 1px solid #d0d0d0;">
                    <div class="box box_new">
                        <div class="label">Tổng Đề Xuất Mua Khẩn</div>
                        <div class="value value_ksnb count_recommended_list_group_dxtsdgmk">-</div>
                    </div>
                </div>
            </section>
            <div style="width:1px; background:#d0d0d0; height: calc(100vh - 268px);margin:50px 0; float:left; display:inline-block;"></div>
            <section class="" style="height: calc(100vh - 169px);width: 33%; float: left;">
                <div class="thongke-box thongke-grid" style="height: 100%;display: flex;flex-direction: column;justify-content: space-evenly;">
                    <div class="box" style="margin: -8px;height: 388px;">
                        <div class="chart js-chart-quotes">
                            <canvas class="chart js-chart-recommended_list_group"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="sub-menu-accounting">
        <div class="sub-menu-child-accting active page_1_class_page" data-value="1" onclick="changeFilteraccting(this,1)">Trang 1 (z)</div>
        <div class="sub-menu-child-accting page_2_class_page" data-value="2" onclick="changeFilteraccting(this,2)">Trang 2 (x)</div>
        <div class="sub-menu-child-accting page_3_class_page" data-value="3" onclick="changeFilteraccting(this,3)">Trang 3 (c)</div>
        <div class="sub-menu-child-accting page_4_class_page" data-value="4" onclick="changeFilteraccting(this,4)">Trang 4 (v)</div>
    </div>
</div>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- ChartDataLabels plugin -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    function changeFilteraccting(_this, id) {
        $('.sub-menu-child-accting').removeClass('active');
        $(_this).addClass('active');
        // hide all pages first, then show the selected one (supports pages 1-4)
        $('.page_1, .page_2, .page_3, .page_4').addClass('hide');
        if ([1, 2, 3, 4].includes(id)) {
            $('.page_' + id).removeClass('hide');
        }
    }
    $(document).on('click', '.js-accounting-pending', function(e) {
        var sample = $('.js-accounting-pending').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_dxnb') ?>',
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
    });
    $(document).on('click', '.js-accounting-no-payed', function(e) {
        var sample = $('.js-accounting-no-payed').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_dxtc/1') ?>',
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
    });
    $(document).on('click', '.js-accounting-dxtc-invoice-not-created', function(e) {
        var sample = $('.js-accounting-dxtc-invoice-not-created').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_dxtc/2') ?>',
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
    });
    $(document).on('click', '.js-accounting-invoice-not-created', function(e) {
        var sample = $('.js-accounting-invoice-not-created').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_nhapkho/1') ?>',
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
    });
    $(document).on('click', '.js-accounting-delivery-no-docs', function(e) {
        var sample = $('.js-accounting-delivery-no-docs').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_delivery/1') ?>',
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
    });        
    $(document).on('click', '.js-accounting-delivery-invoice-not-created', function(e) {
        var sample = $('.js-accounting-delivery-invoice-not-created').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_delivery/2') ?>',
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
    });  
    $(document).on('click', '.js-accounting-delivery-not-cleared', function(e) {
        var sample = $('.js-accounting-delivery-not-cleared').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_delivery/3') ?>',
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
    });  
    $(document).on('click', '.js-accounting-invoice-not-collected', function(e) {
        var sample = $('.js-accounting-invoice-not-collected').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_invoice/1') ?>',
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
    });  
    $(document).on('click', '.count_plan_propose_chi_chh', function(e) {
        var sample = $('.count_plan_propose_chi_chh').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_plan_propose/1') ?>',
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
    });  
     $(document).on('click', '.count_plan_propose_thu_chh', function(e) {
        var sample = $('.count_plan_propose_thu_chh').text();
        if (sample != '-') {
            e.preventDefault();
            var $btn = $(this);
            $btn.addClass('loading');
            $.ajax({
                url: '<?= base_url('dashboard_srceen_vp/modal_accounting_plan_propose/2') ?>',
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
    });  
    function UpdateStatsAccounting(stats) {
        $('.js-accounting-pending').css('cursor', 'unset');
        if (mainNum(stats.internal_proposal_money_pending) > 0) {
            $('.js-accounting-pending').css('cursor', 'pointer');
        }
        $('.js-accounting-no-payed').css('cursor', 'unset');
        if (mainNum(stats.count_not_payed) > 0) {
            $('.js-accounting-no-payed').css('cursor', 'pointer');
        }

        $('.js-accounting-dxtc-invoice-not-created').css('cursor', 'unset');
        if (mainNum(stats.suggestions_without_invoice) > 0) {
            $('.js-accounting-dxtc-invoice-not-created').css('cursor', 'pointer');
        }

        $('.js-accounting-invoice-not-created').css('cursor', 'unset');
        if (mainNum(stats.imports_without_invoice) > 0) {
            $('.js-accounting-invoice-not-created').css('cursor', 'pointer');
        }

        $('.js-accounting-delivery-no-docs').css('cursor', 'unset');
        if (mainNum(stats.count_received_certificate_not) > 0) {
            $('.js-accounting-delivery-no-docs').css('cursor', 'pointer');
        }
        $('.js-accounting-delivery-invoice-not-created').css('cursor', 'unset');
        if (mainNum(stats.count_not_invoice) > 0) {
            $('.js-accounting-delivery-invoice-not-created').css('cursor', 'pointer');
        }

        $('.js-accounting-delivery-not-cleared').css('cursor', 'unset');
        if (mainNum(stats.count_code_custom_null) > 0) {
            $('.js-accounting-delivery-not-cleared').css('cursor', 'pointer');
        }

        $('.js-accounting-invoice-not-collected').css('cursor', 'unset');
        if (mainNum(stats.count_paymented_not) > 0) {
            $('.js-accounting-invoice-not-collected').css('cursor', 'pointer');
        }

        $('.count_plan_propose_chi_chh').css('cursor', 'unset');
        if (mainNum(stats.count_plan_propose_chi_chh) > 0) {
            $('.count_plan_propose_chi_chh').css('cursor', 'pointer');
        }
        $('.count_plan_propose_thu_chh').css('cursor', 'unset');
        if (mainNum(stats.count_plan_propose_thu_chh) > 0) {
            $('.count_plan_propose_thu_chh').css('cursor', 'pointer');
        }
        // accounting fields

        $('.js-accounting-pending').text(mainFmt(mainNum(stats?.internal_proposal_money_pending), 0)); // chưa mở lệnh
        $('.js-accounting-approved').text(mainFmt(mainNum(stats?.internal_proposal_money_approved), 0)); // đã mở lệnh
        $('.js-accounting-payed').text(mainFmt(mainNum(stats?.count_payed), 0));
        $('.js-accounting-no-payed').text(mainFmt(mainNum((stats?.count_not_payed ?? 0)), 0));

        // delivery / invoice fields (fallback nếu server chưa trả)
        $('.js-accounting-delivery-not-cleared').text(mainFmt(mainNum(stats?.count_code_custom_null ?? 0), 0));
        $('.js-accounting-delivery-no-docs').text(mainFmt(mainNum(stats?.count_received_certificate_not ?? 0), 0));
        $('.js-accounting-delivery-invoice-not-created').text(mainFmt(mainNum(stats?.count_not_invoice ?? 0), 0));
        $('.js-accounting-invoice-not-collected').text(mainFmt(mainNum(stats?.count_paymented_not ?? 0), 0));

        $('.js-accounting-delivery-cleared').text(mainFmt(mainNum(stats?.count_code_custom_not_null ?? 0), 0));
        $('.js-accounting-delivery-has-docs').text(mainFmt(mainNum(stats?.count_received_certificate ?? 0), 0));
        $('.js-accounting-delivery-invoice-created').text(mainFmt(mainNum(stats?.count_invoice ?? 0), 0));
        $('.js-accounting-invoice-collected').text(mainFmt(mainNum(stats?.count_paymented ?? 0), 0));

        $('.js-accounting-dxtc-invoice-created').text(mainFmt(mainNum(stats?.suggestions_with_invoice ?? 0), 0));
        $('.js-accounting-dxtc-invoice-not-created').text(mainFmt(mainNum(stats?.suggestions_without_invoice ?? 0), 0));

        $('.js-accounting-invoice-created').text(mainFmt(mainNum(stats?.imports_with_invoice ?? 0), 0));
        $('.js-accounting-invoice-not-created').text(mainFmt(mainNum(stats?.imports_without_invoice ?? 0), 0));


        $('.js-contract_ncc_ctk').text(mainFmt(mainNum(stats?.contract_ncc_ctk ?? 0), 0));
        $('.js-contract_ncc_dtk').text(mainFmt(mainNum(stats?.contract_ncc_dtk ?? 0), 0));

        $('.js-contract_clients_ctk').text(mainFmt(mainNum(stats?.contract_clients_ctk ?? 0), 0));
        $('.js-contract_clients_dtk').text(mainFmt(mainNum(stats?.contract_clients_dtk ?? 0), 0));


        $('.count_approved_repair_ac').text(mainFmt(mainNum(stats?.count_approved_repair)));
        $('.count_un_approve_repair_ac').text(mainFmt(mainNum(stats?.count_un_approve_repair)));
        $('.count_un_approve_finish_repair_ac').text(mainFmt(mainNum(stats?.count_un_approve_finish_repair)));
        $('.count_approved_finish_repair_ac').text(mainFmt(mainNum(stats?.count_approved_finish_repair)));

        $('.count_plan_propose_chi_hh').text(mainFmt(mainNum(stats?.count_plan_propose_chi_hh)));
        $('.count_plan_propose_chi_chh').text(mainFmt(mainNum(stats?.count_plan_propose_chi_chh)));

        $('.count_plan_propose_thu_hh').text(mainFmt(mainNum(stats?.count_plan_propose_thu_hh)));
        $('.count_plan_propose_thu_chh').text(mainFmt(mainNum(stats?.count_plan_propose_thu_chh)));

        $('.count_recommended_list_group_dxmtkht').text(mainFmt(mainNum(stats?.recommended_list_group_dxmtkht)));
        $('.count_recommended_list_group_dxmnkht').text(mainFmt(mainNum(stats?.recommended_list_group_dxmnkht)));
        $('.count_recommended_list_group_dxdgmvkh').text(mainFmt(mainNum(stats?.recommended_list_group_dxdgmvkh)));
        $('.count_recommended_list_group_dxdgmtkh').text(mainFmt(mainNum(stats?.recommended_list_group_dxdgmtkh)));
        $('.count_recommended_list_group_dxtsdgmk').text(mainFmt(mainNum(stats?.recommended_list_group_dxtsdgmk)));


        // ====== Biểu đồ: chỉ recreate khi data thay đổi ======
        // Tạo khóa (hash) đơn giản từ các trường dữ liệu quan trọng để so sánh
        const _chartDataSnapshot = {
            internal_proposal_money_pending: stats?.internal_proposal_money_pending ?? null,
            internal_proposal_money_approved: stats?.internal_proposal_money_approved ?? null,
            count_not_payed: stats?.count_not_payed ?? null,
            count_payed: stats?.count_payed ?? null,
            suggestions_without_invoice: stats?.suggestions_without_invoice ?? null,
            suggestions_with_invoice: stats?.suggestions_with_invoice ?? null,
            imports_without_invoice: stats?.imports_without_invoice ?? null,
            imports_with_invoice: stats?.imports_with_invoice ?? null,
            contract_clients_ctk: stats?.contract_clients_ctk ?? null,
            contract_ncc_ctk: stats?.contract_ncc_ctk ?? null,
            contract_clients_dtk: stats?.contract_clients_dtk ?? null,
            contract_ncc_dtk: stats?.contract_ncc_dtk ?? null,
            count_received_certificate_not: stats?.count_received_certificate_not ?? null,
            count_received_certificate: stats?.count_received_certificate ?? null,
            count_not_invoice: stats?.count_not_invoice ?? null,
            count_invoice: stats?.count_invoice ?? null,
            count_code_custom_null: stats?.count_code_custom_null ?? null,
            count_code_custom_not_null: stats?.count_code_custom_not_null ?? null,
            count_paymented: stats?.count_paymented ?? null,
            count_paymented_not: stats?.count_paymented_not ?? null
        };
        const _currentChartHash = JSON.stringify(_chartDataSnapshot);

        // Nếu dữ liệu không đổi, không cần destroy / recreate chart (tiết kiệm render)
        if (window.accountingStatsHash === _currentChartHash) {
            // console.log('Accounting charts: no data change — skip redraw');
            return;
        }
        // cập nhật hash mới
        window.accountingStatsHash = _currentChartHash;

        // destroy mọi chart cũ nếu tồn tại
        ['myChartAccountingSummary', 'myChartDeliveryPending', 'myChartDeliveryKhq', 'myChartInvoicePayment'].forEach(n => {
            if (window[n]) {
                try {
                    window[n].destroy();
                } catch (e) {
                    console.warn('Error destroying chart', n, e);
                }
                window[n] = null;
            }
        });

        const ctx = document.querySelector('.js-chart-accounting').getContext('2d');

        // 🧩 Plugin tùy chỉnh vẽ nhãn “Chưa / Đã / Tổng”
        const smartLabelPlugin = {
            id: 'smartLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                const scale = chart.scales.x;
                const meta0 = chart.getDatasetMeta(0);
                const data0 = chart.data.datasets[0].data;
                const data1 = chart.data.datasets[1].data;

                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                ctx.textBaseline = 'middle';

                meta0.data.forEach((bar, i) => {
                    const c0 = data0[i] || 0;
                    const c1 = data1[i] || 0;
                    const total = c0 + c1;
                    if (c0 === 0 && c1 === 0) return;

                    const xStart = scale.getPixelForValue(0);
                    const xEnd = scale.getPixelForValue(total);
                    const barLength = xEnd - xStart;
                    const y = bar.y;

                    const label = `Chưa: ${mainFmt(mainNum(c0), 0)}   Đã: ${mainFmt(mainNum(c1), 0)}   Tổng: ${mainFmt(mainNum(total), 0)}`;

                    const inside = barLength > 200; // nếu cột dài hơn 200px => để chữ trong
                    const labelWidth = ctx.measureText(label).width;

                    const xText = inside ?
                        xStart + barLength / 2 - labelWidth / 2 // nằm giữa cột
                        :
                        xEnd + 10; // nằm bên phải cột

                    ctx.fillStyle = inside ? '#000' : '#000';
                    ctx.fillText(label, xText, y);
                });

                ctx.restore();
            }
        };

        window.myChartAccountingSummary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Đề xuất nội bộ',
                    'Chi Đề xuất tài chính',
                    'Hóa đơn Đề xuất tài chính',
                    'Hóa Đơn Nhập kho',
                    'HĐ khách hàng',
                    'HĐ NCC'
                ],
                datasets: [{
                        label: 'Chưa xử lý',
                        data: [
                            stats?.internal_proposal_money_pending ?? 0,
                            stats?.count_not_payed ?? 0,
                            stats?.suggestions_without_invoice ?? 0,
                            stats?.imports_without_invoice ?? 0,
                            stats?.contract_clients_ctk ?? 0,
                            stats?.contract_ncc_ctk ?? 0
                        ],
                        backgroundColor: '#f5b7b1'
                    },
                    {
                        label: 'Đã xử lý',
                        data: [
                            stats?.internal_proposal_money_approved ?? 0,
                            stats?.count_payed ?? 0,
                            stats?.suggestions_with_invoice ?? 0,
                            stats?.imports_with_invoice ?? 0,
                            stats?.contract_clients_dtk ?? 0,
                            stats?.contract_ncc_dtk ?? 0
                        ],
                        backgroundColor: '#82e0aa'
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 200, // chừa chỗ bên phải để text không bị cắt
                        left: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Tổng hợp tình trạng xử lý kế toán',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.x || 0;
                                return `${label}: ${mainFmt(mainNum(value), 0)}`;
                            },
                            afterBody(context) {
                                const idx = context[0].dataIndex;
                                const total =
                                    (context[0].chart.data.datasets[0].data[idx] || 0) +
                                    (context[0].chart.data.datasets[1].data[idx] || 0);
                                return 'Tổng: ' + mainFmt(mainNum(total), 0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng / giá trị'
                        },
                        grid: {
                            color: '#eee'
                        }
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            font: {
                                size: 13
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [ChartDataLabels, smartLabelPlugin] // ✅ đăng ký plugin tại đây
        });

        ctx.canvas.parentNode.style.height = "640px";
        ctx.canvas.parentNode.style.width = "100%";


        // Doughnut kép (2 vòng chồng) cho canvas .js-chart-delivery-pending
        (function() {
            const canvas = document.querySelector('.js-chart-delivery-pending');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // Giá trị lấy từ stats (đã có trong scope)
            const noDocs = mainNum(stats?.count_received_certificate_not ?? 0);
            const hasDocs = mainNum(stats?.count_received_certificate ?? 0);
            const invoiceNot = mainNum(stats?.count_not_invoice ?? 0);
            const invoiceCreated = mainNum(stats?.count_invoice ?? 0);

            // Plugin vẽ "Sổ tổng" ở giữa doughnut và label ngoài vòng với mũi tên
            const centerTextPlugin = {
                id: 'centerTextPlugin',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    // Tổng chỉ tính cho dataset ngoài (dataset 0)
                    let totalOuter = 0;
                    const outerDs = chart.data.datasets[0];
                    (outerDs.data || []).forEach(v => {
                        totalOuter += mainNum(v || 0);
                    });

                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();

                    // Số tổng lớn (tổng của vòng ngoài)
                    ctx.font = 'bold 20px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    const totalText = mainFmt(mainNum(totalOuter), 0);
                    ctx.fillText(totalText, centerX, centerY - 10);

                    // Nhãn nhỏ bên dưới tổng
                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', centerX, centerY + 12);

                    // Vẽ label ngoài vòng và mũi tên trỏ vào vòng tương ứng
                    // Lấy thông tin bán kính từ 2 dataset (nếu có)
                    const metas = [chart.getDatasetMeta(0), chart.getDatasetMeta(1)];
                    // Nếu không có meta hoặc không có arc, bỏ qua
                    if (!metas[0] || !metas[0].data.length) {
                        ctx.restore();
                        return;
                    }

                    // Tọa độ gốc cho label: đặt 2 label sang hai bên (ngoài vòng), dễ nhìn
                    // label 0 -> bên phải, label 1 -> bên left
                    const labelSpacingY = 18;
                    const baseY = centerY - 10;
                    const outerRadiusMax = metas[0].data[0].outerRadius || Math.min(chartArea.width, chartArea.height) / 4;

                    [0, 1].forEach((dsIndex) => {
                        if (!metas[dsIndex] || !metas[dsIndex].data.length) return;

                        const ds = chart.data.datasets[dsIndex];
                        const firstArc = metas[dsIndex].data[0];

                        // mid radius của vòng (điểm mũi tên sẽ hướng tới đây)
                        const midRadius = ((firstArc.outerRadius || outerRadiusMax) + (firstArc.innerRadius || 0)) / 2;

                        // chọn bên (1 = phải, -1 = trái)
                        const side = dsIndex === 0 ? 1 : -1;
                        // điểm mũi tên trên vòng (góc 0 => phải, PI => trái)
                        const angle = side === 1 ? 0 : Math.PI;
                        const targetX = centerX + Math.cos(angle) * midRadius;
                        const targetY = centerY + Math.sin(angle) * midRadius;

                        // vị trí label ngoài vòng
                        const labelX = centerX + side * (outerRadiusMax + 60);
                        const labelY = baseY + dsIndex * labelSpacingY;

                        // vẽ đường nối (một đoạn thẳng + mũi tên)
                        ctx.beginPath();
                        ctx.strokeStyle = '#666';
                        ctx.lineWidth = 1;
                        // nối từ label tới gần vòng (điểm thứ 1)
                        const midLineX = centerX + side * (outerRadiusMax + 20);
                        const midLineY = labelY;
                        ctx.moveTo(labelX - side * 6, labelY);
                        ctx.lineTo(midLineX, midLineY);
                        ctx.lineTo(targetX, targetY);
                        ctx.stroke();

                        // vẽ mũi tên tại target (tam giác nhỏ)
                        const arrowSize = 8;
                        const ang = Math.atan2(targetY - midLineY, targetX - midLineX);
                        ctx.save();
                        ctx.translate(targetX, targetY);
                        ctx.rotate(ang);
                        ctx.beginPath();
                        ctx.fillStyle = '#666';
                        ctx.moveTo(0, 0);
                        ctx.lineTo(-arrowSize, -arrowSize / 2);
                        ctx.lineTo(-arrowSize, arrowSize / 2);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();

                        // vẽ ô màu nhỏ bên cạnh label
                        const boxSize = 10;
                        const boxX = labelX - side * (boxSize + 6);
                        const boxY = labelY - boxSize / 2;
                        // lấy màu dataset (hỗ trợ mảng hoặc chuỗi)
                        let color = '#999';
                        if (Array.isArray(ds.backgroundColor)) {
                            color = ds.backgroundColor[0] || ds.backgroundColor[dsIndex] || color;
                        } else {
                            color = ds.backgroundColor || color;
                        }
                        ctx.fillStyle = color;
                        ctx.fillRect(boxX, boxY, boxSize, boxSize);

                        // text label
                        ctx.textAlign = side === 1 ? 'left' : 'right';
                        ctx.font = '12px sans-serif';
                        ctx.fillStyle = '#333';
                        const textX = labelX + side * (boxSize / 2 + 8);
                        ctx.fillText(ds.label || (`Vòng ${dsIndex + 1}`), textX, labelY);
                    });

                    ctx.restore();
                }
            };

            window.myChartDeliveryPending = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    // labels chung để hiển thị trong tooltip; mỗi dataset sẽ dùng cùng labels
                    labels: ['Chưa', 'Đã'],
                    datasets: [{
                            label: 'Chứng từ (Giao hàng)',
                            data: [noDocs, hasDocs],
                            backgroundColor: ['#f5b7b1', '#82e0aa'],
                            hoverOffset: 6,
                            // vòng ngoài
                            radius: '90%',
                            innerRadius: '60%'
                        },
                        {
                            label: 'Hóa đơn (Giao hàng)',
                            data: [invoiceNot, invoiceCreated],
                            backgroundColor: ['#f8c471', '#58d68d'],
                            hoverOffset: 6,
                            // vòng trong - làm to hơn: tăng radius và innerRadius
                            radius: '75%',
                            innerRadius: '45%'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 8
                            }
                        },
                        title: {
                            display: true,
                            text: 'Giao hàng — Chưa / Đã (Chứng từ & Hóa đơn)'
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed ?? 0;
                                    const dsLabel = ctx.dataset.label ? (ctx.dataset.label + ' - ') : '';
                                    return `${dsLabel}${ctx.label}: ${mainFmt(mainNum(v), 0)}`;
                                },
                                afterBody(ctx) {
                                    // hiển thị tổng của vòng (nếu muốn)
                                    const idx = ctx[0].dataIndex;
                                    const ds = ctx[0].chart.data.datasets[ctx[0].datasetIndex];
                                    // tổng vòng tương ứng là sum của 2 giá trị trong cùng dataset
                                    const total = (ds.data[0] ?? 0) + (ds.data[1] ?? 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        }
                    },
                    // cutout mặc định không quan trọng vì ta điều chỉnh radius/innerRadius trên dataset
                    cutout: '40%'
                },
                plugins: [ChartDataLabels, centerTextPlugin]
            });

            // cố định hiển thị kích thước parent
            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '350px';
                ctx.canvas.parentNode.style.width = '100%';
            }
        })();
        (function() {
            const canvas = document.querySelector('.js-chart-delivery-khq');
            if (!canvas) return;

            if (window.myChartDeliveryKhq) window.myChartDeliveryKhq.destroy();
            const ctx = canvas.getContext('2d');

            const notCleared = mainNum(stats?.count_code_custom_null ?? 0);
            const cleared = mainNum(stats?.count_code_custom_not_null ?? 0);

            const centerLabelPlugin = {
                id: 'khqCenterLabel',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    const total = (chart.data.datasets[0].data || []).reduce((s, v) => s + mainNum(v || 0), 0);
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();
                    ctx.font = 'bold 18px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(mainNum(total), 0), cx, cy - 8);

                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', cx, cy + 14);
                    ctx.restore();
                }
            };

            window.myChartDeliveryKhq = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Chưa khai hải quan', 'Đã khai hải quan'],
                    datasets: [{
                        data: [notCleared, cleared],
                        backgroundColor: ['#f5b7b1', '#82e0aa'],
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed ?? 0;
                                    return `${ctx.label}: ${mainFmt(mainNum(v), 0)}`;
                                },
                                afterBody(ctx) {
                                    const ds = ctx[0].chart.data.datasets[0];
                                    const total = (ds.data[0] ?? 0) + (ds.data[1] ?? 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels, centerLabelPlugin]
            });

            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '160px';
                ctx.canvas.parentNode.style.width = '80%';
            }
        })();
        (function() {
            const canvas = document.querySelector('.js-chart-invoice-payment');
            if (!canvas) return;

            if (window.myChartInvoicePayment) window.myChartInvoicePayment.destroy();
            const ctx = canvas.getContext('2d');

            const collected = mainNum(stats?.count_paymented ?? 0);
            const notCollected = mainNum(stats?.count_paymented_not ?? 0);

            const invoiceCenterPlugin = {
                id: 'invoicePaymentCenter',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    const total = (chart.data.datasets[0].data || []).reduce((s, v) => s + mainNum(v || 0), 0);
                    const cx = (chartArea.left + chartArea.right) / 2;
                    const cy = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();
                    ctx.font = 'bold 16px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(mainNum(total), 0), cx, cy - 8);

                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', cx, cy + 12);
                    ctx.restore();
                }
            };

            window.myChartInvoicePayment = new Chart(ctx, {
                type: 'pie', // switched from doughnut to pie
                data: {
                    labels: ['Chưa thu', 'Đã thu'],
                    datasets: [{
                        data: [notCollected, collected],
                        backgroundColor: ['#f5b7b1', '#82e0aa'],
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed ?? 0;
                                    return `${ctx.label}: ${mainFmt(mainNum(v), 0)}`;
                                },
                                afterBody(ctx) {
                                    const ds = ctx[0].chart.data.datasets[0];
                                    const total = (ds.data[0] ?? 0) + (ds.data[1] ?? 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    }
                },
                plugins: [ChartDataLabels, invoiceCenterPlugin]
            });

            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '160px';
                ctx.canvas.parentNode.style.width = '80%';
            }
        })();

        (function() {
            const canvas = document.querySelector('.js-chart-repair-accounting');
            if (!canvas) return;

            // Tạo hash từ dữ liệu chart để chỉ destroy/recreate khi dữ liệu thay đổi
            const chartDataSnapshot = {
                unApproveRepair: mainNum(stats?.count_un_approve_repair ?? 0),
                approveRepair: mainNum(stats?.count_approved_repair ?? 0),
                unApproveFinishRepair: mainNum(stats?.count_un_approve_finish_repair ?? 0),
                approveFinishRepair: mainNum(stats?.count_approved_finish_repair ?? 0)
            };
            const chartHash = JSON.stringify(chartDataSnapshot);

            if (window.myChartRepairAccountingHash === chartHash && window.myChartRepairAccounting) {
                // Không có thay đổi dữ liệu, không destroy/recreate
                return;
            }
            window.myChartRepairAccountingHash = chartHash;

            if (window.myChartRepairAccounting) {
                try {
                    window.myChartRepairAccounting.destroy();
                } catch (e) {
                    console.warn('Error destroying myChartRepairAccounting', e);
                }
                window.myChartRepairAccounting = null;
            }
            const ctx = canvas.getContext('2d');

            // Lấy dữ liệu từ stats
            const unApproveRepair = chartDataSnapshot.unApproveRepair;
            const approveRepair = chartDataSnapshot.approveRepair;
            const unApproveFinishRepair = chartDataSnapshot.unApproveFinishRepair;
            const approveFinishRepair = chartDataSnapshot.approveFinishRepair;

            // Màu sắc cho từng trạng thái
            const outerColors = ['#F1948A', '#85C1E9']; // [Chưa hoàn thành, Đã hoàn thành]
            const innerColors = ['#F7DC6F', '#BB8FCE']; // [Chưa duyệt, Đã duyệt]

            // Ghi chú trạng thái và màu sắc
            const repairLabels = [{
                    label: 'Chưa hoàn thành',
                    color: outerColors[0]
                },
                {
                    label: 'Đã hoàn thành',
                    color: outerColors[1]
                },
                {
                    label: 'Chưa duyệt',
                    color: innerColors[0]
                },
                {
                    label: 'Đã duyệt',
                    color: innerColors[1]
                }
            ];

            // Plugin vẽ tổng ở giữa
            const centerTextPlugin = {
                id: 'repairCenterText',
                afterDraw(chart) {
                    const {
                        ctx,
                        chartArea
                    } = chart;
                    if (!chartArea) return;

                    // Tổng vòng ngoài
                    let totalOuter = 0;
                    const outerDs = chart.data.datasets[0];
                    (outerDs.data || []).forEach(v => {
                        totalOuter += mainNum(v || 0);
                    });

                    const centerX = (chartArea.left + chartArea.right) / 2;
                    const centerY = (chartArea.top + chartArea.bottom) / 2;

                    ctx.save();
                    ctx.font = 'bold 18px sans-serif';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(mainFmt(mainNum(totalOuter), 0), centerX, centerY - 8);

                    ctx.font = '12px sans-serif';
                    ctx.fillStyle = '#666';
                    ctx.fillText('Tổng', centerX, centerY + 14);
                    ctx.restore();
                }
            };

            window.myChartRepairAccounting = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [
                        'Chưa hoàn thành', // outer 0
                        'Đã hoàn thành', // outer 1
                        'Chưa duyệt', // inner 0
                        'Đã duyệt' // inner 1
                    ],
                    datasets: [{
                            label: 'Hoàn thành',
                            data: [unApproveFinishRepair, approveFinishRepair],
                            backgroundColor: outerColors,
                            hoverOffset: 6,
                            radius: '90%',
                            innerRadius: '60%'
                        },
                        {
                            label: 'Duyệt',
                            data: [unApproveRepair, approveRepair],
                            backgroundColor: innerColors,
                            hoverOffset: 6,
                            radius: '75%',
                            innerRadius: '45%'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Ẩn legend mặc định, dùng custom phía dưới
                        },
                        title: {
                            display: true,
                            text: 'Sửa chữa máy móc thiết bị — Chưa/Đã duyệt & hoàn thành'
                        },
                        tooltip: {
                            callbacks: {
                                label(ctx) {
                                    const v = ctx.parsed ?? 0;
                                    let label = '';
                                    if (ctx.datasetIndex === 0) {
                                        label = ctx.dataIndex === 0 ? 'Chưa hoàn thành' : 'Đã hoàn thành';
                                    } else {
                                        label = ctx.dataIndex === 0 ? 'Chưa duyệt' : 'Đã duyệt';
                                    }
                                    return `${ctx.dataset.label} - ${label}: ${mainFmt(mainNum(v), 0)}`;
                                },
                                afterBody(ctx) {
                                    const ds = ctx[0].chart.data.datasets[ctx[0].datasetIndex];
                                    const total = (ds.data[0] ?? 0) + (ds.data[1] ?? 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        }
                    },
                    cutout: '40%'
                },
                plugins: [ChartDataLabels, centerTextPlugin]
            });

            if (ctx.canvas && ctx.canvas.parentNode) {
                ctx.canvas.parentNode.style.height = '350px';
                ctx.canvas.parentNode.style.width = '100%';
            }

            // Vẽ chú thích màu sắc phía dưới chart
            // Xóa chú thích cũ nếu có
            let legendId = 'repair-accounting-legend';
            let legend = document.getElementById(legendId);
            if (legend) legend.remove();

            legend = document.createElement('div');
            legend.id = legendId;
            legend.style.display = 'flex';
            legend.style.justifyContent = 'center';
            legend.style.gap = '28px';
            legend.style.marginTop = '18px';
            legend.style.flexWrap = 'wrap';

            repairLabels.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.style.display = 'flex';
                itemDiv.style.alignItems = 'center';
                itemDiv.style.fontSize = '14px';
                itemDiv.style.margin = '2px 8px';

                const colorBox = document.createElement('span');
                colorBox.style.display = 'inline-block';
                colorBox.style.width = '16px';
                colorBox.style.height = '16px';
                colorBox.style.background = item.color;
                colorBox.style.borderRadius = '3px';
                colorBox.style.marginRight = '7px';
                colorBox.style.border = '1px solid #ccc';

                itemDiv.appendChild(colorBox);
                itemDiv.appendChild(document.createTextNode(item.label));
                legend.appendChild(itemDiv);
            });

            // Thêm legend vào sau canvas
            if (canvas.parentNode) {
                canvas.parentNode.appendChild(legend);
            }
        })();
        (function() {
            const canvas = document.querySelector('.js-chart-plan_propose-accounting');
            if (!canvas) return;

            // Destroy chart chỉ khi dữ liệu thay đổi (theo hash)
            // Tạo hash từ dữ liệu chart
            const chartDataSnapshot = {
                thu_hh: mainNum(stats?.count_plan_propose_thu_hh ?? 0),
                thu_chh: mainNum(stats?.count_plan_propose_thu_chh ?? 0),
                chi_hh: mainNum(stats?.count_plan_propose_chi_hh ?? 0),
                chi_chh: mainNum(stats?.count_plan_propose_chi_chh ?? 0)
            };
            const chartHash = JSON.stringify(chartDataSnapshot);

            if (window.myChartPlanProposeAccountingHash === chartHash && window.myChartPlanProposeAccounting) {
                // Không có thay đổi dữ liệu, không destroy/recreate
                return;
            }
            window.myChartPlanProposeAccountingHash = chartHash;

            if (window.myChartPlanProposeAccounting) {
                try {
                    window.myChartPlanProposeAccounting.destroy();
                } catch (e) {
                    console.warn('Error destroying myChartPlanProposeAccounting', e);
                }
                window.myChartPlanProposeAccounting = null;
            }
            const ctx = canvas.getContext('2d');

            // Lấy dữ liệu từ stats
            const thu_hh = chartDataSnapshot.thu_hh; // Thu chưa hoàn thành
            const thu_chh = chartDataSnapshot.thu_chh; // Thu đã hoàn thành
            const chi_hh = chartDataSnapshot.chi_hh; // Chi chưa hoàn thành
            const chi_chh = chartDataSnapshot.chi_chh; // Chi đã hoàn thành

            // 🧩 Plugin tùy chỉnh vẽ nhãn “Chưa / Đã / Tổng” giống báo cáo trên
            const smartLabelPlugin = {
                id: 'planProposeSmartLabel',
                afterDatasetsDraw(chart) {
                    const ctx = chart.ctx;
                    const scale = chart.scales.x;
                    const meta0 = chart.getDatasetMeta(0);
                    const data0 = chart.data.datasets[0].data;
                    const data1 = chart.data.datasets[1].data;

                    ctx.save();
                    ctx.font = 'bold 11px sans-serif';
                    ctx.textBaseline = 'middle';

                    meta0.data.forEach((bar, i) => {
                        const c0 = data0[i] || 0;
                        const c1 = data1[i] || 0;
                        const total = c0 + c1;
                        if (c0 === 0 && c1 === 0) return;

                        const xStart = scale.getPixelForValue(0);
                        const xEnd = scale.getPixelForValue(total);
                        const barLength = xEnd - xStart;
                        const y = bar.y;

                        const label = `Chưa: ${mainFmt(mainNum(c0), 0)}   Đã: ${mainFmt(mainNum(c1), 0)}   Tổng: ${mainFmt(mainNum(total), 0)}`;

                        const inside = barLength > 200; // tăng lên cho rộng hơn
                        const labelWidth = ctx.measureText(label).width;

                        const xText = inside ?
                            xStart + barLength / 2 - labelWidth / 2 :
                            xEnd + 10;

                        ctx.fillStyle = '#000';
                        ctx.fillText(label, xText, y);
                    });

                    ctx.restore();
                }
            };

            window.myChartPlanProposeAccounting = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Kế hoạch Thu', 'Kế hoạch Chi'],
                    datasets: [{
                            label: 'Chưa hoàn thành',
                            data: [thu_hh, chi_hh],
                            backgroundColor: '#f5b7b1'
                        },
                        {
                            label: 'Đã hoàn thành',
                            data: [thu_chh, chi_chh],
                            backgroundColor: '#82e0aa'
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            right: 200,
                            left: 10,
                            top: 10,
                            bottom: 10
                        } // tăng right padding cho rộng
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 14,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Kế hoạch Thu/Chi — Chưa/Đã hoàn thành',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.x || 0;
                                    return `${label}: ${mainFmt(mainNum(value), 0)}`;
                                },
                                afterBody(context) {
                                    const idx = context[0].dataIndex;
                                    const total =
                                        (context[0].chart.data.datasets[0].data[idx] || 0) +
                                        (context[0].chart.data.datasets[1].data[idx] || 0);
                                    return 'Tổng: ' + mainFmt(mainNum(total), 0);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            grid: {
                                color: '#eee'
                            },
                            min: 0
                        },
                        y: {
                            stacked: true,
                            ticks: {
                                font: {
                                    size: 13
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels, smartLabelPlugin]
            });

            ctx.canvas.parentNode.style.height = "320px";
            ctx.canvas.parentNode.style.width = "100%";
        })();
        (function() {
            const canvas = document.querySelector('.js-chart-recommended_list_group');
            if (!canvas) return;

            // Destroy chart chỉ khi dữ liệu thay đổi (theo hash)
            if (window.myChartRecommendedListGroup) {
                try {
                    window.myChartRecommendedListGroup.destroy();
                } catch (e) {
                    console.warn('Error destroying myChartRecommendedListGroup', e);
                }
                window.myChartRecommendedListGroup = null;
            }

            const ctx = canvas.getContext('2d');

            // Lấy dữ liệu từ stats
            const dxmtkht = mainNum(stats?.recommended_list_group_dxmtkht ?? 0); // Theo kế hoạch
            const dxmnkht = mainNum(stats?.recommended_list_group_dxmnkht ?? 0); // Ngoài kế hoạch
            const dxdgmvkh = mainNum(stats?.recommended_list_group_dxdgmvkh ?? 0); // Vượt kế hoạch
            const dxdgmtkh = mainNum(stats?.recommended_list_group_dxdgmtkh ?? 0); // Thiếu kế hoạch
            const dxtsdgmk = mainNum(stats?.recommended_list_group_dxtsdgmk ?? 0); // Khẩn

            window.myChartRecommendedListGroup = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        'Theo kế hoạch',
                        'Ngoài kế hoạch',
                        'Vượt kế hoạch',
                        'Thiếu kế hoạch',
                        'Khẩn'
                    ],
                    datasets: [{
                        label: 'Số lượng',
                        data: [dxmtkht, dxmnkht, dxdgmvkh, dxdgmtkh, dxtsdgmk],
                        backgroundColor: [
                            '#5dade2',
                            '#58d68d',
                            '#f4d03f',
                            '#f1948a',
                            '#af7ac5'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Tổng hợp đề xuất mua theo nhóm',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y || 0;
                                    return `${context.label}: ${mainFmt(mainNum(value), 0)}`;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#222',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: function(value) {
                                return mainFmt(mainNum(value), 0);
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: false
                            },
                            grid: {
                                color: '#eee'
                            },
                            ticks: {
                                font: {
                                    size: 13
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            grid: {
                                color: '#eee'
                            },
                            ticks: {
                                font: {
                                    size: 13
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            ctx.canvas.parentNode.style.height = "320px";
            ctx.canvas.parentNode.style.width = "100%";
        })();
    }

    function count_accounting() {
        if ($('#dashboard-accounting').hasClass('active')) {
            $.getJSON("<?= site_url('Dashboard_srceen_office/count_accounting') ?>", res => {
                if (!res || !res.success) return;
                UpdateStatsAccounting(res.stats || {});
            });
        }
    }
    count_accounting();
    setInterval(count_accounting, 20000);
</script>