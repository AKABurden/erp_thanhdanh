<?php
$dimensions = $pdf->getPageDimensions();
$pdf->MultiCell($dimensions['wk'] - ($dimensions['lm'] + 10), 0, $data->content, 0, 'J', 0, 1, '', 45, true, 0, true, true, 0);