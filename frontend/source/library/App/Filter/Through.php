<?php
/**
 * library/App/Filter/Through.php
 *
 * ダミーフィルタクラス。(何もせずそのまま返す)
 *
 * @category    App
 * @package     Filter
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Through.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Filter_Interface
*/

/**
 * App_Filter_Through クラス
 *
 * ダミーフィルタクラス。(何もせずそのまま返す)
 *
 * @category    App
 * @package     Filter
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Filter_Through implements Zend_Filter_Interface
{
    /**
     * Zend_Filter_Interfaceフィルタメソッド
     *
     * @param  string $value フィルタ前の値
     * @return string フィルタ後の値
     */
    public function filter($value)
    {
        // 何もせずそのまま返す
        return $value;
    }
}