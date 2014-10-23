/**
 * @fileOverview リクエスト登録画面ビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リクエスト登録画面ビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    './LibraryItemView',
    './SourceItemView',
    './RegionItemView',
    './StationItemView',
    './LibParamItemView',
    './SendRequestView',
    './UserDataView',
    'app/views//AbstractView'
], function(
    $, _, Backbone, JST, Utils,
    LibraryItemView, SourceItemView,   RegionItemView,
    StationItemView, LibParamItemView, SendRequestView,
    UserDataView,    AbstractView
) {

    'use strict';

    return AbstractView.extend({
        el: '#main',
        events: {
            'change #lib_id'         : 'onLibraryChange',
            'change #source_id'      : 'onSourceChange',
            'change #region_id'      : 'onRegionChange',
            'click #open_dialog_btn' : 'onOpenRegisterDialog',
            'click #submit_btn'      : 'onRequestSubmit'
        },
        render : function() {
            this.$el.html(JST['request/requestView']());
            this.renderSelectItem();
            $('#open_dialog_btn').button();
            $('#reset_btn').button();
            $('#submit_btn').button();
            return this;
        },
        renderSelectItem: function() {
            this.libraryItemView  = new LibraryItemView();
            this.sourceItemView   = new SourceItemView();
            this.regionItemView   = new RegionItemView();
            this.stationItemView  = new StationItemView();
            this.libParamItemView = new LibParamItemView();

            this.libraryItemView.render();
            this.sourceItemView.render();
            this.listenTo(this.regionItemView, 'none_select', this.onRegionChange);
        },
        onLibraryChange: function(e) {
            var libId = $('#lib_id option:selected').attr('value');
            if (libId) {
                this.libParamItemView.render();
                if ((libId == wgs.constant.libIdCligen) || (libId == wgs.constant.libIdCdfdm)) {
                    $('#duration_id').val('daily');
                }
            }
        },
        onSourceChange: function(e) {
            this.regionItemView.render();
        },
        onRegionChange: function(e) {
            this.stationItemView.render();
        },
        onOpenRegisterDialog: function(e) {
            e.preventDefault();
            var userDataView = new UserDataView();
            userDataView.render('#userdata_dialog');
        },
        onRequestSubmit: function(e) {
            e.preventDefault();
            var requestForm = '#request_form';
            if (this.isValid(requestForm)) {
                var requestView = new SendRequestView();
                requestView.sendRequest(requestForm);
            }
        },
        isValid: function(id) {
            Utils.setValidator(id, Utils.getValidatorOptions({
                'rules': {
                    data_type_id: {
                        required: true,
                        digits: true,
                        range: [1,2]
                    },
                    range_type_id: {
                        required:  function(element) {
                            if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                                return true;
                            }
                            return false;
                        },
                        rangetype: true
                    }
                }
            }));
            return $(id).valid();
        }
    });
});
