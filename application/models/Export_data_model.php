<?php defined('BASEPATH') or exit('No direct script access allowed');

class Export_data_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Đếm tổng số sản phẩm theo bộ lọc (dùng cho export)
     */
    public function countProductsForExport($filters = [])
    {
        $this->db->from('tbl_products');
        $this->db->where('tbl_products.is_use_product', 1);

        if (!empty($filters['keyword'])) {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $filters['keyword']);
            $this->db->or_like('tbl_products.code', $filters['keyword']);
            $this->db->group_end();
        }

        if (!empty($filters['category_id'])) {
            if (is_array($filters['category_id'])) {
                $this->db->where_in('tbl_products.category_id', $filters['category_id']);
            } else {
                $this->db->where('tbl_products.category_id', $filters['category_id']);
            }
        }

        return (int) $this->db->count_all_results();
    }

    /**
     * Lấy danh sách sản phẩm theo chunk (phân trang) cho export
     */
    public function getProductsChunkForExport($limit = 100, $offset = 0, $filters = [])
    {
        $this->db->select('
            tbl_products.*,
            tbl_category_products.name AS category_name,
            tbl_category_products.code AS category_code,
            tbl_species.name AS species_name
        ', false);

        $this->db->from('tbl_products');
        $this->db->where('tbl_products.is_use_product', 1);

        $this->db->join(
            'tbl_category_products',
            'tbl_category_products.id = tbl_products.category_id',
            'left'
        );

        $this->db->join(
            'tbl_species',
            'tbl_species.id = tbl_products.species',
            'left'
        );

        if (!empty($filters['keyword'])) {
            $this->db->group_start();
            $this->db->like('tbl_products.name', $filters['keyword']);
            $this->db->or_like('tbl_products.code', $filters['keyword']);
            $this->db->group_end();
        }

        if (!empty($filters['category_id'])) {
            if (is_array($filters['category_id'])) {
                $this->db->where_in('tbl_products.category_id', $filters['category_id']);
            } else {
                $this->db->where('tbl_products.category_id', $filters['category_id']);
            }
        }

        $this->db->order_by('tbl_products.id', 'ASC');
        $this->db->limit((int) $limit, (int) $offset);

        return $this->db->get()->result_array();
    }

    /**
     * Lấy danh sách product_stages theo mảng product_ids
     */
    public function getProductStagesByProductIds($product_ids = [])
    {
        if (empty($product_ids)) {
            return [];
        }

        $this->db->select('tbl_product_stages.*');
        $this->db->from('tbl_product_stages');
        $this->db->where_in('tbl_product_stages.product_id', $product_ids);
        $this->db->order_by('tbl_product_stages.id', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Lấy danh sách product_stages_versions theo mảng version_ids
     */
    public function getProductStagesVersionsByVersionIds($version_ids = [])
    {
        if (empty($version_ids)) {
            return [];
        }

        $this->db->select('
            tbl_product_stages_versions.*,
            tbl_stages.name AS stage_name,
            tbl_stages.code AS stage_code,
            tbl_machines.code AS machine_code
        ', false);

        $this->db->from('tbl_product_stages_versions');

        $this->db->join(
            'tbl_stages',
            'tbl_stages.id = tbl_product_stages_versions.stage_id',
            'left'
        );

        $this->db->join(
            'tbl_machines',
            'tbl_machines.id = tbl_product_stages_versions.machines',
            'left'
        );

        $this->db->where_in('tbl_product_stages_versions.version_id', $version_ids);
        $this->db->order_by('tbl_product_stages_versions.number', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Lấy danh sách norms (BOM) theo mảng product_ids
     */
    public function getProductNormsByProductIds($product_ids = [])
    {
        if (empty($product_ids)) {
            return [];
        }

        $product = "(
            SELECT CONCAT(tbl_products.code, '|||', tbl_products.name)
            FROM tbl_products
            WHERE tbl_element_items.item_id = tbl_products.id
        )";
        $material = "(
            SELECT CONCAT(tbl_materials.code, '|||', tbl_materials.name)
            FROM tbl_materials
            WHERE tbl_element_items.item_id = tbl_materials.id
        )";

        $this->db->select('
            tbl_product_versions.product_id,
            tbl_product_versions.versions,
            tbl_versions_element.element_name,
            tbl_element_items.type, 
            tbl_element_items.item_id, 
            tbl_element_items.quantity, 
            tblunits.unit as unit_name, 
            IF(tbl_element_items.type = "materials", ' . $material . ', ' . $product . ') as code_name,
            tbl_element_items.landscape_print_size as landscape_print_size,
            tbl_element_items.number_children_size as number_children_size,
            tbl_element_items.quantity as quantity,
            tbl_element_items.paper_exchange as paper_exchange,
            tbl_element_items.quantity_compensation as quantity_compensation,
            tbl_stages.name as stage_name
        ');
        $this->db->from('tbl_product_versions');
        $this->db->join('tbl_versions_element', 'tbl_versions_element.version_id = tbl_product_versions.id', 'inner');
        $this->db->join('tbl_element_items', 'tbl_element_items.element_id = tbl_versions_element.id', 'inner');
        $this->db->join('tblunits', 'tblunits.unitid = tbl_element_items.unit_id', 'left');
        $this->db->join('tbl_stages', 'tbl_stages.id = tbl_element_items.stage_id', 'left');
        $this->db->where_in('tbl_product_versions.product_id', $product_ids);

        return $this->db->get()->result_array();
    }

    /**
     * Lấy dữ liệu sản phẩm kèm stages & versions (chunk) cho export
     */
    public function getProductExportChunk($limit = 100, $offset = 0, $filters = [])
    {
        $products = $this->getProductsChunkForExport($limit, $offset, $filters);

        if (empty($products)) {
            return [];
        }

        $product_ids = array_column($products, 'id');

        $product_stages = $this->getProductStagesByProductIds($product_ids);
        
        $product_norms = $this->getProductNormsByProductIds($product_ids);

        $version_ids = [];

        foreach ($product_stages as $stage) {
            if (!empty($stage['id'])) {
                $version_ids[] = $stage['id'];
            }
        }

        $version_ids = array_values(array_unique($version_ids));

        $stage_versions = $this->getProductStagesVersionsByVersionIds($version_ids);

        $versions_by_stage_id = [];

        foreach ($stage_versions as $version) {
            $stage_id = $version['version_id'];

            if (!isset($versions_by_stage_id[$stage_id])) {
                $versions_by_stage_id[$stage_id] = [];
            }

            $versions_by_stage_id[$stage_id][] = $version;
        }

        $stages_by_product_id = [];

        foreach ($product_stages as $stage) {
            $product_id = $stage['product_id'];
            $stage_id = $stage['id'];

            if (!isset($stages_by_product_id[$product_id])) {
                $stages_by_product_id[$product_id] = [];
            }

            $stage['versions_list'] = $versions_by_stage_id[$stage_id] ?? [];

            $stages_by_product_id[$product_id][] = $stage;
        }

        $norms_by_product_id = [];
        foreach ($product_norms as $norm) {
            $product_id = $norm['product_id'];
            if (!isset($norms_by_product_id[$product_id])) {
                $norms_by_product_id[$product_id] = [];
            }
            $norms_by_product_id[$product_id][] = $norm;
        }

        foreach ($products as &$product) {
            $product_id = $product['id'];
            $product['stages'] = $stages_by_product_id[$product_id] ?? [];
            $product['norms'] = $norms_by_product_id[$product_id] ?? [];
        }

        unset($product);

        return $products;
    }
}
