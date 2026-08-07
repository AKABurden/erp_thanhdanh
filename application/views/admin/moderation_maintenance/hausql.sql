-- 24122023
ALTER TABLE `tbl_order_ref` ADD `request_calibration` INT(11) NULL DEFAULT '1' AFTER `request_export_products`;
them tbl_category_calibration
tbl_request_export_products
tbl_request_export_products_item

-- 26/12/2023
ALTER TABLE `tbl_suggest_maintenance_item` ADD `expected_date` DATETIME NULL AFTER `standard`, ADD `start_date` DATETIME NULL AFTER `expected_date`, ADD `end_date` DATETIME NULL AFTER `start_date`;