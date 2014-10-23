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
    'app/models/Request',
    'app/views/AbstractView'
], function($, _, Backbone, JST, Utils, Request, AbstractView) {

    'use strict';

    return AbstractView.extend({
        el : '#result_dialog',
        model : Request,
        initialize : function(options) {
            this.model = new Request();
            this.model.url = Utils.getApiUrl('sendRequest');
        },
        sendRequest: function(formId) {
            if ($('#send_request_msg > img.loading').length) {
                return;
            }

            this.nowLoading();
            var that = this;
            this.model.set(Utils.getFormValues(formId));
            this.model.save(null, {
                success : function(model, res) {
                    that.finishLoading();
                    if (res.request) {
                        that.showResultDialog('#result_dialog', res.request.request_id);
                        return;
                    }
                },
                error : function(model, res) {
                    that.finishLoading();
                    alert('送信エラーが発生しました。');
                    return false;
                }
            });
        },
        nowLoading: function() {
            Utils.showLoadingIcon('#send_request_msg');
        },
        finishLoading: function() {
            Utils.removeLoadingIcon('#send_request_msg');
        },
        showResultDialog: function(id, requestId) {
            var message = "リクエストを送信しました。<br>発行されたリクエストIDは<br>「"
                        + _.escape(requestId)
                        + "」です。<br>"
                        + "リクエストIDは実行結果の問い合わせに必要となります。"
            this.genResultDialog(id, requestId);
            $(id).html(message);
            $(id).dialog('open');
        },
        genResultDialog: function(id, requestId) {
            $(id).empty();
            $(id).dialog({
                title: 'リクエスト送信完了',
                bgiframe: true,
                autoOpen: false,
                width: '500px',
                resizable : false,
                closeOnEscape: false,
                modal: true,
                open: function(event, ui){ $(".ui-dialog-titlebar-close").hide();},
                close: function() {
                    $(this).dialog("destroy");
                    $(this).remove();
                },
                buttons: {
                    'OK': function() {
                         $(this).dialog('close');
                         Utils.jumpCheckView({"request_id" : requestId});
                    }
                }
            });
        }
    });
});
