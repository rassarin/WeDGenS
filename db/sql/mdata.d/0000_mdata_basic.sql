--
-- Data for Name: m_request_status; Type: TABLE DATA; Schema: public; Owner: wgs
--
COPY m_request_status (request_status_id, request_status) FROM stdin;
ACCEPT	リクエスト受付
WAITING	実行待ち
RUNNING	実行中
FINISHED	実行完了
CANCEL	実行キャンセル
TERMINATED	強制終了
ERROR	エラー
\.

--
-- Data for Name: m_generator_status; Type: TABLE DATA; Schema: public; Owner: wgs
--
COPY m_generator_status (generator_status_id, generator_status) FROM stdin;
AVAILABLE	利用可能
UNAVAILABLE	利用不能
UNKNOWN	不明
\.

--
-- Data for Name: m_file_type; Type: TABLE DATA; Schema: public; Owner: wgs
--
COPY m_file_type (file_type_id, file_type) FROM stdin;
METXML	MetXML形式
SERIALIZE	MetSequence Java Serializable形式
\.

-- Data for Name: m_data_format; Type: TABLE DATA; Schema: public; Owner: wgs
--
COPY m_data_format (data_format_id, data_format) FROM stdin;
POINT	地点
MESH	メッシュ
\.

-- Data for Name: m_data_type; Type: TABLE DATA; Schema: public; Owner: wgs
--
COPY m_data_type (data_type_id, data_type, data_format_id, file_type_id) FROM stdin;
1	ユーザ投入データ	POINT	METXML
2	MetBrokerデータ	POINT	SERIALIZE
\.

