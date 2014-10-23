define(function(){

this["JST"] = this["JST"] || {};

this["JST"]["appView"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div id="navi" class="panel">\n<ul id="menu" class="clearfix">\n<li><a id="request_menu" href="">リクエスト登録</a></li>\n<li><a id="download_menu" href="" class="last">実行結果ダウンロード</a></li>\n</ul>\n</div>\n<div id="main" class="panel"></div>';

}
return __p
};

this["JST"]["request/filteringSelect"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<script>\n$(function () {\nvar items = ';
 print(JSON.stringify(child_items)) ;
__p += ';var parentId = \'#' +
__e( parent_id ) +
'\';var childId  = \'#' +
__e( child_id ) +
'\';$(parentId).change(function(){var selected = $(parentId + \' option:selected\').attr(\'value\');if (selected && selected.length) {$(childId).empty();var updateItems = items[selected];_.each(updateItems, function(item) {var optionItem = $("<option>").attr("value", _.escape(item.key))\n.html(_.escape(item.value));$(childId).append(optionItem);});}});\n});\n</script>';

}
return __p
};

this["JST"]["request/libraryList"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<option value="' +
((__t = ( lib_id )) == null ? '' : __t) +
'">' +
((__t = ( lib_name )) == null ? '' : __t) +
'</option>';

}
return __p
};

this["JST"]["request/paramList"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class="element">\n';

var formElement = null;
var container   = $("<div>");var labelObj = $("<label>").html(_.escape(label) + '：');
container.append(labelObj);
switch(type) {
case 'checkbox':
formElement = $("<div>").attr("class", "element indent2em");
_.each(items, function(item) {
var checkboxItem = $("<input>").attr("type",  _.escape(type))
.attr("id",   _.escape(id) + "_" + _.escape(item.key))
.attr("name", _.escape(id) + '[]')
.attr("value", _.escape(item.key));
if (format) {
checkboxItem.attr("class",  'validate-' + _.escape(format));
}var checkboxLabel = $("<span>").html(_.escape(item.value) + ' ')
formElement.append(checkboxItem)
.append(checkboxLabel);
});
break;
case 'select':
formElement = $("<select>").attr("id",   _.escape(id))
.attr("name", _.escape(id));
if (items) {
_.each(items, function(item) {
var optionItem = $("<option>").attr("value", _.escape(item.key))
.html(_.escape(item.value));
formElement.append(optionItem);
});
}
if (relational_items) {
var optionItem = $("<option>").attr("value", "")
.attr("class", "empty")
.html(_.escape(label) + 'を選択');
formElement.append(optionItem);
}
if (format) {
formElement.attr("class",  'validate-' + _.escape(format));
}
break;
case 'text':
formElement = $("<input>").attr("type",  _.escape(type))
.attr("id",   _.escape(id))
.attr("name", _.escape(id));
if (validate.maxlength) {
formElement.attr("maxlength",  _.escape(validate.maxlength));
}
if (validate.minlength) {
formElement.attr("minlength",  _.escape(validate.minlength));
}
if (format) {
formElement.attr("class",  'validate-' + _.escape(format));
}
break;
}
if (formElement) {
container.append(formElement);
}
print(container.html());
;
__p += '\n</div>';

}
return __p
};

this["JST"]["request/regionList"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<option value="' +
((__t = ( region_id )) == null ? '' : __t) +
'">' +
((__t = ( region_name )) == null ? '' : __t) +
'</option>';

}
return __p
};

