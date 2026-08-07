<?php init_head(); ?>
<style type="text/css">
  .table-financial tr td:nth-child(4) {
    max-width: 200px;
    white-space: inherit;
    min-width: 200px;
  }

  .table-financial tr td:nth-child(3) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
  }

  .table-financial tr td:nth-child(2) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
  }

  .table-financial tr td:nth-child(7) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
    text-align: right;
  }

  .table-financial tr td:nth-child(8) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(9) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(10) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(11) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(12) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(13) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(14) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(15) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(16) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(17) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }

  .table-financial tr td:nth-child(18) {
    max-width: 100px;
    white-space: inherit;
    min-width: 100px;
    text-align: right;
  }
</style>

<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
      <div class="_buttons">
        <span class="bold uppercase fsize18 H_title">Kế hoạch chi phí</span>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="clearfix"></div>
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-12">
                <div class="clearfix"></div>
                <div class="panel_s">
                  <div class="panel-body">
                    <a href="<?= admin_url('costs/import') ?>" class="btn mright5 btn-info pull-left display-block"><?php echo _l('import kế hoạch'); ?></a>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                      <label>Năm</label>
                      <select name="year_sales_ss" id="year_sales_ss" class="selectpicker" data-width="100%" data-live-search="true" tabindex="-98">
                        <?php
                        $data = date('Y');
                        for ($i = $data - 5; $i <= $data + 5; $i++) {
                        ?>
                          <option value="<?= $i ?>" <?= ($i == $data) ? 'selected' : '' ?>>Năm:<?= $i ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <?php echo render_select('id_new', $financial, array('idd', 'name'), 'Danh mục'); ?>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <br>
                    <?php
                    $table_columns = array(
                      _l('STT'),
                      _l('Mã mục cha'),
                      _l('Mã chỉ tiêu'),
                      _l('Tên khoản mục phí và chi tiêu'),
                      _l('Cấp'),
                      _l('scheme_year')
                    );
                    foreach (get_months() as $key => $month) {
                      array_push($table_columns, $month);
                    }
                    render_datatable($table_columns, 'financial'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  $('body').on('click', '.editDataTable_ch', function(e) {
    var type = $(this).attr('data-type');
    var client = $(this).attr('data-client');
    var _td = $(this).parents('td');
    _td.find('.lableScript').addClass('hide');
    _td.find('.inputScript').removeClass('hide');
    appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
  })
  $('body').on('click', '.closeEditData', function(e) {
    var type = $(this).attr('data-type');
    var client = $(this).attr('data-client');
    var _td = $(this).parents('td');
    _td.find('.lableScript').removeClass('hide');
    _td.find('.inputScript').addClass('hide');
    var id = _td.find('.inputScript').find('input#id_ch').val();
    _td.find('.inputScript').find('input.ChangeDataTable').val($('#price_items_text_v2_' + id).text());
    appValidateForm($('.formUpdateDataTable'), {}, manage_Udpdatecolum);
  })

  function manage_Udpdatecolum(form) {
    var data = $(form).serialize();
    if (typeof(csrfData) !== 'undefined') {
      data[csrfData['token_name']] = csrfData['hash'];
    }
    action = form.action;
    return $.post(action, data).done(function(form) {
      form = JSON.parse(form)
      $('.table-financial').DataTable().ajax.reload();
      alert_float('success', 'Cập nhật giá thành công!');
    }), !1
  }

  function check(code, i) {
    $('#check_' + code + '_' + i).removeClass('hide');
    $('#change_' + code + '_' + i).addClass('hide');
  }
  var filterList = {
    'year_sales_ss': '[name="year_sales_ss"]',
    'id_new': '[name="id_new"]',
  };
  var headers_sales = $('.table-financial').find('th');
  var not_sortable_sales = (headers_sales.length - 1);
  var not_sotable = $('.table-schemes').find('th').length - 1;
  tableSale = initDataTable('.table-financial', window.location.href, [not_sotable], [not_sotable], filterList, [5, 'ASC']);
  $.each(filterList, (filterIndex, filterItem) => {
    $('' + filterItem).on('change', () => {
      $('.table-financial').DataTable().ajax.reload();
    });
  });
</script>