<?php
/**
 * library/App/Validate/Common.php
 *
 * 共通バリデータ定義クラス。
 *
 * @category    App
 * @package     Validate
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Common.php 83 2014-03-18 12:49:18Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Validate_Common クラス
 *
 * 共通バリデータ定義クラス
 *
 * @category    App
 * @package     Validate
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Validate_Common
{
    /**
     * 基準となるZFのバージョン
     */
    const ZF_BASIC_VERSION = '1.8.4';

    // ------------------------------------------------------------------ //

    /**
     * バリデータエラーメッセージの取得
     *
     * @param  array $messages バリデータエラーメッセージ
     * @return string バリデータエラーメッセージ
     */
    public static function toString($messages)
    {
        $buffer = array();

        foreach ($messages as $param => $message) {
            if (is_array($message)) {
                $tmp = array();
                foreach ($message as $key => $element) {
                    array_push($tmp, $element);
                }
                $validatorMsg = implode('/', $tmp);
            } else {
                $validatorMsg = $message;
            }
            array_push($buffer, "$param => $validatorMsg");
        }

        return implode(', ', $buffer);
    }

    // ------------------------------------------------------------------ //

    /**
     * Zend_Filter_Inputインスタンス生成
     *
     * エスケープ処理：あり(HtmlEntities)
     *
     * @param array $filters フィルタ定義
     * @param array $valids バリデータ定義
     * @param array $request リクエストパラメータ
     * @param array $options Zend_Filter_Inputオプション
     *
     * @return Zend_Filter_Input
    */
    public static function createDefaultFilterInput(
        $filters, $valids, $request, $options = null
    ) {
        // Zend_Filter_Inputオプションの設定
        if (is_null($options)) {
            $options = self::getDefaultFilterInputOption();
        }

        // Zend_Filter_Inputインスタンス生成
        $input = new Zend_Filter_Input(
            $filters,
            $valids,
            $request,
            $options
        );

        return $input;
    }

    // ------------------------------------------------------------------ //

    /**
     * Zend_Filter_Inputオプションの取得
     *
     * エスケープ処理：なし
     *
     * @return array Zend_Filter_Inputオプション
    */
    public static function getDefaultFilterInputOption()
    {
        // サーバサイドではエスケープしない
        $throughFilter = new App_Filter_Through();
        $filterChain   = new Zend_Filter();
        $filterChain->addFilter($throughFilter);
        $options = array(
            Zend_Filter_Input::ESCAPE_FILTER     => $filterChain,
            Zend_Filter_Input::MISSING_MESSAGE   => '必須項目が入力されていません。',
            Zend_Filter_Input::NOT_EMPTY_MESSAGE => '空文字は許可されていません。'
        );

        return $options;
    }

    // ------------------------------------------------------------------ //

    /**
     * パラメータのデフォルトフィルタ生成
     *
     * @return Zend_Filter フィルタ
    */
    public static function setParamDefaultFilter()
    {
        return self::setParamWebInputFilter();
    }

    // ------------------------------------------------------------------ //

    /**
     * 前後の空白除去のみ行うフィルタ生成
     *
     * 前後の空白除去
     *
     * @return Zend_Filter フィルタ
    */
    public static function setParamNoStripTagsFilter()
    {
        $stringTrimFilter = new Zend_Filter_StringTrim();
        $filterChain = new Zend_Filter();
        $filterChain->addFilter($stringTrimFilter);

        return $filterChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * タグの除去のみ行うフィルタ生成
     *
     * タグの除去
     *
     * @return Zend_Filter フィルタ
    */
    public static function setParamNoTrimFilter()
    {
        $stripTagFilter = new Zend_Filter_StripTags();
        $filterChain    = new Zend_Filter();
        $filterChain->addFilter($stripTagFilter);

        return $filterChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 前後の空白、および、タグの除去を行うフィルタ生成
     *
     * 前後の空白、および、タグの除去
     *
     * @return Zend_Filter フィルタ
    */
    public static function setParamStripTagsFilter()
    {
        $stringTrimFilter = new Zend_Filter_StringTrim();
        $stripTagFilter   = new Zend_Filter_StripTags();
        $filterChain = new Zend_Filter();
        $filterChain->addFilter($stringTrimFilter)
                    ->addFilter($stripTagFilter);

        return $filterChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 前後の空白、および、タグの除去、特定文字列の置換を行うフィルタ生成
     *
     * 前後の空白、および、タグの除去、特定文字列の置換
     *
     * @param boolean $stripTag 真：タグを除去する。
     * @return Zend_Filter フィルタ
    */
    public static function setParamWebInputFilter($stripTag = true)
    {
        $stringTrimFilter = new Zend_Filter_StringTrim();
        $stripTagFilter   = new Zend_Filter_StripTags();
        $pregFilter       = new Zend_Filter_PregReplace();

        $pregFilter->setMatchPattern(
                       array(
                           '/　+/u',
                           '/\.\.+/u',
                       )
                   )
                   ->setReplacement(
                       array(
                           ' ',
                           '',
                       )
                   );

        $filterChain = new Zend_Filter();
        $filterChain->addFilter($stringTrimFilter);
        if ($stripTag) {
            $filterChain->addFilter($stripTagFilter);
        }
        $filterChain->addFilter($pregFilter);

        return $filterChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * パスワードのバリデータ生成
     *
     * 必須項目、英数(小文字)+一部記号(@_<>=,.-:;+*#$%&!)のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setPasswordValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z@_<>=,;\.\-\:\+\*\#\$\%\&\!]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * バリデータ不要の場合のダミーバリデータ生成
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setAnyValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^.*$/um');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * バリデータ不要の場合の必須ダミーバリデータ生成
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustAnyValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^.+$/um');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須項目のバリデータ生成
     *
     * 必須項目
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setNotEmptyValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($noEmptyValidator, true);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須IDのバリデータ生成
     *
     * 必須項目、1以上の整数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustIdValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(0);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($intValidator,     true)->
        addValidator($gtValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * IDのバリデータ生成
     *
     * 1以上の整数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setIdValidator($message)
    {
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(0);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($intValidator, true)->
        addValidator($gtValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 列挙型のバリデータ生成
     *
     * 指定範囲($min <= x <= $max)の整数のみ
     *
     * @param string $message エラーメッセージ
     * @param string $min エラーメッセージ
     * @param string $max エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setEnumValidator($message, $min, $max)
    {
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan($min - 1);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $ltValidator = new Zend_Validate_LessThan($max + 1);
        $ltValidator->setMessage(
            $message,
            Zend_Validate_LessThan::NOT_LESS
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($intValidator, true)->
        addValidator($gtValidator)->
        addValidator($ltValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 英数字のバリデータ生成
     *
     * 英字＋数値のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setAlnumValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[A-Za-z0-9]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須英数字のバリデータ生成
     *
     * 必須項目、英字＋数値のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustAlnumValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[A-Za-z0-9]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須テーブルフィールド名のバリデータ生成
     *
     * 必須項目、英字＋数値＋「_」のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustFieldValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[A-Za-z0-9_]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * テーブルフィールド名のバリデータ生成
     *
     * 英字＋数値＋「_」のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setFieldValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[A-Za-z0-9_]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 論理演算子のバリデータ生成
     *
     * AND、ORのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setOperatorValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^(AND|OR)$/iu');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 数値のバリデータ生成
     *
     * 整数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setIntValidator($message)
    {
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(-1);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $ltValidator = new Zend_Validate_LessThan(App_Const::INT_LIMIT);
        $ltValidator->setMessage(
            $message,
            Zend_Validate_LessThan::NOT_LESS
        );

        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($intValidator, true)->
        addValidator($gtValidator)->
        addValidator($ltValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須数値のバリデータ生成
     *
     * 必須項目、整数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustIntValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(-1);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($intValidator)->
        addValidator($gtValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 1以上の自然数のバリデータ生成
     *
     * 1以上の自然数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setNaturalNumberValidator($message)
    {
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(0);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $ltValidator = new Zend_Validate_LessThan(App_Const::INT_LIMIT);
        $ltValidator->setMessage(
            $message,
            Zend_Validate_LessThan::NOT_LESS
        );

        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($intValidator, true)->
        addValidator($gtValidator)->
        addValidator($ltValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須テキストのバリデータ生成
     *
     * 必須項目、1<=文字数<=maxのみ
     *
     * @param string $message エラーメッセージ
     * @param integer $max 最大文字数
     * @return Zend_Validate バリデータ
    */
    public static function setMustTextValidator($message, $max)
    {
        // 文字数をUTF-8でカウントする
        iconv_set_encoding('internal_encoding','UTF-8');

        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $strlenValidator = new Zend_Validate_StringLength(1, $max, 'UTF-8');
        $strlenValidator->setMessage(
            $message,
            Zend_Validate_StringLength::TOO_LONG
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($strlenValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * テキストのバリデータ生成
     *
     * 0<=文字数<=maxのみ
     *
     * @param string $message エラーメッセージ
     * @param integer $max 最大文字数
     * @return Zend_Validate バリデータ
    */
    public static function setTextValidator($message, $max)
    {
        // 文字数をUTF-8でカウントする
        iconv_set_encoding('internal_encoding','UTF-8');

        $strlenValidator = new Zend_Validate_StringLength(0, $max, 'UTF-8');
        $strlenValidator->setMessage(
            $message,
            Zend_Validate_StringLength::TOO_LONG
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($strlenValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 日付のバリデータ生成
     *
     * YYYY-MM-DDのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setDateValidator($message)
    {
        $dateValidator = new Zend_Validate_Date();
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::FALSEFORMAT
        );
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::INVALID
        );
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::INVALID_DATE
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($dateValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * フラグのバリデータ生成
     *
     * 必須項目、1、または、2のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustFlagValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[12]$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * フラグのバリデータ生成
     *
     * 1、または、2のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
     */
    public static function setFlagValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[12]$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * メールアドレスのバリデータ生成
     *
     * MAILアドレスのみ
     *
     * @return Zend_Validate バリデータ
    */
    public static function setMailAddressValidator()
    {
        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessages(
            array(
                Zend_Validate_EmailAddress::INVALID            => 'メールアドレスの書式として不正です。',
                Zend_Validate_EmailAddress::INVALID_HOSTNAME   => 'ホスト名の書式として不正です。',
                Zend_Validate_EmailAddress::INVALID_MX_RECORD  => 'メールを受信できるアドレスではありません。',
                Zend_Validate_EmailAddress::DOT_ATOM           => 'dot-atom形式になっていません。',
                Zend_Validate_EmailAddress::QUOTED_STRING      => 'quoted-string形式になっていません。',
                Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'ユーザ名として不正です。',
            )
        );

        // Zend Framework-1.9の場合
        $cmp = Zend_Version::compareVersion(self::ZF_BASIC_VERSION);
        if ($cmp < 0 ) {
            $emailValidator->setMessage(
                'メールアドレスの書式として不正です。',
                 Zend_Validate_EmailAddress::INVALID_FORMAT
            );
        }

        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($emailValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * URIのバリデータ生成
     *
     * URIのみ
     *
     * @return Zend_Validate バリデータ
    */
    public static function setUriValidator()
    {
        $uriValidator = new Zend_Validate_Callback(array('Zend_Uri', 'check'));
        $uriValidator->setMessages(
            array(
                Zend_Validate_Callback::INVALID_VALUE    => '不正なURIです。',
                Zend_Validate_Callback::INVALID_CALLBACK => 'URI書式チェックに失敗しました。',
            )
        );

        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($uriValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須英数記号のバリデータ生成
     *
     * 必須項目、英数+一部記号(-_)のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustAlnumCharValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 識別子のバリデータ生成
     *
     * 必須項目、英数+一部記号(-_:.)のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustIdentifierValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_\:\.]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 識別子のバリデータ生成
     *
     * 必須項目、英数+一部記号(-_:.)のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setIdentifierValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_\:\.]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 英数記号のバリデータ生成
     *
     * 必須項目、英数+一部記号(-_:)のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setAlnumCharValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_\: 　]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 英数記号のバリデータ生成
     *
     * 必須項目、英数+一部記号(-_:)。空白のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setAlnumCharSearchValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[0-9A-Za-z\-_\: 　]+$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 日付(ISO8601 DateTime)のバリデータ生成
     *
     * YYYY-MM-DDThh:mm:ssZのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setDateTimeValidator($message)
    {
        $dateValidator = new Zend_Validate_Date();
        $dateValidator->setFormat(Zend_Date::ISO_8601);
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::FALSEFORMAT
        );
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::INVALID
        );
        $dateValidator->setMessage(
            $message,
            Zend_Validate_Date::INVALID_DATE
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($dateValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * ソート項目のバリデータ生成
     *
     * 英数記号:[az]形式のみ指定可。
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setSortItemValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex(
            '/^[A-Za-z0-9_]+\:[az]$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 西暦のバリデータ生成
     *
     * 数値4桁のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setYearValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^\d{4}$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 月のバリデータ生成
     *
     * 1～12のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMonthValidator($message)
    {
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(0);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $ltValidator = new Zend_Validate_LessThan(13);
        $ltValidator->setMessage(
            $message,
            Zend_Validate_LessThan::NOT_LESS
        );

        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($intValidator, true)->
        addValidator($gtValidator)->
        addValidator($ltValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 年月のバリデータ生成
     *
     * YYYY-MMのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
     */
    public static function setYearMonthValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^\d{4}\-\d{2}$/u');
        $regexValidator->setMessage(
        $message,
        Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 配列存在チェックバリデータ生成
     *
     * 配列にキーが存在する場合のみ
     *
     * @param string $message エラーメッセージ
     * @param array $check チェック用配列
     * @return Zend_Validate バリデータ
    */
    public static function setExistsValidator($message, $check)
    {
        $inArrayValidator = new Zend_Validate_InArray($check);
        $inArrayValidator->setMessage(
            $message,
            Zend_Validate_InArray::NOT_IN_ARRAY
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($inArrayValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 1ページ当たりのページ数のバリデータ生成
     *
     * 1～100のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setPerPageValidator($message)
    {
        return self::setEnumValidator($message, 1, 100);
    }

    // ------------------------------------------------------------------ //

    /**
     * IPアドレスのバリデータ生成
     *
     * \d.\d.\d.\dのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
     */
    public static function setIpAddrValidator($message)
    {
        $ipValidator = new Zend_Validate_Ip(array('allowipv6' => false));
        $ipValidator->setMessage(
            $message,
            Zend_Validate_Ip::INVALID
        );
        $ipValidator->setMessage(
            $message,
            Zend_Validate_Ip::NOT_IP_ADDRESS
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($ipValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策トークンのバリデータ生成
     *
     * 0-9a-fのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setCsrfTokenValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^[0-9a-f]{40}$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLバリデータ生成
     *
     * XML文字列のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustXmlValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex('/^<\?xml version=\"1\.0\" encoding=\"UTF\-8\"\?>/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * レスポンス形式のバリデータ生成
     *
     * xml、jsonのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setResponseFormatValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^(xml|json)$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 必須UUID形式のバリデータ生成
     *
     * abc3204c-9dd8-11e3-b540-a71d99c51cbe 形式のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustUuidValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex(
            '/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($noEmptyValidator, true)
                       ->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * UUID形式のバリデータ生成
     *
     * abc3204c-9dd8-11e3-b540-a71d99c51cbe 形式のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setUuidValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex(
            '/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * データ種別IDのバリデータ生成
     *
     * 必須項目、1以上の整数のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustDataTypeIdValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $intValidator = new Zend_Validate_Int();
        $intValidator->setMessage(
            $message,
            Zend_Validate_Int::NOT_INT
        );
        $gtValidator = new Zend_Validate_GreaterThan(0);
        $gtValidator->setMessage(
            $message,
            Zend_Validate_GreaterThan::NOT_GREATER
        );
        $dbExistsValidator = new Zend_Validate_Db_RecordExists(
            array(
                'table' => 'm_data_type',
                'field' => 'data_type_id'
            )
        );
        $dbExistsValidator->setMessage(
            $message,
            Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($intValidator,     true)->
        addValidator($gtValidator,      true)->
        addValidator($dbExistsValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 座標のバリデータ生成
     *
     * +/-m.n形式のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setCoordinateValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex(
            '/^(\-|\+)?[0-9]+(\.[0-9]+)?$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 地点選択範囲種別のバリデータ生成
     *
     * point, area, mesh, userのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setRangeTypeIdValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex(
            '/^(point|area|mesh|user)$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     *  デュレーション種別のバリデータ生成
     *
     * daily, hourlyのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setMustDurationIdValidator($message)
    {
        $noEmptyValidator = new Zend_Validate_NotEmpty();
        $noEmptyValidator->setMessage(
            $message,
            Zend_Validate_NotEmpty::IS_EMPTY
        );
        $regexValidator = new Zend_Validate_Regex(
            '/^(daily|hourly)$/u'
        );
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->
        addValidator($noEmptyValidator, true)->
        addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * デュレーション種別のバリデータ生成
     *
     * hourly、dailyのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setDurationIdValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^(hourly|daily)$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 出力形式のバリデータ生成
     *
     * xml、htmlのみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setResultFormatValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^(xml|html|csv|chart|zip)$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }

    // ------------------------------------------------------------------ //

    /**
     * 真偽値のバリデータ生成
     *
     * 0、1のみ
     *
     * @param string $message エラーメッセージ
     * @return Zend_Validate バリデータ
    */
    public static function setBooleanValidator($message)
    {
        $regexValidator = new Zend_Validate_Regex('/^[01]$/u');
        $regexValidator->setMessage(
            $message,
            Zend_Validate_Regex::NOT_MATCH
        );
        $validatorChain = new Zend_Validate();
        $validatorChain->addValidator($regexValidator);

        return $validatorChain;
    }
}
