  <style type="text/css">
    .img_ch{
      height: 20px;
      width: 20px;
    }
  </style>
  <div class="modal fade in" id="view_sales_discount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false" style="display: block;"><div class="modal-dialog modal-lg no-modal-header" style="width: 80%;">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="book-title"><?php echo _l('Thông tin đơn hàng áp dụng chiết khấu'); ?> </span>
            </h4>
          </div>
          <div class="modal-body">
                <table id="example"  class="table table-striped  table-discounts table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="text-center" >STT</th>
                      <th class="text-center" style="width: 30%">Mã đơn hàng</th>
                      <th class="text-center" style="width: 15%">Ngày</th>
                      <th class="text-center" style="width: 15%">Khách hàng</th>
                      <th class="text-center" style="width: 15%">Tổng giá trị</th>
                      <th class="text-center" style="width: 25%">Tiền chiết khấu</th>
                      <th class="text-center" style="width: 25%">Ghi chú</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Thoát</button>
              </div>
            </div>
          </div>
      </div>
  </div>

  <script>
      $(document).ready( function() {
          $('.tip').tooltip();
      });
    $(document).ready(function() {  
    $('#example').DataTable( {
      responsive : true,
        "order": [],
        "columnDefs": [ {
          "targets"  : [0,1,2,3,4,5],
          "orderable": false,
        }],
        scrollY:        '30vh',
        "oLanguage":{
          "sProcessing":   "Đang xử lý...",
          "sLengthMenu":   "Xem _MENU_ mục",
          "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
          "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
          "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
          "sInfoFiltered": "(được lọc từ _MAX_ mục)",
          "sInfoPostFix":  "",
          "sSearch":       "Tìm:",
          "sUrl":          "",
          "oPaginate": {
              "sFirst":    "Đầu",
              "sPrevious": "Trước",
              "sNext":     "Tiếp",
              "sLast":     "Cuối"
          }
      },
      pageLength:50,
    });
  });
  </script>

  </div>