<div role="tabpanel" class="tab-pane" id="tab_contacts_lead">
        <?php $table_data = array(_l('cong_last_firstname'));
        $table_data = array_merge($table_data, array(
            _l('client_email'),
            _l('clients_list_company'),
            _l('client_phonenumber'),
            _l('cong_client_localtion'),
            _l('cong_birtday'),
            _l('cong_contacts_is_primary'),
            _l('cong_note')
        ));
        render_datatable($table_data, 'contacts_lead'); ?>
</div>

<div id="contact_data"></div>
<?php $this->load->view('admin/leads/cong_js/contact_js')?>