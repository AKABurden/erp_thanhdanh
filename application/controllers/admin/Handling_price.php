<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Handling_price extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('products_model');
        $this->load->model('items_model');
        $this->load->model('handling_price_model');
        $this->load->model('category_model');
    }

    public function handlingPriceQuotes_bk()
    {
        $data = [];

        $cItemsId = $this->input->post('cItemsId');
        $cQuantity = $this->input->post('cQuantity');
        $quote_item_id = $this->input->post('quote_item_id');
        $cdataJson = $this->input->post('cdataJson');
        $customers = $this->input->post('customers');
        $quote_stage_id = $this->input->post('quote_stage_id');

        $data['customers'] = $customers;
        $data['cdataJson'] = $cdataJson;
        $data['cItemsId'] = $cItemsId;
        $data['cQuantity'] = $cQuantity;
        $data['quote_item_id'] = $quote_item_id;
        $data['quote_stage_id'] = $quote_stage_id;
        $this->load->view('admin/handling_price/handling_price_quotes', $data);
    }

    public function handlingPriceQuotes()
    {
        $data = [];

        $cItemsId = $this->input->post('cItemsId');
        $cQuantity = $this->input->post('cQuantity');
        $quote_item_id = $this->input->post('quote_item_id');
        $cdataJson = $this->input->post('cdataJson');
        $customers = $this->input->post('customers');
        $quote_stage_id = $this->input->post('quote_stage_id');

        $data['customers'] = $customers;
        $data['cdataJson'] = $cdataJson;
        $data['cItemsId'] = $cItemsId;
        $data['cQuantity'] = $cQuantity;
        $data['quote_item_id'] = $quote_item_id;
        $data['quote_stage_id'] = $quote_stage_id;
        $this->load->view('admin/handling_price/handling_price_quotes_new', $data);
    }

    public function addItemPriceQuotes()
    {
        $material_price_quotes = $this->input->post('material_price_quotes');
        $stages_price_quotes = $this->input->post('stages_price_quotes');
        $group_id = $this->input->post('group_id');
        $height_layout = number_unformat($this->input->post('height_layout'));
        $width_layout = number_unformat($this->input->post('width_layout'));
        $id_customer = $this->input->post('id_customer');
        $quote_stage_id = $this->input->post('quote_stage_id');
        $height = $this->input->post('height');
        $width = $this->input->post('width');

        $data['items_material_price_quotes'] = [];
        if (!empty($material_price_quotes)) {
            $this->db->select('
                tbl_materials.id as id,
                "materials" as type,
                tbl_materials.code as code,
                tbl_materials.name as name,
                tblunits.unit as unit_name,
                tbl_materials.price_sell as price_sell,
                coalesce(tbl_materials.mode, "") as mode,
                tbl_materials.is_single_use as is_single_use,
                tbl_category_items.recipe as recipe,
                tbl_materials.longs as longs,
                tbl_materials.wide as wide,
                tbl_materials.height as height,
                tbl_materials.price_import as price_import
            ');
            $this->db->from('tbl_materials');
            $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');

            $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
            $this->db->where('tbl_materials.id', $material_price_quotes);
            $materials = $this->db->get()->result_array();
            if (!empty($materials)) {
                foreach ($materials as $key => $value) {
                    $recipe = $value['recipe'];
                    $longs = $value['longs'];
                    $wide = $value['wide'];
                    $height = $value['height'];
                    $price_import = $value['price_import'];
                    if ($recipe) {
                        // $price_sell = ($price_import * $height * $wide)/10000;
                        $price_sell = ($price_import * $height_layout * $width_layout) / 10000;
                        $materials[$key]['price_sell'] = $price_sell;
                    } else {
                        $materials[$key]['price_sell'] = 0;
                    }
                }
            }
            $data['items_material_price_quotes'] = $materials;
        }

        $data['items_stages_price_quotes'] = [];
        if (!empty($stages_price_quotes)) {

            if (empty($group_id)) $group_id = 0;
            $group_id = explode(',', $group_id);
            $tbStagesPrice = "(
                SELECT
                    tbl_stages_customer_prices.stage_id as stage_id,
                    MAX(tbl_stages_customer_prices.price_group_customer) as price_group_customer
                FROM tbl_stages_customer_prices
                WHERE tbl_stages_customer_prices.customers_groups_id IN (" . implode(',', $group_id) . ")
                GROUP BY tbl_stages_customer_prices.stage_id 
            ) tb_stages_price";

            $this->db->select('
                tbl_stages.id as id,
                "stages" as type,
                tbl_stages.code as code,
                tbl_stages.name as name,
                "" as unit_name,
                IF (coalesce(tb_stages_price.price_group_customer, 0) > 0, tb_stages_price.price_group_customer, tbl_stages.stage_price_gauge) as price_sell,
                "" as mode,
                0 as is_single_use,
                tbl_stages.formula_m2 as formula_m2
            ');
            $this->db->from('tbl_stages');
            $this->db->join($tbStagesPrice, 'tb_stages_price.stage_id = tbl_stages.id', 'left');
            $this->db->where('tbl_stages.id', $stages_price_quotes);
            $stage = $this->db->get()->result_array();
            if (!empty($stage)) {
                foreach ($stage as $key => $value) {
                    // Code cũ
                    /*
                    $this->db->where('id_stage', $value['id']);
                    // $this->db->where('height', $height);
                    // $this->db->where('width', $width);
//                    $this->db->where('EXISTS (SELECT 1 FROM tbl_stage_quote_client WHERE tbl_stage_quote_client.id_stage_quote = tbl_stage_quote_detail.id_stage_quote AND tbl_stage_quote_client.id_client = "' . $id_customer . '")'); // tạm đóng
                    $this->db->where('id_stage_quote', $quote_stage_id);
                    $stage[$key]['price_sell'] = $this->db->get('tbl_stage_quote_detail')->row('price');
                    */

                    // Code mới lấy theo dài rộng (height, width)
                    $this->db->where('id_stage', $value['id']);
                    $this->db->where('id_stage_quote', $quote_stage_id);
                    // if (!empty($height)) {
                    //     $this->db->where('height', $height);
                    // }
                    // if (!empty($width)) {
                    //     $this->db->where('width', $width);
                    // }
                    $stage[$key]['price_sell'] = $this->db->get('tbl_stage_quote_detail')->row('price');
                    $stage[$key]['price_sell'] = !empty($stage[$key]['price_sell']) ?  $stage[$key]['price_sell'] : 0;

                    if ($value['formula_m2']) {
                        // $price_sell = ($price_sell * $height_layout * $width_layout)/10000;
                        $price_sell = ($stage[$key]['price_sell'] * $height_layout * $width_layout) / 10000;
                        $stage[$key]['price_sell'] = $price_sell;
                    }
                }
            }
            $data['items_stages_price_quotes'] = $stage;
        }
        echo json_encode($data);
    }

    public function handlingReferencePriceBK()
    {
        $data = [];
        $dataPost = $this->input->post();
        if (!empty($dataPost)) {
            $dataJson = [];

            $product_quote_reference = $this->input->post('product_quote_reference');
            $cItemsId = $this->input->post('cItemsId');
            $quote_stage_id = $this->input->post('quote_stage_id');
            $customers = $this->input->post('customers');
            $height = number_unformat($this->input->post('height'));
            $corner_boundary_height = number_unformat($this->input->post('corner_boundary_height'));
            $perpendicular_border_height = number_unformat($this->input->post('perpendicular_border_height'));
            $round_square_border_height = number_unformat($this->input->post('round_square_border_height'));
            $product_calculation_height = $height + $corner_boundary_height + $perpendicular_border_height + $round_square_border_height;

            $width = number_unformat($this->input->post('width'));
            $corner_boundary_width = number_unformat($this->input->post('corner_boundary_width'));
            $perpendicular_border_width = number_unformat($this->input->post('perpendicular_border_width'));
            $round_square_border_width = number_unformat($this->input->post('round_square_border_width'));
            $product_calculation_width = $width + $corner_boundary_width + $perpendicular_border_width + $round_square_border_width;

            $product_calculation_height_width = $product_calculation_height . 'x' . $product_calculation_width . ' cm';

            $height_layout = number_unformat($this->input->post('height_layout'));
            $height_layout_print_tweezers = number_unformat($this->input->post('height_layout_print_tweezers'));
            $height_layout_boong_cut = number_unformat($this->input->post('height_layout_boong_cut'));
            $height_layout_material_size = $height_layout - $height_layout_print_tweezers - $height_layout_boong_cut;
            $height_layout_mode = $product_calculation_height;
            $height_layout_quantity = 0;
            if ($height_layout_material_size != 0) {
                $height_layout_quantity = floor($height_layout_material_size / $height_layout_mode);
            }

            $width_layout = number_unformat($this->input->post('width_layout'));
            $width_layout_print_tweezers = number_unformat($this->input->post('width_layout_print_tweezers'));
            $width_layout_boong_cut = number_unformat($this->input->post('width_layout_boong_cut'));
            $width_layout_material_size = $width_layout - $width_layout_print_tweezers - $width_layout_boong_cut;

            $width_layout_mode = $product_calculation_width;
            $width_layout_quantity = 0;
            if ($width_layout_material_size != 0) {
                $width_layout_quantity = floor($width_layout_material_size / $width_layout_mode);
            }

            $height_layout_total_quantity = floor($height_layout_quantity * $width_layout_quantity);

            $ItemsPrice = [];
            $type_price = $this->input->post('type_price');
            $grandTotalSheet = 0;
            if (!empty($type_price)) {
                foreach ($type_price as $key => $value) {
                    $item_id_price = !empty($this->input->post('item_id_price')[$key]) ? $this->input->post('item_id_price')[$key] : '';
                    if (empty($item_id_price)) continue;
                    $stage_id_price = !empty($this->input->post('stage_id_price')[$key]) ? $this->input->post('stage_id_price')[$key] : 0;
                    $number_operate = !empty($this->input->post('number_operate')[$key]) ? number_unformat($this->input->post('number_operate')[$key]) : 0;
                    $price_about = !empty($this->input->post('price_about')[$key]) ? number_unformat($this->input->post('price_about')[$key]) : 0;
                    $quantity_color = !empty($this->input->post('quantity_color')[$key]) ? number_unformat($this->input->post('quantity_color')[$key]) : 0;
                    $machine = !empty($this->input->post('machine')[$key]) ? $this->input->post('machine')[$key] : 0;
                    $type_npl = !empty($this->input->post('type_npl')[$key]) ? $this->input->post('type_npl')[$key] : '';
                    $quota_bom = !empty($this->input->post('quota_bom')[$key]) ? number_unformat($this->input->post('quota_bom')[$key]) : '';
                    $total_sheet = $number_operate * $price_about;

                    $ItemsPrice[] = [
                        'type_price' => $value,
                        'stage_id_price' => $stage_id_price,
                        'item_id_price' => $item_id_price,
                        'number_operate' => $number_operate,
                        'price_about' => $price_about,
                        'total_sheet' => $total_sheet,
                        'quantity_color' => $quantity_color,
                        'machine' => $machine,
                        'type_npl' => $type_npl,
                        'quota_bom' => $quota_bom,
                    ];

                    $grandTotalSheet += $total_sheet;
                }
            }

            $itemsPriceBackside = [];
            $type_price_backside = $this->input->post('type_price_backside');
            $grandTotalSheetBackside = 0;
            if (!empty($type_price_backside)) {
                foreach ($type_price_backside as $key => $value) {
                    $item_id_price_backside = !empty($this->input->post('item_id_price_backside')[$key]) ? $this->input->post('item_id_price_backside')[$key] : '';
                    if (empty($item_id_price_backside)) continue;
                    $stage_id_price_backside = !empty($this->input->post('stage_id_price_backside')[$key]) ? $this->input->post('stage_id_price_backside')[$key] : 0;
                    $number_operate_backside = !empty($this->input->post('number_operate_backside')[$key]) ? number_unformat($this->input->post('number_operate_backside')[$key]) : 0;
                    $price_about_backside = !empty($this->input->post('price_about_backside')[$key]) ? number_unformat($this->input->post('price_about_backside')[$key]) : 0;
                    $quantity_color_backside = !empty($this->input->post('quantity_color_backside')[$key]) ? number_unformat($this->input->post('quantity_color_backside')[$key]) : 0;
                    $total_sheet_backside = $number_operate_backside * $price_about_backside;

                    $machine_backside = !empty($this->input->post('machine_backside')[$key]) ? $this->input->post('machine_backside')[$key] : 0;
                    $type_npl_backside = !empty($this->input->post('type_npl_backside')[$key]) ? $this->input->post('type_npl_backside')[$key] : '';
                    $quota_bom_backside = !empty($this->input->post('quota_bom_backside')[$key]) ? number_unformat($this->input->post('quota_bom_backside')[$key]) : '';

                    $itemsPriceBackside[] = [
                        'type_price_backside' => $value,
                        'stage_id_price_backside' => $stage_id_price_backside,
                        'item_id_price_backside' => $item_id_price_backside,
                        'number_operate_backside' => $number_operate_backside,
                        'price_about_backside' => $price_about_backside,
                        'total_sheet_backside' => $total_sheet_backside,
                        'quantity_color_backside' => $quantity_color_backside,
                        'machine_backside' => $machine_backside,
                        'type_npl_backside' => $type_npl_backside,
                        'quota_bom_backside' => $quota_bom_backside,
                    ];

                    $grandTotalSheetBackside += $total_sheet_backside;
                }
            }

            $sum1 = $grandTotalSheetBackside + $grandTotalSheet;

            //stages products
            $itemsStagesProducts = [];
            $type_price_products = $this->input->post('type_price_products');
            $grandTotalProduct = 0;
            $grandTotalProductCal = 0;
            if (!empty($type_price_products)) {
                foreach ($type_price_products as $key => $value) {
                    $item_id_price_products = !empty($this->input->post('item_id_price_products')[$key]) ? $this->input->post('item_id_price_products')[$key] : '';
                    if (empty($item_id_price_products)) continue;
                    $stage_id_price_products = !empty($this->input->post('stage_id_price_products')[$key]) ? $this->input->post('stage_id_price_products')[$key] : 0;
                    $number_operate_products = !empty($this->input->post('number_operate_products')[$key]) ? number_unformat($this->input->post('number_operate_products')[$key]) : 0;
                    $face_products = !empty($this->input->post('face_products')[$key]) ? number_unformat($this->input->post('face_products')[$key]) : 0;

                    $not_cpln = !empty($this->input->post('not_cpln')[$key]) ? $this->input->post('not_cpln')[$key] : 0;
                    $long_height = !empty($this->input->post('long_height')[$key]) ? $this->input->post('long_height')[$key] : '';
                    $width_horizontal = !empty($this->input->post('width_horizontal')[$key]) ? $this->input->post('width_horizontal')[$key] : '';

                    // Code cũ lấy đơn giá từ client gửi lên
                    // $price_about_products = !empty($this->input->post('price_about_products')[$key]) ? number_unformat($this->input->post('price_about_products')[$key]) : 0;

                    // Code mới lấy từ bảng tbl_stage_quote_detail
                    $price_about_products = $this->handling_price_model->getStagePrice(
                        $stage_id_price_products,
                        $quote_stage_id,
                        $customers,
                        $height_layout,
                        $width_layout,
                        $long_height,
                        $width_horizontal
                    );
                    $total_sheet_products = $number_operate_products * $price_about_products;

                    $itemsStagesProducts[] = [
                        'type_price_products' => $value,
                        'stage_id_price_products' => $stage_id_price_products,
                        'item_id_price_products' => $item_id_price_products,
                        'number_operate_products' => $number_operate_products,
                        'price_about_products' => $price_about_products,
                        'total_sheet_products' => $total_sheet_products,
                        'not_cpln' => $not_cpln,
                        'long_height' => $long_height,
                        'width_horizontal' => $width_horizontal,
                        'face_products' => $face_products,
                    ];

                    if (!$not_cpln) {
                        $grandTotalProductCal += $total_sheet_products;
                    }

                    $grandTotalProduct += $total_sheet_products;
                }
            }

            $sum2 = $grandTotalProduct;
            $g1 = 0;
            $g1_cal = 0;
            if ($height_layout_total_quantity > 0) {
                $g1 = ($sum1 + $sum2) / $height_layout_total_quantity;
                $g1_cal = ($sum1 + $grandTotalProductCal) / $height_layout_total_quantity;
            }

            $total_price_child_gvc = 0;

            $itemsGVC = [];
            $type_vc = $this->input->post('type_vc');
            $total_price_child_gvc = 0;
            if (!empty($type_vc)) {
                foreach ($type_vc as $key => $value) {
                    if (empty($value)) continue;

                    $unit_kg = !empty($this->input->post('unit_kg')[$key]) ? $this->input->post('unit_kg')[$key] : '';
                    $price_gvc = !empty($this->input->post('price_gvc')[$key]) ? number_unformat($this->input->post('price_gvc')[$key]) : 0;
                    $kg_child_gvc = !empty($this->input->post('kg_child_gvc')[$key]) ? number_unformat($this->input->post('kg_child_gvc')[$key]) : 0;
                    $price_child_gvc = $price_gvc * $kg_child_gvc;
                    $total_price_child_gvc += $price_child_gvc;
                    $itemsGVC[] = [
                        'type_vc' => $value,
                        'price_gvc' => $price_gvc,
                        'unit_kg' => $unit_kg,
                        'kg_child_gvc' => $kg_child_gvc,
                        'price_child_gvc' => $price_child_gvc,
                        'total_price_child_gvc' => $total_price_child_gvc,
                    ];
                }
            }
            $g2 = $total_price_child_gvc;

            // $gsp1 = 0;
            // if ($height_layout_total_quantity > 0) {
            //     $gsp1 = $grandTotalSheet / $height_layout_total_quantity;
            // }

            // $price_gvc = number_unformat($this->input->post('price_gvc'));
            // $kg_child_gvc = number_unformat($this->input->post('kg_child_gvc'));
            // $price_child_gvc = $price_gvc * $kg_child_gvc;

            // $ggc = number_unformat($this->input->post('processing_price'));
            // $gsp2 = $gsp1 + $ggc + $price_child_gvc;

            $cost_of_brand = number_unformat($this->input->post('cost_of_brand'));
            $labor_cost = number_unformat($this->input->post('labor_cost'));
            $loss_cost = number_unformat($this->input->post('loss_cost'));
            $profit = number_unformat($this->input->post('profit'));

            $total_precent = 0;
            $is_not_cost_of_brand = !empty($this->input->post('is_not_cost_of_brand')) ? 1 : 0;
            $is_not_labor_cost = !empty($this->input->post('is_not_labor_cost')) ? 1 : 0;
            $is_not_loss_cost = !empty($this->input->post('is_not_loss_cost')) ? 1 : 0;

            // $total_precent = 0;
            // if (!$is_not_cost_of_brand) {
            //     $total_precent+= $cost_of_brand;
            // }

            // if (!$is_not_labor_cost) {
            //     $total_precent+= $labor_cost;
            // }

            // if (!$is_not_loss_cost) {
            //     $total_precent+= $loss_cost;
            // }

            // $total_precent+= $profit;

            $total_precent = $cost_of_brand + $labor_cost + $loss_cost + $profit;
            // $g3 = ($g1 + $g2) * $total_precent / 100;
            $g3 = ($g1_cal + $g2) * $total_precent / 100;
            // $g = $gsp2 + $g3;
            $g = $g1 + $g2 + $g3;

            $type_vc = $this->input->post('type_vc');
            $unit_kg = $this->input->post('unit_kg');

            $dataJson = [
                'product_quote_reference' => $product_quote_reference,
                'cItemsId' => $cItemsId,
                'height' => $height,
                'corner_boundary_height' => $corner_boundary_height,
                'perpendicular_border_height' => $perpendicular_border_height,
                'round_square_border_height' => $round_square_border_height,
                'product_calculation_height' => $product_calculation_height,
                'width' => $width,
                'corner_boundary_width' => $corner_boundary_width,
                'perpendicular_border_width' => $perpendicular_border_width,
                'round_square_border_width' => $round_square_border_width,
                'product_calculation_width' => $product_calculation_width,
                'product_calculation_height_width' => $product_calculation_height_width,
                'height_layout' => $height_layout,
                'height_layout_print_tweezers' => $height_layout_print_tweezers,
                'height_layout_boong_cut' => $height_layout_boong_cut,
                'height_layout_material_size' => $height_layout_material_size,
                'height_layout_mode' => $height_layout_mode,
                'height_layout_quantity' => $height_layout_quantity,
                'width_layout' => $width_layout,
                'width_layout_print_tweezers' => $width_layout_print_tweezers,
                'width_layout_boong_cut' => $width_layout_boong_cut,
                'width_layout_material_size' => $width_layout_material_size,
                'width_layout_mode' => $width_layout_mode,
                'width_layout_quantity' => $width_layout_quantity,
                'height_layout_total_quantity' => $height_layout_total_quantity,
                'ItemsPrice' => $ItemsPrice,
                'grandTotalSheet' => $grandTotalSheet,
                // 'gsp1' => $gsp1,
                // 'type_vc' => $type_vc,
                // 'unit_kg' => $unit_kg,
                // 'price_gvc' => $price_gvc,
                // 'kg_child_gvc' => $kg_child_gvc,
                // 'price_child_gvc' => $price_child_gvc,
                // 'ggc' => $ggc,
                // 'gsp2' => $gsp2,
                'labor_cost' => $labor_cost,
                'loss_cost' => $loss_cost,
                'profit' => $profit,
                'total_precent' => $total_precent,
                'g3' => $g3,
                'g' => $g,

                'itemsPriceBackside' => $itemsPriceBackside,
                'grandTotalSheetBackside' => $grandTotalSheetBackside,
                'sum1' => $sum1,
                'itemsStagesProducts' => $itemsStagesProducts,
                'grandTotalProduct' => $grandTotalProduct,
                'sum2' => $sum2,
                'g1' => $g1,
                'total_price_child_gvc' => $total_price_child_gvc,
                'g2' => $g2,
                'itemsGVC' => $itemsGVC,
                'cost_of_brand' => $cost_of_brand,
                'is_not_cost_of_brand' => $is_not_cost_of_brand,
                'is_not_labor_cost' => $is_not_labor_cost,
                'is_not_loss_cost' => $is_not_loss_cost,
                'g1_cal' => $g1_cal,
            ];

            // print_arrays($dataJson);
            $dataJson = json_encode($dataJson, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $data['dataJson'] = $dataJson;
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('not_data');
        }
        echo json_encode($data);
    }

    public function handlingReferencePrice()
    {
        $data = [];
        $dataPost = $this->input->post();
        if (!empty($dataPost)) {
            $dataJson = [];

            $product_quote_reference = $this->input->post('product_quote_reference');
            $cItemsId = $this->input->post('cItemsId');
            $quote_stage_id = $this->input->post('quote_stage_id');
            $customers = $this->input->post('customers');
            $height = number_unformat($this->input->post('height'));
            $corner_boundary_height = number_unformat($this->input->post('corner_boundary_height'));
            $perpendicular_border_height = number_unformat($this->input->post('perpendicular_border_height'));
            $round_square_border_height = number_unformat($this->input->post('round_square_border_height'));
            $product_calculation_height = $height + $corner_boundary_height + $perpendicular_border_height + $round_square_border_height;

            $width = number_unformat($this->input->post('width'));
            $corner_boundary_width = number_unformat($this->input->post('corner_boundary_width'));
            $perpendicular_border_width = number_unformat($this->input->post('perpendicular_border_width'));
            $round_square_border_width = number_unformat($this->input->post('round_square_border_width'));
            $product_calculation_width = $width + $corner_boundary_width + $perpendicular_border_width + $round_square_border_width;

            $product_calculation_height_width = $product_calculation_height . 'x' . $product_calculation_width . ' cm';

            $height_layout = number_unformat($this->input->post('height_layout'));
            $height_layout_print_tweezers = number_unformat($this->input->post('height_layout_print_tweezers'));
            $height_layout_boong_cut = number_unformat($this->input->post('height_layout_boong_cut'));
            $height_layout_material_size = $height_layout - $height_layout_print_tweezers - $height_layout_boong_cut;
            $height_layout_mode = $product_calculation_height;
            $height_layout_quantity = 0;
            if ($height_layout_material_size != 0) {
                $height_layout_quantity = floor($height_layout_material_size / $height_layout_mode);
            }

            $width_layout = number_unformat($this->input->post('width_layout'));
            $width_layout_print_tweezers = number_unformat($this->input->post('width_layout_print_tweezers'));
            $width_layout_boong_cut = number_unformat($this->input->post('width_layout_boong_cut'));
            $width_layout_material_size = $width_layout - $width_layout_print_tweezers - $width_layout_boong_cut;

            $width_layout_mode = $product_calculation_width;
            $width_layout_quantity = 0;
            if ($width_layout_material_size != 0) {
                $width_layout_quantity = floor($width_layout_material_size / $width_layout_mode);
            }

            $height_layout_total_quantity = floor($height_layout_quantity * $width_layout_quantity);

            $ItemsPrice = [];
            $type_price = $this->input->post('type_price');
            $grandTotalSheet = 0;
            if (!empty($type_price)) {
                foreach ($type_price as $key => $value) {
                    $item_id_price = !empty($this->input->post('item_id_price')[$key]) ? $this->input->post('item_id_price')[$key] : '';
                    if (empty($item_id_price)) continue;
                    $stage_id_price = !empty($this->input->post('stage_id_price')[$key]) ? $this->input->post('stage_id_price')[$key] : 0;
                    $number_operate = !empty($this->input->post('number_operate')[$key]) ? number_unformat($this->input->post('number_operate')[$key]) : 0;
                    $price_about = !empty($this->input->post('price_about')[$key]) ? number_unformat($this->input->post('price_about')[$key]) : 0;
                    $quantity_color = !empty($this->input->post('quantity_color')[$key]) ? number_unformat($this->input->post('quantity_color')[$key]) : 0;
                    $machine = !empty($this->input->post('machine')[$key]) ? $this->input->post('machine')[$key] : 0;
                    $type_npl = !empty($this->input->post('type_npl')[$key]) ? $this->input->post('type_npl')[$key] : '';
                    $quota_bom = !empty($this->input->post('quota_bom')[$key]) ? number_unformat($this->input->post('quota_bom')[$key]) : '';
                    $total_sheet = $number_operate * $price_about;

                    $ItemsPrice[] = [
                        'type_price' => $value,
                        'stage_id_price' => $stage_id_price,
                        'item_id_price' => $item_id_price,
                        'number_operate' => $number_operate,
                        'price_about' => $price_about,
                        'total_sheet' => $total_sheet,
                        'quantity_color' => $quantity_color,
                        'machine' => $machine,
                        'type_npl' => $type_npl,
                        'quota_bom' => $quota_bom,
                    ];

                    $grandTotalSheet += $total_sheet;
                }
            }

            $itemsPriceBackside = [];
            $type_price_backside = $this->input->post('type_price_backside');
            $grandTotalSheetBackside = 0;
            if (!empty($type_price_backside)) {
                foreach ($type_price_backside as $key => $value) {
                    $item_id_price_backside = !empty($this->input->post('item_id_price_backside')[$key]) ? $this->input->post('item_id_price_backside')[$key] : '';
                    if (empty($item_id_price_backside)) continue;
                    $stage_id_price_backside = !empty($this->input->post('stage_id_price_backside')[$key]) ? $this->input->post('stage_id_price_backside')[$key] : 0;
                    $number_operate_backside = !empty($this->input->post('number_operate_backside')[$key]) ? number_unformat($this->input->post('number_operate_backside')[$key]) : 0;
                    $price_about_backside = !empty($this->input->post('price_about_backside')[$key]) ? number_unformat($this->input->post('price_about_backside')[$key]) : 0;
                    $quantity_color_backside = !empty($this->input->post('quantity_color_backside')[$key]) ? number_unformat($this->input->post('quantity_color_backside')[$key]) : 0;
                    $total_sheet_backside = $number_operate_backside * $price_about_backside;

                    $machine_backside = !empty($this->input->post('machine_backside')[$key]) ? $this->input->post('machine_backside')[$key] : 0;
                    $type_npl_backside = !empty($this->input->post('type_npl_backside')[$key]) ? $this->input->post('type_npl_backside')[$key] : '';
                    $quota_bom_backside = !empty($this->input->post('quota_bom_backside')[$key]) ? number_unformat($this->input->post('quota_bom_backside')[$key]) : '';

                    $itemsPriceBackside[] = [
                        'type_price_backside' => $value,
                        'stage_id_price_backside' => $stage_id_price_backside,
                        'item_id_price_backside' => $item_id_price_backside,
                        'number_operate_backside' => $number_operate_backside,
                        'price_about_backside' => $price_about_backside,
                        'total_sheet_backside' => $total_sheet_backside,
                        'quantity_color_backside' => $quantity_color_backside,
                        'machine_backside' => $machine_backside,
                        'type_npl_backside' => $type_npl_backside,
                        'quota_bom_backside' => $quota_bom_backside,
                    ];

                    $grandTotalSheetBackside += $total_sheet_backside;
                }
            }

            $sum1 = $grandTotalSheetBackside + $grandTotalSheet;

            //stages products
            $itemsStagesProducts = [];
            $type_price_products = $this->input->post('type_price_products');
            $grandTotalProduct = 0;
            $grandTotalProductCal = 0;
            if (!empty($type_price_products)) {
                foreach ($type_price_products as $key => $value) {
                    $item_id_price_products = !empty($this->input->post('item_id_price_products')[$key]) ? $this->input->post('item_id_price_products')[$key] : '';
                    if (empty($item_id_price_products)) continue;
                    $stage_id_price_products = !empty($this->input->post('stage_id_price_products')[$key]) ? $this->input->post('stage_id_price_products')[$key] : 0;
                    $number_operate_products = !empty($this->input->post('number_operate_products')[$key]) ? number_unformat($this->input->post('number_operate_products')[$key]) : 0;
                    $face_products = !empty($this->input->post('face_products')[$key]) ? number_unformat($this->input->post('face_products')[$key]) : 0;

                    $not_cpln = !empty($this->input->post('not_cpln')[$key]) ? $this->input->post('not_cpln')[$key] : 0;
                    $long_height = !empty($this->input->post('long_height')[$key]) ? $this->input->post('long_height')[$key] : '';
                    $width_horizontal = !empty($this->input->post('width_horizontal')[$key]) ? $this->input->post('width_horizontal')[$key] : '';

                    // Code cũ lấy đơn giá từ client gửi lên
                    // $price_about_products = !empty($this->input->post('price_about_products')[$key]) ? number_unformat($this->input->post('price_about_products')[$key]) : 0;

                    // Code mới lấy từ bảng tbl_stage_quote_detail
                    $price_about_products = $this->handling_price_model->getStagePrice(
                        $stage_id_price_products,
                        $quote_stage_id,
                        $customers,
                        $height_layout,
                        $width_layout,
                        $long_height,
                        $width_horizontal
                    );
                    $total_sheet_products = $number_operate_products * $price_about_products;

                    $itemsStagesProducts[] = [
                        'type_price_products' => $value,
                        'stage_id_price_products' => $stage_id_price_products,
                        'item_id_price_products' => $item_id_price_products,
                        'number_operate_products' => $number_operate_products,
                        'price_about_products' => $price_about_products,
                        'total_sheet_products' => $total_sheet_products,
                        'not_cpln' => $not_cpln,
                        'long_height' => $long_height,
                        'width_horizontal' => $width_horizontal,
                        'face_products' => $face_products,
                    ];

                    if (!$not_cpln) {
                        $grandTotalProductCal += $total_sheet_products;
                    }

                    $grandTotalProduct += $total_sheet_products;
                }
            }

            $sum2 = $grandTotalProduct;
            $g1 = 0;
            $g1_cal = 0;
            if ($height_layout_total_quantity > 0) {
                $g1 = ($sum1 + $sum2) / $height_layout_total_quantity;
                $g1_cal = ($sum1 + $grandTotalProductCal) / $height_layout_total_quantity;
            }

            $total_price_child_gvc = 0;

            $itemsGVC = [];
            $type_vc = $this->input->post('type_vc');
            $total_price_child_gvc = 0;
            if (!empty($type_vc)) {
                foreach ($type_vc as $key => $value) {
                    if (empty($value)) continue;

                    $unit_kg = !empty($this->input->post('unit_kg')[$key]) ? $this->input->post('unit_kg')[$key] : '';
                    $price_gvc = !empty($this->input->post('price_gvc')[$key]) ? number_unformat($this->input->post('price_gvc')[$key]) : 0;
                    $kg_child_gvc = !empty($this->input->post('kg_child_gvc')[$key]) ? number_unformat($this->input->post('kg_child_gvc')[$key]) : 0;
                    $price_child_gvc = $price_gvc * $kg_child_gvc;
                    $total_price_child_gvc += $price_child_gvc;
                    $itemsGVC[] = [
                        'type_vc' => $value,
                        'price_gvc' => $price_gvc,
                        'unit_kg' => $unit_kg,
                        'kg_child_gvc' => $kg_child_gvc,
                        'price_child_gvc' => $price_child_gvc,
                        'total_price_child_gvc' => $total_price_child_gvc,
                    ];
                }
            }
            $g2 = $total_price_child_gvc;

            $cost_of_brand = number_unformat($this->input->post('cost_of_brand'));
            $labor_cost = number_unformat($this->input->post('labor_cost'));
            $loss_cost = number_unformat($this->input->post('loss_cost'));
            $profit = number_unformat($this->input->post('profit'));

            $total_precent = 0;
            $is_not_cost_of_brand = !empty($this->input->post('is_not_cost_of_brand')) ? 1 : 0;
            $is_not_labor_cost = !empty($this->input->post('is_not_labor_cost')) ? 1 : 0;
            $is_not_loss_cost = !empty($this->input->post('is_not_loss_cost')) ? 1 : 0;

            //more
            $items_npl = $this->input->post('items_npl');
            $arrItemsNPL = [];
            if (!empty($items_npl)) {
                foreach ($items_npl as $key => $value) {
                    $__item_id = $value['item_id'];
                    $__height = number_unformat($value['height']);
                    $__width = number_unformat($value['width']);
                    $__unit_measure_sp = trim($value['unit_measure_sp']);
                    $__unit_calculation_sp = trim($value['unit_calculation_sp']);
                    $__height1 = number_unformat($value['height1']);
                    $__leave_margin = number_unformat($value['leave_margin']);
                    $__width1 = number_unformat($value['width1']);
                    $__leave_margin1 = number_unformat($value['leave_margin1']);
                    $__unit_measure_sp1 = trim($value['unit_measure_sp1']);
                    $__unit_calculation_sp1 = trim($value['unit_calculation_sp1']);
                    $__leave_tweezers = number_unformat($value['leave_tweezers']);
                    $__leave_discharge_w = number_unformat($value['leave_discharge_w']);
                    $__leave_discharge_h = number_unformat($value['leave_discharge_h']);
                    $__total_child_w = number_unformat($value['total_child_w']);
                    $__total_child_h = number_unformat($value['total_child_h']);
                    $__total_child_page = number_unformat($value['total_child_page']);
                    $__price_page = number_unformat($value['price_page']);
                    $__price_xlt = number_unformat($value['price_xlt']);
                    $__total_money = $__price_page + $__price_xlt;

                    $arrItemsNPL[] = [
                        'item_id' => $__item_id,
                        'height' => $__height,
                        'width' => $__width,
                        'unit_measure_sp' => $__unit_measure_sp,
                        'unit_calculation_sp' => $__unit_calculation_sp,
                        'height1' => $__height1,
                        'leave_margin' => $__leave_margin,
                        'width1' => $__width1,
                        'leave_margin1' => $__leave_margin1,
                        'unit_measure_sp1' => $__unit_measure_sp1,
                        'unit_calculation_sp1' => $__unit_calculation_sp1,
                        'leave_tweezers' => $__leave_tweezers,
                        'leave_discharge_w' => $__leave_discharge_w,
                        'leave_discharge_h' => $__leave_discharge_h,
                        'total_child_w' => $__total_child_w,
                        'total_child_h' => $__total_child_h,
                        'total_child_page' => $__total_child_page,
                        'price_page' => $__price_page,
                        'price_xlt' => $__price_xlt,
                        'total_money' => $__total_money,
                    ];
                }
            }

            $items_lstage = $this->input->post('items_lstage');
            $arrItemsLStage = [];
            if (!empty($items_lstage)) {
                foreach ($items_lstage as $key => $value) {
                    $__item_id = $value['item_id'];
                    $__type = $value['type'];
                    $__height = number_unformat($value['height']);
                    $__width = number_unformat($value['width']);
                    $__number_child_print_f1 = number_unformat($value['number_child_print_f1']);
                    $__number_color_print_f1 = number_unformat($value['number_color_print_f1']);
                    $__type_npl_f1 = trim($value['type_npl_f1']);
                    $__number_zn_f1 = number_unformat($value['number_zn_f1']);
                    $__number_operations_page_f1 = number_unformat($value['number_operations_page_f1']);
                    $__quota_zn_use_f1 = number_unformat($value['quota_zn_use_f1']);
                    $__quota_ctp_f1 = number_unformat($value['quota_ctp_f1']);
                    $__number_child_print_f2 = number_unformat($value['number_child_print_f2']);
                    $__number_color_print_f2 = number_unformat($value['number_color_print_f2']);
                    $__type_npl_f2 = trim($value['type_npl_f2']);
                    $__number_zn_f2 = number_unformat($value['number_zn_f2']);
                    $__number_operations_page_f2 = number_unformat($value['number_operations_page_f2']);
                    $__quota_zn_use_f2 = number_unformat($value['quota_zn_use_f2']);
                    $__quota_ctp_f2 = number_unformat($value['quota_ctp_f2']);
                    $__total_npl = number_unformat($value['total_npl']);
                    $__price = number_unformat($value['price']);
                    $__total_operations_page = $__number_operations_page_f1 + $__number_operations_page_f2;
                    $__total_price = $__total_operations_page * $__price;

                    $arrItemsLStage[] = [
                        'item_id' => $__item_id,
                        'type' => $__type,
                        'height' => $__height,
                        'width' => $__width,
                        'number_child_print_f1' => $__number_child_print_f1,
                        'number_color_print_f1' => $__number_color_print_f1,
                        'type_npl_f1' => $__type_npl_f1,
                        'number_zn_f1' => $__number_zn_f1,
                        'number_operations_page_f1' => $__number_operations_page_f1,
                        'quota_zn_use_f1' => $__quota_zn_use_f1,
                        'quota_ctp_f1' => $__quota_ctp_f1,
                        'number_child_print_f2' => $__number_child_print_f2,
                        'number_color_print_f2' => $__number_color_print_f2,
                        'type_npl_f2' => $__type_npl_f2,
                        'number_zn_f2' => $__number_zn_f2,
                        'number_operations_page_f2' => $__number_operations_page_f2,
                        'quota_zn_use_f2' => $__quota_zn_use_f2,
                        'quota_ctp_f2' => $__quota_ctp_f2,
                        'total_npl' => $__total_npl,
                        'price' => $__price,
                        'total_operations_page' => $__total_operations_page,
                        'total_price' => $__total_price,
                    ];
                }
            }

            $items_psstage = $this->input->post('items_psstage');
            $arrItemsPsStage = [];
            if (!empty($items_psstage)) {
                foreach ($items_psstage as $key => $arrItemsS) {
                    $itemsS = [];
                    if (!empty($arrItemsS)) {
                        foreach ($arrItemsS as $kS => $vS) {
                            $__item_id = $vS['item_id'];
                            $__type = $vS['type'];
                            $__height = number_unformat($vS['height']);
                            $__width = number_unformat($vS['width']);
                            $__number_operating_f1 = number_unformat($vS['number_operating_f1']);
                            $__type_npl_f1 = trim($vS['type_npl_f1']);
                            $__number_operating_side_f1 = number_unformat($vS['number_operating_side_f1']);
                            $__ink_f1 = number_unformat($vS['ink_f1']);
                            $__quota_time_f1 = number_unformat($vS['quota_time_f1']);
                            $__quota_npl_f1 = number_unformat($vS['quota_npl_f1']);
                            $__total_npl_f1 = number_unformat($vS['total_npl_f1']);
                            $__total_time_npl_f1 = number_unformat($vS['total_time_npl_f1']);
                            $__number_operating_f2 = number_unformat($vS['number_operating_f2']);
                            $__type_npl_f2 = trim($vS['type_npl_f2']);
                            $__number_operating_side_f2 = number_unformat($vS['number_operating_side_f2']);
                            $__ink_f2 = number_unformat($vS['ink_f2']);
                            $__quota_time_f2 = number_unformat($vS['quota_time_f2']);
                            $__quota_npl_f2 = number_unformat($vS['quota_npl_f2']);
                            $__total_npl_f2 = number_unformat($vS['total_npl_f2']);
                            $__total_time_npl_f2 = number_unformat($vS['total_time_npl_f2']);
                            $__price = number_unformat($vS['price']);
                            $__total_npl_f12 = $__total_npl_f1 + $__total_npl_f2;
                            $__total_time_f12 = $__total_time_npl_f1 + $__total_time_npl_f2;
                            $__total_number_operating_side = $__number_operating_side_f1 + $__number_operating_side_f2;
                            $__price_page = $__total_number_operating_side * $__price;

                            $itemsS[] = [
                                'item_id' => $__item_id,
                                'type' => $__type,
                                'height' => $__height,
                                'width' => $__width,
                                'number_operating_f1' => $__number_operating_f1,
                                'type_npl_f1' => $__type_npl_f1,
                                'number_operating_side_f1' => $__number_operating_side_f1,
                                'ink_f1' => $__ink_f1,
                                'quota_time_f1' => $__quota_time_f1,
                                'quota_npl_f1' => $__quota_npl_f1,
                                'total_npl_f1' => $__total_npl_f1,
                                'total_time_npl_f1' => $__total_time_npl_f1,
                                'number_operating_f2' => $__number_operating_f2,
                                'type_npl_f2' => $__type_npl_f2,
                                'number_operating_side_f2' => $__number_operating_side_f2,
                                'ink_f2' => $__ink_f2,
                                'quota_time_f2' => $__quota_time_f2,
                                'quota_npl_f2' => $__quota_npl_f2,
                                'total_npl_f2' => $__total_npl_f2,
                                'total_time_npl_f2' => $__total_time_npl_f2,
                                'price' => $__price,
                                'total_npl_f12' => $__total_npl_f12,
                                'total_time_f12' => $__total_time_f12,
                                'total_number_operating_side' => $__total_number_operating_side,
                                'price_page' => $__price_page,
                            ];
                        }
                    }

                    $arrItemsPsStage[] = [
                        'stage_id' => $key,
                        'itemsS' => $itemsS
                    ];
                }
            }

            $items_istage = $this->input->post('items_istage');
            $arrItemsIStage = [];
            if (!empty($items_istage)) {
                foreach ($items_istage as $key => $value) {
                    $__category_stage_id = $value['category_stage_id'];
                    $__height = number_unformat($value['height']);
                    $__width = number_unformat($value['width']);
                    $__unit_f1 = trim($value['unit_f1']);
                    $__type_check_f1 = trim($value['type_check_f1']);
                    $__number_o_side_f1 = number_unformat($value['number_o_side_f1']);
                    $__productivity_norms_f1 = number_unformat($value['productivity_norms_f1']);
                    $__type_check_f2 = trim($value['type_check_f2']);
                    $__number_o_side_f2 = number_unformat($value['number_o_side_f2']);
                    $__productivity_norms_f2 = number_unformat($value['productivity_norms_f2']);

                    $arrItemsIStage[] = [
                        'category_stage_id' => $__category_stage_id,
                        'height' => $__height,
                        'width' => $__width,
                        'unit_f1' => $__unit_f1,
                        'type_check_f1' => $__type_check_f1,
                        'number_o_side_f1' => $__number_o_side_f1,
                        'productivity_norms_f1' => $__productivity_norms_f1,
                        'type_check_f2' => $__type_check_f2,
                        'number_o_side_f2' => $__number_o_side_f2,
                        'productivity_norms_f2' => $__productivity_norms_f2,
                    ];
                }
            }

            $items_pstage = $this->input->post('items_pstage');
            $arrItemsPStage = [];
            if (!empty($items_pstage)) {
                foreach ($items_pstage as $key => $value) {
                    $__category_stage_id = $value['category_stage_id'];
                    $__height = number_unformat($value['height']);
                    $__width = number_unformat($value['width']);
                    $__hight_bottom = number_unformat($value['hight_bottom']);
                    $__unit = trim($value['unit']);
                    $__number_bales = number_unformat($value['number_bales']);
                    $__bale_norms = number_unformat($value['bale_norms']);
                    $__productivity_norms = number_unformat($value['productivity_norms']);
                    $__type_packaging = trim($value['type_packaging']);
                    $__type_tem = trim($value['type_tem']);
                    $__total_tem = number_unformat($value['total_tem']);
                    $__total_bale = number_unformat($value['total_bale']);

                    $arrItemsPStage[] = [
                        'category_stage_id' => $__category_stage_id,
                        'height' => $__height,
                        'width' => $__width,
                        'hight_bottom' => $__hight_bottom,
                        'unit' => $__unit,
                        'number_bales' => $__number_bales,
                        'bale_norms' => $__bale_norms,
                        'productivity_norms' => $__productivity_norms,
                        'type_packaging' => $__type_packaging,
                        'type_tem' => $__type_tem,
                        'total_tem' => $__total_tem,
                        'total_bale' => $__total_bale,
                    ];
                }
            }

            $items_dstage = $this->input->post('items_dstage');
            $arrItemsDStage = [];
            if (!empty($items_dstage)) {
                foreach ($items_dstage as $key => $value) {
                    $__category_stage_id = $value['category_stage_id'];
                    $__height = number_unformat($value['height']);
                    $__width = number_unformat($value['width']);
                    $__hight_bottom = number_unformat($value['hight_bottom']);
                    $__unit = trim($value['unit']);
                    $__number_bales = number_unformat($value['number_bales']);
                    $__bale_norms = number_unformat($value['bale_norms']);
                    $__productivity_norms = number_unformat($value['productivity_norms']);
                    $__type_packaging = trim($value['type_packaging']);
                    $__type_tem = trim($value['type_tem']);
                    $__total_tem = number_unformat($value['total_tem']);
                    $__total_bale = number_unformat($value['total_bale']);

                    $arrItemsDStage[] = [
                        'category_stage_id' => $__category_stage_id,
                        'height' => $__height,
                        'width' => $__width,
                        'hight_bottom' => $__hight_bottom,
                        'unit' => $__unit,
                        'number_bales' => $__number_bales,
                        'bale_norms' => $__bale_norms,
                        'productivity_norms' => $__productivity_norms,
                        'type_packaging' => $__type_packaging,
                        'type_tem' => $__type_tem,
                        'total_tem' => $__total_tem,
                        'total_bale' => $__total_bale,
                    ];
                }
            }

            $items_cstage = $this->input->post('items_cstage');
            $arrItemsCStage = [];
            if (!empty($items_cstage)) {
                foreach ($items_cstage as $key => $value) {
                    $__category_stage_id = $value['category_stage_id'];
                    $__transportation = trim($value['transportation']);
                    $__unit = trim($value['unit']);
                    $__price_delivery = number_unformat($value['price_delivery']);
                    $__total_bale = number_unformat($value['total_bale']);
                    $__subtotal = number_unformat($value['subtotal']);
                    $__supplier = trim($value['supplier']);
                    $__address_delivery = trim($value['address_delivery']);

                    $arrItemsCStage[] = [
                        'category_stage_id' => $__category_stage_id,
                        'transportation' => $__transportation,
                        'unit' => $__unit,
                        'price_delivery' => $__price_delivery,
                        'total_bale' => $__total_bale,
                        'subtotal' => $__subtotal,
                        'supplier' => $__supplier,
                        'address_delivery' => $__address_delivery,
                    ];
                }
            }
            //end more

            $total_precent = $cost_of_brand + $labor_cost + $loss_cost + $profit;
            $g3 = ($g1_cal + $g2) * $total_precent / 100;
            $g = $g1 + $g2 + $g3;

            $type_vc = $this->input->post('type_vc');
            $unit_kg = $this->input->post('unit_kg');

            $dataJson = [
                'product_quote_reference' => $product_quote_reference,
                'cItemsId' => $cItemsId,
                'height' => $height,
                'corner_boundary_height' => $corner_boundary_height,
                'perpendicular_border_height' => $perpendicular_border_height,
                'round_square_border_height' => $round_square_border_height,
                'product_calculation_height' => $product_calculation_height,
                'width' => $width,
                'corner_boundary_width' => $corner_boundary_width,
                'perpendicular_border_width' => $perpendicular_border_width,
                'round_square_border_width' => $round_square_border_width,
                'product_calculation_width' => $product_calculation_width,
                'product_calculation_height_width' => $product_calculation_height_width,
                'height_layout' => $height_layout,
                'height_layout_print_tweezers' => $height_layout_print_tweezers,
                'height_layout_boong_cut' => $height_layout_boong_cut,
                'height_layout_material_size' => $height_layout_material_size,
                'height_layout_mode' => $height_layout_mode,
                'height_layout_quantity' => $height_layout_quantity,
                'width_layout' => $width_layout,
                'width_layout_print_tweezers' => $width_layout_print_tweezers,
                'width_layout_boong_cut' => $width_layout_boong_cut,
                'width_layout_material_size' => $width_layout_material_size,
                'width_layout_mode' => $width_layout_mode,
                'width_layout_quantity' => $width_layout_quantity,
                'height_layout_total_quantity' => $height_layout_total_quantity,
                'ItemsPrice' => $ItemsPrice,
                'grandTotalSheet' => $grandTotalSheet,
                'labor_cost' => $labor_cost,
                'loss_cost' => $loss_cost,
                'profit' => $profit,
                'total_precent' => $total_precent,
                'g3' => $g3,
                'g' => $g,

                'itemsPriceBackside' => $itemsPriceBackside,
                'grandTotalSheetBackside' => $grandTotalSheetBackside,
                'sum1' => $sum1,
                'itemsStagesProducts' => $itemsStagesProducts,
                'grandTotalProduct' => $grandTotalProduct,
                'sum2' => $sum2,
                'g1' => $g1,
                'total_price_child_gvc' => $total_price_child_gvc,
                'g2' => $g2,
                'itemsGVC' => $itemsGVC,
                'cost_of_brand' => $cost_of_brand,
                'is_not_cost_of_brand' => $is_not_cost_of_brand,
                'is_not_labor_cost' => $is_not_labor_cost,
                'is_not_loss_cost' => $is_not_loss_cost,
                'g1_cal' => $g1_cal,
                'arrItemsNPL' => $arrItemsNPL,
                'arrItemsLStage' => $arrItemsLStage,
                'arrItemsPsStage' => $arrItemsPsStage,
                'arrItemsIStage' => $arrItemsIStage,
                'arrItemsPStage' => $arrItemsPStage,
                'arrItemsDStage' => $arrItemsDStage,
                'arrItemsCStage' => $arrItemsCStage,
            ];

            // print_arrays($dataJson);
            $dataJson = json_encode($dataJson, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $data['dataJson'] = $dataJson;
            $data['result'] = 1;
            $data['message'] = lang('success');
        } else {
            $data['result'] = 0;
            $data['message'] = lang('not_data');
        }
        echo json_encode($data);
    }

    public function view_price($object_item_id, $type)
    {
        $data = [];

        $data['object_item_id'] = $object_item_id;
        $data['type'] = $type;
        $this->load->view('admin/handling_price/view_price', $data);
    }

    public function getQuotesProductsPrice($id = 0)
    {
        $data = [];
        $term = $this->input->get('term', TRUE);
        $limit = get_option('select2_limit');
        $params = $this->input->get('params');
        $customer_id_price = !empty($params['customer_id_price']) ? $params['customer_id_price'] : '';
        // CONCAT(tbl_quotes.reference_no, "(", tblclients.company,")", "(", tbl_products.name,")", "(", tbl_products.code,")") as text,

        $this->db->select('
            tbl_quote_items.id as id,
            CONCAT(tbl_products.name, "(", tbl_products.code, ")", " - ", DATE_FORMAT(tbl_quotes.date, "%d/%m/%Y")) as text
        ', false);
        $this->db->from('tbl_quotes');
        $this->db->join('tbl_quote_items', 'tbl_quote_items.quote_id = tbl_quotes.id');
        $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
        $this->db->join('tblclients', 'tblclients.userid = tbl_quotes.customer_id');
        $this->db->where('tbl_quote_items.type_item', 'products');
        $this->db->group_start();
        $this->db->like('tblclients.company', $term);
        $this->db->or_like('tbl_products.code', $term);
        $this->db->or_like('tbl_products.name', $term);
        $this->db->group_end();

        if (!empty($customer_id_price)) {
            $customer_id_price = str_replace('customers__', '', $customer_id_price);
            $this->db->where('tbl_quotes.customer_id', $customer_id_price);
        }

        $this->db->order_by('tbl_quotes.date DESC');
        $this->db->limit($limit);
        $data['results'] = $this->db->get()->result_array();

        if ($id) {
            $this->db->select('
                tbl_quote_items.id as id,
                CONCAT(tbl_products.name, "(", tbl_products.code, ")", " - ", DATE_FORMAT(tbl_quotes.date, "%d/%m/%Y")) as text
            ', false);
            $this->db->from('tbl_quotes');
            $this->db->join('tbl_quote_items', 'tbl_quote_items.quote_id = tbl_quotes.id');
            $this->db->join('tbl_products', 'tbl_products.id = tbl_quote_items.item_id');
            $this->db->where('tbl_quote_items.id', $id);
            $quote_item = $this->db->get()->row_array();
            $data['row'] = ['id' => $quote_item['id'], 'text' => $quote_item['text']];
        }
        echo json_encode($data);
    }

    public function getReferenceProductQuota()
    {
        $data = [];
        $product_quote_reference = $this->input->get('product_quote_reference');

        $quote_item = get_table_where('tbl_quote_items', ['id' => $product_quote_reference], '', 'row_array');
        if (!empty($quote_item)) {
            $arrDataJson = !empty($quote_item['data_price_json']) ? json_decode($quote_item['data_price_json'], true) : null;

            $trItemPrice = '';
            if (!empty($arrDataJson['ItemsPrice'])) {
                foreach ($arrDataJson['ItemsPrice'] as $key => $value) {
                    $type_price = $value['type_price'];
                    $item_id_price = $value['item_id_price'];
                    $dtItem = [];
                    if ($type_price == "materials") {
                        $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                    } else if ($type_price == "stages") {
                        $dtItem = $this->handling_price_model->getItemsStagesPriceQuotes($item_id_price);
                    }

                    $tdCategoryStages = '<td>
                        <input type="hidden" name="type_price[]" class="form-control type_price" value="' . $type_price . '">
                        <input type="hidden" name="item_id_price[]" class="form-control item_id_price" value="' . $item_id_price . '">
                        <div>' . $dtItem['code'] . '</div>
                    </td>';

                    $tdUnits = '<td><div class="text-center">' . $dtItem['unit_name'] . '</div></td>';
                    $tdMode = '<td><div class="text-center">' . $dtItem['mode'] . '</div></td>';
                    $tdNumberOperate = '<td class="text-center">
                        
                        <input type="text" name="number_operate[]" placeholder="' . lang('Số lần vận hành') . '" onchange="totalItemsPrice()" place class="form-control number_operate number-format" value="' . formatNumber($value['number_operate']) . '">
                    </td>';
                    $tdPriceAbout = '<td class="text-center">
                        <input type="text" name="price_about[]" placeholder="' . lang('Đơn giá/lần') . '" onchange="totalItemsPrice()" class="form-control price_about money-format" value="' . formatMoney($value['price_about']) . '">
                    </td>';
                    $tdTotalSheet = '<td class="td-total-sheet text-right">
                        ' . formatMoney($value['total_sheet']) . '
                    </td>';
                    $tdActions = '<td class="text-center"><i onclick="removeItemsPrice(this)" class="fa fa-remove text-danger pointer"></i></td>';

                    $trItemPrice .= '<tr>
                        ' . $tdCategoryStages . '
                        ' . $tdUnits . '
                        ' . $tdMode . '
                        ' . $tdNumberOperate . '
                        ' . $tdPriceAbout . '
                        ' . $tdTotalSheet . '
                        ' . $tdActions . '
                    </tr>';
                }
            }

            $data['arrDataJson'] = $arrDataJson;
            $data['trItemPrice'] = $trItemPrice;
        }
        echo json_encode($data);
    }

    public function saveBOM()
    {
        $data = [];
        if ($this->input->post('is_bom')) {
            $itemsBOM = [];
            $arrStage = [];

            $height = number_unformat($this->input->post('height'));
            $corner_boundary_height = number_unformat($this->input->post('corner_boundary_height'));
            $perpendicular_border_height = number_unformat($this->input->post('perpendicular_border_height'));
            $round_square_border_height = number_unformat($this->input->post('round_square_border_height'));
            $product_calculation_height = $height + $corner_boundary_height + $perpendicular_border_height + $round_square_border_height;

            $width = number_unformat($this->input->post('width'));
            $corner_boundary_width = number_unformat($this->input->post('corner_boundary_width'));
            $perpendicular_border_width = number_unformat($this->input->post('perpendicular_border_width'));
            $round_square_border_width = number_unformat($this->input->post('round_square_border_width'));
            $product_calculation_width = $width + $corner_boundary_width + $perpendicular_border_width + $round_square_border_width;

            $product_calculation_height_width = $product_calculation_height . 'x' . $product_calculation_width . ' cm';

            $height_layout = number_unformat($this->input->post('height_layout'));
            $height_layout_print_tweezers = number_unformat($this->input->post('height_layout_print_tweezers'));
            $height_layout_boong_cut = number_unformat($this->input->post('height_layout_boong_cut'));
            $height_layout_material_size = $height_layout - $height_layout_print_tweezers - $height_layout_boong_cut;
            $height_layout_mode = $product_calculation_height;
            $height_layout_quantity = 0;
            if ($height_layout_material_size != 0) {
                $height_layout_quantity = floor($height_layout_material_size / $height_layout_mode);
            }

            $width_layout = number_unformat($this->input->post('width_layout'));
            $width_layout_print_tweezers = number_unformat($this->input->post('width_layout_print_tweezers'));
            $width_layout_boong_cut = number_unformat($this->input->post('width_layout_boong_cut'));
            $width_layout_material_size = $width_layout - $width_layout_print_tweezers - $width_layout_boong_cut;

            $width_layout_mode = $product_calculation_width;
            $width_layout_quantity = 0;
            if ($width_layout_material_size != 0) {
                $width_layout_quantity = floor($width_layout_material_size / $width_layout_mode);
            }

            // $height_layout_total_quantity = roundNumberFormat($height_layout_quantity * $width_layout_quantity, 2);
            $height_layout_total_quantity = floor($height_layout_quantity * $width_layout_quantity);

            $type_price = $this->input->post('type_price');
            $grandTotalSheet = 0;
            $arr_number_operate_products = [];
            $arrMachineId = [];
            if (!empty($type_price)) {
                foreach ($type_price as $key => $value) {
                    $item_id_price = !empty($this->input->post('item_id_price')[$key]) ? $this->input->post('item_id_price')[$key] : '';
                    if (empty($item_id_price)) continue;
                    $stage_id_price = !empty($this->input->post('stage_id_price')[$key]) ? $this->input->post('stage_id_price')[$key] : 0;
                    $number_operate = !empty($this->input->post('number_operate')[$key]) ? number_unformat($this->input->post('number_operate')[$key]) : 0;
                    $price_about = !empty($this->input->post('price_about')[$key]) ? number_unformat($this->input->post('price_about')[$key]) : 0;
                    $quantity_color = !empty($this->input->post('quantity_color')[$key]) ? number_unformat($this->input->post('quantity_color')[$key]) : 0;
                    $quota_bom = !empty($this->input->post('quota_bom')[$key]) ? number_unformat($this->input->post('quota_bom')[$key]) : 0;
                    $total_sheet = $number_operate * $price_about;
                    $machine = !empty($this->input->post('machine')[$key]) ? $this->input->post('machine')[$key] : 0;

                    $itemsBOM[] = [
                        'type_price' => $value,
                        'stage_id_price' => $stage_id_price,
                        'item_id_price' => $item_id_price,
                        'number_operate' => $number_operate,
                        'price_about' => $price_about,
                        'total_sheet' => $total_sheet,
                        'quantity_color' => $quantity_color,
                        'quota_bom' => $quota_bom,
                        'face' => 1,
                        'face_after' => 0,
                        'machine' => $machine,
                    ];

                    $arrStage[] = $stage_id_price;
                    if (!empty($machine)) {
                        $arrMachineId[$stage_id_price] = $machine;
                    }
                    $arrMachineId[$stage_id_price] = !empty($machine) ? $machine :

                        $arr_number_operate_products[] = 0;
                }
            }

            $type_price_backside = $this->input->post('type_price_backside');
            $grandTotalSheetBackside = 0;
            if (!empty($type_price_backside)) {
                foreach ($type_price_backside as $key => $value) {
                    $item_id_price_backside = !empty($this->input->post('item_id_price_backside')[$key]) ? $this->input->post('item_id_price_backside')[$key] : '';
                    if (empty($item_id_price_backside)) continue;
                    $stage_id_price_backside = !empty($this->input->post('stage_id_price_backside')[$key]) ? $this->input->post('stage_id_price_backside')[$key] : 0;
                    $number_operate_backside = !empty($this->input->post('number_operate_backside')[$key]) ? number_unformat($this->input->post('number_operate_backside')[$key]) : 0;
                    $price_about_backside = !empty($this->input->post('price_about_backside')[$key]) ? number_unformat($this->input->post('price_about_backside')[$key]) : 0;
                    $quantity_color_backside = !empty($this->input->post('quantity_color_backside')[$key]) ? number_unformat($this->input->post('quantity_color_backside')[$key]) : 0;
                    $quota_bom_backside = !empty($this->input->post('quota_bom_backside')[$key]) ? number_unformat($this->input->post('quota_bom_backside')[$key]) : 0;
                    $total_sheet_backside = $number_operate_backside * $price_about_backside;
                    $machine_backside = !empty($this->input->post('machine_backside')[$key]) ? $this->input->post('machine_backside')[$key] : 0;

                    $itemsBOM[] = [
                        'type_price' => $value,
                        'stage_id_price' => $stage_id_price_backside,
                        'item_id_price' => $item_id_price_backside,
                        'number_operate' => $number_operate_backside,
                        'price_about' => $price_about_backside,
                        'total_sheet' => $total_sheet_backside,
                        'quantity_color' => $quantity_color_backside,
                        'quota_bom' => $quota_bom_backside,
                        'face' => 0,
                        'face_after' => 2,
                        'machine_backside' => $machine_backside,
                    ];

                    $arrStage[] = $stage_id_price_backside;
                    if (!empty($machine_backside)) {
                        $arrMachineId[$stage_id_price_backside] = $machine_backside;
                    }

                    $arr_number_operate_products[] = 0;
                }
            }

            $itemsStagesProducts = [];
            $type_price_products = $this->input->post('type_price_products');
            $arrNumberStages = [];
            if (!empty($type_price_products)) {
                foreach ($type_price_products as $key => $value) {
                    $stage_id_price_products = !empty($this->input->post('stage_id_price_products')[$key]) ? $this->input->post('stage_id_price_products')[$key] : 0;
                    $number_operate_products = !empty($this->input->post('number_operate_products')[$key]) ? number_unformat($this->input->post('number_operate_products')[$key]) : 0;
                    $face_products = !empty($this->input->post('face_products')[$key]) ? number_unformat($this->input->post('face_products')[$key]) : 0;

                    $arrStage[] = $stage_id_price_products;

                    $arr_number_operate_products[] = $number_operate_products;
                    // $arrNumberStages[$stage_id_price_products] = $number_operate_products;
                    $arrNumberStages[$stage_id_price_products] = $face_products;
                }
            }

            // print_arrays($arrMachineId);

            $versions = time();
            $cItemsId = explode('__', $this->input->post('cItemsId'));
            $product_id = $cItemsId[0];


            foreach ($itemsBOM as $key => $value) {
            }

            // $count
            $result = array();
            foreach ($itemsBOM as $obj) {
                $isCheck = searchDuplicate($result, $obj);
                if ($isCheck === false) {
                    $result[] = $obj;
                } else {
                    if ($isCheck['result'] == true && $isCheck['face_after'] == 2) {
                        $result[$isCheck['key']]['face_after'] = $isCheck['face_after'];
                    }
                }
            }
            $itemsBOM = $result;

            //
            $items_psstage = $this->input->post('items_psstage');
            $arrItemsPsStage = [];
            if (!empty($items_psstage)) {
                foreach ($items_psstage as $key => $arrItemsS) {
                    if (!empty($arrItemsS)) {
                        foreach ($arrItemsS as $kS => $vS) {
                            $__item_id = $vS['item_id'];
                            $__type = $vS['type'];
                            $__number_operating_side_f1 = number_unformat($vS['number_operating_side_f1']);
                            $__quota_time_f1 = number_unformat($vS['quota_time_f1']);
                            $__quota_time_f2 = number_unformat($vS['quota_time_f2']);
                            $__index = $__type . '__' . $__item_id;
                            $arrItemsPsStage[$__index] = [
                                '__type' => $__type,
                                '__item_id' => $__item_id,
                                '__number_operating_side_f1' => $__number_operating_side_f1,
                                '__quota_time_f1' => $__quota_time_f1,
                                '__quota_time_f2' => $__quota_time_f2,
                            ];
                        }
                    }

                    break;
                }
            }
            //
            // print_arrays($arrItemsPsStage);

            $options = [];
            if (!empty($itemsBOM)) {
                $options = [];
                $status = "unapplication";

                $date_start = null;
                $date_end = null;
                $use_version = 1;
                $options['versions'] = $versions;
                $options['product_id'] = $product_id;
                $options['date_start'] = $date_start;
                $options['date_end'] = $date_end;
                $options['date_created'] = date('Y-m-d H:i:s');
                $options['created_by'] = get_staff_user_id();
                $element_name = 'NPL chính';
                $key = 0;
                $options['element'][$key]['element_name'] = $element_name;
                // $options['element'][$key]['element_number'] = $height_layout_total_quantity;
                $options['element'][$key]['element_number'] = 1;
                $options['element'][$key]['type_element'] = 1;
                foreach ($itemsBOM as $k => $val) {
                    $dtItem = [];
                    $type_price = $val['type_price'];
                    $item_id_price = $val['item_id_price'];
                    $stage_id_price = $val['stage_id_price'];
                    $quota_bom = !empty($val['quota_bom']) ? $val['quota_bom'] : 0;

                    $dtStages = $this->handling_price_model->getItemsStagesPriceQuotes($stage_id_price);
                    if ($type_price == "materials") {
                        $dtItem = $this->handling_price_model->getMaterialPriceQuotes($item_id_price);
                    }
                    if (empty($dtItem)) continue;
                    $is_single_use = $dtItem['is_single_use'];
                    $item_id = $dtItem['id'];

                    $options['element'][$key]['items'][$k]['type'] = 'materials';
                    $options['element'][$key]['items'][$k]['item_id'] = $item_id;
                    $options['element'][$key]['items'][$k]['unit_id'] = $dtItem['unit_id'];
                    // $options['element'][$key]['items'][$k]['element_item_number'] = $is_single_use ? $val['quantity_color'] : $val['number_operate'];
                    $options['element'][$key]['items'][$k]['element_item_number'] = $quota_bom;
                    $options['element'][$key]['items'][$k]['leadtime'] = 0;
                    $options['element'][$key]['items'][$k]['stage'] = $stage_id_price;
                    // $options['element'][$key]['items'][$k]['stage'] = STAGES_MATERIAL;
                    $options['element'][$key]['items'][$k]['machines_id'] = 0;
                    $options['element'][$key]['items'][$k]['type_element_item'] = 1;

                    $landscape_print_size = $height_layout . 'X' . $width_layout;
                    $vertical_print_size = $width_layout;
                    $number_children_size = $height_layout_total_quantity;
                    $options['element'][$key]['items'][$k]['landscape_print_size'] = $landscape_print_size;
                    $options['element'][$key]['items'][$k]['vertical_print_size'] = $vertical_print_size;
                    $options['element'][$key]['items'][$k]['number_children_size'] = $number_children_size;
                    $options['element'][$key]['items'][$k]['face'] = $val['face'];
                    $options['element'][$key]['items'][$k]['face_after'] = $val['face_after'];

                    $paper_exchange = 0;
                    if ($number_children_size) {
                        $paper_exchange = roundNumberFormat(1 / $number_children_size);
                    }
                    $options['element'][$key]['items'][$k]['paper_exchange'] = $paper_exchange;
                }

                $q = $this->products_model->insertBOM($options, $status, 0, 'add');
                if ($q) {
                    if (!empty($use_version)) {
                        $this->products_model->updateProducts($product_id, ['versions' => $versions]);
                    }
                    $data['result'] = 1;
                    $data['message'] = lang('success');
                } else {
                    $data['result'] = 0;
                    $data['message'] = lang('Lưu Bom không thành công');
                    echo json_encode($data);
                    die;
                }
            }

            if (!empty($arrStage)) {
                $arrStage = array_unique($arrStage);
                // print_arrays($arrStage);
                $options = [];
                $status = "unapplication";
                $options['versions'] = $versions;
                $options['product_id'] = $product_id;
                $use_version = 1;
                $number = 1;

                if (!in_array(STAGES_MATERIAL, $arrStage)) {
                    array_unshift($arrStage, STAGES_MATERIAL);
                    array_unshift($arr_number_operate_products, 0);
                }

                $arrStage = array_values($arrStage);
                $ctStage = count($arrStage) - 1;
                foreach ($arrStage as $key => $value) {
                    $options['items'][$key]['stage'] = $value;
                    $options['items'][$key]['number'] = $number;
                    $options['items'][$key]['number_hours'] = 0;
                    $options['items'][$key]['machines'] = !empty($arrMachineId[$value]) ? $arrMachineId[$value] : 0;
                    $options['items'][$key]['final_stage'] = ($key == $ctStage ? 1 : 0);

                    // $numberCur = !empty($arr_number_operate_products[$key]) ? $arr_number_operate_products[$key] : 0;

                    $numberCur = !empty($arrNumberStages[$value]) ? $arrNumberStages[$value] : 0;
                    // $options['items'][$key]['face'] = $numberCur == 1 || $numberCur == 2 ? 1 : 0;
                    // $options['items'][$key]['face_after'] = $numberCur == 2 ? 2 : 0;
                    $options['items'][$key]['face'] = $numberCur == 1 || $numberCur == 3 ? 1 : 0;
                    $options['items'][$key]['face_after'] = $numberCur == 2 || $numberCur == 3 ? 2 : 0;
                    $options['items'][$key]['type'] = $value == STAGES_MATERIAL ? 6 : 0;

                    //
                    $number_face = 0;
                    $quota_time_f1 = 0;
                    $quota_time_f2 = 0;
                    if (!empty($arrItemsPsStage['1__' . $value])) {
                        $_data = $arrItemsPsStage['1__' . $value];
                        $number_face = $_data['__number_operating_side_f1'];
                        $quota_time_f1 = $_data['__quota_time_f1'];
                        $quota_time_f2 = $_data['__quota_time_f2'];
                    }

                    if (!empty($arrItemsPsStage['2__' . $options['items'][$key]['machines']])) {
                        $_data = $arrItemsPsStage['2__' . $options['items'][$key]['machines']];
                        if (empty($number_face)) {
                            $number_face = $_data['__number_operating_side_f1'];
                        }

                        if (empty($quota_time_f1)) {
                            $quota_time_f1 = $_data['__quota_time_f1'];
                        }

                        if (empty($quota_time_f2)) {
                            $quota_time_f2 = $_data['__quota_time_f2'];
                        }
                    }
                    //

                    if ($key == 1 && $value != STAGES_MATERIAL) {
                        $options['items'][$key]['type'] = 2;
                    }

                    $options['items'][$key]['number_face'] = $number_face;
                    $options['items'][$key]['quota_time_f1'] = $quota_time_f1;
                    $options['items'][$key]['quota_time_f2'] = $quota_time_f2;

                    $number++;
                }

                $q = $this->products_model->insertProductStages($options, $status, 0);
                if ($q) {
                    if (!empty($use_version)) {
                        $this->products_model->updateProducts($product_id, ['versions_stage' => $versions]);
                    }

                    $data['result'] = 1;
                    $data['message'] = lang('success');
                }
            }
        } else {
            $data['result'] = 1;
            $data['message'] = lang('success');
        }
        echo json_encode($data);
    }

    public function loadExpenseQuote()
    {
        $data = [];

        $customer_id = str_replace('customers__', '', $this->input->post('customer_id'));
        $quote_stage_id = $this->input->post('quote_stage_id');

        $this->db->select('tbl_stage_quote.*');
        $this->db->from('tbl_stage_quote');
        // $this->db->from('tbl_stage_quote_client');
        // $this->db->join('tbl_stage_quote', 'tbl_stage_quote.id = tbl_stage_quote_client.id_stage_quote');
        // $this->db->where('tbl_stage_quote_client.id_client', $customer_id);
        $this->db->where('tbl_stage_quote.id', $quote_stage_id);
        // $this->db->order_by('tbl_stage_quote.id DESC');
        // $this->db->limit(1);
        $stage_quote_client = $this->db->get()->row_array();
        $data['stage_quote_client'] = $stage_quote_client;
        echo json_encode($data);
    }

    public function addItemMaterialInfoNPL()
    {
        $material_info_npl = $this->input->post('material_info_npl');
        $data['items_material_info_npl'] = [];
        $this->db->select('
            tbl_materials.id as id,
            "materials" as type,
            tbl_materials.code as code,
            tbl_materials.name as name,
            tblunits.unit as unit_name,
            tbl_materials.price_sell as price_sell,
            coalesce(tbl_materials.mode, "") as mode,
            tbl_materials.is_single_use as is_single_use,
            tbl_category_items.recipe as recipe,
            tbl_materials.longs as longs,
            tbl_materials.wide as wide,
            tbl_materials.height as height,
            tbl_materials.price_import as price_import
        ');
        $this->db->from('tbl_materials');
        $this->db->join('tbl_category_items', 'tbl_category_items.id = tbl_materials.category_id');

        $this->db->join('tblunits', 'tblunits.unitid = tbl_materials.unit_id', 'left');
        $this->db->where('tbl_materials.id', $material_info_npl);
        $materials = $this->db->get()->result_array();
        if (!empty($materials)) {
            foreach ($materials as $key => $value) {
            }
        }
        $data['items_material_info_npl'] = $materials;
        echo json_encode($data);
    }

    public function addItemInspectionStage()
    {
        $inspection_stage = $this->input->post('inspection_stage');
        $data['items_inspection_stage'] = [];

        $this->db->select('
            tbl_category_stages.*
        ');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.id', $inspection_stage);
        $category_stages = $this->db->get()->result_array();
        $data['items_inspection_stage'] = $category_stages;
        echo json_encode($data);
    }

    public function addItemPackageStage()
    {
        $package_stage = $this->input->post('package_stage');
        $data['items_package_stage'] = [];

        $this->db->select('
            tbl_category_stages.*
        ');
        $this->db->from('tbl_category_stages');
        $this->db->where('tbl_category_stages.id', $package_stage);
        $category_stages = $this->db->get()->result_array();
        $data['items_package_stage'] = $category_stages;
        echo json_encode($data);
    }
    public function check_qr()
    {
        $code = $this->input->get('code');
        $type = $this->input->get('type');
        if ($code) {
            $code = explode('||', $code);
            if (count($code) == 1) {
                $data['message'] = 'Mã không đúng định dạng';
                $data['result'] = false;
                echo json_encode($data);
                die;
            }
            if ($type == 1) {
                if ($code[0] != 'machine') {
                    $data['message'] = 'Không tìm thấy thiết bị';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($type == 2) {
                if ($code[0] != 'materials') {
                    $data['message'] = 'Không tìm thấy mặt hàng';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($type == 3) {
                if ($code[0] != 'stages') {
                    $data['message'] = 'Không tìm thấy công đoạn';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            // materials||8511
            if ($code[0] == 'materials') {
                $check = get_table_where('tbl_materials', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['id'] = $code[1];
                    $data['result'] = true;
                    $data['message'] = 'Thành công, Vui lòng quét công đoạn';
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy mặt hàng';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'stages') {
                $check = get_table_where('tbl_stages', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['id'] = $code[1];
                    $data['result'] = true;
                    $data['message'] = 'Thành công, Vui lòng quét nguyên phụ liệu nếu chưa quét';
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy công đoạn';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
            if ($code[0] == 'machine') {

                $check = get_table_where('tbl_machines', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['id'] = $code[1];
                    $data['message'] = 'Thành công';
                    $data['result'] = true;
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy thiết bị';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
        }
    }
    public function check_qr_new()
    {
        $code = $this->input->get('code');
        $type = $this->input->get('type');
        if ($code) {
            $code = explode('||', $code);
            if (count($code) == 1) {
                $data['message'] = 'Mã không đúng định dạng';
                $data['result'] = false;
                echo json_encode($data);
                die;
            }
            if ($code[0] == 'stages') {
                $check = get_table_where('tbl_stages', array('id' => $code[1]), '', 'row_array');
                if (!empty($check)) {
                    $data['type'] = 1;
                    $data['id'] = $code[1];
                    $data['result'] = true;
                    $data['message'] = 'Thành công';
                    echo json_encode($data);
                    die;
                } else {
                    $data['message'] = 'Không tìm thấy công đoạn';
                    $data['result'] = false;
                    echo json_encode($data);
                    die;
                }
            }
        }
    }
}
