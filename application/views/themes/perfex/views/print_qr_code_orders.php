<?php
$dimensions = $pdf->getPageDimensions();
$i = 1;
//print_arrays($data);
foreach ($data['data'] as $key => $value) {
    $style = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255)
        'module_width' => 1, // width of a single module in points
        'module_height' => 1 // height of a single module in points
    );
    $code = $value['code'];
    $pdf->write2DBarcode($code, 'QRCODE,Q', 15, 0, 50, 37, $style, 'N');

    $pdf->MultiCell(55, 0, 'Mã thành phẩm: '.$value['code'], 0, 'J', 0, 1, 15, 35, true, 0, true, true, 0);
    $H = $pdf->GetY();
    $pdf->MultiCell(55, 0, 'Tên thành phẩm: '.$value['name'], 0, 'J', 0, 1, 15, ($H), true, 0,
        true, true, 0);
    $H = $pdf->GetY();
    if ($key < (count($data['data']) - 1)) {
        $pdf->AddPage('L', array(80, 53));
    }
}
$pdf->Output();