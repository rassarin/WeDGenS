-- Project Name : 気象データ生成サービス
-- Date/Time    : 2014/03/18 20:55:39
-- Author       : nobushima
-- RDBMS Type   : PostgreSQL
-- Application  : A5:SQL Mk-2

-- ファイル種別
create table m_file_type (
  file_type_id character varying(64) not null
  , file_type character varying(128) not null
  , constraint m_file_type_PKC primary key (file_type_id)
) ;

-- データ形式
create table m_data_format (
  data_format_id character varying(64) not null
  , data_format character varying(128) not null
  , constraint m_data_format_PKC primary key (data_format_id)
) ;

-- データ種別
create table m_data_type (
  data_type_id integer not null
  , data_type character varying(128) not null
  , data_format_id character varying(64) not null
  , file_type_id character varying(64) not null
  , description text
  , constraint m_data_type_PKC primary key (data_type_id)
) ;

-- アクセスログ
create table t_access_log (
  log_id serial not null
  , date_time timestamp default now() not null
  , ip_addr character varying(48) not null
  , priority integer not null
  , code integer not null
  , user_id character varying(64) default 'anonymous' not null
  , message text not null
  , constraint t_access_log_PKC primary key (log_id)
) ;

-- 実行ログ
create table t_execute_log (
  log_id serial not null
  , date_time timestamp default now() not null
  , request_id character varying(36) not null
  , priority integer not null
  , code integer not null
  , message text not null
  , constraint t_execute_log_PKC primary key (log_id)
) ;

-- リクエストステータス
create table m_request_status (
  request_status_id character varying(16) not null
  , request_status character varying(16) not null
  , constraint m_request_status_PKC primary key (request_status_id)
) ;

-- ジェネレータステータス
create table m_generator_status (
  generator_status_id character varying(16) not null
  , generator_status character varying(16) not null
  , constraint m_generator_status_PKC primary key (generator_status_id)
) ;

-- 利用可能ジェネレータ
create table t_available_generator (
  generator_id serial not null
  , lib_id character varying(64) not null
  , ip_addr character varying(48) not null
  , context_name text not null
  , priority integer default 0 not null
  , generator_status_id character varying(16) default 'UNKNOWN' not null
  , registered_at timestamp default now() not null
  , constraint t_available_generator_PKC primary key (generator_id)
) ;

create unique index t_available_generator_IX1
  on t_available_generator(ip_addr,context_name);

-- ユーザデータ
create table t_user_data (
  user_data_id character varying(36) default uuid_generate_v1mc() not null
  , data_name character varying(256) not null
  , user_name character varying(64) default 'anonymous' not null
  , pub_flag integer default 1 not null
  , registered_at timestamp default now() not null
  , comment text
  , constraint t_user_data_PKC primary key (user_data_id)
) ;

-- 利用可能ライブラリ
create table t_available_lib (
  lib_id character varying(64) not null
  , lib_name character varying(256) not null
  , require_params text not null
  , description text
  , constraint t_available_lib_PKC primary key (lib_id)
) ;

-- リクエスト
create table t_request (
  request_id character varying(36) default uuid_generate_v1mc() not null
  , data_type_id integer not null
  , params text not null
  , user_id character varying(64) default 'anonymous' not null
  , pub_flag integer default 1 not null
  , generator_id integer not null
  , request_status_id character varying(16) not null
  , registered_at timestamp default now() not null
  , executed_at timestamp
  , finished_at timestamp
  , constraint t_request_PKC primary key (request_id)
) ;

alter table m_data_type
  add constraint m_data_type_FK1 foreign key (file_type_id) references m_file_type(file_type_id);

alter table m_data_type
  add constraint m_data_type_FK2 foreign key (data_format_id) references m_data_format(data_format_id);

alter table t_request
  add constraint t_request_FK1 foreign key (data_type_id) references m_data_type(data_type_id);

alter table t_request
  add constraint t_request_FK2 foreign key (generator_id) references t_available_generator(generator_id);

alter table t_request
  add constraint t_request_FK3 foreign key (request_status_id) references m_request_status(request_status_id);

alter table t_available_generator
  add constraint t_available_generator_FK1 foreign key (generator_status_id) references m_generator_status(generator_status_id);

alter table t_available_generator
  add constraint t_available_generator_FK2 foreign key (lib_id) references t_available_lib(lib_id);

comment on table m_file_type is 'ファイル種別';
comment on column m_file_type.file_type_id is 'ファイル種別ID	 METXML：MetXML形式、SERIALIZE：MetSequence Java Serializable形式';
comment on column m_file_type.file_type is 'ファイル種別';

