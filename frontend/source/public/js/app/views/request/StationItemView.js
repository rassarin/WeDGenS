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
    'app/models/Station',
    'app/collections/Stations',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Station, Stations, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#station_id',
        model : Station,
        collection : Stations,
        initialize : function(options) {
            _.bindAll(this, 'render');
            this.on('station_empty', this.onStationEmpty);
            this.reset();
        },
        reset : function() {
            this.collection = new Stations();
            this.collection.setApiUrl('getStationList');
        },
        render : function() {
            var dataTypeId = $('#data_type_id_metbroker').val();
            var beginYear  = $('#begin_year').val();
            var endYear    = $('#end_year').val();
            var libId      = $('#lib_id option:selected').attr('value');
            var sourceId   = $('#source_id option:selected').attr('value');
            var regionId   = $('#region_id option:selected').attr('value');
            if (dataTypeId && sourceId && regionId) {
                this.$el.empty().append(Utils.genEmptyOptionItem('データ取得中...'));
                Utils.showLoadingIcon('#station_id_msg');
                this.collection.fetch({
                    data : {
                        lib_id : libId,
                        begin_year : beginYear,
                        end_year : endYear,
                        data_type_id : dataTypeId,
                        source_id : sourceId,
                        region_id : regionId
                    },
                    dataType : 'json',
                    success : $.proxy(this.fetchSuccess, this)
                });
            }
        },
        fetchSuccess: function(collection, resp) {
            this.$el.empty().append(Utils.genEmptyOptionItem('ステーションを選択'));
            this.add(collection, resp);
            $('#station_id_msg').empty();
        },
        add : function(collection, resp) {
            var that = this;
            if (collection.length) {
                collection.each(function(model) {
                    $(that.el).append(
                        JST['request/stationList'](that.presenter(model))
                    );
                });
            } else {
                this.$el.empty().append(Utils.genEmptyOptionItem('該当するステーションはみつかりません'));
                this.trigger('station_empty');
            }
        },
        onStationEmpty: function() {
            $(this.el).val('');
        }
    });
});
