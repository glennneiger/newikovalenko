(function($, window, MapSVG){

    MapSVG.DatabaseService = function(options, mapsvg){
        var _this = this;
        this.mapsvg         = mapsvg;
        this.map_id         = options.map_id;
        this.table          = options.table;
        this.perpage        = options.perpage || 0;
        this.sortBy         = options.sortBy || null;
        this.sortDir        = options.sortDir || null;
        this.lastChangeTime = Date.now();
        this.rows           = [];
        this.cols           = [];
        this.schemaDict     = {};
        this.index          = {};
        this.page           = 1;
        this.hasMoreRecords = false;
        this.query          = new MapSVG.DatabaseQuery();
        this.events         = {
            'create': [],
            'update': [],
            'change': [],
            'schemaChange': [],
            'dataLoaded': []
        }; // array of callbacks
        this.schema         = new MapSVG.DatabaseSchema(options, mapsvg);
        this.schema.on('change',function(){
            _this.getAll();
            _this.trigger('schemaChange');
        });

    };

    MapSVG.DatabaseService.setQuery = function(params){
    };
    MapSVG.DatabaseService.prototype.setPerpage = function(perpage) {
        this.perpage = perpage;
    };

    MapSVG.DatabaseService.prototype.lastChangeTime = function() {
        return this.lastChangeTime;
    };

    MapSVG.DatabaseService.prototype.onFirstPage = function() {
        return this.query.page === 1;
    };
    MapSVG.DatabaseService.prototype.onLastPage = function() {
        return this.hasMoreRecords == false;
    };

    MapSVG.DatabaseService.prototype.reindex = function(){
        var _this = this;
        this.index = {};
        this.rows && this.rows.forEach(function(obj, index) {
            _this.index[obj.id] = index;
        });
    };

    MapSVG.DatabaseService.prototype.create = function(obj){
        this.rows.push(obj);
        var _this = this;
        this.trigger('create');
        this.trigger('change');
        var newObj = $.extend({}, obj);
        newObj.post && delete newObj.post;
        var data = this.toJSON(newObj);
        return $.post(ajaxurl, {action: 'mapsvg_data_create', data: data, map_id: this.map_id, table: this.table}, null, 'json').done(function(_data){
            obj.id = _data.id;
            _this.reindex();
        });
    };
    MapSVG.DatabaseService.prototype.update = function(obj){
        var index = this.index[obj.id];
        this.rows[index] = obj;
        this.trigger('update');
        this.trigger('change');
        var id = obj.id;
        var newObj = $.extend({}, obj);
        newObj.post && delete newObj.post;
        var data = this.toJSON(newObj);
        return $.post(ajaxurl, {action: 'mapsvg_data_update', id: id, data: data, map_id: this.map_id, table: this.table}, null, 'json');
    };
    MapSVG.DatabaseService.prototype.delete = function(id){
        var index = this.index[id];
        this.rows.splice(index,1);
        this.reindex();
        this.trigger('delete');
        this.trigger('change');
        return $.post(ajaxurl, {action: 'mapsvg_data_delete', id: id, map_id: this.map_id, table: this.table}, null, 'json');
    };
    MapSVG.DatabaseService.prototype.getLoadedObject = function(id){
        var _this = this;
        var index = this.index[id];
        if(index != undefined) {
            return _this.rows[index];
        }else{
            return {};
        }
    };

    MapSVG.DatabaseService.prototype.get = function(id){
        var index = this.index[id];
        if(index != undefined){
            defer = $.Deferred();
            var obj = _this.rows[index];
            defer.promise();
            defer.resolve(obj);
            return defer;
        }
        return $.get(ajaxurl, {action: 'mapsvg_data_get', id: id, map_id: this.map_id, table: this.table}, null, 'json');
    };
    MapSVG.DatabaseService.prototype.getAll = function(params){
        var _this = this;

        if(typeof params == 'object' && Object.keys(params).length && !params.page)
            _this.query.page = 1;

        _this.query.set(params);

        // if table isn't created yet then return empty array
        // if(!this.tableName){
        //     defer = $.Deferred();
        //     var obj = [];
        //     defer.promise();
        //     defer.success = defer.done;
        //     defer.resolve([]);
        //     return defer;
        // }

        params = _this.query || {};
        var filters = params.filters || null;
        this.page = params.page? parseInt(params.page) : this.page;
        var perpage = params.perpage? parseInt(params.perpage) : this.perpage;
        var search = params.search;
        var searchFallback = params.searchFallback;
        var searchField = params.searchField;
        return $.get(ajaxurl, {
            action: 'mapsvg_data_get_all',
            search: search,
            searchField: searchField,
            searchFallback: searchFallback,
            filters: filters,
            page: this.page,
            sortBy: this.sortBy,
            sortDir: this.sortDir,
            perpage: perpage,
            map_id: this.map_id,
            table: this.table
        }, null, 'json')
            .success(function(data){
                if(data){
                    _this.hasMoreRecords = _this.perpage && (data.length > _this.perpage);
                    if(_this.hasMoreRecords){
                        data.pop();
                    }
                    _this.rows = _this.formatData(data);
                }else{
                    _this.hasMoreRecords = false;
                    _this.rows = [];
                }

                _this.reindex();
                _this.trigger('dataLoaded');
            });
    };
    MapSVG.DatabaseService.prototype.getLoaded = function(){
        return this.rows || [];
    };
    MapSVG.DatabaseService.prototype.formatData = function(data){

        var _this = this;
        var newdata = [];
        if(this.table == 'regions'){
            data.forEach(function(object){
                // var newObject = object;
                // newObject.data = object;
                // newObject.id = object.id;
                // newObject.title = object.region_title;
                // // newObject._ = _this.mapsvg.getRegion(object.id);
                // r = _this.mapsvg.getRegion(object.id);
                // newObject.disabled = r.disabled;
                // // newObject.objects = r.objects;
                // newdata.push(newObject);
                // for (var name in object){
                //     if (name == 'id'){
                //         object[name] = parseInt(object[name])
                //     } else {
                //         // var type = _this.schemaDict[name];
                //         // if(type == 'checkbox')
                //         //     object[name] = parseInt(object[name]);
                //     }
                // }
            });
        }
        // return newdata;
        return data;
    };

    MapSVG.DatabaseService.prototype.on = function(event, callback){
        this.lastChangeTime = Date.now();
        if(!this.events[event])
            this.events = {};
        this.events[event].push(callback);
    };
    MapSVG.DatabaseService.prototype.trigger = function(event){
        var _this = this;
        if(this.events[event] && this.events[event].length)
            this.events[event].forEach(function(callback){
                callback && callback(_this.rows);
            });
    };
    MapSVG.DatabaseService.prototype.onSchemaChange = function(callback){
        this.onSchemaChangeCallbacks.push(callback);
    };
    MapSVG.DatabaseService.prototype.toJSON = function(_object){
      var object = {};
      for(var i in _object){
          if(_object[i] && (typeof _object[i] == 'object' || typeof _object[i] == 'function') && _object[i].getOptions!=undefined){
              object[i] = _object[i].getOptions();
          }else{
              object[i] = _object[i] || '';
          }
      }
      return object;
    };
    MapSVG.DatabaseService.prototype.getSchema = function(options){
        return this.schema.get(options);
    };
    MapSVG.DatabaseService.prototype.getSchemaField = function(field){
        return this.schema.getField(field);
    };
    MapSVG.DatabaseService.prototype.getSchemaFieldByType = function(type){
        return this.schema.getFieldByType(type);
    };
    MapSVG.DatabaseService.prototype.setSchema = function(options){
        return this.schema.set(options);
    };
    MapSVG.DatabaseService.prototype.loadSchema = function(options){
        return this.schema.load(options);
    };
    MapSVG.DatabaseService.prototype.saveSchema = function(options){
        return this.schema.save(options);
    };
    MapSVG.DatabaseService.prototype.getColumns = function(options){
        return this.schema.getColumns(options);
    };



    /* SCHEMA */

    MapSVG.DatabaseSchema = function(options, mapsvg){
        this.mapsvg         = mapsvg;
        this.map_id         = options.map_id;
        this.table          = options.table;
        this.lastChangeTime = Date.now();
        this.cols           = [];
        this.schema         = [];
        this.schemaDict     = {};
        this.events         = {
            'create': [],
            'update': [],
            'change': []
        }; // array of callbacks
    };
    MapSVG.DatabaseSchema.prototype.set = function(options){
        var _this = this;

        if(options)
            _this.schema = options.map(function(field){
                field.visible = MapSVG.parseBoolean(field.visible);
                _this.schemaDict[field.name] = field;
                return field;
            });

    };
    MapSVG.DatabaseSchema.prototype.save = function(fields){

        var _this = this;

        this.set(fields);

        for(var i in this.schema){
            if(!this.schema[i])
                this.schema.splice(i,1);
        }

        fields = JSON.stringify(fields);


        return $.post(ajaxurl, {action: 'mapsvg_save_schema', schema: fields, map_id: this.map_id, table: this.table}).done(function(){
            _this.trigger('change');
        });
    };
    MapSVG.DatabaseSchema.prototype.get = function(options){
        return this.schema;
    };
    MapSVG.DatabaseSchema.prototype.getField = function(field){
        return this.schemaDict[field];
    };
    MapSVG.DatabaseSchema.prototype.getFieldByType = function(type){
        var f = null;
        this.schema.forEach(function(field){
           if(field.type == type)
               f = field;
        });
        return f;
    };
    MapSVG.DatabaseSchema.prototype.load = function(options){
        var _this = this;
        return $.get(ajaxurl, {action: 'mapsvg_get_schema', map_id: this.map_id, table: this.table}, null, 'json')
            .done(function(schema){
                _this.set(schema);
            });
    };
    MapSVG.DatabaseSchema.prototype.getColumns = function (filters) {

        filters = filters || {};

        var _this = this;
        var columns = this.get().slice(0); // clone array
        if(this.table == 'regions')
            columns.unshift({name: 'title', visible: true, type: 'title'}); // add Title column to the beginning
        columns.unshift({name: 'id', visible: true, type: 'id'}); // add ID column to the beginning
        var needfilters = Object.keys(filters).length !== 0;
        var results = [];

        if(needfilters){
            var filterpass;
            columns.forEach(function(obj){
                filterpass = true;
                for(var param in filters) {
                    filterpass = (obj[param] == filters[param]);
                }
                filterpass && results.push(obj);
            });
        } else {
            results = columns;
        }
        return results;
    };
    MapSVG.DatabaseSchema.prototype.on = function(event, callback){
        this.lastChangeTime = Date.now();
        if(!this.events[event])
            this.events = {};
        this.events[event].push(callback);
    };
    MapSVG.DatabaseSchema.prototype.trigger = function(event){
        var _this = this;
        if(this.events[event] && this.events[event].length)
            this.events[event].forEach(function(callback){
                callback && callback(_this.rows);
            });
    };


    MapSVG.Filters = function(fields){
        this.schema = {};
        this.schemaDict = {};
        this.fields = {};
        this.setSchema(fields);
    };
    MapSVG.Filters.prototype.set = function(fields){
        this.fields = fields;
    };
    MapSVG.Filters.prototype.save = function(fields){
        this.fields = fields;
    };
    MapSVG.Filters.prototype.reset = function(fields){
        this.fields = {};
    };
    MapSVG.Filters.prototype.get = function(fields){
        return this.fields;
    };
    MapSVG.Filters.prototype.getField = function(field){
        return this.schemaDict[field];
    };

    MapSVG.Filters.prototype.getSchema = function(fields){
        return this.schema;
    };
    MapSVG.Filters.prototype.setSchema = function(fields){
        var _this = this;
        if(fields)
            fields.forEach(function(field){
                var paramName = field.parameterName.split('.')[1];
                _this.schemaDict[paramName] = field;
            });
        this.schema = fields;
        return this.schema;
    };

    MapSVG.DatabaseQuery = function(options){
        options = options || {};
        this.sortBy         = options.sortBy || null;
        this.sortDir        = options.sortDir || null;
        this.page           = options.page || 1;
        this.perpage        = options.perpage;
        this.search         = options.search;
        this.searchField    = options.searchField;
        this.filters        = options.filters || {};
    };
    MapSVG.DatabaseQuery.prototype.set = function(fields){
        var _this = this;
        for(var key in fields){
            if(key == 'filters'){
                _this.setFilters(fields[key]);
            }else{
                _this[key] = fields[key];
            }
        }
    };

    MapSVG.DatabaseQuery.prototype.setFilters = function(fields){
        var _this = this;
        for(var key in fields){
            _this.filters[key] = fields[key];
        }
    };
    MapSVG.DatabaseQuery.prototype.resetFilters = function(fields){
        this.filters = {};
    };
    MapSVG.DatabaseQuery.prototype.setFilterField = function(field, value){
        this.filters[field] = value;
    };


})(jQuery, window, MapSVG);