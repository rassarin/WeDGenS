/**
 * @fileOverview リクエストモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リクエストモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "request_id",
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.request;
        }
    });
});
