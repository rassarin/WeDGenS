<?php
/**
 * library/App/Filter/RemoveBOM.php
 *
 * UTF-8 Byte Order Markの除去フィルタクラス。
 *
 * @category    App
 * @package     Filter
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: RemoveBOM.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Filter_Interface
*/

/**
 * App_Filter_RemoveBOM クラス
 *
 * UTF-8 Byte Order Markの除去フィルタクラス。
 *
 * @category    App
 * @package     Filter
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Filter_RemoveBOM implements Zend_Filter_Interface
{
    /**
     * Zend_Filter_Interfaceフィルタメソッド
     *
     * @param  string $value フィルタ前の値
     * @return string フィルタ後の値
     */
    public function filter($value)
    {
        return App_Utils::deleteBOM($value);
    }
}