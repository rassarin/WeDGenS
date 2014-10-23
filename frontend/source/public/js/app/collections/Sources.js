/**
 * @fileOverview データソースコレクションクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * データソースコレクションクラス
 */
define([
    'underscore',
    'backbone',
    'app/models/Source',
    './AbstractCollection'
],
function (_, Backbone, Source, AbstractCollection) {

    'use strict';

    return AbstractCollection.extend({
        url: null,
        model: Source,
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.sources;
        }
    });
});
