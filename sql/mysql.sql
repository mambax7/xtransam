CREATE TABLE `xtransam_files` (
  `id`       INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ioid`     INT(12) UNSIGNED          DEFAULT '0',
  `filename` VARCHAR(255)              DEFAULT NULL,
  `path`     VARCHAR(255)              DEFAULT NULL,
  `imported` TINYINT(2)                DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  CHARSET = utf8;

CREATE TABLE `xtransam_iobase` (
  `id`           INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `point`        ENUM ('core', 'module')   DEFAULT 'module',
  `path`         VARCHAR(255)              DEFAULT NULL,
  `languagefrom` SMALLINT(6)               DEFAULT '0',
  `languageto`   SMALLINT(6)               DEFAULT '0',
  `total`        INT(12)                   DEFAULT '0',
  `done`         INT(12)                   DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

CREATE TABLE `xtransam_languages` (
  `lang_id`    INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `providers`  VARCHAR(500)             DEFAULT 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}',
  `name`       VARCHAR(255)             DEFAULT NULL,
  `code`       VARCHAR(6)               DEFAULT NULL,
  `foldername` VARCHAR(255)             DEFAULT NULL,
  PRIMARY KEY (`lang_id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;

INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Albanian', 'sq', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Arabic', 'ar', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Bulgarian', 'bg', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Catalan', 'ca', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Chinese (Simplified)', 'zh-CN', 'schinese');
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Chinese (Traditional)', 'zh-TW', 'tchinese');
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Chinese (Simplified)', 'zh-CN', 'schinese_utf8');
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Chinese (Traditional)', 'zh-TW', 'tchinese_utf8');
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Croatian', 'hr', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Czech', 'cs', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Danish', 'da', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Dutch', 'nl', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'English', 'en', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Estonian', 'et', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Filipino', 'tl', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Finnish', 'fi', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'French', 'fr', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Galician', 'gl', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'German', 'de', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Greek', 'el', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Hebrew', 'iw', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Hindi', 'hi', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Hungarian', 'hu', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Indonesian', 'id', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Italian', 'it', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Japanese', 'ja', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Korean', 'ko', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Latvian', 'lv', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Lithuanian', 'lt', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Maltese', 'mt', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Norwegian', 'no', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Polish', 'pl', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Portuguese', 'pt', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Romanian', 'ro', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Russian', 'ru', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Serbian', 'sr', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Slovak', 'sk', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Slovenian', 'sl', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Spanish', 'es', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Swedish', 'sv', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Thai', 'th', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Turkish', 'tr', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Ukrainian', 'uk', NULL);
INSERT INTO `xtransam_languages` (`lang_id`, `providers`, `name`, `code`, `foldername`) VALUES (0, 'a:3:{i:0;s:6:"google";i:1;s:8:"mymemory";i:2;s:4:"bing";}', 'Vietnamese', 'vi', NULL);

CREATE TABLE `xtransam_translator` (
  `id`          INT(30) UNSIGNED NOT NULL                           AUTO_INCREMENT,
  `ioid`        INT(12) UNSIGNED                                    DEFAULT '0',
  `fromid`      SMALLINT(6)                                         DEFAULT '0',
  `toid`        SMALLINT(6)                                         DEFAULT '0',
  `fileid`      INT(12)                                             DEFAULT '0',
  `linetype`    ENUM ('define')                                     DEFAULT 'define',
  `name`        MEDIUMTEXT,
  `orginal`     MEDIUMTEXT,
  `translation` MEDIUMTEXT,
  `replacestr`  MEDIUMTEXT,
  `out`         ENUM ('1', '0')                                     DEFAULT '0',
  `line`        INT(12)                                             DEFAULT '0',
  `auto`        TINYINT(2)                                          DEFAULT '0',
  `sm`          ENUM ('urlcode', 'uucode', 'base64', 'hex', 'open') DEFAULT 'urlcode',
  PRIMARY KEY (`id`)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8;
