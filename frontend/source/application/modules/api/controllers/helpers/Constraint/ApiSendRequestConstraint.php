<?php
/**
 * applicaiton/modules/api/controllers/helpers/Constraint/ApiSendRequestConstraint.php
 *
 * Apiモジュール send-requestコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiSendRequestConstraint.php 86 2014-03-19 09:03:41Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Constraint
*/

/**
 * Helper_Api_Constraint_ApiSendRequestConstraint クラス
 *
 * Apiモジュール send-requestコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Constraint_ApiSendRequestConstraint extends App_Controller_Action_Helper_Constraint
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiSendRequestConstraint';
    }

    // ------------------------------------------------------------------ //

    /**
     * index アクション バリデータ設定
     *
     * @param mixed $query ユーザ入力値
     * @return Zend_Filter_Input バリデータ
    */
    // ------------------------------------------------------------------ //

    /**
     * indexアクション 制約チェック設定
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return boolean 真：違反あり
     */
    public function onIndex($input)
    {
        /**
         * データ種別に応じた地点選択範囲種別データが指定されているかどうか。
         */
        $dataTypeId = $input->getEscaped('data_type_id');
        switch($dataTypeId) {
            case App_Const::DATA_TYPE_MET_BROKER:
                if (App_Utils::isEmpty($input->getEscaped('range_type_id'))) {
                    $this->_messages = '地点選択範囲種別が指定されていません。';
                    return true;
                }
                break;
        }

        /**
         * 地点選択範囲種別に応じた地点データが指定されているかどうか。
         */
        $rangeTypeId = $input->getEscaped('range_type_id');
        switch($rangeTypeId) {
            case App_Const::RANGE_TYPE_POINT:
                if (App_Utils::isEmpty($input->getEscaped('begin_year'))
                    || App_Utils::isEmpty($input->getEscaped('end_year'))
                ) {
                    $this->_messages = '観測年が指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('duration_id'))) {
                    $this->_messages = 'デュレーションIDが指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('source_id'))) {
                    $this->_messages = 'ソースIDが指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('region_id'))) {
                    $this->_messages = 'リージョンIDが指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('station_id'))) {
                    $this->_messages = 'ステーションIDが指定されていません。';
                    return true;
                }
                break;
            case App_Const::RANGE_TYPE_AREA:
                if (App_Utils::isEmpty($input->getEscaped('nw_lat'))
                    || App_Utils::isEmpty($input->getEscaped('nw_lon'))
                    || App_Utils::isEmpty($input->getEscaped('se_lat'))
                    || App_Utils::isEmpty($input->getEscaped('se_lon'))
                ) {
                    $this->_messages = 'エリア座標が指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('begin_year'))
                    || App_Utils::isEmpty($input->getEscaped('end_year'))
                ) {
                    $this->_messages = '観測年が指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('duration_id'))) {
                    $this->_messages = 'デュレーションIDが指定されていません。';
                    return true;
                }
                if (App_Utils::isEmpty($input->getEscaped('source_id'))) {
                    $this->_messages = 'ソースIDが指定されていません。';
                    return true;
                }
                break;
            case App_Const::RANGE_TYPE_MESH:
                break;
            case App_Const::RANGE_TYPE_USER:
                if (App_Utils::isEmpty($input->getEscaped('user_data_id'))) {
                    $this->_messages = 'ユーザデータIDが指定されていません。';
                    return true;
                }
                break;
        }

        /**
         * ライブラリ種別に応じたデュレーションIDデータが指定されているかどうか。
         */
        $libId      = $input->getEscaped('lib_id');
        $durationId = $input->getEscaped('duration_id');
        if ($dataTypeId != App_Const::DATA_TYPE_USER_DATA) {
            switch($libId) {
                case App_Const::LIBRARY_TYPE_CLIGEN:
                    if ($durationId != App_Const::DURATION_TYPE_DAILY) {
                        $this->_messages = '使用できないデュレーションIDが選択されています。';
                        return true;
                    }
                    break;
                case App_Const::LIBRARY_TYPE_CDFDM:
                    if ($durationId != App_Const::DURATION_TYPE_DAILY) {
                        $this->_messages = '使用できないデュレーションIDが選択されています。';
                        return true;
                    }
                    break;
            }
        }
        return false;
    }
}
