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
class XtransamFiles extends XoopsObject
{
    public function __construct($id = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ioid', XOBJ_DTYPE_INT);
        $this->initVar('filename', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('path', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('imported', XOBJ_DTYPE_INT, 0);
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
class XtransamFilesHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $regex     = '/define\((.*) \);|define  \((.*) \);|define \((.*) \);|define \( ([\"\', \ta-zA-Z0-9_]+)\);|define\( (.*) \);|define\((.*)\);|define  \((.*)\);|define \((.*)\);|define \( ([\"\', \ta-zA-Z0-9_]+)\);|define\( (.*)\);|define\((.*)   \);|define  \((.*)  \);|define \((.*)   \);|define \( (.*)  \);|define\( (.*)   \);/';
    public $seperator = array(
        '", "',
        '","',
        '"    ,   "',
        '"  , "',
        '",   "',
        '" , "',
        '", \'',
        '",\'',
        '"  ,   \'',
        '" , \'',
        '",  \'',
        '" , \'',
        '\', \'',
        '\',\'',
        '\'   ,   \'',
        '\'    , \'',
        '\', \'',
        '\' , \'',
        '\', "',
        '\',"',
        '\' ,   "',
        '\' , "',
        '\',  "',
        '\' , "'
    );

    public function __construct(XoopsDatabase $db)
    {
        $this->db = $db;
        parent::__construct($db, 'xtransam_files', 'XtransamFiles', 'id', 'path');
    }

    public function importfiles($io)
    {
        if (!is_a($io, 'XtransamIobase')) {
            return false;
        }

        global $xoopsModuleConfig;

        $transHandler = xoops_getModuleHandler('translator', 'xtransam');

        $criteria = new CriteriaCompo(new Criteria('`ioid`', $io->getVar('id')), 'AND');
        $criteria->add(new Criteria('`imported`', 0), 'AND');
        $files = $this->getObjects($criteria);

        foreach ($files as $file) {
            $content = file_get_contents($file->getVar('path') . DS . $file->getVar('filename'));
            $lines   = explode("\n", $content);
            foreach ($lines as $key => $line) {
                if (strpos(' ' . $line, 'define') > 0) {
                    @preg_match_all($this->regex, $line, $matches);

                    $def = array();
                    foreach ($matches as $match) {
                        foreach ($match as $result) {
                            if (!empty($result) && substr($result, 0, 6) !== 'define') {
                                foreach ($this->seperator as $sep) {
                                    if (strpos($result, $sep) > 0) {
                                        if (count(explode($sep, $result)) == 2) {
                                            $result = explode($sep, $result);
                                        }
                                    }
                                }

                                $def[0] = substr(trim($result[0]), 1);
                                $def[1] = substr(trim($result[1]), 0, strlen(trim($result[1])) - 1);
                            } elseif (substr($result, 0, 6) === 'define') {
                                $def[3] = $result;
                            }
                        }
                    }

                    if (!empty($def[0]) && !empty($def[1])) {
                        $trans = $transHandler->create();
                        $trans->setVar('ioid', $io->getVar('id'));
                        $trans->setVar('fromid', $io->getVar('languagefrom'));
                        $trans->setVar('toid', $io->getVar('languageto'));
                        $trans->setVar('fileid', $file->getVar('id'));
                        $trans->setVar('linetype', 'define');
                        $trans->setVar('line', $key);
                        $trans->setVar('name', xtransam_convert_encode($def[0], $xoopsModuleConfig['store_method']));
                        $trans->setVar('orginal', xtransam_convert_encode($def[1], $xoopsModuleConfig['store_method']));
                        $trans->setVar('replacestr', xtransam_convert_encode($def[3], $xoopsModuleConfig['store_method']));
                        $trans->setVar('sm', $xoopsModuleConfig['store_method']);
                        if (!$transHandler->exists($trans)) {
                            $transHandler->insert($trans);
                        }
                    }
                }
            }

            $file->setVar('imported', '1');
            $this->insert($file);
        }
    }

    public function analysepath($io)
    {
        if (!is_a($io, 'XtransamIobase')) {
            return false;
        }

        switch ($io->getVar('point')) {
            case 'core':
                $files = $this->dirToArray(XOOPS_ROOT_PATH . DS . 'language' . DS . $io->getVar('path'), true);
                $path  = XOOPS_ROOT_PATH . DS . 'language' . DS . $io->getVar('path');
                break;
            case 'module':
                $files = $this->dirToArray(XOOPS_ROOT_PATH . DS . 'modules' . DS . $io->getVar('path'), true);
                $path  = XOOPS_ROOT_PATH . DS . 'modules' . DS . $io->getVar('path');
                break;
        }

        foreach ($files as $key => $file) {
            if (file_exists($file)) {
                $tfile = $this->create();
                $tfile->setVar('filename', basename($file));
                $tfile->setVar('path', str_replace(basename($file), '', $file));
                $tfile->setVar('ioid', $io->getVar('id'));
                if (!$this->exists($tfile)) {
                    @$this->insert($tfile);
                }
            }
        }
    }

    private function dirToArray($directory, $recursive, $fileext = 'php')
    {
        $array_items = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                $filecomp = explode('.', $file);
                if ($file !== '.' && $file !== '..') {
                    if (is_dir($directory . '/' . $file)) {
                        if ($recursive) {
                            $array_items = array_merge($array_items, $this->dirToArray($directory . '/' . $file, $recursive));
                        }
                        $file = $directory . '/' . $file;
                        if (in_array($fileext, $filecomp)) {
                            $array_items[] = preg_replace("/\/\//si", '/', $file);
                        }
                    } else {
                        $file = $directory . '/' . $file;
                        if (in_array($fileext, $filecomp)) {
                            $array_items[] = preg_replace("/\/\//si", '/', $file);
                        }
                    }
                }
            }
            closedir($handle);
        }

        return $array_items;
    }

    public function exists($file)
    {
        if (!is_a($file, 'XtransamFiles')) {
            return true;
        }

        $criteria = new CriteriaCompo(new Criteria('`ioid`', $file->getVar('ioid')), 'AND');
        $criteria->add(new Criteria('`path`', $file->getVar('path')), 'AND');
        $criteria->add(new Criteria('`filename`', $file->getVar('filename')), 'AND');
        if ($this->getCount($criteria) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
