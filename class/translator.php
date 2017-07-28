<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author       XOOPS Development Team
 * @author       Simon Roberts (simon@chronolabs.org.au)
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class for policies
 */
class XtransamTranslator extends XoopsObject
{
    public function __construct($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ioid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('fromid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('toid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('fileid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('linetype', XOBJ_DTYPE_OTHER, null, false, 20);
        $this->initVar('name', XOBJ_DTYPE_OTHER);
        $this->initVar('orginal', XOBJ_DTYPE_OTHER);
        $this->initVar('translation', XOBJ_DTYPE_OTHER);
        $this->initVar('replacestr', XOBJ_DTYPE_OTHER, null);
        $this->initVar('out', XOBJ_DTYPE_INT, null, false);
        $this->initVar('line', XOBJ_DTYPE_INT, null, false);
        $this->initVar('auto', XOBJ_DTYPE_INT, null, false);
        $this->initVar('sm', XOBJ_DTYPE_OTHER, 'urlcode', false);
    }

    public function isempty()
    {
        $translation = $this->getVar('translation');

        return empty($translation);
    }
}

/**
 * XOOPS policies handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.org.au>
 * @package kernel
 */
class XtransamTranslatorHandler extends XoopsPersistableObjectHandler
{
    public $db;

    public function __construct(XoopsDatabase $db)
    {
        $this->db = $db;
        parent::__construct($db, 'xtransam_translator', 'XtransamTranslator', 'id', 'name');
    }

    public function change_charset($text, $charset_output = 'UTF-8', $charset_input = 'ISO-8859-1')
    {
        // Values can be : UTF-8 ; ASCII
        //$detected_encoding = mb_detect_encoding($text);
        // if ($charset_output == $detected_encoding) {
        if ($charset_output == $charset_input) {
            // Encoding is already good
            return $text;
        } elseif ($charset_output == 'UTF-8' && $charset_input == 'ISO-8859-1') {
            // ISO-8859-1 -> UTF8
            return utf8_encode($text);
        } elseif ($charset_output == 'ISO-8859-1' && $charset_input == 'UTF-8') {
            // UTF8 -> ISO-8859-1
            return utf8_decode($text);
        } else {
            // * -> UTF8
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($text, $charset_output, $charset_input);
            } else {
                return $text;
            }
        }
    }

    public function exists($trans)
    {
        if (!is_a($trans, 'XtransamTranslator')) {
            return true;
        }

        $criteria = new CriteriaCompo(new Criteria('`ioid`', $trans->getVar('ioid')), 'AND');
        $criteria->add(new Criteria('`fromid`', $trans->getVar('fromid')), 'AND');
        $criteria->add(new Criteria('`toid`', $trans->getVar('toid')), 'AND');
        $criteria->add(new Criteria('`fileid`', $trans->getVar('fileid')), 'AND');
        $criteria->add(new Criteria('`linetype`', $trans->getVar('linetype')), 'AND');
        $criteria->add(new Criteria('`name`', $trans->getVar('name')), 'AND');

        if ($this->getCount($criteria) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
