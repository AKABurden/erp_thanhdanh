<?php echo form_open('admin/category_regulations/detail_score_map/'.$id, array('id'=>'step-salary')); ?>
<style>
    .title_header_item{
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 8px 10px;
        background: #d2e9fd;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= $title ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Tên thang điểm', 'name') ?>
                            <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : (!empty($dtData) ? $dtData['name'] : '')), 'placeholder="'.lang('Tên thang điểm').'" id="name" required class="form-control input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= lang('Số điểm', 'point') ?>
                            <?php echo form_input('point', (isset($_POST['point']) ? $_POST['point'] : (!empty($dtData) ? $dtData['point'] : 0)), 'placeholder="'.lang('Số điểm').'" id="point" required class="form-control number-format input-tip"'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="title_header_item">Tiêu chí</div>
                        <table id="tb-other1"
                               class="dt-tnh table tnh-table table-bordered table-hover" style="margin-top: unset !important;">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">
                                    <a class="btn btn-info btn-icon add-other1"><i
                                            class="fa fa-plus"></i></a>
                                </th>
                                <th><?= lang('Tiêu chí') ?><span class="red">*</span>
                                </th>
                                <th style="width: 50px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $counterOther1 = 0;
                            $dtDataOther1 = !empty($dtData) ? get_table_where(
                                'tbl_score_map_detail',
                                ['score_map_id' => $dtData['id'] ?? 0]
                            ) : [];
                            ?>
                            <?php if (!empty($dtDataOther1)) : ?>
                                <?php foreach ($dtDataOther1 as $key => $value) : ?>
                                    <tr>
                                        <td>
                                            <div class="stt-other1 text-center"><?= ++$key ?></div>
                                        </td>
                                        <td>
                                            <input type="hidden"
                                                   name="id_other1[]"
                                                   class="form-control id_other1"
                                                   value="<?= $value['id'] ?>">
                                            <input type="hidden" name="counterOther1[]"
                                                   id="counterOther1" class="form-control"
                                                   value="<?= $counterOther1 ?>">
                                            <input type="text" name="title[]"
                                                   class="title form-control"
                                                   value="<?= $value['title'] ?>">
                                        </td>
                                        <td>
                                            <div class="td-actions text-center"><span
                                                    class="fa fa-remove btn btn-danger remove-row-other1"></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $counterOther1++; ?>
                                <?php endforeach ?>
                            <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            <button type="submit" class="btn btn-primary add"><?= empty($id) ? _l('add') : _l('edit'); ?></button>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(function(){
        $("#is_ceo_required").select2({
        });
        ajaxSelectParams('#role_id', 'admin/suggest_task/searchRoles', $("#role_id").val(), true, true);
        init_datepicker();
        appValidateForm($('#step-salary'), {
            name: 'required',
        }, handling);

        function handling(form) {
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            var data = $(form).serialize();
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        if (typeof oTable != 'undefined') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    $('.add').removeAttr('disabled', 'disabled');
                    console.log("error");
                });
            return false;
        }
    })
    var counterOther1 = "<?= $counterOther1 ?? 0 ?>";
    $('.add-other1').on('click', function(event) {
        event.preventDefault();
        trItem = `<tr>
                <td>
                    <div class="stt-other1 text-center"></div>
                </td>
                <td>
                 <input type="hidden" name="counterOther1[]"
                       id="counterOther1" class="form-control"
                       value="${counterOther1}">
                <input type="text" name="title[]"
                       class="title form-control"
                       value="">
                </td>
                <td>
                    <div class="td-actions text-center"><span
                                class="fa fa-remove btn btn-danger remove-row-other1"></span>
                    </div>
                </td>
            </tr>
        `;
        $("#tb-other1 tbody").append(trItem);
        counterOther1++;
        totalOther1();
    });
    $(document).on('click', '.remove-row-other1', function(event) {
        event.preventDefault();
        tr = $(this).closest('tr');
        tr.remove();
        totalOther1();
    });
    function totalOther1()
    {
        tbOther1 = '#tb-other1 tbody tr:not("[class^=not-tr]")';
        var nOther1 = $(tbOther1).length;
        var sttOther1 = 0;

        for (i = 0; i < nOther1; i++)
        {
            sttOther1++;
            element = $(tbOther1)[i];
            $(element).find('.stt-other1').html(sttOther1);
        }
    }
</script>