comment on table m_data_format is 'データ形式';
comment on column m_data_format.data_format_id is 'データ形式ID	 POINT：地点、MESH：メッシュ';
comment on column m_data_format.data_format is 'データ形式';

comment on table m_data_type is 'データ種別';
comment on column m_data_type.data_type_id is 'データ種別ID	 1：ユーザ投入データ、2：MetBrokerデータ';
comment on column m_data_type.data_type is 'データ種別';
comment on column m_data_type.data_format_id is 'データ形式';
comment on column m_data_type.file_type_id is 'ファイル種別';
comment on column m_data_type.description is '説明';

comment on table t_access_log is 'アクセスログ';
comment on column t_access_log.log_id is 'アクセスログID';
comment on column t_access_log.date_time is 'アクセス日時';
comment on column t_access_log.ip_addr is 'IPアドレス';
comment on column t_access_log.priority is 'ログプライオリティ';
comment on column t_access_log.code is 'ログコード';
comment on column t_access_log.user_id is 'ユーザID	 将来の認証処理拡張用。当面はanonymousで固定。';
comment on column t_access_log.message is 'メッセージ';

comment on table t_execute_log is '実行ログ';
comment on column t_execute_log.log_id is '実行ログID';
comment on column t_execute_log.date_time is '実行日時';
comment on column t_execute_log.request_id is 'リクエストID';
comment on column t_execute_log.priority is 'ログプライオリティ';
comment on column t_execute_log.code is 'ログコード';
comment on column t_execute_log.message is 'メッセージ';

comment on table m_request_status is 'リクエストステータス';
comment on column m_request_status.request_status_id is 'リクエストステータスID';
comment on column m_request_status.request_status is 'リクエストステータス	 ACCEPT:リクエスト受付、WAITING：実行待ち、RUNNING：実行中、FINISHED：実行完了、TERMINATED：強制終了、ERROR:エラー、CANCEL：実行キャンセル';

comment on table m_generator_status is 'ジェネレータステータス';
comment on column m_generator_status.generator_status_id is 'ジェネレータステータスID';
comment on column m_generator_status.generator_status is 'ジェネレータステータス	 AVAILABLE：利用可能、UNAVAILABLE：利用不能、UNKNOWN：不明';

comment on table t_available_generator is '利用可能ジェネレータ';
comment on column t_available_generator.generator_id is 'ジェネレータID';
comment on column t_available_generator.lib_id is 'ライブラリID';
comment on column t_available_generator.ip_addr is 'ジェネレータIPアドレス';
comment on column t_available_generator.context_name is 'WebAPI コンテキスト名';
comment on column t_available_generator.priority is '優先順位	 数値が少ないほど優先度：高とする。(未使用)';
comment on column t_available_generator.generator_status_id is 'ジェネレータステータスID';
comment on column t_available_generator.registered_at is '登録日時';

comment on table t_user_data is 'ユーザデータ';
comment on column t_user_data.user_data_id is 'ユーザデータID	 UUID Version1(ランダムなマルチキャストMACアドレス使用)。';
comment on column t_user_data.data_name is 'データ名';
comment on column t_user_data.user_name is 'ユーザ名	 将来の認証処理拡張用。当面、ユーザ投入データは「anonymous」とする。システム標準データは、「system」とする。';
comment on column t_user_data.pub_flag is '公開フラグ	 0：非公開、1：公開';
comment on column t_user_data.registered_at is '登録日時';
comment on column t_user_data.comment is 'コメント';

comment on table t_available_lib is '利用可能ライブラリ';
comment on column t_available_lib.lib_id is 'ライブラリID	 CLIGEN：Cligen';
comment on column t_available_lib.lib_name is 'ライブラリ名';
comment on column t_available_lib.require_params is '必要パラメータ	 JSON形式で、ライブラリが必要とするパラメータ一覧を格納する。';
comment on column t_available_lib.description is '説明';

comment on table t_request is 'リクエスト';
comment on column t_request.request_id is 'リクエストID	 UUID Version1(ランダムなマルチキャストMACアドレス使用)';
comment on column t_request.data_type_id is 'データ種別ID	 1：ユーザ投入データ、2：MetBrokerデータ';
comment on column t_request.params is 'パラメータ	 JSON形式で格納。';
comment on column t_request.user_id is 'ユーザID	 将来の認証処理拡張用。当面はanonymousで固定。';
comment on column t_request.pub_flag is '公開フラグ	 0：非公開、1：公開';
comment on column t_request.generator_id is 'ジェネレータID';
comment on column t_request.request_status_id is 'リクエストステータスID';
comment on column t_request.registered_at is 'リクエスト登録日時';
comment on column t_request.executed_at is '処理開始日時';
comment on column t_request.finished_at is '処理完了日時';

