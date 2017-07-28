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

require_once __DIR__ . '/admin_header.php';

error_reporting(E_ALL);
global $xoopsDB, $xoopsModuleConfig;

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'dashboard';

switch ($op) {
    case 'deletebuffer':
        $sql[0] = 'DELETE FROM ' . $xoopsDB->prefix('xtransam_files') . " WHERE ioid = $id";
        $sql[1] = 'DELETE FROM ' . $xoopsDB->prefix('xtransam_translator') . " WHERE ioid = $id";
        $sql[2] = 'DELETE FROM ' . $xoopsDB->prefix('xtransam_iobase') . " WHERE id = $id";

        foreach ($sql as $fquestion) {
            $xoopsDB->queryF($fquestion);
        }

        redirect_header('index.php', 4, _AM_XTRANSAM_IODELETED);
        break;
    case 'save-languages':
        $langHandler = xoops_getModuleHandler('languages', 'xtransam');
        foreach ($id as $key => $value) {
            switch ($value) {
                case 'new':
                    $lang = $langHandler->create();
                    break;
                default:
                    $lang = $langHandler->get($value);
            }

            if (!empty($name[$key]) && !empty($code[$key])) {
                $lang->setVar('name', $name[$key]);
                $lang->setVar('code', $code[$key]);
                $lang->setVar('foldername', $folder[$key]);
                $lang->setVar('providers', $providers[$key]);
                @$langHandler->insert($lang);
            }
        }
        redirect_header('index.php?op=languages', 2, _AM_XTRANSAM_LANGSAVEOK);
        break;
    case 'languages':
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=languages');
        languagesForm_display();
        require_once __DIR__ . '/admin_footer.php';

        break;
    case 'export':
        global $xoopsUser;

        $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
        $io           = $ioHandler->get($id);
        $transHandler = xoops_getModuleHandler('translator', 'xtransam');
        $langHandler  = xoops_getModuleHandler('languages', 'xtransam');
        $filesHandler = xoops_getModuleHandler('files', 'xtransam');
        $criteria     = new CriteriaCompo(new Criteria('ioid', $io->getVar('id')));
        $files        = $filesHandler->getObjects($criteria);

        $from_folder = $langHandler->folder($io->getVar('languagefrom'));
        $to_folder   = $langHandler->folder($io->getVar('languageto'));

        // Changed by Chronolabs - Removed Code by Timgo - ansi conversion - 08/11/2011
        //include($GLOBALS['xoops']->path('modules'.DS.'xtransam'.DS.'include'.DS.'charset_utf-8.php'));

        foreach ($files as $file) {
            $path = array();
            foreach (explode('\\', $file->getVar('path')) as $nodea) {
                foreach (explode('/', $nodea) as $nodeb) {
                    $path[] = $nodeb;
                }
            }
            foreach ($path as $key => $value) {
                if (strtolower($value) == strtolower($from_folder)) {
                    $path[$key] = $to_folder;
                }
                $pdir = DS . $path[$key];
                mkdir($pdir, 0777);
            }
            $wpath = implode(DS, $path);
            if ($wpath != $file->getVar('path')) {
                $bfile    = file($file->getVar('path') . $file->getVar('filename'));
                $criteria = new CriteriaCompo(new Criteria('ioid', $io->getVar('id')));
                $criteria->add(new Criteria('fileid', $file->getVar('id')));
                $trans = $transHandler->getObjects($criteria);
                foreach ($trans as $tran) {
                    $search      = xtransam_convert_decode($tran->getVar('replacestr'), $tran->getVar('sm'));
                    $name        = xtransam_convert_decode($tran->getVar('name'), $tran->getVar('sm'));
                    $translation = xtransam_convert_decode($tran->getVar('translation'), $tran->getVar('sm'));
                    // Changed by Chronolabs - Removed Code by Timgo - ansi conversion - 08/11/2011
                    // foreach($GLOBALS['charset_utf8'] as $search => $replace)
                    // $translation = str_replace($search, $replace, $translation);
                    $replace                      = 'define("' . $name . '", "' . $translation . '");' . "\n";
                    $bfile[$tran->getVar('line')] = $replace;
                }
                // Changed by Chronolabs back to Footer
                $bfile[] = "<?php\n\n// Translation done by XTransam & "
                           . $GLOBALS['xoopsUser']->getVar('uname')
                           . ' ('
                           . $GLOBALS['xoopsUser']->getVar('email')
                           . ")\n// XTransam "
                           . ($GLOBALS['xtransamModule']->getVar('version') / 100)
                           . ' is written by Chronolabs Co-op & XOOPS Project - File Dumped on '
                           . date('Y-m-d H:i')
                           . "\n\n?>";
                @makeWritable($wpath, true);
                if (file_exists($wpath . $file->getVar('filename'))) {
                    unlink($wpath . $file->getVar('filename'));
                }
                $file   = @fopen($wpath . $file->getVar('filename'), 'w');
                $buffer = implode('', $bfile);
                fwrite($file, $buffer, strlen($buffer));
                fclose($file);
            }
            // Changed by Chronolabs to be recursive
            if (file_exists($indexFile = XOOPS_ROOT_PATH . 'modules/xtransam/language/index.html')) {
                copy($indexFile, $wpath . DS . 'index.html');
            }
        }
        redirect_header('index.php?op=bbs', 2, _AM_XTRANSAM_EXPORTCOMPLETE);
        break;
    case 'save':
        $transHandler = xoops_getModuleHandler('translator', 'xtransam');
        foreach ($trans as $key => $value) {
            $tran = $transHandler->get($key);
            $tran->setVar('translation', xtransam_convert_encode($value, $tran->getVar('sm')));
            $transHandler->insert($tran);
        }
        redirect_header("index.php?op=manage&id=$id&fileid=$fileid", 2, _AM_XTRANSAM_SAVECOMPLETE);
        break;
    case 'manage':

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();

        $adminObject->displayNavigation('index.php?op=manage');
        //$adminObject->displayIndex();
        managerForm_display($id, $fileid);

        require_once __DIR__ . '/admin_footer.php';
        break;

    case 'analysis':
        $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
        $filesHandler = xoops_getModuleHandler('files', 'xtransam');
        $io           = $ioHandler->get($id);
        @$filesHandler->analysepath($io);
        redirect_header('index.php?op=bbs', 2, _AM_XTRANSAM_ANLYSISCOMPLETE);
        break;

    case 'import':
        $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
        $filesHandler = xoops_getModuleHandler('files', 'xtransam');
        $io           = $ioHandler->get($id);
        @$filesHandler->importfiles($io);
        redirect_header('index.php?op=bbs', 2, _AM_XTRANSAM_IMPORTCOMPLETE);
        break;

    case 'translate':
        xoops_cp_header();
        echo sprintf(_AM_XTRANSAM_TRANSLATION_IN_PROCESS, $GLOBALS['xtransamModuleConfig']['php_execute_for'], isset($restart) ? $restart : 1);
        require_once __DIR__ . '/admin_footer.php';
        $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
        $io           = $ioHandler->get($id);
        $transHandler = xoops_getModuleHandler('translator', 'xtransam');
        $langHandler  = xoops_getModuleHandler('languages', 'xtransam');
        $criteria     = new CriteriaCompo(new Criteria('ioid', $io->getVar('id')));
        if ($transHandler->getCount($criteria) > 0) {
            $trans = $transHandler->getObjects($criteria);
            $start = time();
            foreach ($trans as $tran) {
                if ($tran->isempty()) {
                    $from                = $langHandler->code($io->getVar('languagefrom'));
                    $to                  = $langHandler->code($io->getVar('languageto'));
                    $GLOBALS['provider'] = $langHandler->provider($tran->getVar('toid'));
                    if (strlen($GLOBALS['provider']) > 0) {
                        $providerHandler = xoops_getModuleHandler('provider', 'xtransam');
                        $translation     = $providerHandler->provider->translate($from, $to, xtransam_convert_decode($tran->getVar('orginal'), $tran->getVar('sm')));
                        $tran->setVar('translation', xtransam_convert_encode($translation, $tran->getVar('sm')));
                        if (strlen($translation) > 0) {
                            $tran->setVar('auto', 1);
                        } else {
                            $tran->setVar('auto', 0);
                        }

                        $transHandler->insert($tran);
                    }
                    if ($start + $GLOBALS['xtransamModuleConfig']['php_execute_for'] - 3 < time()) {
                        ++$restart;
                        redirect_header("index.php?op=translate&id=$id&restart=$restart");
                        exit(0);
                    }
                }
            }
        }
        redirect_header('index.php?op=bbs', 2, _AM_XTRANSAM_TRANSLATIONCOMPLETE);
        break;

    case 'bbs':

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();

        $adminObject->displayNavigation('index.php?op=bbs');
        //$adminObject->displayIndex();
        translationForm_display(false);
        require_once __DIR__ . '/admin_footer.php';

        break;

    case 'wizard':
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('index.php?op=wizard');
        //$adminObject->displayIndex();
        wizardForm_display($step);
        translationForm_display(true);
        require_once __DIR__ . '/admin_footer.php';
        break;
    case 'dashboard':
    default:

        xoops_cp_header();

        $transHandler  = xoops_getModuleHandler('translator', 'xtransam');
        $langHandler   = xoops_getModuleHandler('languages', 'xtransam');
        $filesHandler  = xoops_getModuleHandler('files', 'xtransam');
        $iobaseHandler = xoops_getModuleHandler('iobase', 'xtransam');

        $adminObject = \Xmf\Module\Admin::getInstance();

        $adminObject->addInfoBox(_AM_XTRANSAM_ADMIN_NUMTRASL);
        $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_NUMFILES . '</label>', $filesHandler->getCount(null), 'Green');
        $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_NUMLINES . '</label>', $transHandler->getCount(null), 'Green');
        $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_NUMPROJECTS . '</label>', $iobaseHandler->getCount(null), 'Green');
        $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_NUMLANG . '</label>', $langHandler->getCount(null), 'Green');

        xoops_load('xoopscache');

        if ($googlecodes = XoopsCache::read('xtransam_google_pause')) {
            $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_GOOGLEAVAILABLE . '</label>', _YES, 'Green');
            $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . sprintf(_AM_XTRANSAM_THEREARE_GOOGLEERROR, $googlecodes['code'], $googlecodes['message']) . '</label>', '', 'Green');
        } else {
            $adminObject->addInfoBoxLine(_AM_XTRANSAM_ADMIN_NUMTRASL, '<label>' . _AM_XTRANSAM_THEREARE_GOOGLEAVAILABLE . '</label>', _NO, 'Green');
        }

        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->displayIndex();

        require_once __DIR__ . '/admin_footer.php';

        break;
}
