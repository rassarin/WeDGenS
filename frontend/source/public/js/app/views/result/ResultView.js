/**
 * @fileOverview リクエストビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リクエストビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/models/Request',
    './DownloadView',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Request, DownloadView, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#download_element',
        model : Request,
        events: {
            'click #download_btn' : 'onDownloadSubmit'
        },
        initialize : function(options) {
            this.model = new Request();
            this.model.url = Utils.getApiUrl('getResult');
        },
        render : function(requestId) {
            this.$el.html(JST['result/downloadView']({request_id : requestId}));
            $('#download_btn').button();
            $('#format_xml').attr('checked', true);
            return this;
        },
        onDownloadSubmit: function(e) {
            e.preventDefault();
            this.downloadView = new DownloadView();
            this.downloadView.checkDownload();
        }
    });
});
