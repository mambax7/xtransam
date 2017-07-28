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

function languagesForm_display()
{
    $form_sel = new XoopsThemeForm(_AM_XTRANSAM_LANGUAGEMATRIX, 'languages', $_SERVER['PHP_SELF'] . '');
    $form_sel->setExtra("enctype='multipart/form-data'");
    $langHandler = xoops_getModuleHandler('languages', 'xtransam');
    $langs       = $langHandler->getObjects(null);

    $ele_tray[0] = new XoopsFormElementTray(_AM_XTRANSAM_NEWLANG, '&nbsp;');
    $ele_tray[0]->addElement(new XoopsFormText(_AM_XTRANSAM_NAME, 'name[0]', 26, 255));
    $ele_tray[0]->addElement(new XoopsFormText(_AM_XTRANSAM_CODE, 'code[0]', 10, 6));
    $ele_tray[0]->addElement(new XoopsFormText(_AM_XTRANSAM_FOLDER, 'folder[0]', 26, 255));

    $ele_select    = array();
    $ele_select[0] = new XoopsFormCheckbox(_AM_XTRANSAM_PROVIDER, 'providers[0][]');
    $ele_select[0]->addOption('google', _MI_XTRANSAM_PROVIDER_REST_GOOGLE);
    $ele_select[0]->addOption('mymemory', _MI_XTRANSAM_PROVIDER_REST_MYMEMORY);
    $ele_select[0]->addOption('bing', _MI_XTRANSAM_PROVIDER_REST_BING);

    $ele_tray[0]->addElement($ele_select[0]);
    $ele_tray[0]->addElement(new XoopsFormHidden('id[0]', 'new'));
    $form_sel->addElement($ele_tray[0]);

    foreach ($langs as $lang) {
        $ele_tray[$lang->getVar('lang_id')] = new XoopsFormElementTray($lang->getVar('name'), '&nbsp;');
        $ele_tray[$lang->getVar('lang_id')]->addElement(new XoopsFormText(_AM_XTRANSAM_NAME, 'name[' . $lang->getVar('lang_id') . ']', 26, 255, htmlspecialchars($lang->getVar('name'))));
        $ele_tray[$lang->getVar('lang_id')]->addElement(new XoopsFormText(_AM_XTRANSAM_CODE, 'code[' . $lang->getVar('lang_id') . ']', 10, 6, htmlspecialchars($lang->getVar('code'))));
        $ele_tray[$lang->getVar('lang_id')]->addElement(new XoopsFormText(_AM_XTRANSAM_FOLDER, 'folder[' . $lang->getVar('lang_id') . ']', 26, 255, htmlspecialchars($lang->getVar('foldername'))));
        $ele_tray[$lang->getVar('lang_id')]->addElement(new XoopsFormHidden('id[' . $lang->getVar('id') . ']', $lang->getVar('lang_id')));
        $ele_select[$lang->getVar('lang_id')] = new XoopsFormCheckbox(_AM_XTRANSAM_PROVIDER, 'providers[' . $lang->getVar('lang_id') . '][]');
        $ele_select[$lang->getVar('lang_id')]->addOption('google', _MI_XTRANSAM_PROVIDER_REST_GOOGLE);
        $ele_select[$lang->getVar('lang_id')]->addOption('mymemory', _MI_XTRANSAM_PROVIDER_REST_MYMEMORY);
        $ele_select[$lang->getVar('lang_id')]->addOption('bing', _MI_XTRANSAM_PROVIDER_REST_BING);
        $ele_select[$lang->getVar('lang_id')]->setValue($lang->getVar('providers'));
        $ele_tray[$lang->getVar('lang_id')]->addElement($ele_select[$lang->getVar('lang_id')]);
        $form_sel->addElement($ele_tray[$lang->getVar('lang_id')]);
    }

    $form_sel->addElement(new XoopsFormHidden('op', 'save-languages'));
    $form_sel->addElement(new XoopsFormButton('', 'send', _SUBMIT, 'submit'));
    $form_sel->display();
}

