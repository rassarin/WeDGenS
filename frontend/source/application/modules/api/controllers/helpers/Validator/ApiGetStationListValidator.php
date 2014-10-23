<?php
/**
 * applicaiton/modules/api/controllers/helpers/Validator/ApiGetStationListValidator.php
 *
 * Apiモジュール get-station-listコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetStationListValidator.php 92 2014-03-26 05:42:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Api_Validator_ApiGetStationListValidator クラス
 *
 * Apiモジュール get-station-listコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Validator_ApiGetStationListValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiGetStationListValidator';
    }

    // ------------------------------------------------------------------ //

    /**
     * index アクション バリデータ設定
     *
     * @param mixed $query ユーザ入力値
     * @return Zend_Filter_Input バリデータ
    */
    public function setValidatorOnIndex($query = null)
    {
        if (is_null($query)) {
            $query = $this->getRequest()->getParams();
        }

        // フィルタのセット
        $filters = array(
            '*' => App_Validate_Common::setParamDefaultFilter()
        );

        // バリデータのセット
        $valids = array(
            'response_format' => array(
                App_Validate_Common::setResponseFormatValidator(
                    '不正なレスポンス形式です。'
                ),
                Zend_Filter_Input::DEFAULT_VALUE => App_Const::RESPONSE_FORMAT_JSON,
            ),
            'data_type_id' => array(
                App_Validate_Common::setMustDataTypeIdValidator(
                    '不正なデータ種別です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => false,
                Zend_Filter_Input::PRESENCE    => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'source_id' => array(
                App_Validate_Common::setMustAlnumCharValidator(
                    '不正なデータリソースIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => false,
                Zend_Filter_Input::PRESENCE    => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'region_id' => array(
                App_Validate_Common::setAlnumCharValidator(
                    '不正なリージョンIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'station_id' => array(
                App_Validate_Common::setAlnumCharValidator(
                    '不正なステーションIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'lib_id' => array(
                App_Validate_Common::setIdValidator(
                    '不正なライブラリIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'begin_year' => array(
                App_Validate_Common::setYearValidator(
                    '不正な観測年(開始)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'end_year' => array(
                App_Validate_Common::setYearValidator(
                    '不正な観測年(終了)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'nw_lat' => array(
                App_Validate_Common::setCoordinateValidator(
                    '不正なエリア座標(北西緯度)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'nw_lon' => array(
                App_Validate_Common::setCoordinateValidator(
                    '不正なエリア座標(北西経度)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'se_lat' => array(
                App_Validate_Common::setCoordinateValidator(
                    '不正なエリア座標(南東緯度)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
            'se_lon' => array(
                App_Validate_Common::setCoordinateValidator(
                    '不正なエリア座標(南東経度)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true,
            ),
        );
        $input  = App_Validate_Common::createDefaultFilterInput(
            $filters,
            $valids,
            $query
        );
        return $input;
    }
}
