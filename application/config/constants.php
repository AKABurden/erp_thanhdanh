<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If setss to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

defined('APP_CHMOD_DIR') or define('APP_CHMOD_DIR', (fileperms(FCPATH) & 0777 | 0755));
defined('APP_CHMOD_FILE') or define('APP_CHMOD_FILE', (fileperms(FCPATH . 'index.php') & 0777 | 0644));
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/**
 * Used for phpass
 */
define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * Admin URL
 */
define('ADMIN_URL', 'admin');
/**
 * Admin URI
 * CUSTOM_ADMIN_URL is not yet tested well, don't define it
 */
define('ADMIN_URI', DEFINED('CUSTOM_ADMIN_URL') ? CUSTOM_ADMIN_URL : ADMIN_URL);

/**
 * CRM server update url
 */
define('UPDATE_URL', 'https://www.perfexcrm.com/perfex_updates/index.php');

/**
 * Get latest version info
 */
define('UPDATE_INFO_URL', 'https://www.perfexcrm.com/perfex_updates/update_info.php');

/**
 * Do not send sms to data eq. invoices, estimates older then X days.
 */
if (!defined('DO_NOT_SEND_SMS_ON_DATA_OLDER_THEN')) {
    define('DO_NOT_SEND_SMS_ON_DATA_OLDER_THEN', 45);
}

if (!defined('CUSTOM_FIELD_TRANSFER_SIMILARITY')) {
    define('CUSTOM_FIELD_TRANSFER_SIMILARITY', 85);
}

/**
 * CRM temporary path
 */
define('TEMP_FOLDER', FCPATH . 'temp' . '/');

/**
 * Customer attachments folder from profile
 */
define('CLIENT_ATTACHMENTS_FOLDER', FCPATH . 'uploads/clients' . '/');
/**
 * All tickets attachments
 */
define('TICKET_ATTACHMENTS_FOLDER', FCPATH . 'uploads/ticket_attachments' . '/');
/**
 * Company attachments, favicon, logo etc..
 */
define('COMPANY_FILES_FOLDER', FCPATH . 'uploads/company' . '/');
/**
 * Staff profile images
 */
define('STAFF_PROFILE_IMAGES_FOLDER', FCPATH . 'uploads/staff_profile_images' . '/');
/**
 * Contact profile images
 */
define('CONTACT_PROFILE_IMAGES_FOLDER', FCPATH . 'uploads/client_profile_images' . '/');
/**
 * Newsfeed attachments
 */
define('NEWSFEED_FOLDER', FCPATH . 'uploads/newsfeed' . '/');
/**
 * Newsfeed attachments
 */
define('INVOICE_FOLDER', FCPATH . 'uploads/invoice' . '/');
/**
 * Contracts attachments
 */
define('CONTRACTS_UPLOADS_FOLDER', FCPATH . 'uploads/contracts' . '/');
/**
 * Tasks attachments
 */
define('TASKS_ATTACHMENTS_FOLDER', FCPATH . 'uploads/tasks' . '/');
/**
 * Invoice attachments
 */
define('INVOICE_ATTACHMENTS_FOLDER', FCPATH . 'uploads/invoices' . '/');
/**
 * Estimate attachments
 */
define('ESTIMATE_ATTACHMENTS_FOLDER', FCPATH . 'uploads/estimates' . '/');
/**
 * Proposal attachments
 */
define('PROPOSAL_ATTACHMENTS_FOLDER', FCPATH . 'uploads/proposals' . '/');
/**
 * Expenses receipts
 */
define('EXPENSE_ATTACHMENTS_FOLDER', FCPATH . 'uploads/expenses' . '/');
/**
 * Lead attachments
 */
define('LEAD_ATTACHMENTS_FOLDER', FCPATH . 'uploads/leads' . '/');
/**
 * Project files attachments
 */
define('PROJECT_ATTACHMENTS_FOLDER', FCPATH . 'uploads/projects' . '/');
/**
 * Project discussions attachments
 */
define('PROJECT_DISCUSSION_ATTACHMENT_FOLDER', FCPATH . 'uploads/discussions' . '/');
/**
 * Credit notes attachment folder
 */
define('CREDIT_NOTES_ATTACHMENTS_FOLDER', FCPATH . 'uploads/credit_notes' . '/');
/**
 * Modules Path
 */
define('APP_MODULES_PATH', FCPATH . 'modules/');
/**
 * Helper libraries path
 */
