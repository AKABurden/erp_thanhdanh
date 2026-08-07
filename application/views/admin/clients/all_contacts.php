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


                        <!--Công bổ sung-->
                        <div class="horizontal-scrollable-tabs preview-tabs-top">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav-tabs-horizontal nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_contacts_clients" aria-controls="tab_contacts_clients" role="tab" data-toggle="tab">
                                            <?=_l('cong_contacts_clients')?>
                                        </a>
                                    </li>
                                    <li role="presentation" class="hide">
                                        <a href="#tab_contacts_leads" onclick="loadTableContactLead()" aria-controls="tab_contacts_leads" role="tab" data-toggle="tab">
                                            <?=_l('cong_contacts_leads')?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!--End công bổ sung-->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_contacts_clients">
                                <?php
                                $table_data = array(_l('cong_last_firstname'));
                                if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
                                    array_push($table_data, array(
                                        'name' => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                        'th_attrs' => array('id' => 'th-consent', 'class' => 'not-export')
                                    ));
                                }
                                $table_data = array_merge($table_data, array(
                                    _l('client_email'),
                                    _l('clients_list_company'),
                                    _l('client_phonenumber'),
                                    _l('contact_position'),
                                    _l('cong_birtday'),
                                    _l('cong_note'),
                                ));
                                $custom_fields = get_custom_fields('contacts', array('show_on_table' => 1));
                                foreach ($custom_fields as $field) {
                                    array_push($table_data, $field['name']);
                                }
                                render_datatable($table_data, 'all-contacts');
                                ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_contacts_leads">
                                <?php
                                $table_data = array(_l('cong_last_firstname'));
                                $table_data = array_merge($table_data, array(
                                    _l('client_email'),
                                    _l('clients_list_company'),
                                    _l('client_phonenumber'),
                                    _l('cong_client_localtion'),
                                    _l('cong_birtday'),
                                    _l('cong_contacts_is_primary'),
                                    _l('cong_note')
                                ));
                                render_datatable($table_data, 'all-contacts_lead');
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
<script>
    $(function () {
        var optionsHeading = [];
        var allContactsServerParams = {
            "custom_view": "[name='custom_view']",
        }
        <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        optionsHeading.p ush($('#th-consent').index());
        <?php } ?>
        var _table_api = initDataTable('.table-all-contacts', window.location.href, optionsHeading, optionsHeading, allContactsServerParams, [0, 'asc']);
        if (_table_api) {
            <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
            _table_api.on('draw', function () {
                var tableData = $('.table-all-contacts').find('tbody tr');
                $.each(tableData, function () {
                    $(this).find('td:eq(2)').addClass('bg-light-gray');
                });
            });
            $('select[name="custom_view"]').on('change', function () {
                _table_api.ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
            <?php } ?>
        }
    });

    function loadTableContactLead()
    {
        initDataTable('.table-all-contacts_lead', admin_url+'leads/tableContactsLead', [], [], {}, [0, 'asc']);
    }
</script>
<?php $this->load->view('admin/leads/cong_js/contact_js')?>
</body>
</html>
