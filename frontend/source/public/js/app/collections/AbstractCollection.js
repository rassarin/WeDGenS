/**
 * @fileOverview コレクション抽象クラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * コレクション抽象クラス
 */
define([
    'underscore',
    'backbone',
    'app/utils/Utils'
],
function (_, Backbone, Utils) {

    'use strict';

    return Backbone.Collection.extend({
        setApiUrl: function (action) {
            this.url = Utils.getApiUrl(action);
        }
    });
});
