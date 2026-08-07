<div id="cash-flow" class="hide">
    <div class="col-md-4">
            <label>Năm</label>
              <select name="year_fin" id="year_fin" class="selectpicker" data-width="100%" data-live-search="true" tabindex="-98">
                  <?php
                  $data=date('Y');
                  for($i=$data-5;$i<=$data+5;$i++)
                  {
                      ?>
                      <option value="<?=$i?>" <?=($i==$data)?'selected':''?>>Năm:<?=$i?></option>
                      <?php
                  }
                  ?>
              </select>
        </div>
        <div class="col-md-4">
            <?php
            $month = array(
                            array(
                                'id' => '1',
                                'name' => 'Tháng 1',
                            ),
                            array(
                                'id' => '2',
                                'name' => 'Tháng 2',
                            ),
                            array(
                                'id' => '3',
                                'name' => 'Tháng 3',
                            ),
                            array(
                                'id' => '4',
                                'name' => 'Tháng 4',
                            ),
                            array(
                                'id' => '5',
                                'name' => 'Tháng 5',
                            ),
                            array(
                                'id' => '6',
                                'name' => 'Tháng 6',
                            ),
                            array(
                                'id' => '7',
                                'name' => 'Tháng 7',
                            ),
                            array(
                                'id' => '8',
                                'name' => 'Tháng 8',
                            ),
                            array(
                                'id' => '9',
                                'name' => 'Tháng 9',
                            ),
                            array(
                                'id' => '10',
                                'name' => 'Tháng 10',
                            ),
                            array(
                                'id' => '11',
                                'name' => 'Tháng 11',
                            ),
                            array(
                                'id' => '12',
                                'name' => 'Tháng 12',
                            ),
                        );
            echo render_select('month_fin', $month, array('id', 'name'),'Tháng',date('m'),array(),array(),'','',false);
            ?>
          </div>
        <div class="col-md-4">
            <?php echo render_select('id_new',$costs, array('idd', 'name'), 'Danh mục'); ?>
        </div>
    <div class="clearfix"></div>
    <?php
    $table_columns=array(
        _l('STT'),
        _l('Mã mục cha'),
        _l('Mã chỉ tiêu'),
        _l('Tên khoản mục phí và chi tiêu'),
        _l('Kế hoạch'),
        _l('Đã chi'),
        _l('Chênh lệch'),
        _l('Tỷ trọng'),
        );
    render_datatable($table_columns,'report_financial'); ?>
    <div class="clearfix"></div>
</div>