function managerForm_display($ioid, $fileid)
{
    $form_sel = new XoopsThemeForm(_AM_XTRANSAM_SELECTFILETOEDIT, 'filesel', $_SERVER['PHP_SELF'] . '');
    $form_sel->setExtra("enctype='multipart/form-data'");

    $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
    $langHandler  = xoops_getModuleHandler('languages', 'xtransam');
    $filesHandler = xoops_getModuleHandler('files', 'xtransam');
    $transHandler = xoops_getModuleHandler('translator', 'xtransam');

    $io    = $ioHandler->get($ioid);
    $files = $filesHandler->getObjects(new Criteria('ioid', $ioid));

    if ($fileid == 0) {
        $fileid = $files[0]->getVar('id');
    }

    $table_sel = new XoopsFormSelect(_AM_XTRANSAM_SELECTFILETOEDIT . ':', 'select');
    $table_sel->setExtra('onchange="window.location=\'\'+this.options[this.selectedIndex].value"');
    $table_sel->setValue("index.php?op=manage&id=$ioid&fileid=" . $fileid);
    foreach ($files as $file) {
        $table_sel->addOption("index.php?op=manage&id=$ioid&fileid=" . $file->getVar('id'), $file->getVar('filename'));
    }

    $form_sel->addElement($table_sel);
    $form_sel->display();

    $form_sel = new XoopsThemeForm(sprintf(_AM_XTRANSAM_TRANSLATIONEDITOR, $langHandler->name($io->getVar('languagefrom')), $langHandler->name($io->getVar('languageto'))), 'editor', $_SERVER['PHP_SELF'] . '');
    $form_sel->setExtra("enctype='multipart/form-data'");

    $trans = $transHandler->getObjects(new Criteria('fileid', $fileid));

    foreach ($trans as $tran) {
        $txtbox[$tran->getVar('id')] = new XoopsFormTextArea(xtransam_convert_decode($tran->getVar('name'), $tran->getVar('sm')), 'trans[' . $tran->getVar('id') . ']', xtransam_convert_decode($tran->getVar('translation'), $tran->getVar('sm')), 2, 85);
        $txtbox[$tran->getVar('id')]->setDescription($langHandler->name($tran->getVar('fromid')) . ': ' . xtransam_convert_decode($tran->getVar('orginal'), $tran->getVar('sm')));
        $form_sel->addElement($txtbox[$tran->getVar('id')]);
    }

    $form_sel->addElement(new XoopsFormHidden('op', 'save'));
    $form_sel->addElement(new XoopsFormHidden('id', $ioid));
    $form_sel->addElement(new XoopsFormHidden('fileid', $fileid));
    $form_sel->addElement(new XoopsFormButton('', 'send', _SUBMIT, 'submit'));
    $form_sel->display();
}

function translationForm_display($display = false)
{
    $form_sel = new XoopsThemeForm(_AM_XTRANSAM_TRANSLATIONBUFFER, 'transgo', $_SERVER['PHP_SELF'] . '');
    $form_sel->setExtra("enctype='multipart/form-data'");
    $ioHandler    = xoops_getModuleHandler('iobase', 'xtransam');
    $langHandler  = xoops_getModuleHandler('languages', 'xtransam');
    $filesHandler = xoops_getModuleHandler('files', 'xtransam');
    $transHandler = xoops_getModuleHandler('translator', 'xtransam');
    $ios          = $ioHandler->getObjects(null);
    foreach ($ios as $io) {
        $ele_tray[$io->getVar('id')] = new XoopsFormElementTray($io->getVar('point'), '&nbsp;');
        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel(_AM_XTRANSAM_LABELTRANSLATEPATH, $io->getVar('path')));
        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel(_AM_XTRANSAM_LABELTRANSLATEPOINT, $io->getVar('point')));
        $lang_from = $langHandler->get($io->getVar('languagefrom'));
        $lang_to   = $langHandler->get($io->getVar('languageto'));
        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel(_AM_XTRANSAM_LABELTRANSLATEFROM, $lang_from->getVar('name')));
        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel(_AM_XTRANSAM_LABELTRANSLATETO, $lang_to->getVar('name')));
        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=deletebuffer&id=' . $io->getVar('id') . '">' . _DELETE . '</a>'));
        if ($display === false) {
            $criteria = new CriteriaCompo(new Criteria('ioid', $io->getVar('id')));
            if ($filesHandler->getCount($criteria) == 0) {
                $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=analysis&id=' . $io->getVar('id') . '">' . _AM_XTRANSAM_ANALYSIS . '</a>'));
            } else {
                $criteria->add(new Criteria('imported', '0'));
                if ($filesHandler->getCount($criteria) > 0) {
                    $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=import&id=' . $io->getVar('id') . '">' . _AM_XTRANSAM_IMPORT . '</a>'));
                } else {
                    $criteria = new CriteriaCompo(new Criteria('ioid', $io->getVar('id')));
                    $criteria->add(new Criteria('auto', '1'));
                    if ($transHandler->getCount($criteria) != $transHandler->getCount(new Criteria('ioid', $io->getVar('id')))) {
                        $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=translate&id=' . $io->getVar('id') . '">' . _AM_XTRANSAM_TRANSLATE . '</a>'));
                    }
                    $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=export&id=' . $io->getVar('id') . '">' . _AM_XTRANSAM_EXPORT . '</a>'));
                    $ele_tray[$io->getVar('id')]->addElement(new XoopsFormLabel('&nbsp;|&nbsp;', '<a href="index.php?op=manage&id=' . $io->getVar('id') . '">' . _AM_XTRANSAM_TRANSMANAGER . '</a>'));
                }
            }
        }
        $form_sel->addElement($ele_tray[$io->getVar('id')]);
    }

    $form_sel->addElement(new XoopsFormHidden('op', 'translate'));
    $form_sel->display();
}

