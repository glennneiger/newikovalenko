(function($, window){
    var MapSVGAdminDatabaseListController = function(container, admin, mapsvg){
        var _this = this;
        this.name = 'database-list';
        this.database = mapsvg.getDatabaseService();
        this.database.on('schemaChange',function(){
            _this.redrawDataList();
        });

        _this.database.on('dataLoaded',function(){
            _this.redrawDataList();
        });

        MapSVGAdminController.call(this, container, admin, mapsvg);
    };
    window.MapSVGAdminDatabaseListController = MapSVGAdminDatabaseListController;
    MapSVG.extend(MapSVGAdminDatabaseListController, window.MapSVGAdminController);


    MapSVGAdminDatabaseListController.prototype.viewLoaded = function(){
        var _this = this;
        // this.databaseTimestamp = Date.now();

        _this.redrawDataList();
    };

    MapSVGAdminDatabaseListController.prototype.viewDidAppear = function(){
        MapSVGAdminController.prototype.viewDidAppear.call(this);
        if(this.databaseTimestamp < this.database.lastChangeTime){
            this.redrawDataList();
        }
    };
    MapSVGAdminDatabaseListController.prototype.viewDidDisappear = function(){
        MapSVGAdminController.prototype.viewDidDisappear.call(this);
        this.closeFormHandler();
    };


    MapSVGAdminDatabaseListController.prototype.setEventHandlers = function(){
        var _this = this;

        $('#mapsvg-data-search-cols').on('click', 'li a' ,function(e){
            e.preventDefault();
            var field = $(this).text();
            $(this).closest('.input-group').find('.mapsvg-serch-field').text(field);
            _this.searchField = field;
        });



        this.toolbarView.on('click','.mapsvg-data-cols a',function(e){
            e.preventDefault();

            $(this).closest('li').toggleClass('active');

            var schema =  _this.database.getSchema();
            var field  = $(this).data('field');

            for (var i in schema){
                if(field == schema[i].name)
                    schema[i].visible = !schema[i].visible;
            }
            _this.database.saveSchema(schema);
        });

        $('#mapsvg-btn-data-add').on('click',function(e){
            e.preventDefault();
            _this.btnAdd = $(this);
            // _this.btnAdd.hide();
            _this.btnAdd.addClass('disabled');
            _this.editDataRow();
        });

        this.view.on('click','.mapsvg-data-row',function(e){
            if(!$(this).hasClass('active')){
                _this.editDataRow($(this));
                $('#mapsvg-btn-data-add').show();
                // $('.nano').nanoScroller({scrollTo: _this.tableDataActiveRow});
            }
        }).on('click','.mapsvg-data-delete',function(e){
            e.preventDefault();
            e.stopPropagation();
            var row = $(this).closest('tr');
            _this.deleteDataRow(row);
        });

    };

    MapSVGAdminDatabaseListController.prototype.getTemplateData = function(){
        var _this = this;
        return {
            fields: _this.getDataFieldsForTemplate(true),
            data: _this.database.getLoaded()
        };

    };

    // MapSVGAdminDatabaseListController.prototype.prepareDataForTemplate = function(){
    //     var _this = this;
    //     var fields = _this.getDataFieldsForTemplate();
    //     if(_this.dataObjects && _this.dataObjects.length)
    //         _this.dataObjects.forEach(function(obj, index){
    //             fields.forEach(function(fieldName, index){
    //                 if(obj[fieldName] == undefined)
    //                     obj[fieldName] = '';
    //             });
    //         });
    //     return _this.dataObjects;
    // };

    MapSVGAdminDatabaseListController.prototype.getDataFieldsForTemplate = function (onlyVisible) {
        var _this = this;
        var _fields = [{name: 'id', visible: true, type: 'id'}];
        var schema = this.database.getSchema();
        if(schema){
            schema.forEach(function(obj){
                if(onlyVisible){
                    if(obj.visible)
                        return _fields.push(obj);
                }else{
                    return _fields.push(obj);
                }
            });
        }
        return _fields;
    };

    MapSVGAdminDatabaseListController.prototype.redrawDataList = function(){
        var _this = this;

        // if(this.formBuilder){
        //     this.formBuilder.close();
        //     this.formBuilder.destroy();
        // }

        _this.redraw();

        var fieldsAll = _this.database.getColumns();
        if(fieldsAll.length < 2){
            $('#mapsvg-data-list-table').hide();
            $('#mapsvg-setup-database-msg').show();
        }
        var colsList = _this.toolbarView.find('.mapsvg-data-cols');
        colsList.empty();
        fieldsAll.forEach(function(field){
            colsList.append( $('<li class="'+(field.visible?'active':'')+'"><a href="#" data-field="'+field.name+'">'+field.name+'</a></li>') );
        });

        var pager = this.mapsvg.getPagination(function(){ _this.redrawDataList(); });
        this.view.find('.mapsvg-pagination-container').html(pager);

    };

    MapSVGAdminDatabaseListController.prototype.addDataRow = function(obj){
        var _this = this;
        var d = {
            fields: _this.database.getColumns({visible: true}),
            params: obj
        };
        var row = $(_this.templates.item(d));
        this.view.find('#mapsvg-data-list-table tbody').prepend(row);
        return row;
    };

    MapSVGAdminDatabaseListController.prototype.updateDataRow = function(obj, row){
        var _this = this;
        var d = {
            fields: _this.database.getColumns({visible: true}),
            params: obj
        };
        var newRow = $(_this.templates.item(d));
        row = row || $('#mapsvg-data-'+obj.id);
        row.replaceWith( newRow );
        newRow.addClass('mapsvg-row-updated');

        setTimeout(function(){
            newRow.removeClass('mapsvg-row-updated');
        }, 2600);

    };

    MapSVGAdminDatabaseListController.prototype.deleteDataRow = function(row){
        var _this = this;
        var id = row.data('id');
        var object = this.database.getLoadedObject(id);
        if(object.marker)
            _this.mapsvg.markerDelete(object.marker);
        this.database.delete(id);
        row.fadeOut(300, function(){
            row.remove();
        });
    };

    MapSVGAdminDatabaseListController.prototype.editDataRow = function(row, scrollTo){
        var _this = this;

        var _dataRecord = {};

        if(_this.tableDataActiveRow)
            _this.tableDataActiveRow.removeClass('mapsvg-row-selected');

        if(row){
            _this.updateScroll();
            if(scrollTo)
                _this.contentWrap.data('jsp').scrollToElement(row, true, false);
            _this.tableDataActiveRow = row;
            _this.tableDataActiveRow.addClass('mapsvg-row-selected');
            var id = _this.tableDataActiveRow.data('id');
            _dataRecord = _this.database.getLoadedObject(id);
        }

        var mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose images',
            button: {
                text: 'Choose images'
            },
            multiple: true
        });

        if(_this.formBuilder){
            _this.formBuilder.destroy();
            _this.formBuilder = null;
            _this.formBuilderRow && _this.formBuilderRow.remove();
            $('#mapsvg-btn-data-add').removeClass('disabled');
        }
        if(_this.formContainer)
            _this.formContainer.empty().remove();


        if(_dataRecord && _dataRecord.id){
            // _this.formBuilderRow = $('<tr />').addClass('mapsvg-data-editing-row');
            // _this.formBuilderRow.append('<td colspan="'+(_this.database.getSchema().length+3)+'"></td>');
            // var formContainer = _this.formBuilderRow.find('td');
            // _this.formBuilderRow.insertBefore('#mapsvg-data-'+_dataRecord.id);
            _this.formContainer = $('<div class="mapsvg-modal-edit"></div>');
            this.view.append(_this.formContainer);
        }else{
            // var formContainer = $('<div />');
            // formContainer.insertBefore('#mapsvg-data-list-table');
            _this.formContainer = $('<div class="mapsvg-modal-edit"></div>');
            this.view.append(_this.formContainer);
        }

        var marker_id = _dataRecord.marker && _dataRecord.marker.id ? _dataRecord.marker.id : '';
        _this.mapsvg.hideMarkersExceptOne(marker_id);

        _this.formBuilder = new MapSVG.FormBuilder({
            container: _this.formContainer,
            schema: _this.database.getSchema(),
            editMode: false,
            mapsvg: _this.mapsvg,
            mediaUploader: mediaUploader,
            data: _dataRecord,
            admin: _this.admin,
            events: {
                save: function(data){_this.saveDataObject(data); },
                update:  function(data){ _this.updateDataObject(data); },
                close: function(){ _this.closeFormHandler(); }
            }
        });
    };

    MapSVGAdminDatabaseListController.prototype.saveDataObject = function (obj){
        var _this = this;
        var row = this.addDataRow(obj);
        this.database.create(obj).done(function(_obj){
            _this.updateDataRow(obj, row);
            if(obj.marker){
                obj.marker = _this.mapsvg.getMarker(obj.marker.id);
                obj.marker.setId('marker_'+obj.id);
                obj.marker.setObject(obj);
            }
            // _this.mapsvg.reloadDataObjects();
            _this.mapsvg.showMarkers();
            _this.mapsvg.hideMarkersExceptOne();
        }).fail(function(){
            $.growl.error({title: 'Server error', message: 'Can\'t create object'});
            row.remove();
        });
    };
    MapSVGAdminDatabaseListController.prototype.updateDataObject = function (obj){
        var _this = this;
        this.database.update(obj).fail(function(){
            $.growl.error({title: 'Server error', message: 'Can\'t update object'});
        });
        if(obj.marker){
            var marker = _this.mapsvg.getMarker(obj.marker.id);
            marker.setId('marker_'+obj.id);
            marker.setObject(obj);
        }
        this.closeFormHandler();
        this.updateDataRow(obj);
    };
    MapSVGAdminDatabaseListController.prototype.closeFormHandler = function (){
        var _this = this;
        $('#mapsvg-btn-data-add').removeClass('disabled');
        _this.mapsvg.showMarkers();

        if(_this.formBuilder){
            _this.formBuilder.destroy();
            _this.formBuilder = null;
            _this.formContainer.empty().remove();
            // _this.formBuilderRow && _this.formBuilderRow.remove();
            _this.tableDataActiveRow && _this.tableDataActiveRow.removeClass('mapsvg-row-selected');
            _this.tableDataActiveRow && !_this.tableDataActiveRow.hasClass('mapsvg-row-updated') && _this.tableDataActiveRow.addClass('mapsvg-row-closed');
            setTimeout(function(){
                _this.tableDataActiveRow && !_this.tableDataActiveRow.hasClass('mapsvg-row-updated') && _this.tableDataActiveRow.removeClass('mapsvg-row-closed');
            }, 1600);
            // WP Media Uploader inserts a.browser links, remove them:
            $('a.browser').remove();

            _this.admin.setPreviousMode();
        }


        this.updateScroll();
    };


})(jQuery, window);