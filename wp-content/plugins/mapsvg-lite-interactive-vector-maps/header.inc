<?php if($mapsvg_page=='edit' || $mapsvg_page=='update'){?>
    <script type="text/javascript">
        jQuery('body').addClass('mapsvg-edit-screen');
    </script>
<?php }?>
<iframe src="about:blank" class="stretch" id="stretchIframe" scrolling="no" style="width: 100%;height:100%;z-index: -1;"></iframe>
<div class="mapsvg-dashboard <?php if($mapsvg_page=='edit' || $mapsvg_page=='update'){?>fixed<?php }?>" id="mapsvg-admin">
    <?php if($mapsvg_page=='index'){?>
        <h1 style="margin-bottom: 20px;">MapSVG <sup><?php echo MAPSVG_VERSION;?></sup></h1>
    <?php }else{?>
        <div id="mapsvg-nav-header" <?php if($can_update_to){?> class="mapsvg-can-update"<?php }?>>
            <h2>
                <a href="<?php echo admin_url('admin.php?page=mapsvg-config')?>">MapSVG Lite</a> <sup><?php echo MAPSVG_VERSION;?></sup>  &rarr; <span id="map-page-title"> <?php echo $title;?></span> <small id="mapsvg-shortcode"><?php if(isset($map_id) && $map_id!='new'){?>[mapsvg id="<?php echo $map_id?>"]<?php }?></small>
            </h2>

            <div class="pull-right" id="mapsvg-top-buttons">
                <button id="mapsvg-save" class="btn btn-sm btn-primary" data-loading-text="Saving..." >Save &nbsp;<span class="mapsvg-hotkey-mac">⌘S</span><span class="mapsvg-hotkey-others">Ctrl+S</span></button>
                <div class="btn-group" data-toggle="buttons" id="mapsvg-view-buttons">
                    <label class="btn btn-sm btn-default active" id="mapsvg-panels-view-left">
                        <i class="fa fa-map-o"></i>
                        <input type="checkbox" autocomplete="off" checked name="left">
                    </label>
                    <label class="btn btn-sm btn-default active" id="mapsvg-panels-view-right">
                        <i class="fa fa-sliders"></i>
                        <input type="checkbox" autocomplete="off" checked name="right">
                    </label>
                </div>
                <div >
                    <a style="line-height: 30px;display: inline-block;padding-right: 10px;" href="http://mapsvg.com/?utm_source=mapsvg_lite&utm_medium=admin-link&utm-campaign=MapSVG-Lite" target="_blank">Get Premium version</a>
                </div>


            </div>
        </div>
    <?php }?>

