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
 * @author    Simon Roberts <onokazu@xoops.org>
 * @copyright copyright (c) 2009-2003 XOOPS.org
 * @package   kernel
 */
class XtransamLanguages extends XoopsObject
{
    public function __construct($id = null)
    {
        $this->initVar('lang_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('providers', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('code', XOBJ_DTYPE_TXTBOX, null, true, 6);
        $this->initVar('foldername', XOBJ_DTYPE_TXTBOX, null, false, 255);
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
class XtransamLanguagesHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'xtransam_languages', 'XtransamLanguages', 'lang_id', 'name');
    }

    public function name($id)
    {
        if ($this->getCount(new Criteria('lang_id', $id)) > 0) {
            $objs = $this->getObjects(new Criteria('lang_id', $id), false, false);

            return $objs[0]['name'];
        } else {
            return false;
        }
    }

    public function provider($id)
    {
        if ($this->getCount(new Criteria('lang_id', $id)) > 0) {
            $objs = $this->getObjects(new Criteria('lang_id', $id), false, false);
            if (in_array($GLOBALS['xoopsModuleConfig']['provider'], $objs[0]['providers'])) {
                return $GLOBALS['xoopsModuleConfig']['provider'];
            }
            switch ($GLOBALS['xoopsModuleConfig']['provider']) {
                case 'google':
                    return 'bing';
                    break;
                case 'mymemory':
                    return 'google';
                    break;
                case 'bing':
                    return 'google';
                    break;
            }
        } else {
            return array($GLOBALS['xoopsModuleConfig']['provider']);
        }
    }

    public function folder($id)
    {
        if ($this->getCount(new Criteria('lang_id', $id)) > 0) {
            $objs = $this->getObjects(new Criteria('lang_id', $id), false, false);
            if (empty($objs[0]['foldername'])) {
                return strtolower($objs[0]['name']);
            } else {
                return strtolower($objs[0]['foldername']);
            }
        } else {
            return false;
        }
    }

    public function code($id)
    {
        if ($this->getCount(new Criteria('lang_id', $id)) > 0) {
            $objs = $this->getObjects(new Criteria('lang_id', $id), false, false);

            return $objs[0]['code'];
        } else {
            return false;
        }
    }

    public function validlanguage($name)
    {
        require_once XOOPS_ROOT_PATH . '/class/criteria.php';
        $criteria = new CriteriaCompo(new Criteria('`name`', $name, 'LIKE'), 'OR');
        $criteria->add(new Criteria('`foldername`', $name, 'LIKE'), 'OR');
        if ($this->getCount($criteria) > 0) {
            $objs = $this->getObjects($criteria);

            return $objs[0]->getVar('lang_id');
        } else {
            return false;
        }
    }
}
