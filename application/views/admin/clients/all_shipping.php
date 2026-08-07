<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title">
                    <?= $title ?>
                </span>
                <div class="pull-right mright5 H_border hide">
                    <a class="btn btn-info test H_action_button">
                        <?php echo _l('Export excel'); ?></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($consent_purposes)) { ?>
                            <div class="row mbot15">
                                <div class="col-md-3 contacts-filter-column">
                                    <div class="select-placeholder">
                                        <select name="custom_view" title="<?php echo _l('gdpr_consent'); ?>" id="custom_view" class="selectpicker" data-width="100%">
                                            <option value=""></option>
                                            <?php foreach ($consent_purposes as $purpose) { ?>
                                                <option value="consent_<?php echo $purpose['id']; ?>">
                                                    <?php echo $purpose['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_contacts_clients">
                                <?php
									$table_data = [
                                            'STT',
                                            'Khách hàng',
                                            'Địa chỉ giao hàng',
                                            'Người LH(ĐT)',
                                            'Khu giao hàng',
                                            'Người giao hàng',
                                            'Số điện thoại',
                                            'Địa chỉ giao hàng',
                                            'Quận Huyện',
                                            'Địa chỉ giao hàng chính',
                                    ];
                                    render_datatable($table_data, 'shipping_client');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/clients/client_js'); ?>
<div id="contact_data"></div>
<div id="consent_data"></div>

<?php $this->load->view('admin/clients/shipping_client'); ?>
<script>
	
    $(function () {
        var optionsHeading = [];
        var allContactsServerParams = {
            "custom_view": "[name='custom_view']",
        }
        var _table_api = initDataTable('.table-shipping_client', window.location.href, optionsHeading, optionsHeading, allContactsServerParams, [0, 'asc']);
    });
</script>
