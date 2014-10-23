/**
 * @fileOverview ステーションコレクションクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ステーションコレクションクラス
 */
define([
    'underscore',
    'backbone',
    'app/models/Station',
    './AbstractCollection'
],
function (_, Backbone, Station, AbstractCollection) {

    'use strict';

    return AbstractCollection.extend({
        url: null,
        model: Station,
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.stations;
        }
    });
});
