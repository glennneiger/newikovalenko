<?php if(isset($mapsvg_error)) {?>
    <div class="alert alert-danger">
        <?php echo esc_html($mapsvg_error); ?>
    </div>
<?php }?>
<?php if(isset($mapsvg_notice)) {?>
    <div class="alert alert-info">
        <?php echo $mapsvg_notice; ?>
    </div>
<?php }?>


<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">New map</h3>
            </div>
            <div class="panel-body">
                <form method="POST">
                    <div class="form-group">
                        <a href="" id="hidden-link" style="display: none;"></a>
                        <select class="form-control select-map-list span4">
                            <option value="">...</option>
                            <?php if(isset($maps)) foreach($maps as $m){?>
                                <option data-link="?page=mapsvg-config&map_id=new&map=<?php echo esc_attr($m['url'])?>"><?php echo esc_html($m['path'])?></a></option>
                            <?php }?>
                        </select>
                        <p class="help-block">
                            Choose SVG file to create a new map.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Upload SVG file</h3>
            </div>
            <div class="panel-body">
                <form enctype="multipart/form-data" method="POST">
                    <div class="form-group">
                        <span class="btn btn-primary btn-file" data-loading-text="Uploading...">
                            Browse... <input type="file" name="svg_file" id="svg_file_uploader" />
                        </span>
                        <p class="help-block">
                            Upload new or update (overwrite) already uploaded custom SVG file.
                        </p>
                        <input type="hidden" name="upload_svg" />
                        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('upload_map')?>"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php if(isset($generated_maps) && count($generated_maps)>0){?>
    <!-- MENU -->
    <table class="table table-striped" id="mapsvg-table-maps">
        <thead>
        <th style="width: 30%">Map title</th>
        <th style="width: 20%">Shortcode</th>
        <th>Actions</th>
        </thead>
        <tbody id="mapsvg-table">
        <?php foreach($generated_maps as $m){?>
            <?php setup_postdata($m);?>
            <tr data-id="<?php echo $m->ID?>" data-title="<?php echo $m->post_title?>">
                <td class="mapsvg-map-title"><a href="?page=mapsvg-config&map_id=<?php echo $m->ID?>"><?php echo $m->post_title?></a></td>
                <td class="mapsvg-shortcode">[mapsvg id="<?php echo $m->ID?>"]</td>
                <td class="mapsvg-action-buttons">
                    <!--                        <a href="?page=mapsvg-config&map_id=--><?php //echo $m->ID?><!--" class="btn btn-small btn-info mapsvg-button-edit">Edit</a>-->
                    <a href="#" class="btn btn-sm btn-default mapsvg-copy" data-nonce="<?php echo wp_create_nonce('ajax_mapsvg_copy-'.$m->ID)?>">Duplicate</a>
                    <a href="#" class="btn btn-sm btn-default mapsvg-delete" data-nonce="<?php echo wp_create_nonce('ajax_mapsvg_delete-'.$m->ID)?>">Delete</a>
                    <?php if(isset($m->can_update_to)){?>
                        <a href="#" class="btn btn-sm btn-default mapsvg-update" data-update-to="<?php echo $m->can_update_to?>">Update to <?php echo $m->can_update_to?></a>&nbsp;&nbsp;
                    <?php }?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
<?php }?>


