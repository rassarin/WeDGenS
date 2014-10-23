<?php
/**
 * library/App/Session/Namespace/AppUser.php
 *
 * アプリケーション名前空間操作クラス
 *
 * @category    App
 * @package     Session
 * @subpackage  Namespace
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: AppUser.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Session_Namespace
*/

/**
 * App_Session_Namespace_AppUser クラス
 *
 * アプリケーション名前空間操作クラス
 *
 * @category    App
 * @package     Session
 * @subpackage  Namespace
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Session_Namespace_AppUser extends App_Session_Namespace
{
    // ------------------------------------------------------------------ //

    /**
     * アプリケーションデフォルト名前空間
     */
    const NS_APP = 'user';

    /**
     * 未認証ユーザID(将来の認証処理拡張までの暫定初期値)
     */
    const ANONYMOUS_USER_ID = 'anonymous';

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param  string $namespace 名前空間
     * @param  boolean $singleInstance 真：名前空間へのアクセスを単一インスタンスに制限する
     * @return void
     */
    public function __construct(
        $namespace = parent::NS_SYSTEM, $singleInstance = false
    ) {
        $namespace .= '_' . self::NS_APP;
        parent::__construct($namespace, $singleInstance);
    }

    // ------------------------------------------------------------------ //

    /**
     * 名前空間の取得
     *
     * @return string 名前空間
     */
    public function getAppNamespace()
    {
        return parent::NS_SYSTEM . '_' . self::NS_APP;
    }

    // ------------------------------------------------------------------ //

    /**
     * ログインIDのセット
     *
     * @param string $userId ログインID
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    // ------------------------------------------------------------------ //

    /**
     * ログインIDの取得
     *
     * @return string ログインID
     */
    public function getUserId()
    {
        $userId = $this->userId;
        if (preg_match('/^$/', $userId)) {
            /**
             * 将来の認証処理拡張までの暫定初期値をセット)
             */
            $this->setUserId(self::ANONYMOUS_USER_ID);
            return $this->userId;
        }
        return $userId;
    }
}
