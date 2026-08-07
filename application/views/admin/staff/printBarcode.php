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
      }
    </style>
</head>
<body onload="window.print()">
  <?php foreach ($arr as $key => $value) { ?>
    <?php $get_staff = get_table_where('tblstaff',array('staffid'=>$value),'','row'); ?>
    <?php if(!empty($get_staff->code)) { ?>
      <div class="img-barcode">
        <img src="<?=base_url('Barcode/set_barcode/').$get_staff->code?>" />
      </div>
    <?php } ?>
  <?php } ?>
  </div>
</body>
</html>