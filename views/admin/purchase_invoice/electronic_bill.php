<div class="modal fade in" id="electronic_bill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('ch_electronic_bill'); ?> </span>
            </h4>
          </div>
          <div class="modal-body">
            <input type="text" value="<?=$id_invoice?>" class="hide" id="id_invoice">
            <?php $value = (isset($invoice) ? $invoice->link : ''); ?>
            <?php echo render_input_ch('link','Link',$value); ?>
            <div class="clearfix"></div>
            <?php echo form_open(admin_url('purchase_invoice/add_attachment/'.$id_invoice),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
            <?php echo form_close(); ?>
        <div class="media lead-note">
            <div class="media-body">
                <div class="image-set file_images">
                    <?php
                    $j = 0;
                    $i = 0;
                     foreach ($invoice->attachments as $key=>$value) {?>
                        <?php if(substr($value['filetype'],0,5)=='image'){ ?>
                          <div class="preview_image id_images_<?=$key?>" style="height: 150px;width: auto;float: left;margin: 0 !important;margin-left: 10px;">
                              <div class="display-block contract-attachment-wrapper ">
                                 <a href="#" class="pull-right text-danger" onclick="delete_file_images(<?= $key ?>,<?= $value['id'] ?>);return false;">
                                <i class="fa fa fa-times"  style="top:0px;right: 0px;color: red;"></i>
                                </a>  
                                  <div style="width:100px">
                                      <a href="<?=base_url().'uploads/invoices/'.$id_invoice.'/'.$value['file_name']?>" data-lightbox="customer-profile" class="display-block mbot5">
                                          <div class="table-image">
                                              <img src="<?=base_url().'uploads/invoices/'.$id_invoice.'/'.$value['file_name']?>" style="width:100px;height:100px;" />
                                          </div>
                                      </a>
                                  </div>
                                 
                              </div>
                          </div>


                            <?php $j++;$i++;}?>
                        <?php }
                        ?>
                    </div>
                    <div class="clearfix"></div>

                </div>
            </div>
          </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
              </div>
          </div>
      </div>
  </div>
</div>
<script type="text/javascript">
  $('#run_href').click(function(e) {
    var target = $(e.currentTarget);
    if(target.attr('data-href') != '')
    {
      window.open(target.attr('data-href'), "_blank"); 
      return;
    }
  return;
  });
  function new_link(e) {
    var target = $('#run_href');
    target.attr('type','text');
    $('.suppliers-field-new').attr('onclick','update_link(this);return false;');
    $('#icon_hau').attr('class','fa fa-check');
    target.attr('data-href','');
  }
  function update_link(e) {
    var target = $('#run_href');
    var id = $('#id_invoice').val();
    target.attr('type','text');
    $('.suppliers-field-new').attr('onclick','new_link(this);return false;');
    $('#icon_hau').attr('class','fa fa-plus');
    target.attr('data-href',target.val());
    target.attr('type','submit');

    dataString={link:target.val(),[csrfData['token_name']] : csrfData['hash']};
        jQuery.ajax({   
            type: "post",
            url:"<?=admin_url()?>purchase_invoice/update_link/"+id,
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                tAPI.draw('page');
                alert_float(response.alert_type, response.message);
            }
        });
  return false;
  } 
var j=<?=$j;?>;
     Dropzone.autoDiscover = false;
   $(function () {

    if ($('#contract-attachments-form').length > 0) {
       new Dropzone("#contract-attachments-form",appCreateDropzoneOptions({
          acceptedFiles: '.png,.jpg',
          success: function (file, response) {
             if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                response = JSON.parse(response);
                alert_float(response.alert_type,response.message);
                if(response.success)
                {   
                        if(response.result.image == true)
                        {
                            $('.file_images').append('<div class="preview_image id_images_'+j+'" style="height: 150px;width: auto;float: left;margin: 0 !important;margin-left: 10px;">\
                              <div class="display-block contract-attachment-wrapper ">\
                                 <a href="#" class="pull-right text-danger" onclick="delete_file_images('+j+','+response.result.id+');return false;">\
                                <i class="fa fa fa-times"  style="top:0px;right: 0px;color: red;"></i>\
                                </a>  \
                                  <div style="width:100px">\
                                      <a href="<?=base_url().'uploads/invoices/'.$id_invoice.'/'?>'+response.result.file_name+'" data-lightbox="customer-profile" class="display-block mbot5">\
                                          <div class="table-image">\
                                              <img src="<?=base_url().'uploads/invoices/'.$id_invoice.'/'?>'+response.result.file_name+'" style="width:100px;height:100px;" />\
                                          </div>\
                                      </a>\
                                  </div>\
                              </div>\
                          </div>');
                        }
                        else
                        {
                            $('.new_file_div').append('<div class="media lead-note file_'+j+'">'+
                                '                       <div class="media-body"><i class="mime mime-file"></i><a href="<?=base_url()?>download/import/<?=$id_invoice?>/'+response.result.file_name+'"> +'+response.result.file_name+'</a>'+
                                '                          <a href="#" class="pull-right text-danger" onclick="delete_file('+j+','+response.result.id+');return false;"><i class="fa fa fa-times"></i></a>' +
                                '                  <hr></div>');
                        }
                    j++;
                }
             }
          }
       }));
    }
    });
   function delete_file_images(id,idd)
   {
       if(confirm('<?=_l('ch_delete_file')?>'))
       {
           $('.id_images_'+id).remove();
           $.post(admin_url + 'purchase_invoice/delele_file/'+idd+'/',{[csrfData['token_name']] : csrfData['hash']}).done(function(response){});
      
       }
   }
</script>