/**
 * @fileOverview ライブラリパラメータモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ライブラリパラメータモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "id",
        defaults: function() {
            return {
                "id"         : null,
                "format"     : null,
                "type"       : null,
                "label"      : null,
                "validate"   : null,
                "description": null,
                "items"      : null,
                "relational_items" : null,
            };
        }
    });
});
