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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

require_once $GLOBALS['xoops']->path('modules/xtransam/include/functions.php');
require_once $GLOBALS['xoops']->path('modules/xtransam/include/provider.php');

/**
 * XOOPS translation provider class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Translation API not based in user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.coop>
 * @package xtransam
 */
class XtransamBingProvider extends XtransamProviderHandler
{
    public $url = 'http://api.bing.net/json.aspx';
    public $db  = '';

    public function __construct(XoopsDatabase $db)
    {
        $this->db = $db;
    }

    public function translate($fromcode, $tocode, $value)
    {
        $response = xtransam_obj2array(parent::json_decode($this->send_curl($this->url, $value, $fromcode, $tocode, XOOPS_URL)));
        if (isset($response['SearchResponse']['Translation']['Results'][0]['TranslatedTerm'])) {
            return parent::_unescapeUTF8EscapeSeq(parent::clean($response['SearchResponse']['Translation']['Results'][0]['TranslatedTerm']));
        } else {
            return false;
        }
    }

    public function send_curl($url, $text, $from, $to, $referer = null)
    {
        $response = xtransam_callAPI($url, [
            'AppId'                      => $GLOBALS['xoopsModuleConfig']['bing_api_key'],
            'Query'                      => $text,
            'Sources'                    => 'Translation',
            'Version'                    => '2.2',
            'Translation.SourceLanguage' => $from,
            'Translation.TargetLanguage' => $to
        ], 'GET');

        return $response;
    }
}
