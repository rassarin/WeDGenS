/**
 * @fileOverview 利用可能ライブラリリストアイテムビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * 利用可能ライブラリリストアイテムビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/models/Library',
    'app/collections/Libraries',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Library, Libraries, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#lib_id',
        model : Library,
        collection : Libraries,
        initialize : function(options) {
            _.bindAll(this, 'render');
            this.reset();
        },
        reset : function() {
            this.collection = new Libraries();
            this.collection.setApiUrl('getLibraryList');
        },
        render : function() {
            this.$el.empty().append(Utils.genEmptyOptionItem('データ取得中...'));
            Utils.showLoadingIcon('#lib_id_msg');

            this.collection.fetch({
                dataType : 'json',
                success : $.proxy(this.fetchSuccess, this)
            });
        },
        fetchSuccess: function(collection, resp) {
            this.$el.empty().append(Utils.genEmptyOptionItem('ライブラリを選択'));
            this.add(collection, resp);
            $('#lib_id_msg').empty();
        },
        add : function(collection, resp) {
            var that = this;
            collection.each(function(model) {
                $(that.el).append(
                    JST['request/libraryList'](that.presenter(model))
                );
            });
        }
    });
});
