(function($, window){
    var MapSVGAdminCssController = function(container, admin, mapsvg){
        this.name = 'css';
        MapSVGAdminController.call(this, container, admin, mapsvg);
    };
    window.MapSVGAdminCssController = MapSVGAdminCssController;
    MapSVG.extend(MapSVGAdminCssController, window.MapSVGAdminController);

    MapSVGAdminCssController.prototype.viewLoaded = function(){
        var _this = this;
        this.editors = {};
        this.textarea = this.view.find('#mapsvg-css-editor');
        this.textarea.val(_this.admin.getData().options.mapsvg_css);
        if(this.textarea.val().length==0)
            this.textarea.val("/* Add your styles here */\n\n\n\n\n\n\n\n\n");
        this.editors.css = CodeMirror.fromTextArea(this.textarea[0], {mode: 'css', matchBrackets: true, lineNumbers: true});

        var setCss = function(codemirror, changeobj){
            var css = codemirror.getValue();
            _this.admin.mapsvgCss = css;
            _this.admin.mapsvgCssChanged = true;
            _this.mapsvg.setCss(css);
        };
        this.editors.css.on('change',setCss);

        $(window).on('resize.codemirror.css',function(){
            _this.resizeEditor();
        });
        waiting = setTimeout(function(){_this.resizeEditor();}, 500);


        _this.exampleCode = null;
        _this.cssCodeLoaded = false;

        this.view.on('click','#mapsvg-css-menu a', function (e) {
            e.preventDefault();

            $(this).tab('show');
            if($(this).attr('href') == '#mapsvg-css-default') {
                if(!_this.cssCodeLoaded){
                    $.get(_this.mapsvg.getCssUrl(), function(data){
                        $('#mapsvg-css-default-editor').val(data);
                        _this.highlighDefaultCss();
                        _this.cssCodeLoaded = true;
                    });
                }else{
                    _this.highlighDefaultCss();
                }
            }else{
                _this.exampleCode && _this.exampleCode.toTextArea();
            }
        }).on('shown.bs.tab', function (e){
            _this.admin.updateScroll();
        });
    };

    MapSVGAdminCssController.prototype.resizeEditor = function(){
        this.view.find('.CodeMirror')[0].CodeMirror.setSize(null, this.contentView.height());
    };


    MapSVGAdminCssController.prototype.highlighDefaultCss = function(){
        var _this = this;
        var minLines = 10;
        _this.exampleCode = CodeMirror.fromTextArea($('#mapsvg-css-default-editor')[0], {
            mode: 'css',
            lineNumbers: true,
            matchBrackets: true,
            readOnly: true
        });
        _this.admin.updateScroll();
        waiting = setTimeout(function(){_this.resizeEditor();}, 500);

    };

    MapSVGAdminCssController.prototype.init = function(){

    }
})(jQuery, window);