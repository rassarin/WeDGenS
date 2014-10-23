/**
 * @fileOverview ライブラリパラメータアイテムビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ライブラリパラメータアイテムビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/models/LibParam',
    'app/collections/LibParams',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, LibParam, LibParams, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#lib_params',
        model : LibParam,
        collection : LibParams,
        initialize : function(options) {
            _.bindAll(this, 'render');
            this.reset();
        },
        reset : function() {
            this.collection = new LibParams();
            this.collection.setApiUrl('getParamList');
        },
        render : function() {
            var emptyMessage = $("<div>").attr("class", "empty")
                                         .html('ライブラリパラメータ取得中...');

            this.$el.empty().append(emptyMessage);
            Utils.showLoadingIcon('#lib_params_msg');

            var libId = $('#lib_id option:selected').attr('value');
            if (libId) {
                this.collection.fetch({
                    data : {
                        lib_id : $('#lib_id option:selected').attr('value')
                    },
                    dataType : 'json',
                    success : $.proxy(this.fetchSuccess, this)
                });
            }
        },
        fetchSuccess: function(collection, resp) {
            this.$el.empty();
            this.add(collection, resp);
            $('#lib_params_msg').empty();
        },
        add : function(collection, resp) {
            var that = this;
            collection.each(function(model) {
                var attributes = model.attributes;
                $(that.el).append(
                    JST['request/paramList'](model.attributes)
                );

                if (attributes['parent_item']) {
                    $(that.el).append(
                        JST['request/filteringSelect']({
                            'child_id'    : attributes['id'],
                            'parent_id'   : attributes['parent_item'],
                            'child_items' : attributes['relational_items']
                        })
                    );
                }
            });
        }
    });
});
