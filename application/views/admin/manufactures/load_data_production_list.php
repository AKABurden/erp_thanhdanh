<?php
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $start_date = to_sql_date($start_date);
    $end_date = to_sql_date($end_date);

    $this->db->select('
        tbl_type_productionlist.id as type_productionlist_id,
        tbl_type_productionlist.code as type_productionlist_code
    ', false);
    $this->db->from('tbl_productions_orders');
    $this->db->join('tbl_productions_orders_items_stages', 'tbl_productions_orders_items_stages.productions_orders_id = tbl_productions_orders.id');
    $this->db->join('tbl_stages', 'tbl_stages.id = tbl_productions_orders_items_stages.stage_id');
    $this->db->join('tbl_category_stages', 'tbl_category_stages.id = tbl_stages.category_stages');
    $this->db->join('tbl_type_productionlist', 'tbl_type_productionlist.id = tbl_category_stages.type_productionlist_id');
    $this->db->where('tbl_productions_orders.date >=', '2023-02-01 00:00:00');
    $this->db->where('tbl_productions_orders.date >=', $start_date.' 00:00:00');
    $this->db->where('tbl_productions_orders.date <=', $end_date.' 23:59:59');
    $this->db->where('tbl_category_stages.is_in', 1);
    $this->db->group_by('tbl_type_productionlist.id');
    $stages = $this->db->get()->result_array();
?>

<div class="horizontal-scrollable-tabs">
    <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
            <?php
                $idDefault = 0;
            ?>
            <?php if($stages): ?>
                <?php foreach ($stages as $key => $value): ?>
                <?php
                    $active = '';
                    if ($key == 0) {
                        $idDefault = $value['type_productionlist_id'];
                        $active = 'active';
                    }
                ?>
                <li role="presentation" data-li-productionlist_id="<?= $value['type_productionlist_id'] ?>" onclick="loadDataTablePL(<?= $value['type_productionlist_id'] ?>)" class="<?= $active ?>">
                    <a href="#<?= $value['type_productionlist_id'] ?>" aria-controls="<?= $value['type_productionlist_id'] ?>" role="tab"
                        value="<?= $value['type_productionlist_id'] ?>" data-toggle="tab"><?= $value['type_productionlist_code'] ?></a>
                </li>
                <?php endforeach ?>
            <?php endif; ?>
        </ul>
        <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="<?= $idDefault ?>">
    </div>
</div>
<div class="div-table-data-production-list">
</div>

<script>

    async function showDataPL(_type_productionlist_id) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['type_productionlist_id'] = _type_productionlist_id;
        dataPOST['production_list_id'] = $('#production_list_id').val();
        dataPOST['start_date'] = '<?= $start_date ?>';
        dataPOST['end_date'] = '<?= $end_date ?>';

        await $.ajax({
            type: "POST",
            url: site.base_url+'admin/production_list/loadDataTableProductionList',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.div-table-data-production-list').html(response);
                $('#status_table').val(_type_productionlist_id);
            }
        });
        init_selectpicker();

    }

    function loadDataTablePL(_type_productionlist_id) {
        status_table = $('#status_table').val();
        if (status_table != _type_productionlist_id) {
            bootbox.confirm('Bạn chắc chắn đã lưu trước khi chuyển sang công đoạn khác?', function(result) { 
                if (result) {
                    showDataPL(_type_productionlist_id);
                } else {
                    $('li[data-li-productionlist_id="'+_type_productionlist_id+'"]').removeClass('active');
                    $('li[data-li-productionlist_id="'+status_table+'"]').addClass('active');
                }
            });
        } else {
            showDataPL(_type_productionlist_id);
        }
        init_selectpicker();
    }

    $(document).ready(function () {
        <?php if($idDefault > 0): ?>
            loadDataTablePL(<?= $idDefault ?>);
        <?php endif; ?>
    });
</script>