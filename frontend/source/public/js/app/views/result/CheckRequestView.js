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
        el : '#status_elemnt',
        model : Request,
        initialize : function(options) {
            this.model = new Request();
            this.model.url = Utils.getApiUrl('checkRequest');
        },
        checkRequest: function(formId) {
            if ($('#status_elemnt > img.loading').length) {
                return;
            }

            this.$el.empty().append(this.genResponseMessage('リクエスト実行状況確認中...', true));
            $('#download_element').empty();
            Utils.showLoadingIcon(this.el);

            var requestId = $('#request_id').val();
            if (requestId) {
                this.model.fetch({
                    data : {
                        request_id : requestId
                    },
                    dataType : 'json',
                    success : $.proxy(this.fetchSuccess, this)
                });
            }
        },
        fetchSuccess: function(model, resp) {
            this.$el.empty();
            var message = 'リクエスト実行状況を取得できません。';
            switch(model.get('status')) {
                case 'FINISHED':
                    message = 'データ生成が完了しました。';
                    this.trigger('finished', model.get('request_id'));
                    break;
                case 'ACCEPT':
                    message = 'データ生成処理の実行待ちです。';
                    break;
                case 'WAITING':
                    message = 'データ生成処理の実行待ちです。';
                    break;
                case 'RUNNING':
                    message = 'データ生成処理を実行中です。';
                    break;
                case 'TERMINATED':
                    message = 'データ生成処理でエラーが発生しました。';
                    break;
                case 'ERROR':
                    message = 'データ生成処理でエラーが発生しました。';
                    break;
            }
            this.$el.empty().append(this.genResponseMessage(message, false));
        },
        genResponseMessage: function(message, grayOut) {
            var responseMessage = $("<div>").html(message);
            if (grayOut) {
                responseMessage.attr("class", "empty");
            }
            return responseMessage;
        }
    });
});
