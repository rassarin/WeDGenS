/**
 * @fileOverview モデル抽象クラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * モデル抽象クラス
 */
define([
    'underscore',
    'backbone'
],
function (_, Backbone) {

    'use strict';

    return Backbone.Model.extend({
        toEscapedJSON: function () {
            var data = this.toJSON();
            _.each(data, function (value, name) {
                data[name] = _.escape(value);
            });
            return data;
        }
    });
});