define('LIBSPATH', APPPATH . 'libraries/');

define('MATERIALS', FCPATH . 'uploads/materials' . '/');
define('PRODUCTS', FCPATH . 'uploads/products' . '/');
define('TOOLS_SUPPLIES', FCPATH . 'uploads/tools_supplies' . '/');
define('ORDERS', FCPATH . 'uploads/orders' . '/');
define('WAREHOUSES_CAPACITY', 8);
define('WAREHOUSES_HOLD', 7);
define('WAREHOUSES_TAMP', 2);
define('BRANCH_DEFAULT', 1);
define('STAGE_PRINT_BARCODE', 1);


define('PURCHASE_ORDER_FEEDBACK', FCPATH . 'uploads/purchase_order_feedback' . '/');
define('PURCHASES_FEEDBACK', FCPATH . 'uploads/purchases_feedback' . '/');
define('IMPORT_FEEDBACK', FCPATH . 'uploads/import_feedback' . '/');
define('ORDERS_FEEDBACK', FCPATH . 'uploads/orders_feedback' . '/');

define('ORDER_PRODUCTION_DETAILS_FEEDBACK', FCPATH . 'uploads/order_production_details_feedback/');
define('VIOLATION_RECORDS_FEEDBACK', FCPATH . 'uploads/violation_records_feedback/');
define('CURRENCY_VND', 5);
define('ORDER_DEFAULT', 1);
define('ORDER_CHANGE', 2);
define('ORDER_CHANGE_SIZE', 3);
define('LOCATIONS_DEFAULT_MANUFACTURES', 26);
define('STAGES_MATERIAL', 2);
define('STAGES_COMMUNE', 3);
define('WAREHOUSES_ERRORS', 1);
//define('WAREHOUSES_SOLD', 12);
//
//define('WAREHOUSE_WARRANTY', 13);
//define('WAREHOUSE_SUPPLIES_TASK', 14);

define('COUNT_DAY_WORK', 26);
define('HOUR_DAY', 8);
define('DEDUCT_BHXH', 8);
define('DEDUCT_BHYT', 1.5);
define('DEDUCT_BHTN', 1);
define('UNION', 0.5);
define('BHDN', 21.5);
define('RICE_MONEY', 30000);
//define('CODE_HAND_OVER_CATEGORY', '7.BAGI/CĐ');
define('CODE_HAND_OVER_CATEGORY', 'BAGI-07');
define('TYPE_SAMPLE_ORDER', 11);
define('TYPE_COMPENSATE_ORDER', 4);
define('TYPE_KH_ORDER', 1);
define('TYPE_PTM', 13);
define('QUANTITY_PTM', 200);
define('WARNING_WEIGHT_NUMBER_KPI', 14);

define('WAREHOUSES_SYSTEM', WAREHOUSES_ERRORS.','.WAREHOUSES_HOLD.','.WAREHOUSES_CAPACITY.','.WAREHOUSES_TAMP);

define('FIX_QUANTITY_COMPENSATION', 1);
define('CAL_PL_1', [1]);
define('CAL_PL_2', [7, 6, 10, 11, 12]);
define('CAL_PL_3', [8, 9]);
define('CAL_PL_4', [13]);
define('CAL_PL_5', [14, 15, 16]);


define('STAGE_TYPE_KIEM', 20);
define('STAGE_TYPE_PHAN_DON', 21);
define('STAGE_TYPE_GIAO_HANG', 22);

define('ALLOWANCE_THAMNIEN', 5);
define('ALLOWANCE_CHUYENCAN', 8);
define('ALLOWANCE_FSC', 18);
define('ALLOWANCE_PCCC', 19);
define('ALLOWANCE_DH', 20);
define('LIST_VEHICLE', FCPATH . 'uploads/list_vehicle' . '/');
define('CR_SUGGEST_PAYSLIPS_ID', 41);
define('ID_TYPE_COST_REQUIREMENTS', 5);
define('id_category_request_repair', 40);
// define('MA_CONG_VIEC_DON_HANG', 3210); //Mở lệnh sản xuất
// define('MA_CONG_VIEC_LSX', 3210); //Mở lệnh sản xuất
define('MA_CONG_VIEC_DON_HANG', 4916); //Mở lệnh sản xuất
define('MA_CONG_VIEC_LSX', 4916); //Mở lệnh sản xuất
define('CODE_MANAGE_HUMAN', 'P05');
define('TYPE_USE', 1);
