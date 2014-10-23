/**
 * @fileOverview ライブラリパラメータコレクションクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ライブラリパラメータコレクションクラス
 */
define([
    'underscore',
    'backbone',
    'app/models/LibParam',
    './AbstractCollection'
],
function (_, Backbone, LibParam, AbstractCollection) {

    'use strict';

    return AbstractCollection.extend({
        url: null,
        model: LibParam,
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.parameters;
        }
    });
});
