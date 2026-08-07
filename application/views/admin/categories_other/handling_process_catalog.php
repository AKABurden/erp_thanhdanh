<?php echo form_open_multipart('admin/categories_other/handlingProcessCatalog/'.$id.'/'.$status, array('id' => 'handling-process-catalog')); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Mã quy trình', 'code') ?>
                        <input type="text" name="code" id="code" placeholder="<?= lang('Mã quy trình') ?>" class="form-control code" value="<?= !empty($process_catalog) ? $process_catalog['code'] : '' ?>" required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('Tên quy trình', 'name') ?>
                        <input type="text" name="name" id="name" placeholder="<?= lang('Tên quy trình') ?>" class="form-control name" value="<?= !empty($process_catalog) ? $process_catalog['name'] : '' ?>" required="required">
                    </div>
                </div>
                <div class="col-md-12">
                    <?= lang('Các bước thực hiện', 'step') ?>
                    <table id="tb-process-catalog" class="table table-hover table-cs dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">
                                    <a class="hover-svg dropdown-toggle add-row" onclick="addProcessCatalog()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                        </svg>
                                    </a>
                                </th>
                                <th><?= lang('Nội dung các bước') ?><span class="text-danger">*</span></th>
                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 0; ?>
                            <?php if(!empty($process_catalog['steps'])): ?>
                                <?php $steps = json_decode($process_catalog['steps'], true); ?>
                                <?php foreach($steps as $key => $value): ?>
                                    <?php 
                                        $tdNumbers = '<td class="text-center td-numbers"></td>';
                                        
                                        $tdContent = '<td>
                                            <input type="text" name="steps['.$counter.'][content]" class="form-control withdraw_check" value="'.$value['content'].'" placeholder="'.lang('Nội dung các bước').'">
                                        </td>';

                                        $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcessCatalog(this)"></i></td>';

                                        $trProcessCatalog = '<tr>
                                            '.$tdNumbers.'
                                            '.$tdContent.'
                                            '.$tdActions.'
                                        </tr>';
                                        echo $trProcessCatalog;
                                        $counter++;
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="save" id="save" class="form-control" value="1">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    var counter = <?= $counter ?? 0 ?>;
    function totalProcessCatalog()
    {
        tbProcessCatalog = '#tb-process-catalog tbody tr';
        var nProcessCatalog = $(tbProcessCatalog).length;
        var sttProcessCatalog = 0;
        for (iProcessCatalog = 0; iProcessCatalog < nProcessCatalog; iProcessCatalog++)
        {
            sttProcessCatalog++;
            elProcessCatalog = $(tbProcessCatalog)[iProcessCatalog];
            $(elProcessCatalog).find('.td-numbers').html(sttProcessCatalog);
        }
    }

    function removeProcessCatalog(_this) {
        $(_this).closest('tr').remove();
        totalProcessCatalog();
    }

    function addProcessCatalog()
    {
        tdNumbers = `<td class="text-center td-numbers"></td>`;
        tdContent = `<td>
            <input type="text" name="steps[${counter}][content]" class="form-control withdraw_check" value="" placeholder="<?= lang('Nội dung các bước') ?>">
        </td>`
       
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcessCatalog(this)"></i></td>`;

        trProcessCatalog = `<tr>
            ${tdNumbers}
            ${tdContent}
            ${tdActions}
        </tr>`;

        $('#tb-process-catalog tbody').append(trProcessCatalog);
        totalProcessCatalog();
        counter++;
    }
</script>
<script>
    $(function() {
        appValidateForm($('#handling-process-catalog'), {
            code: 'required',
            name: 'required',
        }, handlingData);

        function handlingData(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    if (typeof oTable != 'undefined' && oTable != '') {
                        oTable.draw();
                    }
                    $('.modal-dialog .close').trigger('click');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
            return false;
        }

        totalProcessCatalog();
    })
</script>