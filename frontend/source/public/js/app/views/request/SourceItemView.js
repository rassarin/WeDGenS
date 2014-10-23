/**
 * @fileOverview データソースリストアイテムビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * データソースリストアイテムビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/models/Source',
    'app/collections/Sources',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Source, Sources, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#source_id',
        model : Source,
        collection : Sources,
        initialize : function(options) {
            _.bindAll(this, 'render');
            this.reset();
        },
        reset : function() {
            this.collection = new Sources();
            this.collection.setApiUrl('getSourceList');
        },
        render : function() {
            var dataTypeId = $('#data_type_id_metbroker').val();
            if (dataTypeId) {
                this.$el.empty().append(Utils.genEmptyOptionItem('データ取得中...'));
                Utils.showLoadingIcon('#source_id_msg');

                this.collection.fetch({
                    data : {
                        data_type_id : dataTypeId
                    },
                    dataType : 'json',
                    success : $.proxy(this.fetchSuccess, this)
                });
            }
        },
        fetchSuccess: function(collection, resp) {
            this.$el.empty().append(Utils.genEmptyOptionItem('データソースIDを選択'));
            this.add(collection, resp);
            $('#source_id_msg').empty();
        },
        add : function(collection, resp) {
            var that = this;
            collection.each(function(model) {
                $(that.el).append(
                    JST['request/sourceList'](that.presenter(model))
                );
            });
        }
    });
});
