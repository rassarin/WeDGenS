/**
 * @fileOverview リージョンコレクションクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リージョンコレクションクラス
 */
define([
    'underscore',
    'backbone',
    'app/models/Region',
    './AbstractCollection'
],
function (_, Backbone, Region, AbstractCollection) {

    'use strict';

    return AbstractCollection.extend({
        url: null,
        model: Region,
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.regions;
        }
    });
});
