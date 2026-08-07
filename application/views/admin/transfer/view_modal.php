   <style type="text/css">
       .img_ch {
           height: 20px;
           width: 20px;
       }

       .tab-pane {
           display: none;
       }

       .tab-pane.active {
           display: block;
       }
   </style>
   <div style="z-index: 999999999999;" class="modal fade in" id="view_transfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
       <div class="modal-dialog modal-xl">
           <div class="modal-content">
               <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                   <h4 class="modal-title">
                       <span class="book-title"><?php echo _l('ch_transfer_t'); ?> </span>
                   </h4>
               </div>
               <div class="modal-body">
                   <div class="row">
                       <div class="col-md-12">
                           <div class="info">
                               <?php
                                $type = '';
                                if (!isset($items))
                                    $type = 'warning';
                                elseif ($items->status == 1)
                                    $type = 'warning';
                                elseif ($items->status == 2)
                                    $type = 'danger';
                                elseif ($items->status == 3)
                                    $type = 'success';

                                ?>
                               <div style="right: 10px;" class="ribbon <?= $type ?>" project-status-ribbon-2="">
                                   <?php
                                    if (isset($items)) {
                                        $status = format_import_status($items->status, '', false);
                                    } else {
                                        $status = format_import_status(-1, '', false);
                                    }
                                    ?>
                                   <span><?= $status ?></span>
                               </div>
                               <div class="title-modal">
                                   <h3>Thông tin</h3>
                               </div>
                               <div class="body-modal">
                                   <div class="row-modal">
                                       <div class="row-group">
                                           <div class="row-contro">
                                               <div><?= format_purchase_order_father($items->id, '', true, '12px') ?></div>
                                           </div>
                                           <div class="row-contro">
                                               <div><?= _l('ch_code_p') ?>: </div>
                                               <div class="ml-at t-bold"><?php echo $items->prefix . '-' . $items->code ?></div>
                                           </div>
                                           <div class="row-contro">
                                               <div><?= _l('ch_staff_crate_rfq') ?>: </div>
                                               <div class="ml-at t-bold"><?php echo staff_profile_image($items->staff_id, array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                'data-toggle' => 'tooltip',
                                                                                'data-title' => get_staff_full_name($items->staff_id)
                                                                            )) . get_staff_full_name($items->staff_id) ?></div>
                                           </div>
                                           <div class="row-contro">
                                               <div><?= _l('ch_date_p') ?>: </div>
                                               <div class="ml-at t-bold"><?php echo _d($items->date) ?></div>
                                           </div>
                                       </div>
                                       <?php
                                            //DDH
                                            $order_id = [];
                                            if ($items->order_id_new) {
                                                $order_id[] = $items->order_id_new;
                                            }
                                            if (!empty($items->productions_capacity_id)) {
                                                $this->db->select('
                                                    tbl_productions_plan_object.object_id
                                                ', false);
                                                $this->db->from('tbl_productions_plan_object');
                                                $this->db->where('tbl_productions_plan_object.object_type', 'orders');
                                                $this->db->where('tbl_productions_plan_object.productions_plan_id', $items->productions_capacity_id);
                                                $object = $this->db->get()->result_array();
                                                if ($object) {
                                                    $order_id = array_merge($order_id, array_column($object, 'object_id'));
                                                }
                                            }

                                            if (!empty($order_id)) {
                                                $order_id = array_unique($order_id);
                                                $this->db->select('
                                                    GROUP_CONCAT(tbl_orders.reference_no SEPARATOR ", ") as reference_order
                                                ', false);
                                                $this->db->from('tbl_orders');
                                                $this->db->where_in('tbl_orders.id', $order_id);
                                                $orders = $this->db->get()->row_array();

                                                $this->db->select('
                                                    MIN(tbl_order_item_shippings.date_shipping) as date_shipping
                                                ', false);
                                                $this->db->from('tbl_order_items');
                                                $this->db->join('tbl_order_item_shippings', 'tbl_order_item_shippings.order_item_id = tbl_order_items.id');
                                                $this->db->where_in('tbl_order_items.order_id', $order_id);
                                                $order_item = $this->db->get()->row_array();
                                            }
                                       ?>
                                       
                                       <div class="row-group">
                                            <div class="row-contro">
                                                <div><?= _l('DDH') ?>: </div>
                                                <div class="ml-at t-bold"><?php echo !empty($orders) ? $orders['reference_order'] : '' ?></div>
                                            </div>
                                            <div class="row-contro">
                                                <div><?= _l('Ngày dự kiến giao hàng') ?>: </div>
                                                <div class="ml-at t-bold"><?php echo !empty($order_item) ? _d($order_item['date_shipping']) : '' ?></div>
                                            </div>
                                           <?php
                                            $history_status = explode('|', $items->history_status);
                                            foreach ($history_status as $key => $value) {
                                                $data = explode(',', $value);
                                                if (is_numeric($data[0])) {
                                            ?>
                                                   <div class="row-contro">
                                                       <div><?= _l('ch_status_import') ?>: </div>
                                                       <div class="ml-at t-bold"><?php echo staff_profile_image($data[0], array('staff-profile-image-small mright5 img_ch'), 'small', array(
                                                                                        'data-toggle' => 'tooltip',
                                                                                        'data-title' => ' Vào lúc: ' . _dt($data[1])
                                                                                    )) . get_staff_full_name($data[0]) ?></div>
                                                   </div>

                                           <?php
                                                }
                                            }
                                            ?>
                                           <div class="row-contro">
                                               <div><?= _l('ch_note_t') ?>: </div>
                                               <div class="ml-at t-bold"><?php echo $items->note ?></div>
                                           </div>
                                       </div>
                                       <div class="clearfix"></div>
                                   </div><!-- ./row-modal -->
                               </div>
                           </div>
                       </div>
                   </div>
                   <ul class="nav nav-tabs" role="tablist">
                       <li role="presentation" class="active">
                           <a href="#item_info" aria-controls="item_info" role="tab" data-toggle="tab"><i class="icon-foso fal fa-info-circle"></i><?= _l('ch_information') ?></a>
                       </li>
                       <li role="presentation">
                           <a href="#item_activity" aria-controls="item_activity" role="tab" data-toggle="tab"><i class="icon-foso fal fa-history"></i><?= _l('activity_log_puchases') ?></a>
                       </li>
                   </ul>
                   <div role="tabpanel" class="tab-pane active" id="item_info">
                       <?php
                        $totalQuantity = 0;
                        $total = 0;
                        if (isset($items->items) && (count($items->items) > 0)) { ?>
                           <div class="table-responsive">
                               <table id="view-enquiry" class="table table-bordered table-hover">
                                   <thead>
                                       <tr>
                                           <th class="text-center"><?= _l('image') ?><input type="hidden" id="itemID" value="" /></th>
                                           <th style="width: 200px" class="text-center"><?php echo _l('ch_items_name_t'); ?></th>
                                           <th style="width: 150px" class="text-center"><?php echo _l('Vị trí kho chuyển'); ?></th>
                                           <th style="width: 150px" class="text-center"><?php echo _l('Vị trí kho nhận'); ?></th>
                                           <th class="text-center"><?php echo _l('item_unit'); ?> / kho</th>
                                           <!-- <th class="text-center"><?php echo _l('item_quantity'); ?></th> -->
                                           <th class="text-center"><?php echo _l('item_quantity'); ?> / kho</th>
                                           <!-- <th class="text-center"><?php echo _l('Giá bán'); ?></th> -->
                                           <!-- <th class="text-center"><?php echo _l('invoice_total'); ?></th> -->
                                           <th class="text-center"><?php echo _l('note'); ?></th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       <?php foreach ($items->items as $key => $value) { ?>
                                           <tr>
                                               <td class="center">
                                                   <div class="preview_image text-center" style="width: 135px;margin-bottom:0;margin-top:0">
                                                       <div class="display-block contract-attachment-wrapper img-<?= $value['id'] ?>">
                                                           <div>
                                                               <?php
                                                                $avatar = base_url('assets/images/preview-not-available.jpg');
                                                                if ($value['type'] == 'product') {
                                                                    $avatar =  (!empty($value['avatar']) ? base_url('uploads/products/' . $value['avatar'])  : base_url('assets/images/preview-not-available.jpg'));
                                                                }
                                                                if ($value['type'] == 'nvl') {
                                                                    $avatar =  (!empty($value['avatar']) ? base_url('uploads/materials/' . $value['avatar'])  : base_url('assets/images/preview-not-available.jpg'));
                                                                }
                                                                ?>
                                                               <a href="<?= $avatar ?>" data-lightbox="customer-profile" class="display-block mbot5">

                                                                   <img class="mbot5" style="border-radius: 50%;width: 2em;height: 2em;" src="<?= $avatar ?>"><?= format_item_purchases($value['type']) ?>
                                                               </a>
                                                           </div>
                                                       </div>
                                                   </div>

                                               </td>
                                               <td>
                                                   <?php echo $value['name_item'] . ' (' . $value['code_item'] . ')'; ?><br><?= format_item_color($value['id_items'], $value['type']) ?>
                                               </td>
                                               <td>
                                                   <?php echo $value['warehouse_name_id']; ?>
                                                   <?php echo $value['localtion_name_id']; ?>
                                                   <div style="font-size: 11px;font-style: italic;">
                                                       <?= _l('Lot') ?>:<?= $value['lot_code'] ?>
                                                       <?php if ($value['type'] == 'nvl' || $value['type'] == 'product') { ?>
                                                           <br><?= _l('ch_date_of_manufacture_m') ?>: <?= _d($value['date_sx']) ?>
                                                           <br><?= _l('ch_items_dateed_m') ?>: <?= _d($value['date_sd']) ?>
                                                       <?php } ?>
                                                   </div>
                                               </td>
                                               <td>
                                                   <?php echo $value['warehouse_name_to']; ?>
                                                   <?php echo $value['localtion_name_to']; ?>
                                               </td>
                                               <td>
                                                   <?php echo $value['unit_name_stock']; ?>
                                               </td>
                                               <!-- <td class="center">
                                                   <?php echo formatNumber($value['quantity']); ?>
                                               </td> -->
                                               <td class="center">
                                                   <?php echo formatNumber($value['quantity_net']); ?>
                                               </td>
                                               <!-- <td class="text-right">
                                                   <?php echo number_format($value['price']); ?>
                                               </td>
                                               <td class="text-right">
                                                   <?php echo number_format($value['amount']); ?>
                                               </td> -->
                                               <td class="text-left">
                                                   <?php echo $value['note']; ?>
                                               </td>
                                           </tr>
                                       <?php
                                            $totalQuantity += $value['quantity_net'];
                                            $total += $value['amount'];
                                        } ?>
                                   </tbody>
                               </table>
                           </div>
                       <?php } ?>

                       <div id="bottom-total" class="well well-sm" style="margin-bottom: 5px;">
                           <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                               <tbody>
                                   <tr class="success">
                                       <td><?= _l('item_quantity_all') ?>:<span class="pull-right"><?= formatNumber($totalQuantity) ?></span></td>
                                       <td><?= _l('ch_all_total') ?>:<span class="pull-right"><?= number_format($total) ?></span></td>
                                   </tr>
                               </tbody>
                           </table>
                       </div>
                   </div>
                   <div role="tabpanel" class="tab-pane" id="item_activity">
                       <div class="activity-container">
                           <?php foreach ($dataLog as $key => $value) { ?>
                               <div class="feed-item">
                                   <div class="activity-text">
                                       <?= staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small'); ?> <?= get_staff_full_name($value['staff_id']); ?>
                                   </div>
                                   <div class="activity-time">
                                       <?= time_ago($value['date']) ?> <span class="activity-module"><?= _l($value['table_obj']) ?></span>
                                   </div>
                                   <div>
                                       <?= $value['content'] ?>
                                   </div>
                               </div>
                           <?php } ?>
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
                   </div>
               </div>
           </div>
       </div>
       <script type="text/javascript">
           $(document).ready(function() {
               $('.tip').tooltip();
           });
           $('body').on('hidden.bs.modal', '#views_import', function() {
               $('#import_data').html('');
               $('.table-import').DataTable().ajax.reload();
           });
           $(document).ready(function() {
               var flagView = <?= !empty($flagView) ? 1 : 0; ?>;
            //    dtItems = $('#view-enquiry').DataTable({
            //        "language": app.lang.datatables,
            //        "pageLength": app.options.tables_pagination_limit,
            //        "lengthMenu": [
            //            [10, 25, 50, 100, -1],
            //            [10, 25, 50, 100, "<?= lang('all') ?>"]
            //        ],
            //        // scrollY: '300px',
            //        scrollX: true,
            //        // fixedColumns:   {
            //        //     leftColumns: 4,
            //        //     rightColumns: 0
            //        // },
            //        // 'searching': false,
            //        // 'ordering': false,
            //        // 'paging': false,
            //        // "info": false,
            //        'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
            //        "initComplete": function(settings, json) {
            //            var t = this;
            //            t.parents('.table-loading').removeClass('table-loading');
            //            t.removeClass('dt-table-loading');
            //        }
            //    });


               dtItems = $('#view-enquiry').DataTable({
                            "language": app.lang.datatables,
                            "pageLength": app.options.tables_pagination_limit,
                            "ordering": false,
                            "lengthMenu": [
                                [10, 25, 50, 100, -1],
                                [10, 25, 50, 100, "<?= lang('all') ?>"]
                            ],
                            // scrollY: '300px',
                            // fixedColumns:   {
                            //     leftColumns: 4,
                            //     rightColumns: 0
                            // },
                            // 'searching': false,
                            // 'ordering': false,
                            // 'paging': false,
                            dom: 'Blfrtip',
                            buttons: [{
                                extend: "excel",
                                text: app.lang.dt_button_excel,
                                footer: !0,
                                exportOptions: {
                                    columns: [":not(.not-export)"],
                                    rows: function (t) {
                                        return _dt_maybe_export_only_selected_rows(t, $('#table-items-modal'))
                                    },
                                    format: {
                                        body: function(data, row, column, node) {
                                            data = $('<p>' + data + '</p>').text();
                                            if(column == 4){
                                                let trimmedText = data.trim();
                                                let noWhiteSpaceText = trimmedText.replace(/\s+/g, " ");
                                                let noCommaText = noWhiteSpaceText.replace(/,/g, "");
                                                console.log(noCommaText)
                                                return noCommaText;
                                            }else{
                                                return $.isNumeric(data.replace(',', '')) ? data.replace(',', '') : data;
                                            }
                                        }
                                    }
                                },
                                customize: function (xlsx) {

                                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                    var mergeCells = $('mergeCells', sheet);
                                    var downrows = 0;
                                    code = $('.code_data').text();
                                    name = $('.name_data').text();

                                    var downrows = 2;
                                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                        attr: {
                                            ref: 'A1:G1'
                                        }
                                    }));
                                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                        attr: {
                                            ref: 'A2:G2'
                                        }
                                    }));
                                    mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                        attr: {
                                            ref: 'A3:G3'
                                        }
                                    }));
                                    var clRow = $('row', sheet);
                                    function _createNode(doc, nodeName, opts) {
                                        var tempNode = doc.createElement(nodeName);

                                        if (opts) {
                                            if (opts.attr) {
                                                $(tempNode).attr(opts.attr);
                                            }

                                            if (opts.children) {
                                                $.each(opts.children, function (key, value) {
                                                    tempNode.appendChild(value);
                                                });
                                            }

                                            if (opts.text !== null && opts.text !== undefined) {
                                                tempNode.appendChild(doc.createTextNode(opts.text));
                                            }
                                        }

                                        return tempNode;
                                    }
                                    //update Row
                                    clRow.each(function () {
                                        var attr = $(this).attr('r');
                                        var ind = parseInt(attr);
                                        ind = ind + downrows;
                                        $(this).attr("r", ind);
                                    });
                                    // Update  row > c
                                    $('row c ', sheet).each(function () {
                                        var attr = $(this).attr('r');
                                        var pre = attr.substring(0, 1);
                                        var ind = parseInt(attr.substring(1, attr.length));
                                        ind = ind + downrows;
                                        $(this).attr("r", pre + ind);
                                    });

                                    function Addrow(index, data) {
                                        msg = '<row r="' + index + '">'
                                        for (i = 0; i < data.length; i++) {
                                            var key = data[i].k;
                                            var value = data[i].v;
                                            msg += '<c t="inlineStr" r="' + key + index + '" s="2">';
                                            msg += '<is>';
                                            msg += '<t>' + value + '</t>';
                                            msg += '</is>';
                                            msg += '</c>';
                                        }
                                        msg += '</row>';
                                        return msg;
                                    }
                                    var r1 = Addrow(1, [{ k: 'A', v: (code) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                                    var r2 = Addrow(2, [{ k: 'A', v: (name) }, { k: 'B', v: "" }, { k: 'C', v: "" }]);
                                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML;
                                    // }
                                }
                            }],
                            'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                            "initComplete": function(settings, json) {
                                var t = this;
                                t.parents('.table-loading').removeClass('table-loading');
                                t.removeClass('dt-table-loading');
                            }
                        });
               setTimeout(function() {
                   dtItems.draw('page');
               }, 150);
           });
       </script>

   </div>