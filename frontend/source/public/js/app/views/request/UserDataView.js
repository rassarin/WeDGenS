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
        el : '#upload_result_dialog',
        render: function(id) {
            $(id).empty();
            $(id).append(JST['request/userDataView']());
            this.genUserDataDialog(id);
            $(id).dialog('open');
        },
        registerUserData: function(formId) {
            this.nowLoading();

            var that  = this;
            var query = this.genFormData(formId);
            $.ajax({
                type:"post",
                url: Utils.getApiUrl('registerData'),
                data :query,
                dataType: 'json',
                processData : false,
                contentType : false,
                success: function(res){
                    that.finishLoading();
                    if (res.error) {
                        alert(_.escape(res.error));
                        return;
                    }
                    var registerStatus = res.user_data.register_status;
                    if (registerStatus != 'SUCCESS') {
                        alert('登録に失敗しました。');
                        return;
                    }
                    var userDataId = res.user_data.user_data_id;
                    that.showUpdateResultDialog('#upload_result_dialog', userDataId);
                },
                error: function(){
                    that.finishLoading();
                    alert('送信エラーが発生しました。');
                    return false;
                }
            });
        },
        nowLoading: function() {
            Utils.showLoadingIcon('#upload_msg');
        },
        finishLoading: function() {
            Utils.removeLoadingIcon('#upload_msg');
        },
        genFormData: function(formId) {
            var registerData = new FormData();
            registerData.append('xml_file',  $('#xml_file').prop("files")[0]);
            registerData.append('data_name', $('#data_name').val());
            registerData.append('comment',   $('#comment').val());
            return registerData;
        },
        showUpdateResultDialog: function(id, userDataId) {
            this.genUpdateResultDialog(id, userDataId);
            $(id).dialog('open');
        },
        genUpdateResultDialog: function(id, userDataId) {
            var that = this;
            $(id).empty();
            $(id).dialog({
                title: 'ユーザデータアップロード完了',
                bgiframe: true,
                autoOpen: false,
                width: '500px',
                resizable : false,
                closeOnEscape: false,
                modal: true,
                open:function(event, ui){ $(".ui-dialog-titlebar-close").hide();},
                close: function() {
                    $(this).dialog("destroy");
                    $(this).empty();
                },
                buttons: {
                    'OK': function() {
                         that.onCloseResultDialog(id, userDataId);
                    }
                }
            });
            var message = "ユーザデータをアップロードしました。<br>発行されたユーザデータIDは<br>「"
                        + _.escape(userDataId)
                        + "」です。";
            $(id).html(message);
        },
        genUserDataDialog: function(id) {
            var that = this;
            $(id).dialog({
                title: '気象データ生成サービス',
                bgiframe: true,
                autoOpen: false,
                width: '640px',
                resizable : false,
                closeOnEscape: false,
                modal: true,
                open:function(event, ui){ $(".ui-dialog-titlebar-close").hide();},
                close: function() {
                    $(this).dialog("destroy");
                    $(this).empty();
                },
                buttons: {
                    'キャンセル': function() {
                        $(this).dialog('close');
                    },
                    'アップロード': function() {
                        if ($('#upload_msg > img.loading').length) {
                            return;
                        }
                        var uploadForm = '#upload_form';
                        if (that.isValid(uploadForm)) {
                            that.registerUserData(uploadForm);
                        }
                    }
                }
            });
        },
        onCloseResultDialog: function(id, userDataId) {
            $(id).dialog('close');
            $(id).empty();
            $('#userdata_dialog').dialog('close');
            $('#user_data_id').val(userDataId);
            $('#userdata_dialog').empty();
        },
        isValid: function(id) {
            Utils.setValidator(id, Utils.getValidatorOptions());
            return $(id).valid();
        }
    });
});
