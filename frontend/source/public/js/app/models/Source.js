/**
 * @fileOverview データソースモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * データソースモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "source_id",
        defaults: function() {
            return {
                "source_id": null,
                "source_name": null
            };
        }
    });
});
