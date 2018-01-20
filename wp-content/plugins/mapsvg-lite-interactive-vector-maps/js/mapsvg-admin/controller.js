// Get plugin's path
// var _scripts       = document.getElementsByTagName('script');
// var _myScript      = _scripts[_scripts.length - 1].src.split('/');
// console.log(_scripts);
// _myScript.pop();
// var controllersURL   =  _myScript.join('/')+'/';

(function($, window){

    var MapSVGAdminController = function(container, admin, mapsvg){
        this.name = this.name || 'controller';
        this.container = typeof container == 'object' ? container : $('#'+container);
        this.admin = admin;
        this.mapsvg = mapsvg;
        this.templates = {};
        this.scrollable = this.scrollable === undefined ? true : this.scrollable;
        this.controllers = {};
        this._init();
    };
    MapSVGAdminController.prototype.nameCamel = function(){
        var name = this.name.split('-').map(function(n, index){
            if(index === 0)
                return n;
            else
                return n.charAt(0).toUpperCase() + n.slice(1);
        }).join('');
        return name;
    };
    MapSVGAdminController.prototype.viewLoaded = function(){
    };

    MapSVGAdminController.prototype._viewLoaded = function(){
        var _this = this;

        this.view.find('.mapsvg-select2').select2({
            minimumResultsForSearch: 20
        });

        if(this.scrollable)
        // setTimeout(function(){
            _this.updateScroll();
        // }, 500);

        this.view.find('.mapsvg-onoff').bootstrapToggle({
            onstyle: 'default',
            offstyle: 'default'
        });
    };
    MapSVGAdminController.prototype.viewDidAppear = function(){
        var _this = this;
        if(_this.controllers) for(var i in _this.controllers){
            _this.controllers[i].viewDidAppear();
        }
        if(this.scrollable)
            // setTimeout(function(){
                _this.updateScroll();
            // }, 500);
    };

    MapSVGAdminController.prototype.viewDidDisappear = function(){
        if(this.controllers){
            for(var name in this.controllers){
                this.controllers[name].viewDidDisappear();
            }
        }
    };
    MapSVGAdminController.prototype.updateScroll = function(){
        if(!this.contentWrap)
            return;
        if(!this.contentWrap.data('jsp'))
            this.contentWrap.jScrollPane();
        var jsp = this.contentWrap.data('jsp');
        jsp.reinitialise();
        setTimeout(function(){
            jsp.reinitialise();
        }, 500);
    };
    MapSVGAdminController.prototype._init = function(){
        var _this = this;
        if(!_this.templatesLoaded)
            $.get(MapSVG.urls.templates+this.name+'.hbs?'+Math.random(), function(data){
                $(data).each(function(index, tmpl){
                    var name = $(tmpl).data('name');
                    if(name){
                        _this.templates[name] = Handlebars.compile($(tmpl).html());
                        if($(tmpl).data('partial')){
                            Handlebars.registerPartial(_this.nameCamel()+'Partial', $(tmpl).html());
                        }
                    }
                });
                _this.templatesLoaded = true;
                _this.render();
            });
        else
            _this.render();
    };
    MapSVGAdminController.prototype.render = function(){

        this.view && this.view.empty().remove();

        this.view    = $('<div />').attr('id','mapsvg-admin-controller-'+this.name).addClass('mapsvg-view');

        // Wrap cointainer, includes scrollable container
        this.contentWrap    = $('<div />').attr('id','mapsvg-admin-content-'+this.name).addClass('mapsvg-view-wrap');

        // Scrollable container
        this.contentView    = $('<div />').addClass('mapsvg-view-content');
        if(this.scrollable){
            this.contentWrap.addClass('nano');
            this.contentView.addClass('nano-content');
        }
        this.contentWrap.append(this.contentView);

        // Add toolbar if it exists in template file
        if(this.templates.toolbar){
            this.toolbarView = $('<div />').attr('id','mapsvg-admin-toolbar-'+this.name).addClass('mapsvg-view-toolbar');
            this.view.append(this.toolbarView);
        }

        this.view.append(this.contentWrap);

        // Add view into container
        this.container.append(this.view);
        this.container.data('controller', this);

        this.redraw();
        this._viewLoaded();
        this.viewLoaded();
        this.setEventHandlersCommon();
        this.setEventHandlers();
    };
    MapSVGAdminController.prototype.redraw = function(){
        this.templateData = this.getTemplateData();

        this.contentView.html( this.templates.main(this.templateData) );
        if(this.templates.toolbar)
            this.toolbarView.html( this.templates.toolbar(this.templateData) );
        if(this.scrollable)
            this.updateScroll();
    };

    MapSVGAdminController.prototype.getTemplateData = function(){
        return this.mapsvg.getOptions(true, null, this.admin.getData().optionsDelta);
    };
    MapSVGAdminController.prototype.setEventHandlersCommon = function(){
        var _this = this;
        $(window).on('resize', function(){
            if(_this.scrollable)
                _this.updateScroll();
        });
    };
    MapSVGAdminController.prototype.setEventHandlers = function(){

    };

    window.MapSVGAdminController = MapSVGAdminController;

})(jQuery, window);