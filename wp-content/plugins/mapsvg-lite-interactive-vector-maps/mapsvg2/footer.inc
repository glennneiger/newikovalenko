<script type="text/javascript">
    var madmin = 1;

    jQuery(document).ready(function(){
        madmin = jQuery().mapsvgadmin('init', {
            mapsvg_options : <?php echo  $js_mapsvg_options ? str_replace('\n','\\n',$js_mapsvg_options) : '{}'?>,
            map_id : "<?php echo $map_id?>",
            markerImages : <?php echo (isset($markerImages) ? json_encode($markerImages) : '[]')?>,
            map_title: "<?php echo esc_attr($title)?>",
            _wpnonce: '<?php echo wp_create_nonce('ajax_mapsvg_save-'.$map_id)?>'
        });
    })
</script>

</div>