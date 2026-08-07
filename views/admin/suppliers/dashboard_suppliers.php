<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .panel-body {
        text-align: center;
    }
    .success {
        color: #3c763d;
    }
    .primary {
        color: #3197d0;
    }
    .warning {
        color: #e6b66a;
    }
    .danger {
        color: #a94442;
    }
    .font_2em {
        font-size: 2em;
    }
    .content-list-client {
        padding: 10px 0;
    }
    .content-list-client:not(:last-child) {
        border-bottom: 1px solid #d4d4d4;
    }
    .img-client {
        float: left;
        width: 10%;
        text-align: left;
    }
    .img-client img {
        border-radius: 50%;
        width: 25px;
        height: 25px;
    }
    .name-client {
        float: left;
        width: 60%;
        text-align: left;
    }
    .type-client {
        float: right;
        width: 30%;
        text-align: right;
    }
    .scroll_list {
        max-height: 300px;
        overflow: auto;
    }
    canvas {
        height: unset;
    }

    .step-status {
        float: left;
        width: 20%;
        text-align: center;
        padding: 0 10px;
    }
    .step-status img{
        position: relative;
        cursor: pointer;
        z-index: 0;
    }
    .step-status .active img {
        border: 3px solid #4ab138;
    }
    .step-status .cancel img {
        border: 3px solid #f00;
    }
    .line {
        border: 1px solid #7d7d7d;
        position: relative;
        height: 1px;
        width: 100%;
        top: 40px;
        z-index: 0;
    }
    .line10:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 10%;
        height: 1px;
        border: 1px solid #4ab138;
    }
    .line30:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 30%;
        height: 1px;
        border: 1px solid #4ab138;
    }
    .line50:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 50%;
        height: 1px;
        border: 1px solid #4ab138;
    }
    .line70:before {
        content: "";
        position: absolute;
        top: -1px;
        display: block;
        width: 70%;
        height: 1px;
        border: 1px solid #4ab138;
    }
    .no-drop img {
        cursor: no-drop;
    }

    .table-purchase-order tbody tr th:nth-child(1){
        min-width: 40px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tbody tr td:nth-child(1){
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(2){
        min-width: 50px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr th:nth-child(3){
        min-width: 100px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(3){
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(4){
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr th:nth-child(5){
        min-width: 200px;
        white-space: unset;
    }
    .table-purchase-order tr td:nth-child(5){
        min-width: 200px;
        white-space: unset;
    }
    .table-purchase-order tr td:nth-child(6){
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(7){
        min-width: 90px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(8){
        min-width: 110px;
        white-space: unset;
        text-align: center;
    }
    .table-purchase-order tr td:nth-child(11){
        min-width: 200px;
        white-space: unset;
    }
    .table-purchase-order thead tr th{
        text-align: center;
    }
    .wap-icon {
        float: left;
        width: 20%;
    }
    .wap-icon img {
        cursor: pointer;
        position: relative;
    }
    .wap-icon img:hover {
        top: -5px;
        transition: all 0.5s;
    }
    .wap-icon.active .wap-title span {
        color: #2887d4;
        border: 2px solid #2887d46b;
        padding: 5px 25px;
    }
    .wap-icon.active .wap-title span::before {
        content: "✔";
        margin-right: 5px;
    }
    .wap-title {
        margin-top: 10px;
    }
    .wap-title-status {
        margin-top: 20px;
    }
    .wap-title-status {
        position: relative;
    }
    .wap-title-status::before {
        content: "";
        width: 10px;
        height: 10px;
        position: absolute;
        background: #7d7d7d;
        border-radius: 50%;
        top: -16px;
        left: calc(50% - 5px);
    }
    .wap-title-status.success::before {
        background: #4ab138;
    }
    .dt-buttons {
        display: block;
    }
    .table-purchase-order .delete-remind {
        display: none;
    }
</style>
<div id="wrapper">
    <div class="screen-options-area"></div>
    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>
            <div class="clearfix"></div>

            <div class="col-md-4" data-container="top-left-md-3">
                <?php render_dashboard_widgets_suppliers('top-left-md-3'); ?>
            </div>

            <div class="col-md-4" data-container="top-middle-left-md-3">
                <?php render_dashboard_widgets_suppliers('top-middle-left-md-3'); ?>
            </div>
            <div class="col-md-4" data-container="top-middle-right-md-3">
                <?php render_dashboard_widgets_suppliers('top-middle-right-md-3'); ?>
            </div>
            <div class="col-md-12" data-container="table_purchase_order">
                <?php render_dashboard_widgets_suppliers('table_purchase_order'); ?>
            </div>
        </div>
    </div>
</div>
<script>
</script>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<?php $this->load->view('admin/suppliers/dashboard/dashboard_js'); ?>
</body>
</html>