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

/** @var XoopsModuleHandler $moduleHandler */

/*
$moduleHandler     = xoops_getHandler('module');
$GLOBALS['xtransamModule'] = XoopsModule::getByDirname('xtransam');
$moduleInfo                = $moduleHandler->get($GLOBALS['xtransamModule']->getVar('mid'));
//$pathIcon32 = XOOPS_URL .'/'. $moduleInfo->getInfo('icons32');
$pathIcon32 = $moduleInfo->getInfo('icons32');
*/

use Xmf\Module\Admin;
use Xmf\Module\Helper;

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

//$path = dirname(dirname(dirname(__DIR__)));
//require_once $path . '/mainfile.php';

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Helper::getHelper('system');
}
$pathIcon32    = Admin::menuIconPath('');
$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

xoops_loadLanguage('modinfo', $moduleDirName);

$adminmenu[] = [
    'title' => _MI_XTRANSAM_ADMENU1,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_XTRANSAM_ADMENU2,
    'link'  => 'admin/index.php?op=wizard',
    'icon'  => $pathIcon32 . '/wizard.png',
];

$adminmenu[] = [
    'title' => _MI_XTRANSAM_ADMENU3,
    'link'  => 'admin/index.php?op=bbs',
    'icon'  => $pathIcon32 . '/translations.png',
];

$adminmenu[] = [
    'title' => _MI_XTRANSAM_ADMENU4,
    'link'  => 'admin/index.php?op=languages',
    'icon'  => $pathIcon32 . '/languages.png',
];

$adminmenu[] = [
    'title' => _MI_XTRANSAM_ADMENU5,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];

