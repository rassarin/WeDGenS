<?php
/**
 * applicaiton/modules/api/controllers/helpers/Validator/ApiGetLibraryListValidator.php
 *
 * Apiモジュール get-library-listコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetLibraryListValidator.php 92 2014-03-26 05:42:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Api_Validator_ApiGetLibraryListValidator クラス
 *
 * Apiモジュール get-library-listコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Validator_ApiGetLibraryListValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiGetLibraryListValidator';
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
        );
        $input  = App_Validate_Common::createDefaultFilterInput(
            $filters,
            $valids,
            $query
        );
        return $input;
    }
}
