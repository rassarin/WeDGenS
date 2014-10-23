<?php
/**
 * applicaiton/modules/api/controllers/helpers/Validator/ApiGetRegionListValidator.php
 *
 * Apiモジュール get-region-listコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetRegionListValidator.php 92 2014-03-26 05:42:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Api_Validator_ApiGetRegionListValidator クラス
 *
 * Apiモジュール get-region-listコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Validator_ApiGetRegionListValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiGetRegionListValidator';
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
        );
        $input  = App_Validate_Common::createDefaultFilterInput(
            $filters,
            $valids,
            $query
        );
        return $input;
    }
}
