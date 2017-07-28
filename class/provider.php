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

require_once $GLOBALS['xoops']->path('modules/xtransam/include/functions.php');

/**
 * XOOPS translation provider master class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Translation API not based in user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.coop>
 * @package xtransam
 */
class XtransamProviderHandler extends XoopsPersistableObjectHandler
{
    public $db       = '';
    public $provider = '';

    public function __construct(XoopsDatabase $db)
    {
        if (is_object($db)) {
            $this->db = $db;
        } else {
            $this->db = $GLOBALS['xoopsDB'];
        }
        if (isset($GLOBALS['provider'])) {
            $this->provider = $this->createInstance($GLOBALS['provider']);
        } else {
            $this->provider = $this->createInstance($GLOBALS['xoopsModuleConfig']['provider']);
        }
    }

    public function createInstance($provider = '')
    {
        if (empty($provider)) {
            $provider = $GLOBALS['xoopsModuleConfig']['provider'];
        }
        require_once $GLOBALS['xoops']->path('modules/xtransam/providers' . DS . $provider . '.php');
        $class = 'Xtransam' . ucfirst($provider) . 'Provider';
        if (class_exists($class)) {
            return new $class($this->db);
        }

        return false;
    }

    public function json_decode($data)
    {
        if (!function_exists('json_decode')) {
            if (!class_exists('Services_JSON')) {
                require_once $GLOBALS['xoops']->path('modules/xtransam/include/JSON.php');
            }
            $json = new Services_JSON();

            return xtransam_obj2array($json->decode($data));
        } else {
            return xtransam_obj2array(json_decode($data));
        }
    }

    public function _unescapeUTF8EscapeSeq($str)
    {
        return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return html_entity_decode(\'&#x\'.$matches[1].\';\', ENT_NOQUOTES, \'UTF-8\');'), $str);
    }

    public function clean($var)
    {
        $var = htmlspecialchars_decode($var);

        return $var;
    }
}
