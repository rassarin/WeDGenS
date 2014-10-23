<?php
/**
 * applicaiton/services/Search.php
 *
 * 検索サービスクラス
 *
 * 検索サービス。
 *
 * @category    Default
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Search.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Service
*/

/**
 * Service_Search クラス
 *
 * 検索サービスクラス
 *
 * @category    Default
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class Service_Search extends App_Service
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // 共通初期化処理を記述
    }

    // ------------------------------------------------------------------ //

    /**
     * LIKE検索条件式の生成
     *
     * @param Zend_Db_Select $select
     * @param string $field フィールド名
     * @param string $value 値
     * @return string LIKE検索条件式
     */
    protected static function _createLike($select, $field, $value)
    {
        $value = preg_replace('/\s+AND\s+/u',' ', $value);
        $args  = preg_split('/( |　)+/u', $value);
        if (count($args) > 1) {
            $conditions = array();
            $values     = array();
            foreach ($args as $arg) {
                $select->where($field . ' like ?', '%' . $arg . '%');
            }
        } else {
            $select->where($field . ' like ?', '%' . $value . '%');
        }
        return $select;
    }
}

