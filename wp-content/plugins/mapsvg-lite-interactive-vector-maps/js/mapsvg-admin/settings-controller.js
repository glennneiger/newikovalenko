(function($, window){
    var MapSVGAdminSettingsController = function(container, admin, mapsvg){
        this.name = 'settings';
        MapSVGAdminController.call(this, container, admin, mapsvg);
    };
    window.MapSVGAdminSettingsController = MapSVGAdminSettingsController;
    MapSVG.extend(MapSVGAdminSettingsController, window.MapSVGAdminController);

    MapSVGAdminSettingsController.prototype.viewLoaded = function(){
        var _this = this;

        this.view.find('.mapsvg-select2').select2();

        _this.updateGaugeFields();
        _this.mapsvg.regionsDatabase.on('schemaChange',function(){
            _this.updateGaugeFields();
        });

        zoomLimit = _this.mapsvg.getData().options.zoom.limit;

        $('#mapsvg-controls-zoomlimit').ionRangeSlider({
            type: "double",
            grid: true,
            min: -100,
            max: 100,
            from_min: -100,
            from_max: 0,
            to_min: 1,
            to_max: 100,
            onFinish: function () {
                var limit = $('#mapsvg-controls-zoomlimit').val().split(';');
                _this.mapsvg.update({zoom: {limit:[limit[0], limit[1]]}});
            },
            from: zoomLimit[0],
            to: zoomLimit[1]
        });
    };

    MapSVGAdminSettingsController.prototype.setEventHandlers = function(){
        var _this = this;

        $('#mapsvg-controls-width').on('keyup', function(e){_this.setHeight(e); });
        $('#mapsvg-controls-height').on('keyup', function(e){_this.setWidth(e); });
        $('#mapsvg-controls-ratio').on('change', function(e){_this.keepRatioClickHandler(e); });
        $('#mapsvg-controls-set-viewbox').on('click', function(e){
            e.preventDefault();
            var v = _this.mapsvg.getViewBox();
            $('#mapsvg-controls-viewbox').val(v.join(' ')).trigger('change');
        });
        $('#mapsvg-controls-reset-viewbox').on('click', function(e){
            e.preventDefault();
            var v = _this.mapsvg.getData().svgDefault.viewBox;
            $('#mapsvg-controls-viewbox').val(v.join(' ')).trigger('change');
            _this.mapsvg.viewBoxReset();
        });

        $('#mapsvg-controls-zoom').on('change',':radio',function(){
            var on = parseBoolean($('#mapsvg-controls-zoom :radio:checked').val());
            on ? $('#mapsvg-controls-zoom-options').show() : $('#mapsvg-controls-zoom-options').hide();
            _this.admin.updateScroll();
        });
        $('#mapsvg-controls-scroll').on('change',':radio',function(){
            var on = parseBoolean($('#mapsvg-controls-scroll :radio:checked').val());
            on ? $('#mapsvg-controls-scroll-options').show() : $('#mapsvg-controls-scroll-options').hide();
            _this.admin.updateScroll();
        });
        this.view.find('#mapsvg-gauge-control').on('change',':radio',function(){
            var value = parseBoolean($('#mapsvg-gauge-control').find(':radio:checked').val());
            if(value)
                $('#table-regions').addClass('mapsvg-gauge-on');
            else
                $('#table-regions').removeClass('mapsvg-gauge-on');

        });
        this.view.on('click','#mapsvg-set-prefix-btn',function(e){
            e.preventDefault();
            _this.admin.save().done(function(){
                window.location.reload();
            });
        });
        this.mapsvg.on('sizeChange', function(){
            _this.admin.resizeDashboard();
        });
    };
    
    MapSVGAdminSettingsController.prototype.setWidth = function (){
        var _this = this;

        var w = $('#mapsvg-controls-width').val();
        var h = $('#mapsvg-controls-height').val();
        if($('#mapsvg-controls-ratio').is(':checked')){
            w = Math.round(h * _this.mapsvg.getData().svgDefault.width / _this.mapsvg.getData().svgDefault.height);
            $('#mapsvg-controls-width').val(w);
        }
        _this.mapsvg.viewBoxSetBySize(w,h);
        _this.mapsvg.updateSize();
        _this.admin.resizeDashboard();
    };
    MapSVGAdminSettingsController.prototype.setHeight = function (){
        var _this = this;

        var w = $('#mapsvg-controls-width').val();
        var h = $('#mapsvg-controls-height').val();
        if($('#mapsvg-controls-ratio').is(':checked')){
            h = Math.round(w * _this.mapsvg.getData().svgDefault.height / _this.mapsvg.getData().svgDefault.width);
            $('#mapsvg-controls-height').val(h);
        }
        _this.mapsvg.viewBoxSetBySize(w,h);
        _this.mapsvg.updateSize();
        _this.admin.resizeDashboard();
        _this.mapsvg.viewBoxSetBySize(w,h);
        _this.mapsvg.updateSize();
        _this.admin.resizeDashboard();
    };
    MapSVGAdminSettingsController.prototype.keepRatioClickHandler = function (){
        var _this = this;
        if($('#mapsvg-controls-ratio').is(':checked')){
            _this.setHeight();
        }
    };
    MapSVGAdminSettingsController.prototype.setWidthViewbox = function (){
        var _this = this;
        if($('#mapsvg-controls-ratio').is(':checked'))
            var k = _this.mapsvg.getData().svgDefault.width / _this.mapsvg.getData().svgDefault.height;
        else
            var k = ($('#map_width').val() / $('#map_height').val());

        var new_width = Math.round($('#viewbox_height').val() * k);

        if (new_width > _this.mapsvg.getData().svgDefault.viewBox[2]){
            new_width  = _this.mapsvg.getData().svgDefault.viewBox[2];
            var new_height = _this.mapsvg.getData().svgDefault.viewBox[3] * k;
            $('#viewbox_height').val(new_height);
        }

        $('#viewbox_width').val(new_width);
    };
    MapSVGAdminSettingsController.prototype.setViewBoxRatio = function (){
        var _this = this;
        var mRatio = $('#map_width').val() / $('#map_height').val();
        var vRatio = $('#viewbox_width').val() / $('#viewbox_height').val();

        if(mRatio != vRatio){
            if(mRatio >= vRatio){ // viewBox is too tall
                $('#viewbox_height').val( _this.mapsvg.getData().svgDefault.viewBox[2] * mRatio ) ;
            }else{ // viewBox is too wide
                $('#viewbox_width').val( _this.mapsvg.getData().svgDefault.viewBox[3] / mRatio ) ;
            }
        }
    };
    MapSVGAdminSettingsController.prototype.setHeightViewbox = function (){
        var _this = this;

        if($('#mapsvg-controls-ratio').is(':checked'))
            var k = _this.mapsvg.getData().svgDefault.height / _this.mapsvg.getData().svgDefault.width;
        else
            var k = ($('#map_height').val() / $('#map_width').val());

        var new_height = Math.round($('#viewbox_width').val() * k);

        if (new_height > _this.mapsvg.getData().svgDefault.viewBox[3]){
            new_height  = _this.mapsvg.getData().svgDefault.viewBox[3];
            var new_width = _this.mapsvg.getData().svgDefault.viewBox[2] * k;
            $('#viewbox_width').val(new_width);
        }

        $('#viewbox_height').val(new_height);
    };

    MapSVGAdminSettingsController.prototype.updateGaugeFields = function (){
        var _this = this;
        var fields = _this.mapsvg.regionsDatabase.getSchema();
        var choroplethField = _this.mapsvg.getOptions().regionChoroplethField;
        var select = this.view.find('#mapsvg-region-data-fields').empty();
        select.append('<option></option>');
        fields.forEach(function(f){
            select.append('<option '+(choroplethField == f.name ? 'selected' :'')+'>'+f.name+'</option>');
        });
        select.trigger('change');

    };


})(jQuery, window);