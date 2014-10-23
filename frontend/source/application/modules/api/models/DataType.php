<?php
/**
 * applicaiton/modules/api/models/DataType.php
 *
 * 利用可能データ種別モデルクラス
 *
 * 利用可能データ種別モデル。
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: DataType.php 19 2014-02-26 11:43:31Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Model
*/

/**
 * Api_Model_DataType クラス
 *
 * 利用可能データ種別モデルクラス
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_DataType extends App_Model
{
    // ------------------------------------------------------------------ //

    /**
     * 利用可能データ種別リストの取得。
     *
     * @return array 利用可能データ種別リスト
     */
    public function fetchAll()
    {
        $select = $this->getAdapter()->select();
        $select->from(
            'm_data_type',
            array('data_type_id','data_type', 'description')
        )
        ->order('data_type_id');

        $results = $this->getAdapter()->fetchAll($select);

        return $results;
    }
}

