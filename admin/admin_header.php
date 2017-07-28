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

require_once __DIR__ . '/../../../include/cp_header.php';
//require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');

//require_once __DIR__ . '/../class/utility.php';
//require_once __DIR__ . '/../include/common.php';

$moduleDirName = basename(dirname(__DIR__));

if (!defined('_CHARSET')) {
    define('_CHARSET', 'UTF-8');
}
if (!defined('_CHARSET_ISO')) {
    define('_CHARSET_ISO', 'ISO-8859-1');
}

$GLOBALS['myts'] = MyTextSanitizer::getInstance();

/** @var XoopsModuleHandler $moduleHandler */
$moduleHandler                   = xoops_getHandler('module');
$configHandler                   = xoops_getHandler('config');
$GLOBALS['xtransamModule']       = $moduleHandler->getByDirname('xtransam');
$GLOBALS['xtransamModuleConfig'] = $configHandler->getConfigList($GLOBALS['xtransamModule']->getVar('mid'));

set_time_limit($GLOBALS['xtransamModuleConfig']['php_execute_for']);

xoops_load('pagenav');
xoops_load('xoopslists');
xoops_load('xoopsformloader');

require_once $GLOBALS['xoops']->path('class/xoopsmailer.php');
require_once $GLOBALS['xoops']->path('class/xoopstree.php');
require_once $GLOBALS['xoops']->path('modules/xtransam/include/functions.php');
require_once $GLOBALS['xoops']->path('modules/xtransam/include/forms.php');

$pathImageIcon        = XOOPS_URL . '/' . $GLOBALS['xtransamModule']->getInfo('icons16');
$pathImageAdmin       = XOOPS_URL . '/' . $GLOBALS['xtransamModule']->getInfo('icons32');
$xoopsModuleAdminPath = $GLOBALS['xoops']->path('www/' . $GLOBALS['xoopsModule']->getInfo('dirmoduleadmin'));
require_once "{$xoopsModuleAdminPath}/moduleadmin.php";

$myts = MyTextSanitizer::getInstance();

if ($xoopsUser) {
    $modulepermHandler = xoops_getHandler('groupperm');
    if (!$modulepermHandler->checkRight('module_admin', $GLOBALS['xtransamModule']->getVar('mid'), $xoopsUser->getGroups())) {
        redirect_header(XOOPS_URL, 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new XoopsTpl();
}

$xoopsTpl->assign('pathImageIcon', $pathImageIcon);
//xoops_cp_header();

$traslactionsHandler = xoops_getModuleHandler('iobase', 'xtransam');

if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        ${$k} = $v;
    }
}

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}
