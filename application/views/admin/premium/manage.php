<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(false); ?>
<style>
  .wrap-container-page {
    display: flex;
    justify-content: center;
  }
  .wrap-content-page {
    width: 60%;
  }
  .wrap-box-premium {
    background: #fff;
    border: 1px solid #b7b7b7;
    border-radius: 15px;
    padding: 10px;
    margin-top: 10px;
    box-shadow: 1px 1px 5px #00000036;
  }
  .wrap-header-premium {
    height: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

  }
  .wrap-header-premium img {
    width: 70px;
    height: 70px;
  }
  .wrap-text-header {
    font-weight: 600;
    margin-top: 15px;
  }
  .wrap-body-premium {
    text-align: center;
    height: 80px;
    color: #888;
    margin-bottom: 10px;
  }
  .wrap-footer-premium {
    text-align: right;
    padding: 10px 20px;
  }
  .wrap-btn-footer {
    padding: 10px 20px;
    background: #1aa700;
    color: #fff;
    border-radius: 5px;
    margin-bottom: 10px;
  }
  .wrap-btn-footer:hover {
    background: #148000;
  }
</style>
<div class="wrap-container-page">
  <div class="wrap-content-page">
    <div class="col-md-4">
      <div class="wrap-box-premium">
        <div class="wrap-header-premium">
          <img src="<?= base_url('uploads/Google_Drive.png'); ?>">
          <div class="wrap-text-header">
            GOOGLE DRIVE
          </div>
        </div>
        <div class="wrap-body-premium">
          Sao lưu dữ liệu qua GOOGLE DRIVE.
        </div>
        <div class="wrap-footer-premium">
          <a>
            <span class="wrap-btn-footer">599K/tháng</span>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="wrap-box-premium">
        <div class="wrap-header-premium">
          <img src="<?= base_url('uploads/111.png'); ?>">
          <div class="wrap-text-header">
            TÍCH HỢP HOÁ ĐƠN ĐIỆN TỬ
          </div>
        </div>
        <div class="wrap-body-premium">
          Tính hợp hóa đơn điện tử.
        </div>
        <div class="wrap-footer-premium">
          <a>
            <span class="wrap-btn-footer">199k/tháng</span>
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="wrap-box-premium">
        <div class="wrap-header-premium">
          <img src="<?= base_url('uploads/11.png'); ?>">
          <div class="wrap-text-header">
            TÍCH HỢP GIAO HÀNG SUPPERSHIP
          </div>
        </div>
        <div class="wrap-body-premium">
          Tích hợp giao hàng SUPPERSHIP.
        </div>
        <div class="wrap-footer-premium">
          <a>
            <span class="wrap-btn-footer">199k/tháng</span>
          </a>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</div>
<?php init_tail(); ?>
<script>
</script>
</body>
</html>