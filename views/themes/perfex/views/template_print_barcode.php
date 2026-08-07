<?php
$dimensions = $pdf->getPageDimensions();
$pdf->MultiCell(40, 0, $data['series']['code'], 0, 'C', 0, 1, 4, 0, true, 0, true, true, 0);
$pdf->MultiCell(45, 10, $data['series']['barcode'], 0, 'C', 0, 1, 1, 6, true, 0, true, true, 0);
$pdf->MultiCell(35, 5,$data['series']['quanliti'], 0, 'C', 0, 1, 5, 13, true, 0, true, true, 0);