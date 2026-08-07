<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="bold no-margin font-medium">
              <?php echo $title; ?>
            </h4>
            <hr />
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="row">
              <div class="col-md-12">
                 <?php echo render_input('name','template_name', (!empty($template) ? ($template->name) : ''),'text'); ?>
                <hr />
                <?php
                  $editors = array();
                  array_push($editors,'content');
                ?>
                <p class="bold"><?php echo _l('cong_content'); ?></p>
                <?php echo render_textarea('content','', (!empty($template) ? ($template->content) : ''),array('data-url-converter-callback'=>'myCustomURLConverter'),array(),'','tinymce tinymce-manual'); ?>
                
                <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
              </div>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script>
  $(function(){
    <?php foreach($editors as $id){ ?>
      init_editor('textarea[name="<?php echo $id; ?>"]',{urlconverter_callback:'merge_field_format_url'});
      <?php } ?>
      var merge_fields_col = $('.merge_fields_col');
        // If not fields available
        $.each(merge_fields_col, function() {
          var total_available_fields = $(this).find('p');
          if (total_available_fields.length == 0) {
            $(this).remove();
          }
        });
    // Add merge field to tinymce
    $('.add_merge_field').on('click', function(e) {
     e.preventDefault();
     tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).text());
   });
    _validate_form($('form'), {
      name: 'required',
    });
  });
</script>
</body>
</html>