this["JST"]["request/requestView"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div id="request_panel" class="panel">\n<form id="request_form" method="POST"><table class="form centering">\n<caption>リクエスト登録</caption>\n<tr>\n<th>ライブラリ選択</th>\n<td>\n<div class="element" id="lib_id_elemnt">\n<select id="lib_id" name="lib_id" class="validate-library"></select><span id="lib_id_msg" class="loading"></span>\n</div>\n</td>\n</tr>\n<tr>\n<th>入力データ選択</th>\n<td>\n<fieldset>\n<legend><input type="radio" id="data_type_id_metbroker" name="data_type_id" value="2">MetBrokerデータ</legend>\n<div class="element" id="source_id_elemnt">\n<label>データソースID：</label>\n<select id="source_id" name="source_id" class="validate-source"></select>\n<span id="source_id_msg" class="loading"></span>\n</div>\n<div class="element">\n<label>観測年(開始)-(終了)：</label>\n<input type="text" id="begin_year" name="begin_year" size="8" maxlength="4" class="validate-year"> -\n<input type="text" id="end_year"   name="end_year"   size="8" maxlength="4" class="validate-year">\n</div>\n<div class="element">\n<label>デュレーション：</label>\n<select id="duration_id" name="duration_id" class="validate-duration">\n<option value="daily">日別</option>\n<option value="hourly">時別</option>\n</select>\n</div>\n<div class="element">\n<label>地点選択：</label>\n<div class="element indent2em">\n<fieldset>\n<legend><input type="radio" name="range_type_id" value="point">地点指定</legend>\n<div class="element" id="region_id_elemnt">\n<label>リージョン：</label>\n<select id="region_id" name="region_id" class="validate-region">\n<option value="" class="empty">リージョンを選択</option>\n</select>\n<span id="region_id_msg" class="loading"></span>\n</div>\n<div class="element" id="station_id_elemnt">\n<label>ステーション：</label>\n<select id="station_id" name="station_id" class="validate-station">\n<option value="" class="empty">ステーションを選択</option>\n</select>\n<span id="station_id_msg" class="loading"></span>\n</div>\n</fieldset>\n<fieldset>\n<legend><input type="radio" name="range_type_id" value="area">エリア指定</legend>\n<div class="element">\n<label>北西頂点(緯度, 経度)：</label>\n(<input type="text" name="nw_lat" size="10" maxlength="36" class="validate-latlong"> , <input type="text" name="nw_lon" size="10" maxlength="36" class="validate-latlong">)\n</div>\n<div class="element">\n<label>南東頂点(緯度, 経度)：</label>\n(<input type="text" name="se_lat" size="10" maxlength="36" class="validate-latlong"> , <input type="text" name="se_lon" size="10" maxlength="36" class="validate-latlong">)\n</div>\n</fieldset>\n</div>\n</div>\n</fieldset>\n<fieldset>\n<legend><input type="radio" id="data_type_id_userdata" name="data_type_id" value="1">ユーザデータ</legend>\n<div class="element">\n<label>ユーザデータID：</label><input id="user_data_id" type="text" name="user_data_id" size="36" maxlength="36" class="validate-user_data_id">\n<button id="open_dialog_btn">データ登録画面を開く</button>\n</div>\n</fieldset>\n</td>\n</tr>\n<tr>\n<th>パラメータ入力</th>\n<td>\n<div class="element" id="lib_params">ライブラリを選択してください。</div>\n<div class="element" id="lib_params_msg" class="loading"></div>\n</td>\n</tr>\n</table>\n<div class="button_panel">\n<input id="reset_btn"  class="width100px" type="reset"  name="reset"  value="リセット">\n<input id="submit_btn" class="width200px" type="submit" name="submit" value="リクエスト登録">\n<span id="send_request_msg" class="loading"></span>\n</div>\n</form>\n</div>\n<div id="userdata_dialog"></div>\n<div id="result_dialog"></div>\n<div id="upload_result_dialog"></div>\n<div id="alert_dialog"></div>';

}
return __p
};

this["JST"]["request/sourceList"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<option value="' +
((__t = ( source_id )) == null ? '' : __t) +
'">' +
((__t = ( source_name )) == null ? '' : __t) +
'</option>';

}
return __p
};

this["JST"]["request/stationList"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<option value="' +
((__t = ( station_id )) == null ? '' : __t) +
'">' +
((__t = ( station_name )) == null ? '' : __t) +
'</option>';

}
return __p
};

this["JST"]["request/userDataView"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div id="user_data_panel" class="panel">\n<form id="upload_form" enctype="multipart/form-data" method="POST">\n<table class="form centering">\n<caption>データ登録</caption>\n<tr>\n<th>アップロードファイル選択</th>\n<td>\n<div class="element" id="request_id_elemnt">\n<input type="file" name="xml_file" id="xml_file" class="validate-xml_file">\n<span id="upload_msg" class="loading"></span>\n</div>\n</td>\n</tr>\n<tr>\n<th>ユーザデータ情報入力</th>\n<td>\n<div class="element" id="request_id_elemnt">\n<label>データ名：</label><input type="text" name="data_name" id="data_name" value="" class="validate-data_name">\n</div>\n<div class="element" id="request_id_elemnt">\n<label>コメント：</label><input type="text" name="comment" id="comment" value="" class="validate-comment">\n</div>\n</td>\n</tr>\n</table>\n</form>\n</div>';

}
return __p
};

this["JST"]["result/checkView"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div id="result_panel" class="panel">\n<form id="check_form" method="POST"><table class="form centering">\n<caption>リクエスト実行結果ダウンロード</caption>\n<tr>\n<th>リクエストID入力</th>\n<td>\n<div class="element" id="request_id_elemnt">\n<input id="request_id" type="text" name="request_id" size="40" maxlength="36" class="validate-uuid validate-required">\n</div>\n</td>\n</tr>\n<tr>\n<th>実行状況</th>\n<td>\n<div class="element" id="status_elemnt">リクエストIDを入力してください。</div>\n<div class="element" id="download_element"></div>\n</td>\n</tr>\n</table>\n<div class="button_panel">\n<input id="reset_btn"  class="width100px" type="reset"  name="reset"  value="リセット">\n<input id="submit_btn" class="width200px" type="submit" name="submit" value="実行結果問い合わせ">\n</div>\n</form>\n</div>\n<div id="tmp_box"></div>';

}
return __p
};

this["JST"]["result/downloadView"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="element" id="format_elemnt">\n<input type="hidden" id="selected_request_id" name="selected_request_id" value="' +
__e( request_id ) +
'" class="validate-uuid validate-required">\n<input type="radio"  id="format_xml"   name="format" value="xml">XML\n<input type="radio"  id="format_csv"   name="format" value="csv">CSV\n<input type="radio"  id="format_html"  name="format" value="html">表(HTML)\n<input type="radio"  id="format_html"  name="format" value="chart">グラフ(HTML)\n<input type="radio"  id="format_zip"   name="format" value="zip">XML+XSL\n<button id="download_btn">ダウンロード</button>\n<span id="download_msg" class="loading"></span>\n</div>';

}
return __p
};

  return this["JST"];

});