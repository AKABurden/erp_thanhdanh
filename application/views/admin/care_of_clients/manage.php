<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $this->load->view('admin/care_of_clients/style_css')?>

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a class="btn btn-info mright5 test pull-right H_action_button">
               <?php echo _l('Export excel'); ?></a>
            <div class="line-sp"></div>
            <a href="#" class="btn btn-info pull-right H_action_button" onclick="editCare_of_clients()">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('cong_button_add'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div  class="btn-group mbot15">
                            <button type="button" data-toggle="tab" class="btn font11 btn-filter filter_all btn-icon btn-info active"><?=_l('cong_all')?></button>
                            <?php if(!empty($client_detail)){?>
                                <?php foreach($client_detail as $key => $value){
                                    $tbody_search = "<div class='div-form-group' id_data='".$value['id']."'>";
                                    $tbody_search .= "    <div class='form-group'>
                                                                <label  class='control-label'>"._l('cong_date_start')."</label>
                                                                <div class='input-group date'>
                                                                    <input type='text' class='form-control datepicker date_start_filter' id_data='".$value['id']."' value='' autocomplete='off' aria-invalid='false'>
                                                                    <div class='input-group-addon'><i class='fa fa-calendar calendar-icon'></i></div>
                                                                </div>
                                                          </div>";
                                    $tbody_search .= "    <div class='form-group'>
                                                                <label class='control-label'>"._l('cong_date_end')."</label>
                                                                <div class='input-group date'>
                                                                    <input type='text'  class='form-control datepicker date_end_filter' id_data='".$value['id']."' value='' autocomplete='off' aria-invalid='false'>
                                                                    <div class='input-group-addon'><i class='fa fa-calendar calendar-icon'></i></div>
                                                                </div>
                                                          </div>";
                                    $tbody_search .= "    <button type='button' class='btn btn-success search-btn'>"._l('cong_filter_data')."</button>";
                                    $tbody_search .= "</div>"
                                    ?>
                                        <button type="button" data-toggle="popover" id_data="<?=$value['id']?>" data-placement="bottom" data-html="true" data-content="<?=$tbody_search?>" class="btn-filter btn font11 btn-icon btn-info">
                                            <?=$value['name']?>
                                        </button>
                                <?php } ?>
                            <?php } ?>
                            <?php echo form_hidden('date_start'); ?>
                            <?php echo form_hidden('date_end'); ?>
                            <?php echo form_hidden('procedure'); ?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                            <?php echo render_input('name_client', 'cong_name_client');?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_input('code_care_of', 'cong_code_care_of');?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_input('code_client', 'cong_code_client');?>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_input('vip_code', 'vip_code');?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3">
                            <?php
                                $data_vip_rating = [
                                        ['id' => '1', 'name' => _l('cong_1_start')],
                                        ['id' => '2', 'name' => _l('cong_2_start')],
                                        ['id' => '3', 'name' => _l('cong_3_start')],
                                        ['id' => '4', 'name' => _l('cong_4_start')],
                                        ['id' => '5', 'name' => _l('cong_5_start')]
                                ];
                                echo render_select('vip_rating_lead', $data_vip_rating, ['id', 'name'], 'cong_vip_rating');
                            ?>
                        </div>
                        <div class="clearfix"></div>

                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                            <?php render_datatable(array(
                                _l('cong_fullcode_care_of'),
                                _l('cong_client'),
                                _l('cong_date_start'),
                                _l('cong_priority'),
                                _l('cong_rating'),
                                _l('cong_orders'),
                                _l('cong_theme_of'),
                                _l('cong_event_care_of'),
                                _l('cong_solution'),
                                _l('cong_staff_success'),
                                _l('cong_date_create'),
                                _l('cong_create_by'),
                                _l('cong_date_client_contact'),
                                '<p class="mw600 text-center">'._l('cong_step_care_of').'</p>',
                            ),'care_of_clients'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_care_of_clients" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<?php init_tail(); ?>
<?php $this->load->view('admin/care_of_clients/script_js')?>


</body>
</html>
