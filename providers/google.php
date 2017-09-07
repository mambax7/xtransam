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

xoops_load('xoopscache');

xoops_loadLanguage('google', 'xtransam');

/**
 * XOOPS policies handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Simon Roberts <simon@chronolabs.org.au>
 * @package kernel
 */
class XtransamGoogleProvider extends XtransamProviderHandler
{
    public $url = 'https://www.googleapis.com/language/translate/v2';
    public $db  = '';

    public function __construct(XoopsDatabase $db)
    {
        $this->db = $db;
    }

    public function translate($fromcode, $tocode, $value)
    {
        if ($message = XoopsCache::read('xtransam_google_pause')) {
            redirect_header(XOOPS_URL . '/modules/xtransam/admin/index.php', 10, sprintf(_GL_XTRANSAM_ERROR, $message['code'], $message['message']));
        }
        if (strlen($value) < (5 * 1024)) {
            $start = microtime(true);
            if ($chars = XoopsCache::read('xtransam_google_chars')) {
                if ($chars['totals']['seconds'] + strlen($value) > $GLOBALS['xoopsModuleConfig']['google_chars_seconds']) {
                    $go = false;
                    while ($go !== true) {
                        foreach ($chars['seconds'] as $microseconds => $countb) {
                            if (microtime(true) - $start > $GLOBALS['xoopsModuleConfig']['wait_in_case']) {
                                redirect_header(XOOPS_URL . '/modules/xtransam/admin/index.php', 10, sprintf(_GL_XTRANSAM_WAIT_TIMED_OUT, $GLOBALS['xoopsModuleConfig']['wait_in_case']));
                            }
                            if (microtime(true) - $microseconds > (float)$GLOBALS['xoopsModuleConfig']['micro_seconds_diff']
                                && $chars['seconds_cu'][$microseconds]['last'] - microtime(true) > (float)$GLOBALS['xoopsModuleConfig']['micro_seconds_diff']) {
                                $chars['seconds_cu'][$microseconds]['last']  = microtime(true);
                                $chars['seconds_cu'][$microseconds]['count'] = $chars['seconds_cu'][$microseconds]['count'] - $GLOBALS['xoopsModuleConfig']['google_chars_seconds'];
                                $chars['totals']['seconds']                  = $chars['totals']['seconds'] - $GLOBALS['xoopsModuleConfig']['google_chars_seconds'];
                                if ($chars['seconds_cu'][$microseconds]['count'] <= 0) {
                                    $chars['totals']['seconds'] = $chars['totals']['seconds'] - $countb;
                                    unset($chars['seconds'][$microseconds]);
                                    unset($chars['seconds_cu'][$microseconds]);
                                    if ($chars['totals']['seconds'] < $GLOBALS['xoopsModuleConfig']['google_chars_seconds']) {
                                        $go = true;
                                        continue;
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
                $go = false;
                if ($chars['totals']['day'] + strlen($value) > $GLOBALS['xoopsModuleConfig']['google_chars_day']) {
                    while ($go !== true) {
                        foreach ($chars['day'] as $microseconds => $countb) {
                            if (microtime(true) - $start > $GLOBALS['xoopsModuleConfig']['wait_in_case']) {
                                redirect_header(XOOPS_URL . '/modules/xtransam/admin/index.php', 10, sprintf(_GL_XTRANSAM_WAIT_TIMED_OUT, $GLOBALS['xoopsModuleConfig']['wait_in_case']));
                            }
                            if (microtime(true) - $microseconds > (float)$GLOBALS['xoopsModuleConfig']['micro_day_diff']
                                && $chars['day_cu'][$microseconds]['last'] - microtime(true) > (float)$GLOBALS['xoopsModuleConfig']['micro_seconds_diff']) {
                                $chars['day_cu'][$microseconds]['last']  = microtime(true);
                                $chars['day_cu'][$microseconds]['count'] = $chars['day_cu'][$microseconds]['count'] - $GLOBALS['xoopsModuleConfig']['google_chars_seconds'];
                                $chars['totals']['day']                  = $chars['totals']['day'] - $GLOBALS['xoopsModuleConfig']['google_chars_seconds'];
                                if ($chars['day_cu'][$microseconds]['count'] <= 0) {
                                    unset($chars['day'][$microseconds]);
                                    unset($chars['day_cu'][$microseconds]);
                                    if ($chars['totals']['day'] < $GLOBALS['xoopsModuleConfig']['google_chars_day']) {
                                        $go = true;
                                        continue;
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $chars = [
                    'totals' => [
                        'seconds' => 0,
                        'day'     => 0
                    ]
                ];
            }
            $chars['totals']['day']              = $chars['totals']['day'] + strlen($value);
            $chars['totals']['seconds']          = $chars['totals']['seconds'] + strlen($value);
            $response                            = xtransam_obj2array(parent::json_decode($this->send_curl($this->url, $value, $fromcode, $tocode, XOOPS_URL)));
            $time                                = microtime(true);
            $chars['day'][$time]                 = strlen($value);
            $chars['seconds'][$time]             = strlen($value);
            $chars['day_cu'][$time]['last']      = microtime(true);
            $chars['seconds_cu'][$time]['last']  = microtime(true);
            $chars['day_cu'][$time]['count']     = strlen($value);
            $chars['seconds_cu'][$time]['count'] = strlen($value);
            XoopsCache::write('xtransam_google_chars', $chars, 60 * 60 * 48);

            if (count($response['error']['errors']) > 0) {
                switch ($response['code']) {
                    case 403:
                        XoopsCache::write('xtransam_google_pause', [
                            'code'    => $response['error']['code'],
                            'message' => $response['error']['message']
                        ], 60 * 60 * 24);
                        break;
                    default:
                        XoopsCache::write('xtransam_google_pause', [
                            'code'    => $response['error']['code'],
                            'message' => $response['error']['message']
                        ], 60);
                        break;
                }

                return '';
            } else {
                return parent::_unescapeUTF8EscapeSeq(parent::clean($response['data']['translations'][0]['translatedText']));
            }
        }
    }

    public function send_curl($url, $text, $from, $to, $referer = null)
    {
        $response = xtransam_callAPI($url, [
            'key'    => $GLOBALS['xoopsModuleConfig']['google_api_key'],
            'source' => $from,
            'target' => $to,
            'q'      => $text
        ], 'GET');

        return $response;
    }
}
