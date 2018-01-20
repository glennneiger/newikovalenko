(function($, window){
    var MapSVGAdminDirectoryController = function(container, admin, mapsvg){
        this.name = 'directory';
        MapSVGAdminController.call(this, container, admin, mapsvg);
    };
    window.MapSVGAdminDirectoryController = MapSVGAdminDirectoryController;
    MapSVG.extend(MapSVGAdminDirectoryController, window.MapSVGAdminController);

    MapSVGAdminDirectoryController.prototype.setEventHandlers = function(){


    };

    MapSVGAdminDirectoryController.prototype.updateDirSource = function(val){
        val = val || this.mapsvg.getData().options.menu.source;
        this.view.find('#mapsvg-dir-object-2').html(val == 'database'? 'Database object' : "Region object");
    };

    MapSVGAdminDirectoryController.prototype.viewLoaded = function(){
        var _this = this;

        _this.updateDirSource();


        this.database = this.mapsvg.getDatabaseService();

        _this.setSortFields();

        this.database.on('schemaChange', function(){
            _this.setSortFields();
        });
        $('#mapsvg-directory-data-source').on('change',':radio',function(){
            _this.setSortFields();
            var val = $(this).val();
            _this.updateDirSource(val);
            if(_this.admin.getData().controllers['actions'])
                _this.admin.getData().controllers['actions'].updateDirSource(val);
            // setTimeout(function(){
            // },1000);
        });
        $('#mapsvg-directory-sort-control').select2().on("select2:select",function(){
            var link = $(this).find("option:selected").data('link');
            if (link)
                window.location = link;
        });
        this.view.on('change', '#mapsvg-details-width :radio',function(){
           var value = $(this).closest('.form-group').find(':radio:checked').val();
            if(value != 'full'){
              $('#mapsvg-details-width-custom').prop('disabled',false).trigger('keyup');
            }else{
                $('#mapsvg-details-width-custom').prop('disabled',true);

            }
        });
        _this.updateSortList();
    };
    MapSVGAdminDirectoryController.prototype.updateSortList = function(){
        var _this = this;

    };

    MapSVGAdminController.prototype.getTemplateData = function(){
        var data = this.mapsvg.getOptions(true, null, this.admin.getData().optionsDelta);
        data.fulltext_min_word_len = mapsvg_fulltext_min_word;
        return data;
    };

    MapSVGAdminDirectoryController.prototype.setSortFields = function(){
        var _this = this;
        var _fields = ['id'];
        var schema = this.database.getSchema();
        if(schema){
            schema.forEach(function(obj){
                _fields.push(obj.name);
            });
        }
        this.sort = {
            regions: ['id','title'],
            database: _fields
        };
        var source = $('#mapsvg-directory-data-source :radio:checked').val();
        var options = _this.sort[source].map(function(field){
            return '<option '+(_this.templateData.menu.sortBy==field ? "selected":"")+'>'+field+'</option>';
        });
        $('#mapsvg-directory-sort-control').html(options).trigger('change');
    }
})(jQuery, window);