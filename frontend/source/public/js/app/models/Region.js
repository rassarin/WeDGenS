/**
 * @fileOverview リージョンモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リージョンモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "region_id",
        defaults: function() {
            return {
                "region_id": null,
                "region_name": null
            };
        }
    });
});
