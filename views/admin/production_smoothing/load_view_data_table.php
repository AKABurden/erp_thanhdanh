<table id="table-production-smoothing" class="table dt-tnh table-production-smoothing-new"
       style="">
    <thead>
    <tr>
        <th></th>
        <th class="text-center"><?= lang('Mã LSX chi tiết') ?></th>
        <th class="text-center"><?= lang('Mã LSX tổng') ?></th>
        <th class="text-center"><?= lang('Ngày lập') ?></th>
        <th class="text-center"><?= lang('Ngày giao') ?></th>
        <th class="text-center"><?= lang('Công ty') ?></th>
        <?php if(!empty($result)){ ?>
            <?php foreach ($result as $key => $value){ ?>
                <th class="text-center"><?= $value['name'] ?></th>
            <?php } ?>
        <?php } ?>
    <tbody>
    <tr>
        <td colspan="99"></td>
    </tr>
    </tbody>
</table>
<script>
    var oTable = '';
    var fnserverparamsitems = {
        name_search: '#name_search',
        status_table: '#status_table',
        end_date_search: '#end_date_search',
        start_date_search: '#start_date_search',
        productions_orders_search: '#productions_orders_search',
        'stage_search[]': '#stage_search'
    };
    function loadTableNew() {
        oTable = tnhInitDataTable('#table-production-smoothing',
            '<?= site_url('admin/production_smoothing/getProductionSmoothing') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                // scrollY: height_body,
                scrollX: true,
                fixedColumns:{
                    leftColumns: 5,
                },
                "ajax": {
                    "url": '<?= site_url('admin/production_smoothing/getProductionSmoothing') ?>',
                    "type": "POST",
                    "data": function (d) {
                        if (typeof (csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparamsitems) {
                            d[key] = $(fnserverparamsitems[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function (json) {
                        $.each(json.arrCheck,function (k,v){
                            if (v != -1){
                                oTable.columns(v).visible(false, false);
                            }
                        })
                        return json.aaData;
                    }
                },
                "createdRow": function (row, data, index) {
                    rows = $(row);
                    // colums = $(row).find('td .check_empty');
                    // check = false;
                    // key_colums = 0;
                    // $.each(colums,function (k,v){
                    //    if ($(v).val() == 1){
                    //        check = true;
                    //        key_colums = $(v).attr('data-key');
                    //        // oTable.columns(key_colums).visible(false, false);
                    //    }
                    // });
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    total_hour =  $(nRow).attr('data-hour');
                    if(total_hour > 0) {
                        $(nRow).find('.total_hour_new').html('Tổng thời gian: '+ total_hour+ ' (giờ)');
                    }
                    check_finish = $(nRow).find('td .check_finish');
                    check_finish_new = $(nRow).find('td .check_finish_new');

                    $.each(check_finish,function (k,v){
                        if ($(v).val() == 1){
                            $(v).closest('td').css({
                                "background-color": "rgb(44 133 44)",
                                "color": "white"
                            });
                        }
                    })

                    $.each(check_finish_new,function (k,v){
                        if ($(v).val() == 1){
                            $(v).closest('td').css({
                                "background-color": "#b4bd2a",
                                "color": "white"
                            });
                        }
                    })

                    // if (check_finish_new == 1){
                    //     $(nRow).find('.check_finish_new').closest('td').css({
                    //         "background-color": "#b4bd2a",
                    //         "color": "white"
                    //     });
                    // }
                    // if (check_finish == 1) {
                    //     $(nRow).find('.check_finish').closest('td').css({
                    //         "background-color": "rgb(44 133 44)",
                    //         "color": "white"
                    //     });
                    // }
                
                },
                "columnDefs": [
                ],
            });
    }

    $(document).on('change', '#productions_orders_search,#end_date_search,#start_date_search', function(
        event) {
        event.preventDefault();
        if ($("#status_table").val() != 'all_new') {
            if (typeof oTable != 'undefined' && oTable != '') {
                // oTable.draw();
            }
        }
    });

    $(document).ready(function () {
        loadTableNew();
    });
</script>