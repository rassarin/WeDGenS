--
-- Data for Name: t_available_lib; Type: TABLE DATA; Schema: public; Owner: wgs
--
INSERT INTO t_available_lib (lib_id, lib_name, require_params) VALUES (
1, 'CLIGEN',
'{
    "climate_data": {
        "required" : [
            "airtemperature",
            "rain"
        ],
        "use_duration": "daily"
    },
    "parameters": [
        {
            "id" : "start_year",
            "format" : "required_year",
            "type" : "text",
            "label" : "生成開始年",
            "validate" : {
                "required" : true,
                "digits" : true,
                "maxlength" : 4,
                "minlength" : 4
            },
            "description" : "CLIGENによる生成開始年を指定する。"
        },
        {
            "id" : "num_of_year",
            "format" : "required_range10",
            "type" : "select",
            "label" : "生成年数",
            "validate" : {
                "required" : true,
                "digits" : true,
                "range" : [1, 10]
            },
            "items" : [
                {"key": "0",  "value" : "生成年数を選択"},
                {"key": "1",  "value" : "1年"},
                {"key": "2",  "value" : "2年"},
                {"key": "3",  "value" : "3年"},
                {"key": "4",  "value" : "4年"},
                {"key": "5",  "value" : "5年"},
                {"key": "6",  "value" : "6年"},
                {"key": "7",  "value" : "7年"},
                {"key": "8",  "value" : "8年"},
                {"key": "9",  "value" : "9年"},
                {"key": "10", "value" : "10年"}
            ],
            "description" : "CLIGENによる生成年数を指定する。"
        },
        {
            "id" : "climate_id",
            "format" : "climate",
            "type" : "checkbox",
            "label" : "生成する気候データ",
            "validate" : {
                "required" : true,
                "climate" : true
            },
            "items" : [
                {"key": "rain",             "value" : "雨量"},
                {"key": "airtemperature",   "value" : "気温"},
                {"key": "wind",             "value" : "風速"},
                {"key": "radiation",        "value" : "日射"}
            ],
            "description" : "CLIGENで生成する気候データを指定する。"
        }
    ]
}');

INSERT INTO t_available_lib (lib_id, lib_name, require_params) VALUES (
2, 'cdfdm',
'{
    "climate_data": {
        "optional" : [
            "airtemperature",
            "rain",
            "wind"
        ],
        "use_duration": "daily"
    },
    "parameters": [
        {
            "id" : "model_id",
            "format" : "required_alphanum",
            "type" : "select",
            "label" : "使用するモデル",
            "validate" : {
                "required" : true,
                "alphanum" : true
            },
            "items" : [
                {"key": "", "value" : "使用するモデルを選択"},
                {"key": "01-month-lead", "value" : "モデル1"},
                {"key": "10-month-lead", "value" : "モデル2"}
            ],
            "description" : "cdfdmで使用するモデルを指定する。"
        },
        {
            "id" : "set_id",
            "format" : "required_alphanum",
            "type" : "select",
            "label" : "使用するセット",
            "validate" : {
                "required" : true,
                "alphanum" : true
            },
            "parent_item" : "model_id",
            "relational_items" : {
                "01-month-lead" : [
                    {"key": "", "value" : "使用するセットを選択"},
                    {"key": "en1", "value" : "セット1"},
                    {"key": "en2", "value" : "セット2"},
                    {"key": "en3", "value" : "セット3"},
                    {"key": "en4", "value" : "セット4"},
                    {"key": "en5", "value" : "セット5"},
                    {"key": "en6", "value" : "セット6"},
                    {"key": "en7", "value" : "セット7"},
                    {"key": "en8", "value" : "セット8"},
                    {"key": "en9", "value" : "セット9"}
                ],
                "10-month-lead" : [
                    {"key": "", "value" : "使用するセットを選択"},
                    {"key": "en1", "value" : "セット1"},
                    {"key": "en2", "value" : "セット2"},
                    {"key": "en3", "value" : "セット3"},
                    {"key": "en4", "value" : "セット4"},
                    {"key": "en5", "value" : "セット5"},
                    {"key": "en6", "value" : "セット6"},
                    {"key": "en7", "value" : "セット7"},
                    {"key": "en8", "value" : "セット8"},
                    {"key": "en9", "value" : "セット9"}
                ]
            },
            "description" : "cdfdmで使用するモデルのセットを指定する。"
        },
        {
            "id" : "start_year",
            "format" : "year",
            "type" : "text",
            "label" : "生成開始年",
            "validate" : {
                "required" : false,
                "digits" : true,
                "min" : 1982,
                "max" : 2013,
                "maxlength" : 4,
                "minlength" : 4
            },
            "description" : "cdfdmによる生成開始年を指定する。"
        },
        {
            "id" : "stop_year",
            "format" : "year",
            "type" : "text",
            "label" : "生成終了年",
            "validate" : {
                "required" : false,
                "digits" : true,
                "min" : 1982,
                "max" : 2013,
                "maxlength" : 4,
                "minlength" : 4
            },
            "description" : "cdfdmによる生成終了年を指定する。"
        },
        {
            "id" : "climate_id",
            "format" : "climate",
            "type" : "checkbox",
            "label" : "生成する気候データ",
            "validate" : {
                "required" : true,
                "climate" : true
            },
            "items" : [
                {"key": "rain",           "value" : "雨量"},
                {"key": "airtemperature", "value" : "気温"},
                {"key": "wind",           "value" : "風速"}
            ],
            "description" : "cdfdmで生成する気候データを指定する。"
        }
    ]
}');


