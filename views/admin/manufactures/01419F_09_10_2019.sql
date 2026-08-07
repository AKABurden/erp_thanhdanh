ALTER TABLE `tbl_order_ref` ADD `warehousing` INT NULL DEFAULT '1' AFTER `purchase_products`;
INSERT INTO `tbloptions` (`id`, `name`, `value`, `autoload`) VALUES (NULL, 'prefix_warehousing', 'WS', '1');
ALTER TABLE `tbl_productions_orders_details` ADD `quantity_warehoused` DOUBLE NULL DEFAULT '0' AFTER `quantity_finished`;
