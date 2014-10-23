<?php
/**
 * applicaiton/modules/api/models/Request.php
 *
 * リクエストモデルクラス
 *
 * リクエストモデル。
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Request.php 64 2014-03-12 11:21:41Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Model
*/

/**
 * Api_Model_Request クラス
 *
 * リクエストモデルクラス
 *
 * @category    Api
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Request extends App_Model
{
    // ------------------------------------------------------------------ //

    /**
     * リクエストの進捗確認。
     *
     * @param string $requestId リクエストID
     * @return array リクエスト進捗確認結果
     */
    public function check($requestId)
    {
        return $this->find($requestId);
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト情報の取得。
     *
     * @param string $requestId リクエストID
     * @return array リクエスト情報
     */
    public function find($requestId)
    {
        $select = $this->getAdapter()->select();
        $select->from(
            array('req' => 't_request'),
            array(
                'req.*',
                'gen.ip_addr',
                'gen.context_name',
                'gen.priority',
                'gen.generator_status_id',
                'gen.lib_id',
                'lib.lib_name',
                'md.data_type',
                'rs.request_status',
            )
        )
        ->join(
            array('gen' => 't_available_generator'),
            "req.generator_id = gen.generator_id",
            array()
        )
        ->join(
            array('lib' => 't_available_lib'),
            "gen.lib_id = lib.lib_id",
            array()
        )

        ->join(
            array('md' => 'm_data_type'),
            "req.data_type_id = md.data_type_id",
            array()
        )
        ->join(
            array('rs' => 'm_request_status'),
            "req.request_status_id = rs.request_status_id",
            array()
        )
        ->where('request_id = ?', $requestId);

        $results = $this->getAdapter()->fetchRow($select);

        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストの登録。
     *
     * @param Zend_Filter_Input $dataInput ユーザ入力(入力データ部)
     * @param Zend_Filter_Input $paramInput ユーザ入力(ライブラリパラメータ部)
     * @return array ユーザデータ登録結果
     */
    public function registerRequest($dataInput, $paramJson)
    {
        $libId       = $dataInput->getEscaped('lib_id');
        $dataTypeId  = $dataInput->getEscaped('data_type_id');
        $requestId   = App_Utils::generateUUID();

        // ジェネレータIDの取得
        $generatorId = $this->_getGenerator($libId);
        if (!$generatorId) {
            throw new App_Exception(
                'ジェネレータIDが取得できません。',
                App_Exception::APP_SYSTEM_ERROR
            );
        }

        if ($requestId) {
            $data       = array(
                'request_id'        => $requestId,
                'data_type_id'      => $dataTypeId,
                'generator_id'      => $generatorId,
                'params'            => $paramJson,
                'request_status_id' => App_Const::REQUEST_STAT_ACCEPT,
            );
            $this->getAdapter()->insert('t_request', $data);
        }
        return $requestId ;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストのキャンセル。
     *
     * @param string $requestId リクエストID
     * @return boolean 真：キャンセル成功
     */
    public function cancelRequest($requestId)
    {
        if ($requestId) {
            $where   = $this->getAdapter()->quoteInto('request_id = ?', $requestId);
            $deleted = $this->getAdapter()->delete('t_request', $where);
            if ($deleted) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * 実行ジェネレータの取得。
     *
     * @param integer $libId ライブラリID
     * @return integer ジェネレータID
     */
    private function _getGenerator($libId)
    {
        $select = $this->getAdapter()->select();
        $select->from(
            't_available_generator',
            array('generator_id',)
        )
        ->where('lib_id = ?', $libId)
        ->where('generator_status_id = ?', App_Const::GENERATOR_STAT_AVAILABLE)
        ->order('priority ASC');

        $generatorId = $this->getAdapter()->fetchOne($select);
        return $generatorId;
    }
}

