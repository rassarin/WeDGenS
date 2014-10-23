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
    './CheckRequestView',
    './ResultView',
    'app/views/AbstractView'
], function(
    $, _, Backbone, JST, Utils,
    CheckRequestView,
    ResultView,
    AbstractView
) {

    'use strict';

    return AbstractView.extend({
        el: '#main',
        events: {
            'click #submit_btn' : 'onCheckSubmit'
        },
        render : function() {
            this.$el.html(JST['result/checkView']());
            $('#reset_btn').button();
            $('#submit_btn').button();
            return this;
        },
        onCheckSubmit: function(e) {
            e.preventDefault();
            var checkForm = '#check_form';
            if (this.isValid(checkForm)) {
                this.checkView = new CheckRequestView();
                this.checkView.checkRequest(checkForm);
                this.checkView.on('finished', function(requestId) {
                    this.resultView = new ResultView();
                    this.resultView.render(requestId);
                });
            }
        },
        isValid: function(id) {
            Utils.setValidator(id, Utils.getValidatorOptions());
            return $(id).valid();
        }
    });
});
