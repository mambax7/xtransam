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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

function xoops_module_update_xtransam(XoopsModule $module)
{
    global $xoopsDB;
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_files') . ' ADD COLUMN(ioid INT(12))');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_translator') . " ADD COLUMN(`sm` ENUM('urlcode','uucode','base64','hex','open') DEFAULT 'urlcode')");
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_translator') . ' CHANGE COLUMN `hexval_name` `name` MEDIUMTEXT');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_translator') . ' CHANGE COLUMN `hexval_orginal` `orginal` MEDIUMTEXT');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_translator') . ' CHANGE COLUMN `hexval_translation` `translation` MEDIUMTEXT');
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('xtransam_languages') . ' CHANGE COLUMN `provider` `providers` VARCHAR(500)');
    $result = $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtransam_languages') . " SET `providers` = '" . serialize(array('google', 'mymemory', 'bing')) . "'");

    return true;
}
