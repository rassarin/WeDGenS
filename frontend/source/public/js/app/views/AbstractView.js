/**
 * @fileOverview ビュー抽象クラスモジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ビュー抽象クラス
 */
define([
    'underscore',
    'backbone'
],
function (_, Backbone) {

    'use strict';

    return Backbone.View.extend({
        presenter: function (model) {
            return model.toEscapedJSON();
        }
    });
});
