/**
 * @fileOverview ユーティリティメソッド定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ユーティリティメソッド
 */
define([
    'jquery',
    'underscore',
    'jquery.ui',
    'jquery.validate',
],
function ($, _) {

    'use strict';

    return {
        getApiUrl: function (action) {
            var url = wgs.constant.apiBaseUrl
                    + wgs.constant[action];
            return url;
        },
        clearForm: function () {
            var formElement = 'input[type="text"], input[type="radio"], input[type="checkbox"], select';
            $(formElement).val("")
                          .removeAttr('checked')
                          .removeAttr('selected');
        },
        getFormValues: function(formId) {
            var formData = {};
            _.each($(formId).serializeArray(), function(element) {
                var key = element.name;
                if (/\[\]$/.test(key)) {
                    var multiKey = key.replace(/\[\]$/,"");
                    if (!_.has(formData, multiKey)) {
                        formData[multiKey] = new Array();
                    }
                   	formData[multiKey].push(element.value);
                } else {
                    formData[key] = element.value;
                }
            });
            return formData;
        },
        genAlertDialog: function(id) {
            $(id).empty();
            $(id).dialog({
                title: 'エラー',
                bgiframe: true,
                autoOpen: false,
                width: '300px',
                resizable : false,
                closeOnEscape: false,
                modal: true,
                open:function(event, ui){ $(".ui-dialog-titlebar-close").hide();},
                buttons: {
                    '確認': function() {
                         $(this).dialog('close');
                    }
                }
            });
        },
        genEmptyOptionItem: function(message) {
            if (!message) {
                message = '';
            }
            var emptyItem = $("<option>").attr("value", "")
                                         .attr("class", "empty")
                                         .html(message);
            return emptyItem;
        },
        showAlertDialog: function(id, message) {
            this.genAlertDialog(id);
            $(id).html(_.escape(message));
            $(id).dialog('open');
        },
        showLoadingIcon: function(id) {
            var loadingIcon = $("<img>").attr("src", wgs.constant.imgBaseUrl + 'loader.gif')
                                        .attr("alt",   "loading now")
                                        .attr("class", "loading");
            $(id).append(loadingIcon);
        },
        removeLoadingIcon: function(id) {
            $(id).empty();
        },
        disableButton: function(id) {
            $(id).attr('disabled', true);
        },
        enableButton: function(id) {
            $(id).removeAttr('disabled');
        },
        jumpCheckView: function(params) {
            var url = 'download';
            if (params.request_id) {
                url += '/' + _.escape(params.request_id);
            }
            Backbone.history.navigate(url, true);
        },
        setValidator: function(id, options) {
            $(id).validate(options);
        },
        initValidator: function() {
            _.extend($.validator.messages, {
                required: "必須項目です。",
                maxlength: jQuery.format("{0} 文字以下を入力してください。"),
                minlength: jQuery.format("{0} 文字以上を入力してください。"),
                rangelength: jQuery.format("{0} 文字以上 {1} 文字以下で入力してください。"),
                email: "メールアドレスを入力してください。",
                url: "URLを入力してください。",
                date: "有効な日付を入力してください。",
                dateISO: "日付を入力してください。",
                number: "有効な数字を入力してください。",
                digits: "数値(0-9)で入力してください。",
                equalTo: "同じ値を入力してください。",
                range: jQuery.format(" {0} から {1} までの値を入力してください。"),
                max: jQuery.format("{0} 以下の値を入力してください。"),
                min: jQuery.format("{0} 以上の値を入力してください。"),
                creditcard: "クレジットカード番号を入力してください。"
            });

            $.validator.addMethod("alphanum", function(value, element) {
                return this.optional(element) || /^[0-9A-Za-z\-_]+$/.test(value);
            }, "利用可能な文字は英数字、と記号(_, -)のみです。");
            $.validator.addMethod("duration", function(value, element) {
                return this.optional(element) || /^(daily|hourly)$/.test(value);
            }, "日別と時間別のみ選択可能です。");
            $.validator.addMethod("rangetype", function(value, element) {
                return this.optional(element) || /^(point|area|mesh|user)$/.test(value);
            }, "地点選択に誤りがあります。");
            $.validator.addMethod("uuid", function(value, element) {
                return this.optional(element) || /^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/.test(value);
            }, "入力可能なIDはUUID形式のみです。");
            $.validator.addMethod("formattype", function(value, element) {
                return this.optional(element) || /^(xml|csv|html)$/.test(value);
            }, "XML、CSV、表(HTML)のみ選択可能です。");
            $.validator.addMethod("climate", function(value, element) {
                return this.optional(element) || /^[0-9A-Za-z]$/.test(value);
            }, "利用可能な文字は英数字のみです。");
            $.validator.addMethod("climate", function(value, element) {
                return this.optional(element) || /^(rain|airtemperature|wind|radiation|humidity|soiltemperature|watertemperature|leafwetness|brightsunlight)$/.test(value);
            }, "気候データ選択に誤りがあります。");

            for (var className in this.getDefaultValidatorRules) {
                $.validator.addClassRules(
                    'validate-' + className,
                    this.getDefaultValidatorRules[className]
                );
            }
        },
        getValidatorOptions: function(options) {
            options = options || {};
            var stateError = "ui-state-error";
            var validationOptions = {
                errorElement: "em",
                success: "success",
                errorClass: "error",
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass(stateError).removeClass(validClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass(stateError).addClass(validClass);
                },
                errorPlacement: function(error, element) {
                    if (element.is(':visible')) {
                        element.attr('title', error.text()).tooltip({
                            tooltipClass: "ui-state-error error_tip",
                            position: {
                                my: 'left bottom',
                                at: 'right top'
                            },
                            open: function(){
                                element.delay(3000).queue(function() {
                                    element.tooltip("close");
                                });
                            }
                        }).tooltip("open");
                    }
                }
            };
            _.extend(validationOptions, options);
            return validationOptions;
        },
        getDefaultValidatorRules: {
            required: {
                required: true,
            },
            data_type: {
                required: true,
                digits: true,
                range: [1,2]
            },
            range_type: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        return true;
                    }
                    return false;
                },
                rangetype: true
            },
            library: {
                required: true,
                digits: true
            },
            source: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        return true;
                    }
                    return false;
                },
                alphanum: true
            },
            region: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        if ($("input[name=range_type_id]:checked").val() == wgs.constant.rangeTypePoint) {
                            return true;
                        }
                    }
                    return false;
                },
                alphanum: true
            },
            station: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        if ($("input[name=range_type_id]:checked").val() == wgs.constant.rangeTypePoint) {
                            return true;
                        }
                    }
                    return false;
                },
                alphanum: true
            },
            duration: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        return true;
                    }
                    return false;
                },
                duration: true
            },
            year: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        return true;
                    }
                    return false;
                },
                digits: true,
                maxlength: 4,
                minlength: 4
            },
            latlong: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeMetBroker) {
                        if ($("input[name=range_type_id]:checked").val() == wgs.constant.rangeTypeArea) {
                            return true;
                        }
                    }
                    return false;
                },
                number: true
            },
            user_data_id: {
                required:  function(element) {
                    if ($("input[name=data_type_id]:checked").val() == wgs.constant.dataTypeUserData) {
                        return true;
                    }
                    return false;
                },
                uuid: true
            },
            uuid: {
                uuid: true
            },
            xml_file: {
                required: true
            },
            data_name: {
                required: true,
                minlength: 1,
                maxlength: 256
            },
            comment: {
                maxlength: 1024
            },
            format: {
                formattype: true
            },
            required_year: {
                required: true,
                digits: true,
                maxlength: 4,
                minlength: 4
            },
            required_range10: {
                required: true,
                digits: true,
                range: [1,10]
            },
            required_alphanum: {
                required: true,
                alphanum: true
            },
            climate: {
                climate: true
            }
        }
    };
});
