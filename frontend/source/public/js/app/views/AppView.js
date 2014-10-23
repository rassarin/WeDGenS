/**
 * @fileOverview アプリケーションビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * アプリケーションビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/views/request/RequestView',
    'app/views/result/CheckView',
    './AbstractView'
], function(
    $, _, Backbone, JST, Utils,
    RequestView, CheckView, AbstractView
) {

    'use strict';

    return AbstractView.extend({
        el: '#content',
        events: {
            'click #request_menu'  : function (e) {
                e.preventDefault();
                Backbone.history.navigate('', true);
            },
            'click #download_menu' : function (e) {
                e.preventDefault();
                Backbone.history.navigate('download', true);
            }
        },
        initialize : function(options) {
            Utils.initValidator();
            this.listenTo(options.router, 'route', this.dispatch);
        },
        render : function(viewId) {
            this.$el.html(JST['appView']());
            switch(viewId) {
                case 'request':
                    this.requestView = new RequestView();
                    this.requestView.render();
                    break;
                case 'check':
                    this.checkView = new CheckView();
                    this.checkView.render();
                    break;
            }
            return this;
        },
        dispatch : function(name, args) {
            if (!_.include([ 'request', 'download'], name)) {
                return;
            }
            args || (args = []);
            switch (name) {
                case 'request':
                    this.onShowRequestView.apply(this, args);
                    break;
                case 'download':
                    this.onShowDownloadView.apply(this, args);
                    break;
            }
        },
        onShowRequestView : function(args) {
            $('#content').empty();
            $('#content').append(this.render('request').el);
        },
        onShowDownloadView : function(requestId) {
            $('#content').empty();
            $('#content').append(this.render('check').el);
            if (requestId) {
                $('#request_id').val(_.escape(requestId));
            }
        }
    });
});
