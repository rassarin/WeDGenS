<?php
/**
 * library/App/Controller/Action.php
 *
 * アプリケーション基底アクションコントローラ。
 *
 * 各アクションコントローラの共通処理を実装する基底クラス。
 * 各アクションコントローラは本クラスを継承する。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Action.php 34 2014-03-05 12:39:08Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action
*/

/**
 * App_Controller_Action クラス
 *
 * アプリケーション基底アクションコントローラ定義クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Controller_Action extends Zend_Controller_Action
{
    // ---------------------------------------------------------------------- //

    /**
     * キャッシュ有効期限
     */
    const CONFIG_CACHE_LIFETIME = 7200;

    /**
     * キャッシュIDデフォルト値
     */
    const DEFAULT_CACHE_ID = '20140220';

    // ---------------------------------------------------------------------- //

    /**
     * コントローラの初期化。
     *
     * @return void
    */
    public function init()
    {
        // 共通初期化設定
    }

    // ---------------------------------------------------------------------- //

    /**
     * サービスの取得
     *
     * @param string $serviceName サービス名
     * @return App_Service サービスクラスインスタンス
    */
    public function getService($serviceName)
    {
        return $this->_helper->service->$serviceName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * コントローラ別アクションヘルパーの接頭辞の取得
     *
     * @return string コントローラ別アクションヘルパーの接頭辞
    */
    public function getControllerHelperPrefix()
    {
        $moduleName = $this->getRequest()->getModuleName();
        if ($moduleName == 'default') {
            $moduleName = '';
        }
        $actionName   = $this->getRequest()->getControllerName();
        $helperPrefix = $moduleName . '-' . $actionName;
        return App_Utils::dashToCamelCase($helperPrefix);
    }

    // ---------------------------------------------------------------------- //

    /**
     * ACLへルパーの取得
     *
     * @return App_Controller_Action_Helper_Acl ACLへルパーインスタンス
    */
    public function getAclHelper()
    {
        $helperPrefix = $this->getControllerHelperPrefix();
        $helperName   = $helperPrefix . 'Acl';

        return $this->_helper->$helperName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * バリデータへルパーの取得
     *
     * @return App_Controller_Action_Helper_Validator バリデータへルパーインスタンス
    */
    public function getValidatorHelper()
    {
        $helperPrefix = $this->getControllerHelperPrefix();
        $helperName   = $helperPrefix . 'Validator';

        return $this->_helper->$helperName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * 制約へルパーの取得
     *
     * @return App_Controller_Action_Helper_Constraint 制約へルパーインスタンス
    */
    public function getConstraintHelper()
    {
        $helperPrefix = $this->getControllerHelperPrefix();
        $helperName   = $helperPrefix . 'Constraint';

        return $this->_helper->$helperName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ログへルパーの取得
     *
     * @return App_Controller_Action_Helper_Log ログへルパーインスタンス
    */
    public function getLogHelper()
    {
        $helperPrefix = $this->getControllerHelperPrefix();
        $helperName   = $helperPrefix . 'Log';

        return $this->_helper->$helperName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * アプリケーション設定の取得
     *
     * @param string $key 項目名
     * @return mixed 設定値
    */
    public function getConfig($key = null)
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options   = $bootstrap->getOptions();
        if (!is_null($key)) {
            return $options[$key];
        }
        return $options;
    }

    // ---------------------------------------------------------------------- //

    /**
     * データベースアダプタの取得
     *
     * @param string $dbName DB設定名
     * @return Zend_Db_Adapter データベースアダプタ
    */
    public function getDbAdapter($dbName = null)
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $resource  = $bootstrap->getPluginResource('multidb');
        if (!is_null($dbName)) {
            return $resource->getDb($dbName);
        }
        return $resource->getDefaultDb();
    }

    // ---------------------------------------------------------------------- //

    /**
     * 指定モジュールのブートストラップの取得
     *
     * @param stirng $module モジュール名
     * @return Zend_Application_Module_Bootstrap
     */
    public function getModuleBootstrap($module)
    {
        $bootstrap        = $this->getInvokeArg('bootstrap');
        $resource         = $bootstrap->getPluginResource('modules');
        $moduleBootstraps = $resource->getExecutedBootstraps();
        $moduleBootstrap  = $moduleBootstraps[$module];

        return $moduleBootstrap;
    }

    // ---------------------------------------------------------------------- //

    /**
     * モジュールサービスの取得
     *
     * @param string $serviceName サービス名
     * @return App_Service サービスクラスインスタンス
     */
    public function getModuleService($serviceName)
    {
        return $this->_helper->ModuleService->$serviceName;
    }

    // ---------------------------------------------------------------------- //

    /**
     * アクションバリデータの取得
     *
     * @return Zend_Filter_Input バリデータ
    */
    public function getFilterInput()
    {
        return $this->getValidatorHelper()->getFilterInput();
    }

    // ---------------------------------------------------------------------- //

    /**
     * アクセス権があるかチェック
     *
     * @param array $query ユーザ入力
     * @return boolean 真：アクセス権あり
    */
    public function hasAccessPermission($query = null)
    {
        // アクセス権チェック
        $acl = $this->getAclHelper();
        if ($acl->checkPermission($query)) {
            return true;
        }
        $this->noticeLog(
            $acl->getMessages(),
            App_Log_Access::CODE_PERMISSION_ERR
        );
        return false;
    }

    // ---------------------------------------------------------------------- //

    /**
     * 制約違反があるかチェック
     *
     * @param mixed $query 制約違反チェックに用いるデータ
     * @return boolean 真：制約違反あり
    */
    public function checkViolation($query = null)
    {
        // アクセス権チェック
        $constraint = $this->getConstraintHelper();
        if ($constraint->checkViolation($query)) {
            $this->noticeLog(
                'Violationエラー : ' . $constraint->getMessages(),
                App_Log_Access::CODE_VIOLATION_ERR
            );
            $this->view->violation_error = $constraint->getMessages();
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRFチェック
     *
     * @param string $token CSRFトークン
     * @return boolean 真：問題なし
    */
    public function csrfCheck($token)
    {
        $session = $this->getService('auth')->getSession();
        return $session->checkCsrfToken($token);
    }

    // ---------------------------------------------------------------------- //

    /**
     * ビュー初期設定
     *
     * @return void
    */
    public function initViewSetting()
    {
        $configPath = $this->getConfig('config_path');
        $viewConfig = $this->loadPartialConfig($configPath['view']);

        $this->includeStyleSheets($viewConfig);
        $this->includeJavaScripts($viewConfig);
    }

    // ---------------------------------------------------------------------- //

    /**
     * CSS読み込み設定の初期化
     *
     * @param array $viewConfig 設定値
     * @return void
    */
    public function includeStyleSheets($viewConfig)
    {
        $cssFiles = array();
        $baseUrl  = $this->view->baseUrl();
        $currentModule     = $this->getRequest()->getModuleName();
        $currentController = $this->getRequest()->getControllerName();
        $currentAction     = $this->getRequest()->getActionName();

        // アプリケーション共通設定読み込み
        $commonSettings = $viewConfig['common_settings'];
        if (array_key_exists('css', $commonSettings)) {
            $commonCss = $commonSettings['css'];
            if (count($commonCss) > 0) {
                $cssFiles = array_merge($cssFiles, $commonCss);
            }
        }

        // コントローラ単位での共通設定読み込み
        $viewConfigPaths = $viewConfig['view_config_path'];
        foreach ($viewConfigPaths as $moduleName => $configPath) {
            if ($currentModule == $moduleName) {
                $eachController = $configPath . '/' . $currentController . '.ini';
                if (file_exists($eachController)) {
                    $eachControllerViewConfig = $this->loadPartialConfig($eachController);
                    $eachControllerSettings   = $eachControllerViewConfig['common_settings'];
                    if (array_key_exists('css', $eachControllerSettings)) {
                        $eachControllerCss = $eachControllerSettings['css'];
                        if (count($eachControllerCss) > 0) {
                            $cssFiles = array_merge($cssFiles, $eachControllerCss);
                        }
                    }

                    // アクション単位での共通設定読み込み
                    if (array_key_exists($currentAction, $eachControllerViewConfig)) {
                        $eachActionSettings = $eachControllerViewConfig[$currentAction];
                        if (array_key_exists('css', $eachActionSettings)) {
                            $eachActionCss = $eachActionSettings['css'];
                            if (count($eachActionCss) > 0) {
                                $cssFiles = array_merge($cssFiles, $eachActionCss);
                            }
                        }
                    }
                }
            }
        }

        $cacheId = self::DEFAULT_CACHE_ID;
        if (array_key_exists('cache_id', $commonSettings)) {
            $cacheId = $commonSettings['cache_id'];
        }
        foreach ($cssFiles as $cssFile) {
            if (empty($cssFile)) {
                continue;
            }
            if (!preg_match('/^https?\:\/\//u', $cssFile)) {
                $cssFile = $baseUrl . '/' . $cssFile;
            }
            $this->view->headLink()
                       ->appendStylesheet($cssFile . '?' . $cacheId);
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * JavaScript読み込み設定の初期化
     *
     * @param array $viewConfig 設定値
     * @return void
    */
    public function includeJavaScripts($viewConfig)
    {
        $jsFiles = array();
        $baseUrl = $this->view->baseUrl();
        $currentModule     = $this->getRequest()->getModuleName();
        $currentController = $this->getRequest()->getControllerName();
        $currentAction     = $this->getRequest()->getActionName();

        // アプリケーション共通設定読み込み
        $commonSettings = $viewConfig['common_settings'];
        if (array_key_exists('js', $commonSettings)) {
            $commonJs = $commonSettings['js'];
            if (count($commonJs) > 0) {
                $jsFiles = array_merge($jsFiles, $commonJs);
            }
        }

        // コントローラ単位での共通設定読み込み
        $viewConfigPaths = $viewConfig['view_config_path'];
        foreach ($viewConfigPaths as $moduleName => $configPath) {
            if ($currentModule == $moduleName) {
                $eachController = $configPath . '/' . $currentController . '.ini';
                if (file_exists($eachController)) {
                    $eachControllerViewConfig = $this->loadPartialConfig($eachController);
                    $eachControllerSettings   = $eachControllerViewConfig['common_settings'];
                    if (array_key_exists('js', $eachControllerSettings)) {
                        $eachControllerJs = $eachControllerSettings['js'];
                        if (count($eachControllerJs) > 0) {
                            $jsFiles = array_merge($jsFiles, $eachControllerJs);
                        }
                    }

                    // アクション単位での共通設定読み込み
                    if (array_key_exists($currentAction, $eachControllerViewConfig)) {
                        $eachActionSettings = $eachControllerViewConfig[$currentAction];
                        if (array_key_exists('js', $eachActionSettings)) {
                            $eachActionJs = $eachActionSettings['js'];
                            if (count($eachActionJs) > 0) {
                                $jsFiles = array_merge($jsFiles, $eachActionJs);
                            }
                        }
                    }
                }
            }
        }

        $cacheId = self::DEFAULT_CACHE_ID;
        if (array_key_exists('cache_id', $commonSettings)) {
            $cacheId = $commonSettings['cache_id'];
        }
        foreach ($jsFiles as $jsFile) {
            if (empty($jsFile)) {
                continue;
            }
            if (preg_match('/,/u', $jsFile)) {
                $buffer = preg_split('/,/u', $jsFile, 2);
                $srcFile      = $baseUrl . '/' . $buffer[0] . '?' . $cacheId;
                $dataMainFile = $baseUrl . '/' . $buffer[1] . '?' . $cacheId;
                $this->view->headScript()
                           ->setAllowArbitraryAttributes(true)
                           ->appendFile(
                                $srcFile,
                                'text/javascript',
                                array('charset' => "utf-8", 'data-main' => $dataMainFile)
                           );
            } else {
                $this->view->headScript()
                           ->appendFile(
                                $baseUrl . '/' . $jsFile . '?' . $cacheId,
                                'text/javascript',
                                array('charset' => "utf-8")
                           );
            }
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * 404Not Foundエラーの表示
     *
     * @return void
     * @exception Zend_Controller_Dispatcher_Exception
    */
    public function forward404()
    {
        throw new Zend_Controller_Dispatcher_Exception('404 Not found');
    }

    // ---------------------------------------------------------------------- //

    /**
     * 403Forbiddenエラーの表示
     *
     * @return void
     * @exception App_Exception
    */
    public function forwardForbidden()
    {
        $this->_forward('forbidden', 'index', 'default');
    }

    // ---------------------------------------------------------------------- //

    /**
     * GETメソッドでない場合、404Not Foundエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forward404UnlessGetMethod()
    {
        if (!$this->getRequest()->isGet()) {
            $this->forward404();
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * POSTメソッドでない場合、404Not Foundエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forward404UnlessPostMethod()
    {
        if (!$this->getRequest()->isPost()) {
            $this->forward404();
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * GETメソッドでない場合、403Forbiddenエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forwardForbiddenUnlessGetMethod()
    {
        if (!$this->getRequest()->isGet()) {
            $this->forwardForbidden();
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * POSTメソッドでない場合、403Forbiddenエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forwardForbiddenUnlessPostMethod()
    {
        if (!$this->getRequest()->isPost()) {
            $this->forwardForbidden();
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * GET/POSTメソッドでない場合、404Not Foundエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forward404UnlessGetOrPostMethod()
    {
        if ($this->getRequest()->isGet()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            return;
        }
        $this->forward404();
    }

    // ---------------------------------------------------------------------- //

    /**
     * GET/POSTメソッドでない場合、403Forbiddenエラー表示へリダイレクト
     *
     * @return void
     * @exception App_Exception
    */
    public function forwardForbiddenUnlessGetOrPostMethod()
    {
        if ($this->getRequest()->isGet()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            return;
        }
        $this->forwardForbidden();
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::INFO)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function accessLog($message, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->accessLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::NOTICE)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function noticeLog($message, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->noticeLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::DEBUG)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function debugLog($message, $code = App_Log::CODE_DEBUG)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->debugLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::CRIT)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function criticalLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->criticalLog($exception, $code);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::ERR)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function errorLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->errorLog($exception, $code);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::WARN)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function warningLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->warningLog($exception, $code);
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * アクセスログインスタンスの取得
     *
     * @return App_Log_Access|boolean
    */
    public function getAccessLogger()
    {
        $loggers = $this->_getLoggers();
        if (is_array($loggers)) {
            if (array_key_exists('accesslog', $loggers)) {
                return $loggers['accesslog'];
            }
        }
        return false;
    }

    // ---------------------------------------------------------------------- //

    /**
     * エラーログインスタンスの取得
     *
     * @return App_Log_Error|boolean
    */
    public function getErrorLogger()
    {
        $loggers = $this->_getLoggers();
        if (is_array($loggers)) {
            if (array_key_exists('errorlog', $loggers)) {
                return $loggers['errorlog'];
            }
        }
        return false;
    }

    // ---------------------------------------------------------------------- //

    /**
     * Loggerプラグインリソースの取得
     *
     * @return array ロガー格納配列
    */
    private function _getLoggers()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Logger')) {
            return false;
        }
        $loggers = $bootstrap->getResource('Logger');
        return $loggers;
    }

    // ------------------------------------------------------------------ //

    /**
     * 分割設定ファイルの読み込み
     *
     * @param string $configFile 設定ファイルパス
     * @param string $section セクション名
     * @return array 設定値
     */
    public function loadPartialConfig($configFile, $section = null)
    {
        // 設定読み込み
        $cache   = $this->getConfigCache($configFile);
        $cacheId = $this->getRequest()->getModuleName()
                 .'_'
                 . $this->getRequest()->getControllerName()
                 .'_'
                 . preg_replace('/\./', '_', basename($configFile));
        if (!is_null($section)) {
            $cacheId .= '_' . $section;
        }
        $cacheId = preg_replace('/\-/', '_', $cacheId);

        if (!($config = $cache->load($cacheId))) {
            $config = App_Utils::loadConfig($configFile, $section);
            $cache->save($config, $cacheId);
        }
        return $config->toArray();
    }

    // ------------------------------------------------------------------ //

    /**
     * 設定ファイル用キャッシュの取得
     *
     * @param string $configFile 設定ファイルパス
     * @return Zend_Cache Zend_Cacheインスタンス
     */
    public function getConfigCache($configFile)
    {
        $cacheConfig     = $this->getConfig('cache');
        $frontendOptions = array(
            'lifetime'                => self::CONFIG_CACHE_LIFETIME,
            'automatic_serialization' => true,
            'master_file'             => $configFile,
        );
        $backendOptions = array(
            'cache_dir' => $cacheConfig['file']['config']
        );
        $cache = Zend_Cache::factory(
            'File',
            'File',
            $frontendOptions,
            $backendOptions
        );

        return $cache;
    }

    // ---------------------------------------------------------------------- //

    /**
     * バリデーションエラーメッセージの取得
     *
     * @param Zend_Filter_Input $input ユーザ入力値
     * @param string $glue 区切り文字
     * @return string バリデーションエラーメッセージ
    */
    public function outputValiationError($input, $glue = ',')
    {
        $errors = array();
        foreach ($input->getMessages() as $key => $value) {
            array_push($errors, implode(", ", $value));
        }

        return implode($glue, $errors);
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストされたURLの取得
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return string リクエストされたURL
     */
    public function getRequestUrl($input = null)
    {
        $baseUrl   = $this->view->baseUrl();
        $requestUrl= $this->getRequest()->getScheme()
                   . '://'
                   . $this->getRequest()->getHttpHost()
                   . $baseUrl
                   . '/' . $this->getRequest()->getModuleName()
                   . '/' . $this->getRequest()->getControllerName()
                   . '/' . $this->getRequest()->getActionName();

        if (!is_null($input)) {
            $params = array();
            foreach ($input->getEscaped() as $key => $value) {
                if (!App_Utils::isEmpty($value)) {
                    array_push($params, $key);
                    array_push($params, urlencode($value));
                }
            }
            $requestUrl .= '/' . implode('/', $params);
        }

        return $requestUrl;
    }

    // ---------------------------------------------------------------------- //

    /**
     * アプリケーションベースURLの取得
     *
     * @return string アプリケーションベースURL
     */
    public function getApplicationUrl()
    {
        $request = $this->getRequest();
        $scheme  = $request->getScheme();
        $host    = $request->getHttpHost();
        $baseUrl = $request->getBaseUrl();
        $appUrl  = $scheme . '://' . $host . $baseUrl;
        return $appUrl;
    }

    // ------------------------------------------------------------------ //

    /**
     * JSONの送信
     *
     * @param string $json JSON文字列
     * @return void
    */
    public function sendJson($json)
    {
        // レイアウト機能を無効化、viewRendererを無効化
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $response = $this->getResponse();
        $response->setHeader('Content-Type', App_Const::CONTENT_TYPE_JSON);

        // キャッシュコントロール無効化
        $response->setHeader('Cache-Control','', true);
        $response->setHeader('Pragma',       '', true);

        $response->setBody($json);
    }

    // ---------------------------------------------------------------------- //

    /**
     * XMLの送信
     *
     * @param string $xml XML文字列
     * @return void
     */
    public function sendXml($xml)
    {
        // レイアウト機能を無効化、viewRendererを無効化
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $response = $this->getResponse();
        $response->clearAllHeaders();
        $response->clearBody();
        $response->setHeader(
            'Content-Type',
            App_Const::CONTENT_TYPE_XML
        );
        $response->setHeader('Cache-Control', 'private', true);
        $response->setHeader('Pragma',        'private', true);
        $response->setHeader('Connection',    'close');

        // データ出力
        ob_end_clean();
        $response->sendHeaders();
        echo $xml;
        $response->clearAllHeaders();
        return;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ファイルのダウンロード
     *
     * @param string $output XML文字列
     * @param string $fileName ダウンロードファイル名
     * @return void
     */
    public function sendDownloadHttpHeader(
        $output, $fileName = null, $contentType = App_Const::CONTENT_TYPE_XML
    ) {
        // レイアウト機能を無効化、viewRendererを無効化
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // ファイルダウンロード用HTTPヘッダ生成
        $response = $this->getResponse();
        $response->clearAllHeaders();
        $response->clearBody();
        $response->setHeader('Content-Type', $contentType);

        if (!is_null($fileName)) {
            $response->setHeader(
                'Content-Disposition',
                'attachment; filename=' . $fileName
            );
        }
        $response->setHeader('Cache-Control', 'private', true);
        $response->setHeader('Pragma',        'private', true);
        $response->setHeader('Connection',    'close');

        // データ出力
        ob_end_clean();
        $response->sendHeaders();
        echo $output;
        $response->clearAllHeaders();
        return;
    }
}
