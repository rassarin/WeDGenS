/**
 * @fileOverview リクエストビュークラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * リクエストビュークラス
 */
define([
    'jquery',
    'underscore',
    'backbone',
    'app/templates/jst',
    'app/utils/Utils',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#tmp_box',
        checkDownload : function() {
            if ($('#download_msg > img.loading').length) {
                return;
            }
            this.$el.empty();

            var requestId = $('#selected_request_id').val();
            var formatId  = $("input[name='format']:checked").val();

            if (requestId && formatId) {
                Utils.showLoadingIcon('#download_msg');
                var query = {
                    'request_id' : requestId,
                    'format'     : formatId,
                    'check_only' : 1
                };
                var that = this;
                $.ajax({
                    type:"post",
                    url: Utils.getApiUrl('getResult'),
                    data :query,
                    dataType: 'json',
                    success: function(res){
                        if (res.response == 'SUCCESS') {
                            that.doDownload();
                            return;
                        }
                        $('#download_msg').empty();
                    },
                    error: function(){
                        alert('ダウンロードが失敗しました。');
                        $('#download_msg').empty();
                    }
                });
            }
        },
        doDownload : function() {
            if ($('#tmpframe').length) {
                return this;
            }

            var method = "POST";
            var url    = wgs.constant.apiBaseUrl + wgs.constant.getResult;

            var iframe = $("<iframe/>").attr('id',    'tmpframe')
                                       .attr('name',  'tmpframe')
                                       .attr('src',   'about:blank')
                                       .css('display','none')
                                       .appendTo(this.$el);
            iframe.on('load', _.bind(this.onDownloaded, this));

            var hiddenBox = $('<div/>').attr('id',    'downloader')
                                       .css('display','none')
                                       .appendTo(iframe);

            var tmpForm   = $('<form/>').attr('method', method)
                                        .attr('action', url)
                                        .attr('target', 'tmpframe')
                                        .attr('id',     'tmp_form')
                                        .appendTo(hiddenBox);

            _.each(this.getParameters(), function(param) {
                $('<input/>').attr('type', 'hidden')
                             .attr('name', _.escape(param.name))
                             .val(_.escape(param.value))
                             .appendTo(tmpForm);
            });
            if (this.isValid('#tmp_form')) {
                tmpForm.submit();
                $('#download_msg').empty();
            }
            return this;
        },
        getParameters: function() {
            var requestId = $('#selected_request_id').val();
            var formatId  = $("input[name='format']:checked").val();
            var params    = [];
            if (requestId && formatId) {
                params = [
                    { name : 'request_id', value : requestId },
                    { name : 'format',     value : formatId }
                ];
            }
            return params;
        },
        isValid: function(id) {
            Utils.setValidator(id, Utils.getValidatorOptions({
                'rules': {
                    format: {
                        required: true,
                        formattype: true
                    }
                }
            }));
            return $(id).valid();
        },
        onDownloaded: function(e) {
            $('#download_msg').empty();
            this.$el.empty();
        }
    });
});
