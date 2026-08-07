<?php echo form_open_multipart('admin/kpi/handling_criteria/' . $id, array('id' => 'add-handling_criteria', 'class' => '', 'enctype' => 'multipart/form-data',)); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang('tnh_code_columns', 'code') ?>
                        <?php echo form_input('code', (isset($_POST['code']) ? $_POST['code'] : (!empty($columns) ? $columns['code'] : '')), 'placeholder="' . lang('tnh_code_columns') . '" id="criteria" required class="form-control input-tip"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <table id="tb-handling-columns" class="table table-hover table-cs dataTable sortable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <a class="hover-svg dropdown-toggle add-row" onclick="addColumns()" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)" aria-expanded="false">
                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                                <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th><?= lang('tnh_name_columns') ?><span class="text-danger">*</span></th>
                                    <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                                </tr>
                            </thead>
                            <tbody class="ui-sortable">
                                <?php
                                    if (!empty($columns)) {
                                        $dtColumnsDetail = $this->columns_model->getColumnsDetail($id);
                                        if (!empty($dtColumnsDetail)) {
                                            foreach ($dtColumnsDetail as $key => $value) {
                                                $tdNumbers = '<td class="text-center td-numbers dragger">'.(++$key).'</td>';

                                                $tdColumns = '<td>
                                                    <input type="hidden" name="columns_detail_id[]" class="form-control columns_detail_id" value="'.$value['id'].'">
                                                    <input type="text" name="name[]" class="form-control name" value="'.$value['name'].'" placeholder="'.lang('tnh_name_columns').'">
                                                </td>';
                                                $tdActions = '<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeColumns(this)"></i></td>';

                                                $trColumns = '<tr class="sortable item">
                                                    '.$tdNumbers.'
                                                    '.$tdColumns.'
                                                    '.$tdActions.'
                                                </tr>';

                                                echo $trColumns;
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
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
    function totalColumns()
    {
        tbColumns = '#tb-handling-columns tbody tr';
        var nColumns = $(tbColumns).length;
        var sttColumns = 0;
        for (iColumns = 0; iColumns < nColumns; iColumns++)
        {
            sttColumns++;
            elColumns = $(tbColumns)[iColumns];
            $(elColumns).find('.td-numbers').html(sttColumns);
        }
    }

    function removeColumns(_this) {
        $(_this).closest('tr').remove();
        totalColumns();
    }

    function addColumns()
    {
        tdNumbers = `<td class="text-center td-numbers dragger"></td>`;

        tdColumns = `<td>
            <input type="text" name="name[]" class="form-control name" value="" placeholder="<?= lang('tnh_name_columns') ?>">
        </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeColumns(this)"></i></td>`;

        trColumns = `<tr class="sortable item">
            ${tdNumbers}
            ${tdColumns}
            ${tdActions}
        </tr>`;
        $('#tb-handling-columns tbody').append(trColumns);
        totalColumns();
    }

    $(function() {
        $('.sortable tbody').sortable({
			start: function() {},
			stop: function() {
				totalColumns();
			}
		});

        init_selectpicker();
        appValidateForm($('#add-handling_criteria'), {
            code: 'required',
        }, handlingColumns);

        $('select.recipe').change(function(event) {
            cTrH = $(this).closest('tr');
            recipe = $(this).val();
            if (recipe == 4) {
                cTrH.find('.span-cs-hide').removeClass('hide');
                cTrH.find('.input-cs-hide').removeClass('hide');
            } else {
                cTrH.find('.span-cs-hide').addClass('hide');
                cTrH.find('.input-cs-hide').addClass('hide');
            }
        });

        function handlingColumns(form) {
            $('.add').attr('disabled', 'disabled');
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.append(tag.name, file);
                });
            });

            $.each(formParams, function(i, val) {
                formData.append(val.name, val.value);
            });

            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/columns/handling_columns/<?= $id ?>',
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
        init_selectpicker();
        init_datepicker();
    })
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>