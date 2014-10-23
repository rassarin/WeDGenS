/**
 * @fileOverview ルータークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ルータークラス
 */
define([
    'backbone'
], function (Backbone) {

    'use strict';

    return Backbone.Router.extend({
        routes: {
            'download/:id': 'showDownloadView',
            'download': 'showDownloadView',
            '': 'showRequestView',
        },
        showRequestView: function () {
            this.trigger('route', 'request');
        },
        showDownloadView: function (id) {
            this.trigger('route', 'download', [id]);
        }
    });
});
