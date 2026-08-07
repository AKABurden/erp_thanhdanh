<?php if (!empty($products)) { ?>
    <?php foreach ($products as $product_index => $product) { ?>

        <?php
        $stt = $offset + $product_index + 1;

        $product_code = !empty($product['code']) ? $product['code'] : '';
        $product_name = !empty($product['name']) ? $product['name'] : '';
        $category_name = !empty($product['category_name']) ? $product['category_name'] : '';
        $species_name = !empty($product['species_name']) ? $product['species_name'] : '';

        $has_versions = false;

        if (!empty($product['stages'])) {
            foreach ($product['stages'] as $stage) {
                if (!empty($stage['versions'])) {
                    $has_versions = true;
                    break;
                }
            }
        }
        ?>

        <?php if (!$has_versions) { ?>
            <tr>
                <td><?= $stt ?></td>
                <td><?= html_escape($product_code) ?></td>
                <td><?= html_escape($product_name) ?></td>
                <td><?= html_escape($category_name) ?></td>
                <td><?= html_escape($species_name) ?></td>
                <td colspan="3">Chưa có công đoạn</td>
            </tr>
        <?php } else { ?>

            <?php
            $is_first_product_row = true;
            ?>

            <?php foreach ($product['stages'] as $stage) { ?>
                <?php if (!empty($stage['versions'])) { ?>
                    <?php foreach ($stage['versions'] as $version) { ?>
                        <tr>
                            <?php if ($is_first_product_row) { ?>
                                <td><?= $stt ?></td>
                                <td><?= html_escape($product_code) ?></td>
                                <td><?= html_escape($product_name) ?></td>
                                <td><?= html_escape($category_name) ?></td>
                                <td><?= html_escape($species_name) ?></td>
                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php } ?>

                            <td>
                                <?= html_escape($version['stage_code'] ?? '') ?>
                                -
                                <?= html_escape($version['stage_name'] ?? '') ?>
                            </td>

                            <td><?= html_escape($version['machine_code'] ?? '') ?></td>
                            <td><?= html_escape($version['number'] ?? '') ?></td>
                        </tr>

                        <?php $is_first_product_row = false; ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

        <?php } ?>

    <?php } ?>
<?php } ?>