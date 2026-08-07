<?php defined('BASEPATH') or exit('No direct script access allowed');
$tags = get_tags();
$totalTags = count($tags);
?>
<div class="row">
    <div class="col-md-<?php if($totalTags > 0){ echo '12';} else {echo '12 text-center';}?>">
        <table class="table-bordered table">
            <thead>
                <tr>
                    <th>TOTAL USE</th>
                    <th>NAME</th>
                    <th>COLOR</th>
                    <th>BACKGROUND</th>
                    <th>OPTION</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($tags as $tag){ ?>
                <tr>
                    <td><?php echo total_rows(db_prefix().'taggables',array('tag_id'=>$tag['id'])); ?></td>
                    <td>
                        <input type="text" name="tags[<?php echo $tag['id']; ?>][name]" value="<?php echo $tag['name']; ?>" class="form-control">
                    </td>
                    <td>
                        <div class="input-group colorpicker-input colorpicker-element">
                            <input type="text"  name="tags[<?php echo $tag['id']; ?>][color]" value="<?php echo $tag['color']; ?>"  class="form-control">
                            <span class="input-group-addon"><i style="background-color: <?php echo $tag['color']; ?>;"></i></span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group colorpicker-input colorpicker-element">
                            <input type="text"  name="tags[<?php echo $tag['id']; ?>][background_color]" value="<?php echo $tag['background_color']; ?>"  class="form-control">
                            <span class="input-group-addon"><i style="background-color: <?php echo $tag['background_color']; ?>;"></i></span>
                        </div>
                    </td>
                    <td><a class="btn btn-danger _delete" href="<?php echo admin_url('settings/delete_tag/'.$tag['id']); ?>"><i class="fa fa-remove"></i></a></td>
                </tr>
            <?php } ?>
            <?php if($totalTags == 0){ ?>
                <tr>
                    <td colspan="5"><?php echo _l('no_tags_used'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
