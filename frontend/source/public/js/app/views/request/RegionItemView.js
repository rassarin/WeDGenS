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
    'app/models/Region',
    'app/collections/Regions',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Region, Regions, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#region_id',
        model : Region,
        collection : Regions,
        initialize : function(options) {
            _.bindAll(this, 'render');
            this.on('region_empty', this.onRegionEmpty);
            this.reset();
        },
        reset : function() {
            this.collection = new Regions();
            this.collection.setApiUrl('getRegionList');
        },
        render : function() {
            var dataTypeId = $('#data_type_id_metbroker').val();
            var sourceId   = $('#source_id option:selected').attr('value');
            if (dataTypeId && sourceId) {
                this.$el.empty().append(Utils.genEmptyOptionItem('データ取得中...'));
                Utils.showLoadingIcon('#region_id_msg');
                this.collection.fetch({
                    data : {
                        data_type_id : dataTypeId,
                        source_id : sourceId
                    },
                    dataType : 'json',
                    success : $.proxy(this.fetchSuccess, this)
                });
            }
        },
        fetchSuccess: function(collection, resp) {
            this.$el.empty().append(Utils.genEmptyOptionItem('リージョンを選択'));
            this.add(collection, resp);
            $('#region_id_msg').empty();
        },
        add : function(collection, resp) {
            var that = this;
            if (collection.length) {
                collection.each(function(model) {
                    $(that.el).append(
                        JST['request/regionList'](that.presenter(model))
                    );
                });
            } else {
                $(that.el).append(
                    JST['request/regionList']({region_id : 'none', region_name: 'リージョンなし'})
                );
                this.trigger('region_empty');
            }
        },
        onRegionEmpty: function() {
            $(this.el).val('none');
            this.trigger('none_select');
        }
    });
});
