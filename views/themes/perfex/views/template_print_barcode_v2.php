<?php
$CI = &get_instance();
$CI->load->library('ciqrcode');
$dimensions = $pdf->getPageDimensions();
$i = 1;
//print_arrays($data);
$H = $pdf->GetY();
$kk = 1;
$check_key_new = 0;
$tableTem = '';
foreach ($data['staffs'] as $key => $value) {
    $style = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255)
        'module_width' => 1, // width of a single module in points
        'module_height' => 1 // height of a single module in points
    );
    $code = $value['codes'];
    $check_key_new ++;
    if ($check_key_new == 1) {
        $tableTem .= '<table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="0">
                        <tr nobr="true" style="">';
    }
    $qr = $code;


    $params['data'] = $qr;
    $qr = vn_to_str(str_replace('||','__',$qr));
    $params['level'] = 'H';
    $params['size'] = 20;
    $params['savename'] = FCPATH . 'uploads/staffs/qrcode/' . $qr . '.png';
    $CI->ciqrcode->generate($params);
    $img = file_get_contents(FCPATH . 'uploads/staffs/qrcode/' . $qr . '.png');
    $tableTem .= '<td class="" style="width: 50%;">
                    <table nobr="true" class="bold" cellspacing="0" cellpadding="5" border="1">
                        <tr nobr="true" style="">
                            <td style="width: 40%"> <img width="80" src="data:image/png;base64,' . base64_encode($img) . '"/></td>
                            <td style="width: 60%">'. $value['data']['code_staff'].'<br><span style="font-weight: bold">'.$value['data']['name_staff'].'</span><br>'._dhau($value['staffs']->birthday).'</td>
                        </tr>
                    </table>
                </td>';
    if ($check_key_new == 2 || (count($data['staffs'])-1 == $key)) {
        $tableTem .= '</tr></table>';
        $check_key_new = 0;
    }
}
$pdf->MultiCell(180, 0, $tableTem, 0, 'J', 0, 10, 15, ($H), true, 0,true, true, 0);
$pdf->Output();