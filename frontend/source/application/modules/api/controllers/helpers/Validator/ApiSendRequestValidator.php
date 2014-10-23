<?php
/**
 * applicaiton/modules/api/controllers/helpers/Validator/ApiSendRequestValidator.php
 *
 * Apiモジュール send-requestコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiSendRequestValidator.php 92 2014-03-26 05:42:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Api_Validator_ApiSendRequestValidator クラス
 *
 * Apiモジュール send-requestコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Validator_ApiSendRequestValidator extends App_Controller_Action_Helper_Validator
{
    // ------------------------------------------------------------------ //

    /**
     * 文字列長バリデータ：最大文字数
     */
    const STRING_MAX_LENGTH = 1024;

    /**
     * 最大値バリデータ：最大値
     */
    const MAX_NUMBER = 9999;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiSendRequestValidator';
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
            // Content-Typeがapplication/jsonの場合、getRawBody()を使用する。
            $contentType = preg_split('/\s*;\s*/', $this->getRequest()->getHeader('Content-Type'));
            if ($contentType[0] == App_Const::CONTENT_TYPE_JSON) {
                $query  = App_Json::decode($this->getRequest()->getRawBody());
                $params = $this->getRequest()->getParams();
                $query['response_format'] = $params['response_format'];
            } else {
                $query = $this->getRequest()->getParams();
            }
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
            'range_type_id' => array(
                App_Validate_Common::setRangeTypeIdValidator(
                    '不正な地点選択範囲種別IDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true
            ),
            'duration_id' => array(
                App_Validate_Common::setMustDurationIdValidator(
                    '不正なデュレーションIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true
            ),
            'begin_year' => array(
                App_Validate_Common::setYearValidator(
                    '不正な観測年(開始)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true
            ),
            'end_year' => array(
                App_Validate_Common::setYearValidator(
                    '不正な観測年(終了)です。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => true
            ),
            'lib_id' => array(
                App_Validate_Common::setMustIdValidator(
                    '不正なライブラリIDです。'
                ),
                Zend_Filter_Input::ALLOW_EMPTY => false,
                Zend_Filter_Input::PRESENCE    => Zend_Filter_Input::PRESENCE_REQUIRED,
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
                Zend_Filter_Input::ALLOW_EMPTY => true,
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
            'user_data_id' => array(
                App_Validate_Common::setUuidValidator(
                    '不正なユーザデータIDです。'
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

    // ------------------------------------------------------------------ //

    /**
     * ライブラリパラメータ定義に基づくバリデータ設定
     *
     * @param array $paramDef ライブラリパラメータ定義
     * @return Zend_Filter_Input バリデータ
    */
    public function getLibraryParamFilterInput($paramDefList)
    {
        $query  = array();
        $valids = array();

        // Content-Typeがapplication/jsonの場合、getRawBody()を使用する。
        $contentType = preg_split('/\s*;\s*/', $this->getRequest()->getHeader('Content-Type'));
        if ($contentType[0] == App_Const::CONTENT_TYPE_JSON) {
            $query = App_Json::decode($this->getRequest()->getRawBody());
        } else {
            $query = $this->getRequest()->getParams();
        }

        // フィルタのセット
        $filters = array(
            '*' => App_Validate_Common::setParamDefaultFilter()
        );

        foreach ($paramDefList as $paramDef) {
            $diabled = false;
            if (array_key_exists('disabled', $paramDef)) {
                $diabled  = $paramDef['disabled'];
            }
            if ($diabled) {
                continue;
            }

            $name   = $paramDef['id'];
            $label  = $paramDef['label'];
            $format = $paramDef['format'];

            $required  = false;
            $digits    = false;
            $alphanum  = false;
            $climate   = false;
            $maxlength = null;
            $minlength = null;
            $max       = null;
            $min       = null;
            $range     = array();

            // validateブロックからバリデータ定義取得
            $validateRules = $paramDef['validate'];
            $validatorMsg  = '不正な' . $label . 'です。';
            foreach ($validateRules as $key => $ruleDef) {
                switch($key) {
                    case 'required':
                        if ($ruleDef) {
                            $required = true;
                        }
                        break;
                    case 'digits':
                        if ($ruleDef) {
                            $digits = true;
                        }
                        break;
                    case 'alphanum':
                        if ($ruleDef) {
                            $alphanum = true;
                        }
                        break;
                    case 'climate':
                        if ($ruleDef) {
                            $climate  = true;
                        }
                        break;
                    case 'maxlength':
                        $maxlength = $ruleDef;
                        break;
                    case 'minlength':
                        $minlength = $ruleDef;
                        break;
                    case 'max':
                        $max = $ruleDef;
                        break;
                    case 'min':
                        $min = $ruleDef;
                        break;
                    case 'range':
                        if (is_array($ruleDef) && (count($ruleDef) == 2)) {
                            $range = $ruleDef;
                        }
                        break;
                    default:
                        break;
                }
            }

            // バリデータセット
            $validatorChain = new Zend_Validate();
            if ($required) {
                $noEmptyValidator = new Zend_Validate_NotEmpty();
                $noEmptyValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_NotEmpty::IS_EMPTY
                );
                $validatorChain->addValidator($noEmptyValidator, true);
            }
            if ($digits) {
                $intValidator = new Zend_Validate_Int();
                $intValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_Int::NOT_INT
                );
                $validatorChain->addValidator($intValidator, true);
            }
            if ($alphanum) {
                $alphanumValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_]+$/u');
                $alphanumValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_Regex::NOT_MATCH
                );
                $validatorChain->addValidator($alphanumValidator, true);
            }
            if ($climate) {
                $climateValidator = new Zend_Validate_Regex(
                    '/^(rain|airtemperature|wind|radiation|humidity|soiltemperature|watertemperature|leafwetness|brightsunlight)$/u'
                );
                $climateValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_Regex::NOT_MATCH
                );
                $validatorChain->addValidator($climateValidator, true);
            }
            if (!App_Utils::isEmpty($maxlength) || !App_Utils::isEmpty($minlength)) {
                if (is_null($minlength)) {
                    $minlength = 0;
                }
                if (is_null($maxlength)) {
                    $maxlength = self::STRING_MAX_LENGTH;
                }
                $strlenValidator = new Zend_Validate_StringLength(
                    $minlength,
                    $maxlength,
                    'UTF-8'
                );
                $strlenValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_StringLength::TOO_LONG
                );
                $strlenValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_StringLength::TOO_SHORT
                );
                $validatorChain->addValidator($strlenValidator, true);
            }
            if (!App_Utils::isEmpty($max)) {
                $max = $max + 1;
                $maxValidator = new Zend_Validate_LessThan($max);
                $maxValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_LessThan::NOT_LESS
                );
                $validatorChain->addValidator($maxValidator, true);
            }

            if (!App_Utils::isEmpty($min)) {
                $min = $min - 1;
                $minValidator = new Zend_Validate_GreaterThan($min);
                $minValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_GreaterThan::NOT_GREATER
                );
                $validatorChain->addValidator($minValidator, true);
            }
            if (!App_Utils::isEmpty($range)) {
                $begin = $range[0] - 1;
                $end   = $range[1] + 1;
                $gtValidator = new Zend_Validate_GreaterThan($begin);
                $gtValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_GreaterThan::NOT_GREATER
                );
                $ltValidator = new Zend_Validate_LessThan($end);
                $ltValidator->setMessage(
                    $validatorMsg,
                    Zend_Validate_LessThan::NOT_LESS
                );
                $validatorChain->addValidator($gtValidator, true);
                $validatorChain->addValidator($ltValidator, true);
            }
            $valids[$name] = array(
                $validatorChain,
            );
            if ($required) {
                $valids[$name][Zend_Filter_Input::ALLOW_EMPTY] = false;
                $valids[$name][Zend_Filter_Input::PRESENCE]    = Zend_Filter_Input::PRESENCE_REQUIRED;
            } else {
                $valids[$name][Zend_Filter_Input::ALLOW_EMPTY] = true;
            }
        }

        $input  = App_Validate_Common::createDefaultFilterInput(
            $filters,
            $valids,
            $query
        );
        return $input;
    }
}
