<?php
/**
 * public/index.php
 *
 * フロントコントローラ。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Front
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: index.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * アプリケーションrootディレクトリパス
 */
defined('APP_ROOT_PATH')
    || define('APP_ROOT_PATH', realpath(dirname(dirname(__FILE__))));

/**
 * ZF applicationディレクトリパス
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', APP_ROOT_PATH . '/application');

/**
 * アプリケーション実行環境取得
 */
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/**
 * アプリケーション設定ディレクトリパス
 */
defined('APP_CONFIG_PATH')
    || define('APP_CONFIG_PATH', APPLICATION_PATH . '/configs');

/**
 * アプリケーション環境別設定ディレクトリパス
 */
defined('APP_ENV_CONFIG_PATH')
    || define('APP_ENV_CONFIG_PATH', APP_CONFIG_PATH . '/' . APPLICATION_ENV);

/**
 * インクルードパス設定
 */
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APP_ROOT_PATH . '/library'),
    get_include_path(),
)));

/**
 * Zend Frameworkモジュール
 */
require_once 'Zend/Application.php';

/**
 *  アプリケーション初期化
 */
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

/**
 * アプリケーションブートストラップ
 */
$application->bootstrap();

/**
 * アプリケーション開始
 */
$application->run();
