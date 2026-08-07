<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Handles uploads error with translation texts
 * @param  mixed $error type of error
 * @return mixed
 */
if (!function_exists('data_dahahi')) {
    function data_dahahi()
    {
        $data = [];
        $data['link'] = get_option('link_dahahi');
        return $data;
    }
    // {
    //     $data = [];
    //     $data['link'] = 'https://demokh.dahahi.vn';
    //     return $data;
    // }
}
if (!function_exists('dahahi_get_MachineBoxId')) {
    function    dahahi_get_MachineBoxId($FullName = '')
    {
        $url = 'api/facereg/searchByName';
        $data = [];
        $data['FullName'] = $FullName;
        $response = dahahi_post($url, $data);

        $_data = [];
        $_data['result'] = 1;
        $_data['message'] = 'Chưa kết nối';
        if (($response['ErrorCode'] != '000000')) {
            if (($response['ErrorCode'] == 'FARE0013')) {
                $_data['result'] = 1;
                $_data['message'] = 'Thiết bị không hoạt động';
            }
            if (($response['ErrorCode'] == 'FARE0008')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nối';
            }
            if (($response['ErrorCode'] == 'FARE000FOSO')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nói với phần mềm chấm công';
            }
        } else {
            $_data['result'] = 2;
            $_data['message'] = '';
            $_data['data'] = ($response['Data']);
        }
        return ($_data);
    }
}
if (!function_exists('dahahi_remove_MachineBoxId')) {
    function dahahi_remove_MachineBoxId($FacePersionId = '', $MachineBoxId = '')
    {
        $url = '/api/facereg/remove';
        $data = [];
        $data['FacePersionId'] = $FacePersionId;
        $data['MachineBoxId'] = $MachineBoxId;
        $response = dahahi_post($url, $data);
        $_data = [];
        $_data['result'] = 1;
        $_data['message'] = 'Chưa kết nối';
        if (($response['ErrorCode'] != '000000')) {
            if (($response['ErrorCode'] == 'FARE0013')) {
                $_data['result'] = 1;
                $_data['message'] = 'Thiết bị không hoạt động';
            }
            if (($response['ErrorCode'] == 'FARE0008')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nối';
            }
            if (($response['ErrorCode'] == 'FARE000FOSO')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nói với phần mềm chấm công';
            }
        } else {
            $_data['result'] = 2;
            $_data['message'] = '';
            $_data['data'] = ($response['Data']);
        }
        return ($_data);
    }
}
if (!function_exists('dahahi_check_getInfo')) {
    function dahahi_check_getInfo($FaceID = '', $MachineBoxId = '')
    {
        $url = 'api/facereg/getInfo';
        $data = [];
        $data['FacePersionId'] = $FaceID;
        $data['MachineBoxId'] = $MachineBoxId;
        $response = dahahi_post($url, $data);
        $_data = [];
        $_data['result'] = 1;
        $_data['message'] = 'Chưa kết nối';
        if (($response['ErrorCode'] != '000000')) {
            if (($response['ErrorCode'] == 'FARE0013')) {
                $_data['result'] = 1;
                $_data['message'] = 'Thiết bị không hoạt động';
            }
            if (($response['ErrorCode'] == 'FARE0008')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nối';
            }
            if (($response['ErrorCode'] == 'FARE000FOSO')) {
                $_data['result'] = 1;
                $_data['message'] = 'Chưa kết nói với phần mềm chấm công';
            }
        } else {
            $response['Data'][0]['MachineBoxId'] = $MachineBoxId;
            $_data['result'] = 2;
            $_data['message'] = '';
            $_data['data'] = ($response['Data'][0]);
        }
        return ($_data);
    }
}
if (!function_exists('dahahi_getInfo')) {
    function dahahi_getInfo($FaceID = '', $MachineBoxId = '')
    {
        $url = 'api/facereg/getInfo';
        $data = [];
        $data['FacePersionId'] = $FaceID;
        $data['MachineBoxId'] = $MachineBoxId;
        $response = dahahi_post($url, $data);
        if (($response['ErrorCode'] != '000000')) {
            return false;
        } else {
            return ($response['Data'][0]);
        }
    }
}
if (!function_exists('dahahi_getAllMachine')) {
    function dahahi_getAllMachine()
    {
        $url = 'api/faceid/getAllMachine';
        $data = [];
        $response = dahahi_post($url, $data);
        if (($response['ErrorCode'] == '000000')) {
            return ($response['Data']);
        } else {
            if (($response['ErrorCode'] == 'FARE000FOSO')) {
                return 'Chưa kết nối với phần mềm chấm công';
            }
            return [];
        }
    }
}

if (!function_exists('dahahi_editInfo')) {
    function dahahi_editInfo($id = '', $_data = '', $MachineBoxId)
    {
        $url = 'api/facereg/edit';
        $data = [];
        $data['FacePersionId'] = $_data['FacePersionId'];
        $data['MachineBoxId'] = $MachineBoxId;
        $data['FullName'] = $_data['FullName'];
        $data['Base64Image'] = $_data['Base64Image'];
        $response = dahahi_post($url, $data);
        return $response['ErrorCode'];
    }
}
if (!function_exists('dahahi_getcheckinhis')) {
    function dahahi_getcheckinhis($_data = '', $MachineBoxId, $date)
    {
        $url = 'api/facereg/checkinhis';
        $data = [];
        $data['FacePersionId'] = $_data['FacePersionId'];
        $data['EmployeeCode'] = $_data['EmployeeCode'];
        $data['MachineBoxId'] = $MachineBoxId;
        $data['FromTimeStr'] = $_data['FromTimeStr'];
        $data['ToTimeStr'] = $_data['ToTimeStr'];
        $response = dahahi_post($url, $data);
        return $response['ErrorCode'];
    }
}
if (!function_exists('dahahi_post')) {
    function dahahi_post($url = '', $data = '')
    {
        $link = data_dahahi();
        if (!empty($data)) {
            $data = json_encode($data);
        } else {
            $data = '';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link['link'] . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'AppKey: ' . get_option('appKey_dahahi'),
                'SecretKey: ' . get_option('secretKey_dahahi'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $_data = json_decode($response, true);
        if ($_data == 'Unauthorized access resource') {
            $_data = [];
            $_data['ErrorCode'] = 'FARE000FOSO';
        }
        return $_data;
    }
}
if (!function_exists('history_checkins')) {
    function history_checkins($timekeeping_detail_id = '', $date = '', $personnel_id = '', $time = '', $type = '', $active = 1, $MachineBoxId = NULL)
    {
        $CI = &get_instance();
        $in['timekeeping_detail_id'] = ($timekeeping_detail_id);
        $in['date'] = ($date);
        $in['personnel_id'] = ($personnel_id);
        $in['time'] = ($time);
        $in['type'] = ($type);
        $in['active'] = ($active);
        $in['MachineBoxId'] = ($MachineBoxId);
        $CI->db->insert('tbl_history_checkins', $in);
    }
}
