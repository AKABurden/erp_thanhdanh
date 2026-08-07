
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title">Import kế hoạch chi phí</span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
        <div class="col-md-4">
            <label>Năm</label>
              <select name="year_sales_ss" id="year_sales_ss" class="selectpicker" data-width="100%" data-live-search="true" tabindex="-98">
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
        <div class="clearfix"></div>
        <br>
        <br>
                    <a onclick="excel(); return false;" href="#" target="_blank" class="btn btn-success">Download Sample</a>
                    <hr />
                    <!-- <?php echo form_close(); ?> -->
                       <?php $max_input = ini_get('max_input_vars');
                       if(($max_input>0 && isset($total_rows_post) && $total_rows_post >= $max_input)){ ?>
                        <div class="alert alert-warning">
                            Your hosting provider has PHP setting <b>max_input_vars</b> at <?php echo $max_input;?>.<br/>
                            Ask your hosting provider to increase the <b>max_input_vars</b> setting to <?php echo $total_rows_post;?> or higher or import less rows.
                        </div>
                        <?php } ?>
                        <?php

                        if(!isset($simulate) > 0) { ?>
                        <p>
                        </p>
                        
                        <?php } ?>

                        <?php if(isset($message)) { ?>
                        
                        <div class="panel-body" style="margin-bottom: 20px">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h3>Kết quả nhập</h3> <br />
                                <?php echo $message?>
                                
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?php if(isset($row_imported)) : ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <strong><?php echo _l('category_import_success') . $row_imported; ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                                <?php echo form_hidden('leads_import','true'); ?>
                                <?php echo render_input('file_import','import_choose_file','','file'); ?>
                                <div class="form-group">
                                    <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                                </div>
                                <?php echo form_close(); ?>
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
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
    function excel() {
        var  year = $('#year_sales_ss').val();
        var  month = $('#month_fin').val();
        if(year == '' || month == '')
        {
            alert('Bạn chưa chọn tháng hoặc năm');
            return;
        }
        var link = '<?php echo admin_url('costs/excel/') ?>'+month+'/'+year;
        window.open(link);
    }
    _validate_form($('#import_form'),{file_csv:{required:true,extension: "csv"},source:'required',status:'required'});
    $(function(){
     $('.btn-import-submit').on('click',function(){
       if($(this).hasClass('simulate')){
         $('#import_form').append(hidden_input('simulate',true));
       }
       $('#import_form').submit();
     });
    })
</script>
</body>
</html>