<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .wap-container {
    background: #2270ab;
  }
  .absolute {
    position: absolute;
    width: calc(100% - 30px);
  }
  .relative {
    position: relative;
  }
  .wap-title {
    padding: 10px 10px;
    margin-top: 5px;
  }
  .wap-content {
    text-align: center;
    padding: 10px 10px;
    margin: 5px 0;
  }
  .wap-left {
    float: left;
    width: calc(40% - 20px);
    margin: 0 10px;
  }
  .wap-left .wap-title {
    color: #fff;
    background: red;
  }
  .wap-left .wap-content {
    background: #96b6f1;
  }
  .wap-center {
    position: relative;
    float: left;
    width: 30%;
  }
  .wap-center .wap-title {
    color: #fff;
    background: #001665;
  }
  .wap-center .wap-content {
    cursor: pointer;
    text-align: left;
    padding: 10px 10px;
    background: #fff;
  }
  .wap-center .wap-content:hover {
    box-shadow: 0 20px 10px -10px rgba(31, 31, 31, 0.5);
  }
  .wap-center:not(.title):hover {
    top: -2px;
  }
  .wap-right {
    float: left;
    width: calc(30% - 20px);
    margin: 0 10px;
  }
  .wap-right .wap-title {
    color: #fff;
    background: #001665;
  }
  .wap-right .wap-content {
    padding: 0 0;
    background: #fff;
  }
  .wap-right .wap-content .wap-percent {
    text-align: center;
    color: #fff;
    background: red;
    padding: 10px 0;
  }
  .scoll-content {
    overflow: hidden;
  }
  .wap-hide-content {
    visibility: hidden;
    opacity: 0;
    transition: visibility 0s, opacity 0.5s linear;

    max-height: 300px;
    overflow: overlay;
    position: absolute;
    background: #fff;
    border-radius: 3px;
    padding: 10px;
    width: 100%;
    top: 91%;
    z-index: 999;
    box-shadow: 0 15px 10px -10px rgba(31, 31, 31, 0.5);
  }
  .drop-down {
    position: absolute;
    z-index: 1;
    bottom: 3px;
    left: 49%;
  }
  .drop-down i {
    font-size: 20px;
    color: #a2a2a2;
  }
  .wap-center.active {
    top: -2px;
  }
  .wap-center.active .wap-content i {
    -ms-transform: rotate(180deg); /* IE 9 */
    -webkit-transform: rotate(180deg); /* Safari prior 9.0 */
    transform: rotate(180deg); /* Standard syntax */
    transition: all 0.5s;
  }
  .wap-center.active .wap-content .drop-down {
    bottom: -3px;
  }
  .wap-center.active .wap-hide-content {
    visibility: visible;
    opacity: 1;
    transition: visibility 0s, opacity 0.5s linear;
  }
  .wap-center.unactive .wap-content i {
    -ms-transform: rotate(0deg); /* IE 9 */
    -webkit-transform: rotate(0deg); /* Safari prior 9.0 */
    transform: rotate(0deg); /* Standard syntax */
    transition: all 0.5s;
  }
  .wap-center.unactive .wap-content .drop-down {
    bottom: 3px;
  }
  .wap-center.unactive .wap-hide-content {
    visibility: hidden;
    opacity: 0;
    transition: visibility 0.5s, opacity 0.5s linear;
  }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
          <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
      </div>
    </div>
    <div class="screen-options-area"></div>
    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <?php hooks()->do_action( 'before_start_render_dashboard_content' ); ?>

            <div class="clearfix"></div>
            <div class="col-md-6" data-container="top-left-md-6">
                <?php render_dashboard_widgets_screen_work_customer('top-left-md-6'); ?>
            </div>
            <div class="col-md-6" data-container="top-right-md-6">
                <?php render_dashboard_widgets_screen_work_customer('top-right-md-6'); ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6" data-container="second-left-md-6">
                <?php render_dashboard_widgets_screen_work_customer('second-left-md-6'); ?>
            </div>
            <div class="col-md-6" data-container="second-right-md-6">
                <?php render_dashboard_widgets_screen_work_customer('second-right-md-6'); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/screen_work/dashboard_js'); ?>
<?php init_tail(); ?>
</body>
</html>