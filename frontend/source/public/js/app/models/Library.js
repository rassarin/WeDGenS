/**
 * @fileOverview 利用可能ライブラリモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * 利用可能ライブラリモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "lib_id",
        defaults: function() {
            return {
                "lib_id": null,
                "lib_name": null,
                "description": null
            };
        }
    });
});
