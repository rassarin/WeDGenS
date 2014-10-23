<?php
/**
 * applicaiton/modules/api/models/Library.php
 *
 * 利用可能ライブラリモデルクラス
 *
 * 利用可能ライブラリモデル。
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Library.php 38 2014-03-06 06:38:16Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Model
*/

/**
 * Api_Model_Library クラス
 *
 * 利用可能ライブラリモデルクラス
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Library extends App_Model
{
    // ------------------------------------------------------------------ //

    /**
     * 利用可能ライブラリリストの取得。
     *
     * @return array 利用可能ライブラリリスト
     */
    public function fetchAll()
    {
        $select = $this->getAdapter()->select();
        $select->from(
            't_available_lib',
            array('lib_id','lib_name', 'description')
        )
        ->order('lib_id');

        $results = $this->getAdapter()->fetchAll($select);

        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * ライブラリ情報の取得。
     *
     * @param integer $libId ライブラリID
     * @return array ライブラリ情報
     */
    public function find($libId)
    {
        $select = $this->getAdapter()->select();
        $select->from('t_available_lib',
            array('lib_id','lib_name', 'require_params', 'description')
        )
        ->where('lib_id = ?', $libId);

        $results = $this->getAdapter()->fetchRow($select);

        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * ライブラリパラメータの取得。
     *
     * @param integer $libId ライブラリID
     * @return array ライブラリパラメータ
     */
    public function getParams($libId)
    {
        $params  = array();
        $libInfo = $this->find($libId);
        $requiredParams = App_Json::decode($libInfo['require_params']);
        if (array_key_exists('parameters', $requiredParams)) {
            $params = $requiredParams['parameters'];
        }
        return $params;
    }

    // ------------------------------------------------------------------ //

    /**
     * ライブラリで必要とする気候データ定義の取得。
     *
     * @param integer $libId ライブラリID
     * @return array 気候データ定義
     */
    public function getClimateDataDef($libId)
    {
        $climateDataDef  = array();
        $libInfo = $this->find($libId);
        $requiredParams = App_Json::decode($libInfo['require_params']);
        if (array_key_exists('climate_data', $requiredParams)) {
            $climateDataDef = $requiredParams['climate_data'];
        }

        return $climateDataDef;
    }
}

