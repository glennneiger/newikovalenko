/**
 * MapSvg Builder javaScript
 * Version: 2.0.0
 * Author: Roman S. Stepanov
 * http://codecanyon.net/user/RomanCode/portfolio
 */
(function( $ ) {

    $.fn.inputToObject = function(formattedValue) {

        var obj = {};

        function add(obj, name, value){
            //if(!addEmpty && !value)
            //    return false;
            if(name.length == 1) {
                obj[name[0]] = value;
            }else{
                if(obj[name[0]] == null)
                    obj[name[0]] = {};
                add(obj[name[0]], name.slice(1), value);
            }
        }

        if($(this).attr('name') && !($(this).attr('type')=='radio' && !$(this).prop('checked'))){
            add(obj, $(this).attr('name').replace(/]/g, '').split('['), formattedValue);
        }

        return obj;
    };

    function parseBoolean (string) {
        switch (String(string).toLowerCase()) {
            case "on":
            case "true":
            case "1":
            case "yes":
            case "y":
                return true;
            case "off":
            case "false":
            case "0":
            case "no":
            case "n":
                return false;
            default:
                return undefined;
        }
    }
    window.parseBoolean = parseBoolean;
    MapSVG.isMac = function(){
        return navigator.platform.toUpperCase().indexOf('MAC')>=0;
    };


    function isValidURL(url) {
        return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    }


    var WP = true; // required for proper positioning of control panel in WordPress
    var msvg;
    var editingMark;
    var _data = {}, _this = {};
    _data.optionsDelta = {};
    _data.optionsMode = {
        preview : {
            responsive: true,
            disableLinks: true
        },
        editRegions : {
            responsive: true,
            disableLinks: true,
            zoom: {on: true, limit:[-1000,1000]},
            scroll: {on: true},
            onClick: null,
            mouseOver: null,
            mouseOut: null,
            tooltips: {
                on: true
            },
            templates: {
                tooltip: '{{#if isRegion}}<b>{{id}}</b>{{#if title}}: {{title}} {{/if}} {{/if}}'
            },
            popovers: {
                on : false
            },
            actions: {
                region: {
                    click: {
                        showDetails: false,
                        filterDirectory: false,
                        loadObjects: false,
                        showPopover: false,
                        goToLink: false
                    }
                },
                marker: {
                    click: {
                        showDetails: false,
                        showPopover: false,
                        goToLink: false
                    }
                }
            }
        },
        editData : {
            responsive: true,
            disableLinks: true,
            zoom: {on: true, limit:[-1000,1000]},
            scroll: {on: true},
            actions: {
                region: {
                    click: {
                        showDetails: false,
                        filterDirectory: true,
                        loadObjects: false,
                        showPopover: false,
                        goToLink: false
                    }
                },
                marker: {
                    click: {
                        showDetails: false,
                        showPopover: false,
                        goToLink: false
                    }
                }
            },
            onClick: function(){
                var region = this;
                $('#myTab a[href="#tab_database"]').tab('show');
                var filter = {};
                if(region.mapsvg_type == 'region'){
                    filter.region_id = region.id;
                }else if(region.mapsvg_type == 'marker'){
                    filter.id = region.databaseObject.id;

                }
                _data.controllers.database.setFilters(filter);
            },
            mouseOver: null,
            mouseOut: null,
            tooltips: {
                on: true
            },
            templates: {
                tooltip: '<b>{{id}}</b> Data objects: {{data.length}}'
            },
            popovers: {
                on : false
            }
        },
        editMarkers : {
            responsive: true,
            disableLinks: true,
            zoom: {on: true, limit:[-1000,1000]},
            scroll: {on: true},
            onClick: null,
            mouseOver: null,
            mouseOut: null,
            tooltips: {
                on: true
            },
            templates: {
                template: '{{#if isMarker}}<b>{{id}}</b>{{/if}}'
            },
            popovers: {
                on: false
            },
            actions: {
                region: {
                    click: {
                        showDetails: false,
                        filterDirectory: true,
                        loadObjects: false,
                        showPopover: false,
                        goToLink: false
                    }
                },
                marker: {
                    click: {
                        showDetails: false,
                        showPopover: false,
                        goToLink: false
                    }
                }
            }
        }
    };
    _data.mode = "preview";


    methods = {

        getData : function(){
          return _data;
        },
        getMapId: function(){
          return _data.options.map_id;
        },
        selectCheckbox : function (){
            c = $(this).attr('checked') ? true : false;
            $('.region_select').removeAttr('checked');
            if(c)
                $(this).attr('checked','true');
        },
        disableAll : function (){
            c = $(this).attr('checked') ? true : false;
            if(c)
                $('.region_disable').attr('checked','true');
            else
                $('.region_disable').removeAttr('checked');
        },
        save : function (skipMessage){
            var form = $(this);
            $('#mapsvg-save')._button('loading');
            var options = msvg.getOptions(false, null, _data.optionsDelta);
            var data = {mapsvg_data: MapSVG.convertToText(options), title: options.title, map_id: _data.options.map_id, region_prefix: options.regionPrefix, source: options.source };
            if(_this.mapsvgCssChanged)
                data.css = _this.mapsvgCss;

            return $.post(ajaxurl, {action: 'mapsvg_save',_wpnonce: _data.options._wpnonce, data: data}, function(id){
                if($.isNumeric(id)){
                    var t = _data.options._wpnonce.split('-');
                    t[1] = id;
                    _data.options._wpnonce = t.join('-');
                    var msg = 'Settings saved';
                    $('#map-page-title').html(options.title);
                    if(_data.options.map_id=='new'){
                        $('#mapsvg-shortcode').html('[mapsvg id="'+id+'"]');
                        msg += '. Shortcode: [mapsvg id="'+id+'"]';
                        _data.options.map_id = id;
                        msvg.id = id;
                    }
                }else{
                    msg = "Error!"
                }
                !skipMessage && $.growl.notice({title: "OK", message: msg});
            }).always(function(){
                $('#mapsvg-save')._button('reset');
            }).fail(function(){
                $.growl.error({message: 'Server error: settings were not saved'});
            });
        },

        mapDelete : function(e){
            e.preventDefault();

            var nonce     = $(this).data('nonce');
            var table_row = $(this).closest('tr');
            var id = table_row.attr('data-id');
            table_row.fadeOut();
            $.post(ajaxurl, {action: 'mapsvg_delete', _wpnonce: nonce, id: id}, function(){
            });
        },
        mapCopy : function(e){

            e.preventDefault();

            var nonce     = $(this).data('nonce');
            var table_row = $(this).closest('tr');
            var id        = table_row.attr('data-id');
            var map_title = table_row.attr('data-title');

            if(!(new_name = prompt('Enter new map title', map_title+' - copy')))
                return false;

            $.post(ajaxurl, {'action': 'mapsvg_copy', _wpnonce: nonce, 'id': id, 'new_name': new_name}, function(new_id){
                var new_row = table_row.clone();

                var map_link = '?page=mapsvg-config&map_id='+new_id;
                new_row.attr('data-id', new_id).attr('data-title', new_name);
                new_row.find('.mapsvg-map-title a').attr('href', map_link).html(new_name);
                new_row.find('.mapsvg-action-buttons a.mapsvg-button-edit').attr('href', map_link);
                new_row.find('.mapsvg-shortcode').html('[mapsvg id="'+new_id+'"]');
                new_row.prependTo(table_row.closest('tbody'));
            });
        },
        mapUpdate : function(e){
            e.preventDefault();
            var btn = $(this);
            var table_row = $(this).closest('tr');
            var map_id = table_row.length ? table_row.attr('data-id') : msvg.id;

            var update_to = $(this).data('update-to');
            jQuery.get(ajaxurl, {action: "mapsvg_get", id: map_id}, function (data) {
                var disabledRegions = [];
                eval('var options = ' + data);
                if(options.regions){
                    for (var id in options.regions){
                        if(options.regions[id].disabled)
                            disabledRegions.push(id);
                    }
                }
                $.post(ajaxurl, {action: 'mapsvg_update',
                                 id: map_id,
                                 update_to: update_to,
                                 disabledRegions: disabledRegions,
                                 disabledColor: options.colors && options.colors.disabled!==undefined? options.colors.disabled : ''
                }, function(){
                    btn.fadeOut();
                    if(!table_row.length)
                        window.location.reload();
                }).fail(function(){
                    $.growl.error({title: "Server Error", message: 'Can\'t update the map'});
                });
            });

        },
        markerEditHandler : function(updateGeoCoords){
            editingMark = this.getOptions();
            // var markerForm = $('#table-markers').find('#mapsvg-marker-'+editingMark.id);
            // $('#myTab a[href="#tab_markers"]').tab('show');
            if(hbData.isGeo && updateGeoCoords){
                // if(markerForm.length)
                //     markerForm.find('.mapsvg-marker-geocoords a').html(this.geoCoords.join(','));
                if(editingMark.attached && editingMark.dataId){
                    var obj = msvg.database.getLoadedObject(editingMark.dataId);
                    msvg.database.update(obj);
                }
                // $('.nano').nanoScroller({scrollTo: markerForm});
            }else{
                // if(!markerForm.length){
                //     editingMark.isSafari = hbData.isSafari;
                    // _data.controllers.markers.addMarker(editingMark);
                    // _this.updateScroll();
                    // $('.nano').nanoScroller({scroll: 'top'});
                // }else{
                //     $('.nano').nanoScroller({scrollTo: markerForm});
                // }
            }
        },
        regionEditHandler : function(){
            var region = this;
            var row = $('#mapsvg-region-'+region.id_no_spaces);
            $('#myTab a[href="#tab_regions"]').tab('show');
            _data.controllers.regions.controllers.list.editRegion(region, true);
            // $('.nano').nanoScroller({scrollTo: regionForm});
            // regionForm.trigger('click');
        },
        dataEditHandler : function(){
            var region = this;
            $('#myTab a[href="#tab_database"]').tab('show');
            var filter = {};
            if(region instanceof Region){
                filter.region_id = region.id;
            }else if(region instanceof Marker){
                filter.id = region.object.id;
            }
            _data.controllers.database.controllers.list.setFilters(filter);
        },
        resizeDashboard : function(){
           var w = _data.iframeWindow.width();
           var top = $('#wpadminbar').height();
           var left = $(window).width() - _data.iframeWindow.width();
           var h = $(window).height()-top;
            $('#mapsvg-admin').css({width: w, height: h, left: left, top : top});
            _this.resizeSVGCanvas();
           // _this.updateScroll();
        },
        resizeSVGCanvas : function(){
            var l = $('#mapsvg-container');
            var v = msvg && msvg.getData().viewBox;
            if(msvg && v[3]>v[2]){
                var ratio = v[2]/v[3];
                var newWidth = ratio * l.height();
                var per = (newWidth*100)/l.width();
                $('#mapsvg').css({'width': per+'%'});
            }else{
                $('#mapsvg').css({width: 'auto'});
            }
        },
        updateScroll : function(){
        },
        getCoordsFromAdress : function(address, callback){
            $.get('//maps.googleapis.com/maps/api/geocode/json?address='+address+'&sensor=false',function(data){
                callback(data);
            });
        },
        // Returns formatted options or MapSVGError object
        mapSvgUpdate : function(e) {
            var jQueryElem = $(this);
            if (jQueryElem.is(':radio')){
                jQueryElem = jQueryElem.closest('.form-group').find(':radio:checked');
            }
            var delay = parseInt($(this).data('delay'));
            jQueryElem.closest('.form-group').removeClass('has-error');
            if (delay){
                var t = $(this).data('timer');
                t && clearTimeout(t);
                $(this).data('timer',setTimeout(function() {
                    _this.mapSvgUpdateFinal(jQueryElem);
                }, delay));
            }else{
                _this.mapSvgUpdateFinal(jQueryElem);
            }
        },
        mapSvgUpdateFinal : function(jQueryElem){


            // Validate input field and format if necessary
            var data = _this.validateInput(jQueryElem);

            if (data instanceof TypeError){
            // If error, highlight input field
                jQueryElem.closest('.form-group').addClass('has-error');
                // TODO highlight line number in CodeMirror
            }else{
            // If no errors, check if attribute is read-only in current map mode
                for(var _key in data) {
                    var key = _key;
                }
                if (_data.optionsMode[_data.mode].hasOwnProperty(key)){
                // Attribute is read-only, save to dirty
                    $.extend(true, _data.optionsDelta, data);

                }else{
                // Attribute can be written into MapSVG instance
                    msvg.update(data);
                    if (data.disableAll !== undefined){
                        $('#table-regions .mapsvg-region-row').each(function(i,region){
                           var id = $(region).attr('data-region-id');
                            var disabled = msvg.getRegion(id).disabled;
                            var checkbox = $(this).find('.mapsvg-region-disabled').prop('checked',disabled);
                            var label = checkbox.closest('label');
                            disabled && label.addClass('active') || label.removeClass('active');
                        });
                    }

                }
            }
        },
        validateInput : function(jQueryElem){
            var val;
            if(jQueryElem.is(':checkbox')){
                if(jQueryElem.is(':checked'))
                    val = jQueryElem.attr('value') && jQueryElem.attr('value')!='on' ? jQueryElem.attr('value') : true;
                else
                    val = false;
            }else{
                val = jQueryElem.val();
            }
            var validate = jQueryElem.data('validate');
            if(validate && val!=""){
                if(validate == 'function'){
                    val = val!="" ? msvg.functionFromString(val) : null;
                    if(val instanceof TypeError || val instanceof SyntaxError)
                        return val;
                    // if(val && val.error){
                    //     return new TypeError("MapSVG error: error in function", "", val.error.line);
                    // }
                }else if(validate == 'link'){
                    if (!isValidURL(val))
                        return new TypeError('MapSVG error: wrong URL format. URL must start with "http://"');
                }else if(validate == 'number'){
                    if (!$.isNumeric(val))
                        return new TypeError('MapSVG error: value must be a number');
                }else if(validate == 'object') {
                    if(data.substr(0,1)=='[' || data.substr(0,1)=='{'){
                        try{
                            var tmp;
                            eval('tmp = '+val);
                            var val = tmp;
                        }catch(err){
                            return new TypeError("MapSVG error: wrong object format for "+jQueryElem.attr('name'));
                        }
                    }
                }
            }
            return jQueryElem.inputToObject(val);
        },
        setPreviousMode : function(){
            if(_data.previousMode)
                _this.setMode(_data.previousMode);
        },
        setMode : function(mode, dontSwitchTab){
            if(_data.mode == mode)
                return;

            _data.previousMode = _data.mode;
            _data.mode = mode;
            // save settings from previous "dirty" state
            msvg.update(_data.optionsDelta);
            // get current all saved settings
            var currentOptions = msvg.getOptions();
            _data.optionsDelta = {};
            // remember all settings which are going to be changed in mode
            // into options delta
            $.each(_data.optionsMode[_data.mode],function(key, options){
                _data.optionsDelta[key] = currentOptions[key] !== undefined ? currentOptions[key] : null;
            });

            msvg.update(_data.optionsMode[mode]);
            var _mode = mode;
            $('#mapsvg-map-mode').find('label').removeClass('active').find('input').prop('checked',false);
            var btn = $('#mapsvg-map-mode').find('label[data-mode="'+_mode+'"]');
            btn.addClass('active');

            $('body').off('click.switchTab');

            if (mode=="editRegions") {
                msvg.setMarkersEditMode(false);
                msvg.setRegionsEditMode(true);
                msvg.setDataEditMode(false);
                msvg.getData().$map.addClass('mapsvg-edit-regions');
                // !dontSwitchTab && $('#myTab a[href="#tab_regions"]').tab('show');
                $('body').on('click.switchTab','.mapsvg-region', function(){
                    $('#myTab a[href="#tab_regions"]').tab('show');
                });
            } else if(mode=="editMarkers") {
                msvg.setMarkersEditMode(true);
                msvg.setRegionsEditMode(false);
                msvg.setDataEditMode(false);
                msvg.getData().$map.removeClass('mapsvg-edit-regions');
                // !dontSwitchTab && $('#myTab a[href="#tab_markers"]').tab('show');
            } else if(mode=="editData") {
                msvg.setMarkersEditMode(false);
                msvg.setRegionsEditMode(false);
                msvg.setDataEditMode(true);
                msvg.getData().$map.removeClass('mapsvg-edit-regions');
                $('body').on('click.switchTab','.mapsvg-region',function(){
                    $('#myTab a[href="#tab_database"]').tab('show');
                });
                $('body').on('click.switchTab','.mapsvg-marker',function(){
                    $('#myTab a[href="#tab_database"]').tab('show');
                    var marker = msvg.getMarker($(this).prop('id'));
                    _data.controllers.database.controllers.list.editDataRow( $('#mapsvg-data-'+marker.object.id), true );
                });
            } else {
                msvg.setMarkersEditMode(false);
                msvg.setRegionsEditMode(false);
                msvg.setDataEditMode(false);
                msvg.viewBoxReset(true);
                _this.resizeSVGCanvas();
                msvg.getData().$map.removeClass('mapsvg-edit-regions');
            }
            $('#mapsvg-admin').attr('data-mode', mode);
        },
        enableMarkersMode : function(on) {
            var mode = $('#mapsvg-map-mode').find('[data-mode="editMarkers"]');
            if(on){
                mode.removeClass('disabled').find('input');
            }else{
                // if(_data.mode == 'editMarkers')
                //     _this.setMode('preview');
                mode.addClass('disabled').find('input');
            }
        },
        addHandlebarsMethods : function(){

        },
        getPostTypes : function(){
            return _data.options.post_types;
        },
        togglePanel : function(panelName, visibility){
            if(!visibility)
                $('#mapsvg-panels').addClass('hide-'+panelName);
            else
                $('#mapsvg-panels').removeClass('hide-'+panelName);

            var btn = $('#mapsvg-panels-view-'+panelName);
            if(btn.hasClass('active') != visibility){
                if(visibility)
                    btn.addClass('active');
                else
                    btn.removeClass('active');
                btn.prop('checked',visibility);
            }

                setTimeout(function(){
                    _this.resizeDashboard();
                }, 700);

        },
        rememberPanelsState : function(){
            _data.panelsState = {};
            _data.panelsState.left = !$('#mapsvg-panels').hasClass('hide-left');
            _data.panelsState.right = !$('#mapsvg-panels').hasClass('hide-right');
        },
        restorePanelsState : function(){
            for(var panelName in _data.panelsState){
                _this.togglePanel(panelName, _data.panelsState[panelName]);
            }
            _data.panelsState = {};
        },
        setEventHandlers : function(){

            $('body').on('mousewheel','.jspContainer',
                    function(e)
                    {
                        e.preventDefault();
                    }
                );

            $(window).on('keydown.save.mapsvg', function(e) {
                if((e.metaKey||e.ctrlKey) && e.keyCode == 83){
                    e.preventDefault();
                    _this.save();
                }
            });

            $(window).on('keydown.form.mapsvg', function(e) {
                if(MapSVG.formBuilder){
                    if((e.metaKey||e.ctrlKey) && e.keyCode == 13)
                        MapSVG.formBuilder.save();
                    else if(e.keyCode == 27)
                        MapSVG.formBuilder.close();
                }
            });

            $('#mapsvg-view-buttons').on('change','[type="checkbox"]',function(){
                var visible = $(this).prop('checked');
                var name = $(this).attr('name');
                _this.togglePanel(name, visible);
            });

            $('body').on('click','.mapsvg-template-link',function(){
                var template = $(this).data('template');

                if(!_data.controllers['templates']){
                    $('#myTab a[href="#tab_templates"]').tab('show');
                    setTimeout(function(){
                        $('#tab_templates').find('select').val(template).trigger('change');
                    },500);
                }else{
                    $('#myTab a[href="#tab_templates"]').tab('show');
                    $('#tab_templates').find('select').val(template).trigger('change');
                }
            });

            $('body').on('click','a.mapsvg-toggle-visibility',function(){
                var selector = $(this).data('toggle-visibility');
                $(selector).toggle();
                if($(selector).is(':visible'))
                    $(this).text('Hide');
                else
                    $(this).text('Read more');
            });

            _data.view
                .on('change paste','[data-live="change"]', _this.mapSvgUpdate)
                .on('keyup paste','[data-live="keyup"]', _this.mapSvgUpdate)
                .on('select','[data-live="select"]', _this.mapSvgUpdate)
                .on('click','[data-live="click"]', _this.mapSvgUpdate)
                .on('keypress','form.safarifix input',function(e){
                    if (e.which == 13 || event.keyCode == 13)
                        e.preventDefault();
                })
                .on('click', 'input.input-switch', function(){
                    if($(this).is(':checked')){
                        $(this).closest('.controls').find('.radio').next().attr('disabled','disabled');
                        $(this).parent().next().removeAttr('disabled');
                    }
                }).on('change', '#mapsvg-map-mode :radio',function(){
                    var mode = $('#mapsvg-map-mode :radio:checked').val();
                    _this.setMode(mode);
                }).on('click','button',function(e){
                    e.preventDefault();
                }).on('change','.mapsvg-toggle-visibility',function(){
                    var parent = $(this).closest('.btn-group');
                    var on = $(this).is(':checkbox') ? parseBoolean($(this).prop('checked')) : true;
                    var selector = $(this).data('toggle-visibility');
                    var selectorReverse = $(this).data('toggle-visibility-reverse');
                    if(selector)
                        on ? $(selector).show() : $(selector).hide();
                    if(selectorReverse)
                        on ? $(selectorReverse).hide() : $(selectorReverse).show();
                }).on('click','button.mapsvg-toggle-visibility',function(e){
                    e.preventDefault();
                    var selector = $(this).data('toggle-visibility');
                    $(selector).toggle();
                })
            //     .on('click','#myTab a', function (e) {
            //     e.preventDefault();
            //     $(this).tab('show');
            //     $('#mapsvg-tabs').removeClass('no-padding');
            //     $('.mapsvg-panel-left').removeClass('closed');
            //     $('.mapsvg-panel-right').removeClass('fullscreen');
            // })
                .on('click', '#mapsvg-save', function(){_this.save()})
                .on('click','.disabled', function(e){
                    e.preventDefault();
                    return false;
                }).on('click','.btn-group-checkbox a',function(){
                var btn = $(this);
                var type = btn.attr('data-toggle');
                setTimeout(function(){
                    var on = btn.hasClass('active');
                    if(on)
                        btn.closest('.btn-group-checkbox').find('input.input-toggle-'+type).val('true');
                    else
                        btn.closest('.btn-group-checkbox').find('input.input-toggle-'+type).val('');
                },200);
            });

            $('#myTab a').on('click',function(e){
                e.preventDefault();
                $(this).tab('show');
            });

            $('#myTab a').on('shown.bs.tab', function (e){
                $('#myTab .menu-name').html( $(this).text() );
                var h = $(this).attr('href');
                _this.resizeDashboard();
                var controllerContainer = $(h);
                var id = h.replace('#','');
                var controller = controllerContainer.attr('data-controller');

                if(!_data.controllers[controller]){
                    var capitalized = controller.charAt(0).toUpperCase() + controller.slice(1);
                    _data.controllers[controller] =  new window['MapSVGAdmin'+capitalized+'Controller'](id, _this, msvg);
                }
                _data.controllers[controller].viewDidAppear();
                var previousTabId = $(e.relatedTarget).attr('href');
                if(previousTabId){
                    var prevControllerName = $(previousTabId).attr('data-controller');
                    _data.controllers[prevControllerName].viewDidDisappear();
                }

            });


        },
        codeMirrorToTextareaValue : function(codemirror){
            var handler =  codemirror.getValue();
            var textarea = $(codemirror.getTextArea());
            textarea.val(handler).trigger('change');
        },
        init : function(options){

            if(MapSVG.isMac()){
                $('body').addClass('mapsvg-os-mac');
            }else{
                $('body').addClass('mapsvg-os-other');
            }

            _data.options = options;
            _data.controllers = {};
            _data.view = $('#mapsvg-admin');

            var onEditMapScreen = _data.options.mapsvg_options.source ? true : false;

            $(document).ready(function(){

                // Position control panel in WordPress
                if(WP && onEditMapScreen){
                    // Append an iFrame to the page.
                    _data.iframe = $('#stretchIframe');

                    // Called once the Iframe's content is loaded.
                    // The Iframe's child page BODY element.
                    // Bind the resize event. When the iframe's size changes, update its height as
                    // well as the corresponding info div.
                    _data.iframeWindow = $(_data.iframe[0].contentWindow);//iframe.contents().find('body');
                    _data.iframeWindow.on('resize',function(){
                        var elem = $(this);
                        _this.resizeDashboard();
                    });
                    $(window).on('resize',function(){
                        _this.resizeDashboard();
                    });
                    _this.resizeDashboard();
                }

                _data.view.tooltip({
                    selector: '.toggle-tooltip'
                });

                $('body')
                    .on('click', '.mapsvg-update', methods.mapUpdate);


                if(onEditMapScreen){

                    _this.addHandlebarsMethods();

                    var originalAferLoad = _data.options.mapsvg_options.afterLoad;

                    _data.options.mapsvg_options.backend = true;

                    // Load control panel after the map loads
                    _data.options.mapsvg_options.afterLoad = function(){

                        msvg.database.setSchema(_data.options.mapsvg_schema_database);
                        msvg.regionsDatabase.setSchema(_data.options.mapsvg_schema_regions);

                        msvg.setMarkerEditHandler(methods.markerEditHandler);
                        msvg.setRegionEditHandler(methods.regionEditHandler);
                        msvg.setAfterLoad(originalAferLoad);
                        // var source = $("#mapsvg-control-panel").html();

                        hbData = msvg.getOptions(true);
                        _this.hbData = hbData;
                        if(msvg.getData().presentAutoID){
                            $('#mapsvg-auto-id-warning').show();
                        }

                        _this.setMode('preview');

                        hbData.isGeo = msvg.getData().mapIsGeo;
                        if(hbData.isGeo){
                            $('#mapsvg-admin').addClass('mapsvg-is-geo');
                        }
                        window.markerImages = _data.options.markerImages || [];
                        window.defaultMarkerImage = (_data.options.markerImages && _data.options.markerImages.length)
                            ? _data.options.markerImages[0].url : '';

                        // Safary is laggy when there are many input fields in a form. We'll need
                        // to wrap each input with <form /> tag
                        hbData.isSafari = navigator.vendor && navigator.vendor.indexOf('Apple') > -1 &&
                            navigator.userAgent && !navigator.userAgent.match('CriOS');
                        hbData.title = _data.options.map_title;
                        if(!hbData.title){
                            hbData.title = hbData.svgFilename.split('.');
                            hbData.title.pop();
                            hbData.title = hbData.title.join('.');
                            hbData.title = hbData.title.charAt(0).toUpperCase() + hbData.title.substr(1);
                        }

                        msvg.update({title: hbData.title});

                        if(_data.options.mapsvg_options.extension &&  $().mapSvg.extensions && $().mapSvg.extensions[_data.options.mapsvg_options.extension]){
                            var ext = $().mapSvg.extensions[_data.options.mapsvg_options.extension];
                            ext && ext.backend(msvg, _this);
                        }

                        // Preload
                        _data.controllers.settings = new MapSVGAdminSettingsController('tab_settings', _this, msvg);
                        _data.controllers.database = new MapSVGAdminDatabaseController('tab_database', _this, msvg);
                        _data.controllers.regions = new MapSVGAdminRegionsController('tab_regions', _this, msvg);

                        _data.view.find('.mapsvg-select2').select2({
                            minimumResultsForSearch: 20
                        });
                        $(document).on('focus', '.select2-selection--single', function(e) {
                            select2_open = $(this).parent().parent().prev('select');
                            select2_open.select2('open');
                        });

                        // Wrap input into form for Safari, otherwise form will be very slow
                        if (hbData.isSafari){
                            _data.view.find('input[type="text"]').closest('.form-group').wrap('<form />');
                        }

                        _this.setEventHandlers();
                        _this.resizeDashboard();

                        try {
                            originalAferLoad(msvg);
                        } catch(err){

                        }

                        if(_data.options.mapsvg_options.extension &&  $().mapSvg.extensions && $().mapSvg.extensions[_data.options.mapsvg_options.extension]){
                            var ext = $().mapSvg.extensions[_data.options.mapsvg_options.extension];
                            ext && ext.backendAfterLoad(msvg);
                        }

                    };

                    _data.options.mapsvg_options.db_map_id = _this.getMapId();
                    _data.options.mapsvg_options.editMode = true;
                    msvg = $("#mapsvg").mapSvg(_data.options.mapsvg_options);
                    window.mapsvg = msvg;
                    window.madmin = _this;
                    return _this;

                }else{
                    $(".select-map-list").select2().on("select2:select",function(){
                        var link = $(this).find("option:selected").data('link');
                        if (link)
                            window.location = link+'&noheader=true';
                    });

                    $('#svg_file_uploader').on('change',function(){
                        $(this).closest('form').submit();
                    });

                    $('#mapsvg-table-maps')
                        .on('click', 'a.mapsvg-delete', methods.mapDelete)
                        .on('click', 'a.mapsvg-copy', methods.mapCopy);
                }
          });
        }
  };

  _this = methods;

  /** $.FN **/
  $.fn.mapsvgadmin = function( opts ) {

    if ( methods[opts] ) {
      return methods[opts].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof opts === 'object') {
      return methods.init.apply( this, arguments );
    }else if (!opts){
        return methods;
    } else {
      $.error( 'Method ' +  method + ' does not exist on mapSvg plugin' );
    }
  };

})( jQuery );