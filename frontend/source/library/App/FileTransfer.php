<?php
/**
 * library/App/FileTransfer.php
 *
 * ファイル関連処理定義クラス。
 *
 * ファイル関連処理を定義する。
 *
 * @category    App
 * @package     FileTransfer
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: FileTransfer.php 11 2014-02-25 11:49:12Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_File_Transfer_Adapter_Http
*/

/**
 * App_FileTransfer クラス
 *
 * ファイル関連処理定義クラス。
 *
 * @category    App
 * @package     FileTransfer
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_FileTransfer extends Zend_File_Transfer_Adapter_Http
{
    // ------------------------------------------------------------------ //

    /**
     * アップロード設定
     *
     * @var array
     */
    protected $_config = array();

    /**
     * アップロードファイル情報
     *
     * @var array
     */
    protected $_uploadFiles = array();

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param array $config アップロード設定
     * @param array $options Zend_File_Transfer_Adapter_Httpオプション
     * @return void
     */
    public function __construct($config, $options = array())
    {
        // スーパークラスコンストラクタ
        parent::__construct($options);
        $this->setOptions(
            array(
                'useByteString' => false,
                'ignoreNoFile'  => true,
            )
        );
        $this->setConfig($config);
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイルのバリデーション
     *
     * @param string $name パラメータ名
     * @param string $type ファイル種別
     * @return string|boolean 真：OK
     */
    public function checkUploadFile($name, $type)
    {
        $this->setFileValidator($type);
        if ($this->isValid($name)) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーメッセージの取得
     *
     * @return string エラーメッセージ
     */
    public function getErrorMessages()
    {
        return App_Validate_Common::toString(
            $this->getMessages()
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロード設定のセット
     *
     * @param array $value アップロード設定
     * @return void
     */
    public function setConfig($value)
    {
        $this->_config = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロード設定の取得
     *
     * @return array アップロード設定
     */
    public function getConfig()
    {
        return $this->_config;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイル情報のセット
     *
     * @param array $value アップロードファイル情報
     * @return void
     */
    public function setUploadFiles($value)
    {
        $this->_uploadFiles = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイル情報の取得
     *
     * @return array アップロードファイル情報
     */
    public function getUploadFiles()
    {
        return $this->_uploadFiles;
    }

    // ------------------------------------------------------------------ //

    /**
     * 指定パラメータ名のアップロードファイル情報の取得
     *
     * @param string $paramName パラメータ名
     * @return array アップロードファイル情報
     */
    public function getUploadFileInfo($paramName)
    {
        $uploadFiles = $this->getUploadFiles();
        if (array_key_exists($paramName, $uploadFiles)) {
            return $uploadFiles[$paramName];
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイルの一時格納ディレクトリの取得。
     *
     * @return string アップロードファイルの一時格納ディレクトリ
     */
    public function getTmpRootPath()
    {
        $config = $this->getConfig();
        return $config['tmp_root_path'];
    }

    // ------------------------------------------------------------------ //

    /**
     * 一時格納アップロードファイルのファイルID取得。
     *
     * @param string $paramName パラメータ名
     * @return string ファイルID
     */
    public function getTmpFileId($paramName)
    {
        $uploadFileInfo = $this->getUploadFileInfo($paramName);
        if (!is_null($uploadFileInfo)) {
            $fileId  = $uploadFileInfo['file_id'];
            return $fileId;
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * 一時格納アップロードファイルの取得。
     *
     * @param string $paramName パラメータ名
     * @return string アップロードファイルの一時格納ディレクトリ
     */
    public function getTmpFile($paramName)
    {
        $uploadFileInfo = $this->getUploadFileInfo($paramName);
        if (!is_null($uploadFileInfo)) {
            $fileId  = $uploadFileInfo['file_id'];
            $tmpFile = $this->getTmpFilePath($fileId);
            if (file_exists($tmpFile)) {
                return $tmpFile;
            }
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロード一時格納ファイルパスの取得。
     *
     * @param string $fileId ファイルID
     * @return string アップロード一時格納ファイル
     */
    public function getTmpFilePath($fileId)
    {
        $rootPath = $this->getTmpRootPath();
        $buffer   = preg_split('/_/u', $fileId);
        $tmpFile  = $rootPath . '/' . $buffer[0] . '/' . $fileId;
        if (file_exists($tmpFile)) {
            return $tmpFile;
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードバリデータ設定の取得。
     *
     * @param string $type ファイル種別
     * @return array バリデータ設定
     */
    public function getValidatorConfig($type)
    {
        $config = $this->getConfig();
        return $config['validator'][$type];
    }

    // ------------------------------------------------------------------ //

    /**
     * ファイルIDの生成
     *
     * @return string ファイルID
     */
    public static function generateFileId()
    {
        $fileId = null;
        try {
            $db     = Zend_Db_Table::getDefaultAdapter();
            $fileId = $db->fetchOne('select nextval(\'seq_upload_file_id\')');
        } catch (Exception $exception) {
            return null;
        }
        return $fileId;
    }

    // ------------------------------------------------------------------ //

    /**
     * 一時格納ディレクトリへアップロードファイルを一時保存
     *
     * @param string $name パラメータ名
     * @param string $type ファイル種別
     * @return string 一時格納ファイル名
     */
    public function storeToTemporary($name, $type)
    {
        // fileIdをセットする
        $fileId   = self::generateFileId();
        if (is_null($fileId)) {
            return false;
        }

        $filePath = $this->generateTmpFilePath($fileId, $type);
        if (is_null($filePath)) {
            return false;
        }

        // バリデ―ション
        if (!$this->checkUploadFile($name, $type)) {
            return false;
        }

        // 元のファイル数を取得しフィルタを設定する
        $orgFiles = $this->getFileName($name);
        if (is_array($orgFiles)) {
            $cnt = 0;
            $filePaths  = array();
            foreach ($orgFiles as $key => $val) {
                $cnt++;
                $filePaths[$key] = $filePath . '_' . $cnt;
            }
        } else {
            $filePaths  = $filePath;
        }

        // アップロードファイルを保存
        try {
            if ($this->receive($name)) {
                if (is_array($filePaths)) {
                    $baseFileName = array();
                    foreach ($filePaths as $key => $val) {
                        rename($orgFiles[$key], $filePaths[$key]);
                        chmod($filePaths[$key], 0644);
                        $baseFileName[$key] = basename($filePaths[$key]);
                    }
                } else {
                    rename($orgFiles, $filePaths);
                    chmod($filePaths, 0644);
                    $baseFileName = basename($filePaths);
                }
                return $baseFileName;
            }
        } catch (Exception $exception) {
            return false;
        }

        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイルの一時格納パスの生成
     *
     * @param string $fileId ファイルID
     * @param string $type ファイル種別
     * @return string 一時格納パス
     */
    public function generateTmpFilePath($fileId, $type)
    {
        $tmpFilePath = null;
        $elements = array();
        $tmpDir   = $this->getTmpRootPath();
        $tmpFile  = $type
                  . '_' . App_Utils::getToday()
                  . '_' . sprintf('%010d', $fileId);
        array_push($elements, $tmpDir);
        array_push($elements, $type);
        array_push($elements, $tmpFile);
        $tmpFilePath = implode(DIRECTORY_SEPARATOR, $elements);
        return $tmpFilePath;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードファイルのバリデータのセット
     *
     * @param string $type ファイル種別
     * @return void
     */
    public function setFileValidator($type)
    {
        $validatorConfig = $this->getValidatorConfig($type);
        if (array_key_exists('size', $validatorConfig)) {
            $sizeValidator = new Zend_Validate_File_Size($validatorConfig['size']);
            $sizeValidator->setMessages(
                array(
                    Zend_Validate_File_Size::TOO_BIG   => "アップロード可能なファイルサイズは%max% [byte]までです。",
                    Zend_Validate_File_Size::TOO_SMALL => "ファイルサイズが%min%[byte]以上のファイルを指定してください。",
                    Zend_Validate_File_Size::NOT_FOUND => "ファイル：%value%は読み込み不可となっているか、または、存在しません。",
                )
            );
            $this->addValidator($sizeValidator, false);
        }
        if (array_key_exists('count', $validatorConfig)) {
            $countValidator = new Zend_Validate_File_Count($validatorConfig['count']);
            $countValidator->setMessages(
                array(
                    Zend_Validate_File_Count::TOO_FEW  => '最低%min%ファイルを指定してください。',
                    Zend_Validate_File_Count::TOO_MANY => 'アップロード可能なファイル数は%max%までです。',
                )
            );
            $this->addValidator($countValidator, false);
        }
        if (array_key_exists('extension', $validatorConfig)) {
            $extValidator = new Zend_Validate_File_Extension($validatorConfig['extension']);
            $extValidator->setMessages(
                array(
                    Zend_Validate_File_Extension::FALSE_EXTENSION  => '%value%はアップロード可能な拡張子ではありません。',
                    Zend_Validate_File_Extension::NOT_FOUND        => "ファイル：%value%は読み込み不可となっているか、または、存在しません。",
                )
            );
            $this->addValidator($extValidator, false);
        }
        if (array_key_exists('mime', $validatorConfig)) {
            $mimeValidator = new Zend_Validate_File_MimeType($validatorConfig['mime']);
            $mimeValidator->setMessages(
                array(
                    Zend_Validate_File_MimeType::FALSE_TYPE   => "'%type%'は許可されたMime-Typeではありません。",
                    Zend_Validate_File_MimeType::NOT_DETECTED => "判別できないMime-Typeです。",
                    Zend_Validate_File_MimeType::NOT_READABLE => "ファイル：%value%は読み込み不可となっているか、または、存在しません。",
                )
            );
            $this->addValidator($mimeValidator, false);
        }
    }
}