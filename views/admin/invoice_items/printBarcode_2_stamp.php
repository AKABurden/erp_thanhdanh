<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <title>In barcode</title>
    <style type="text/css" media="print,screen">
      p {
        text-align: center;
        margin: 0;
      }
      body {
        text-align: center;
        width: 100%;
      }
      .img-barcode {
        float: left;
        width: 50%;
      }
      .clearfix {
        clear: both;
      }
      @media print {
        .break-page {
          page-break-before: always;
        }
      }
    </style>
</head>
<body onload="window.print()">
  <?php $dem_temp = 0; ?>
  <?php foreach ($item as $key => $value) { ?>
    <?php for ($i = 0; $i < $value->quantity_print; $i++) { ?>
      <div class="img-barcode">
        <?php if($print_show == 3) { ?>
          <img src="<?=base_url('Barcode/set_barcode/').$value->code?>" />
          <p><?=$value->name?></p>
          <p><?=number_format($value->price)?></p>
        <?php } else if($print_show == 2) { ?>
          <img src="<?=base_url('Barcode/set_barcode/').$value->code?>" />
          <p><?=number_format($value->price)?></p>
        <?php } else if($print_show == 1) { ?>
          <img src="<?=base_url('Barcode/set_barcode/').$value->code?>" />
          <p><?=$value->name?></p>
        <?php } else if($print_show == 0) { ?>
          <img src="<?=base_url('Barcode/set_barcode/').$value->code?>" />
        <?php } ?>
      </div>
      <?php $dem_temp++; ?>
      <?php if($dem_temp == 2) { ?>
        <div class="clearfix"></div>
        <div class="break-page"></div>
        <?php $dem_temp = 0; ?>
      <?php } ?>
    <?php } ?>
  <?php } ?>
  </div>
</body>
</html>