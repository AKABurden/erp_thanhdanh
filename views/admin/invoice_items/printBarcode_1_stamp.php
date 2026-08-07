<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <title>In barcode</title>
    <style type="text/css" media="print,screen">
      p {
        text-align: center;
        margin: 0;
      }
      @page {
        size: 100mm 100mm;
      }
      @media print {
        table {
          page-break-after: always;
        }
      }
    </style>
</head>
<body onload="window.print()">
  <center>
  <?php foreach ($item as $key => $value) { ?>
    <?php for ($i = 0; $i < $value->quantity_print; $i++) { ?>
      <table>
        <tbody>
          <tr>
            <td>
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
            </td>
          </tr>
        </tbody>
      </table>
    <?php } ?>
  <?php } ?>
  </center>
</body>
</html>