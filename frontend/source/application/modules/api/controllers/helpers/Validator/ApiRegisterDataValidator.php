<?php
/**
 * applicaiton/modules/api/controllers/helpers/Validator/ApiRegisterDataValidator.php
 *
 * Apiモジュール register-dataコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiRegisterDataValidator.php 92 2014-03-26 05:42:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Api_Validator_ApiRegisterDataValidator クラス
 *
 * Apiモジュール register-dataコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Validator_ApiRegisterDataValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiRegisterDataValidator';
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
            'data_name' => array(
                App_Validate_Common::setMustTextValidator(
                    '不正なデータ名です。', 256
                ),
                Zend_Filter_Input::ALLOW_EMPTY => false,
                Zend_Filter_Input::PRESENCE    => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'comment' => array(
                App_Validate_Common::setTextValidator(
                    '不正なコメントです。', 1024
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
