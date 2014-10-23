/**
 * @fileOverview 利用可能ライブラリコレクションクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * 利用可能ライブラリコレクションクラス
 */
define([
    'underscore',
    'backbone',
    'app/models/Library',
    './AbstractCollection'
],
function (_, Backbone, Library, AbstractCollection) {

    'use strict';

    return AbstractCollection.extend({
        url: null,
        model: Library,
        parse: function(res){
            if (res.error) {
                return alert(res.error);
            }
            return res.libraries;
        }
    });
});