function wizardForm_display($step)
{
    $langHandler = xoops_getModuleHandler('languages', 'xtransam');

    switch ($step) {
        case 'save':
            $ioHandler = xoops_getModuleHandler('iobase', 'xtransam');
            $io        = $ioHandler->create();
            $io->setVar('point', $_POST['point']);
            switch ($_POST['point']) {
                case 'core':
                    $io->setVar('path', $_POST['path']);
                    break;
                default:
                case 'module':
                    $io->setVar('path', $_POST['module'] . '/language/' . $_POST['path']);
            }

            $io->setVar('languagefrom', (int)$_POST['languagefrom']);
            $io->setVar('languageto', (int)$_POST['languageto']);

            if (!$ioHandler->exists($io)) {
                if (!$ioHandler->insert($io)) {
                    redirect_header('index.php?op=wizard', 2, _AM_XTRANSAM_IOSAVEFAIL);
                } else {
                    redirect_header('index.php?op=wizard', 2, _AM_XTRANSAM_IOSAVEGOOD);
                }
            } else {
                redirect_header('index.php?op=wizard', 2, _AM_XTRANSAM_IOEXISTS);
            }

            break;
        case 'finished':

            $form_sel = new XoopsThemeForm(_AM_XTRANSAM_SELECTLANGUAGE, 'sellang', $_SERVER['PHP_SELF'] . '');
            $form_sel->setExtra("enctype='multipart/form-data'");
            $form_sel->addElement(new XoopsFormHidden('point', $_POST['point']));
            $form_sel->addElement(new XoopsFormHidden('module', $_POST['module']));
            $form_sel->addElement(new XoopsFormHidden('path', $_POST['path']));
            $xl = new XoopsLists();

            switch ($_POST['point']) {
                case 'core':
                    $form_sel->addElement(new XoopsFormHidden('step', 'save'));
                    $dlist = $xl->getDirListAsArray(XOOPS_ROOT_PATH . '/language');
                    break;
                default:
                case 'module':
                    $form_sel->addElement(new XoopsFormHidden('step', 'save'));
                    $dlist = $xl->getDirListAsArray(XOOPS_ROOT_PATH . '/modules/' . $_POST['module'] . '/language/');
            }

            $table_from = new XoopsFormSelect(_AM_XTRANSAM_SELECTLANGFROM . ':', 'languagefrom');

            $id = $langHandler->validlanguage($_POST['path']);
            if ($id > 0) {
                $criteria                             = new Criteria('lang_id', $id);
                $lang                                 = $langHandler->getObjects($criteria);
                $validto[$lang[0]->getVar('lang_id')] = $lang[0]->getVar('name');
            }

            $table_from->addOptionArray($validto);
            $form_sel->addElement($table_from);

            $table_to = new XoopsFormSelect(_AM_XTRANSAM_SELECTLANGTO . ':', 'languageto');
            foreach ($langHandler->getObjects(null) as $lang) {
                if (!in_array($lang->getVar('name'), $validto)) {
                    $validfrom[$lang->getVar('lang_id')] = $lang->getVar('name');
                }
            }

            $table_to->addOptionArray($validfrom);
            $form_sel->addElement($table_to);
            $form_sel->addElement(new XoopsFormHidden('op', 'wizard'));
            $form_sel->addElement(new XoopsFormButton('', 'send', _SUBMIT, 'submit'));
            $form_sel->display();
            break;

            break;
        case '2':

            $form_sel = new XoopsThemeForm(_AM_XTRANSAM_SELECTMODULEPATH, 'sellang', $_SERVER['PHP_SELF'] . '');
            $form_sel->setExtra("enctype='multipart/form-data'");
            $form_sel->addElement(new XoopsFormHidden('point', $_POST['point']));
            $form_sel->addElement(new XoopsFormHidden('module', $_POST['module']));

            $xl = new XoopsLists();

            switch ($_POST['point']) {
                default:
                case 'module':
                    $form_sel->addElement(new XoopsFormHidden('step', 'finished'));
                    $dlist = $xl->getDirListAsArray(XOOPS_ROOT_PATH . '/modules/' . $_POST['module'] . '/language');
            }

            $table_sel = new XoopsFormSelect(_AM_XTRANSAM_SELECTPATH . ':', 'path');
            foreach ($dlist as $path) {
                $table_sel->addOption($path, $_POST['module'] . '/language/' . $path);
            }

            $form_sel->addElement($table_sel);
            $form_sel->addElement(new XoopsFormHidden('op', 'wizard'));
            $form_sel->addElement(new XoopsFormButton('', 'send', _SUBMIT, 'submit'));
            $form_sel->display();
            break;

        case '1':

            $form_sel = new XoopsThemeForm(_AM_XTRANSAM_SELECTBASEPATH, 'sellang', $_SERVER['PHP_SELF'] . '');
            $form_sel->setExtra("enctype='multipart/form-data'");
            $form_sel->addElement(new XoopsFormHidden('point', $_GET['point']));

            $xl = new XoopsLists();

            switch ($_GET['point']) {
                case 'core':
                    $form_sel->addElement(new XoopsFormHidden('step', 'finished'));
                    $dlist     = $xl->getDirListAsArray(XOOPS_ROOT_PATH . '/language');
                    $table_sel = new XoopsFormSelect(_AM_XTRANSAM_SELECTPATH . ':', 'path');
                    break;
                default:
                case 'module':
                    $form_sel->addElement(new XoopsFormHidden('step', '2'));
                    $dlist     = $xl->getDirListAsArray(XOOPS_ROOT_PATH . '/modules');
                    $table_sel = new XoopsFormSelect(_AM_XTRANSAM_SELECTPATH . ':', 'module');
            }

            foreach ($dlist as $path) {
                $table_sel->addOption($path, $path);
            }

            $form_sel->addElement($table_sel);
            $form_sel->addElement(new XoopsFormHidden('op', 'wizard'));
            $form_sel->addElement(new XoopsFormButton('', 'send', _SUBMIT, 'submit'));
            $form_sel->display();
            break;

        case '0':
        default:

            $form_sel = new XoopsThemeForm(_AM_XTRANSAM_SELECTPOINTMODE, 'selpoint', $_SERVER['PHP_SELF'] . '');
            $form_sel->setExtra("enctype='multipart/form-data'");

            $table_sel = new XoopsFormSelect(_AM_XTRANSAM_SELECTPOINT . ':', 'select');
            $table_sel->setExtra('onchange="window.location=\'\'+this.options[this.selectedIndex].value"');

            $table_sel->addOption('index.php?op=wizard&step=0', '(Please Select an Option)');
            $table_sel->addOption('index.php?op=wizard&point=module&step=1', 'Module Language');
            $table_sel->addOption('index.php?op=wizard&point=core&step=1', 'Core Language');

            $form_sel->addElement($table_sel);
            $form_sel->display();
    }
}
