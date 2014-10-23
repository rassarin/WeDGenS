<?php
/**
 * applicaiton/modules/api/models/Data.php
 *
 * 利用可能ライブラリモデルクラス
 *
 * 利用可能ライブラリモデル。
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Data.php 11 2014-02-25 11:49:12Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Model
*/

/**
 * Api_Model_Data クラス
 *
 * 利用可能データモデルクラス
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Data extends App_Model
{
    // ------------------------------------------------------------------ //

    /**
     * 利用可能データリストの取得。
     *
     * @return array 利用可能データリスト
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

    // ------------------------------------------------------------------ //

    /**
     * ユーザデータの登録。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return array ユーザデータ登録結果
     */
    public function registerUserData($input)
    {
        $userDataId = App_Utils::generateUUID();
        if ($userDataId) {
            $data       = array(
                'user_data_id' => $userDataId,
                'data_name'    => $input->getEscaped('data_name'),
                'comment'      => $input->getEscaped('comment'),
            );
            $this->getAdapter()->insert('t_user_data', $data);
        }
        return $userDataId ;
    }
}

