CREATE TABLE IF NOT EXISTS `browser_copys` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `disk_config` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL,
  `password` varchar(16) DEFAULT '',
  `space_all` int(10) NOT NULL DEFAULT '0',
  `space_use` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `browser_books` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL DEFAULT '0',
  `title` varchar(120) NOT NULL,
  `url` varchar(250) NOT NULL,
  `nums` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `browser_caches` (
  `keyid` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '0',
  `uid` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `browser_cookies` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `domain` varchar(255) NOT NULL COMMENT '有效域名',
  `path` varchar(255) NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  `key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `host` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `browser_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pass` varchar(30) NOT NULL,
  `num_time` int(10) NOT NULL,
  `num_look` int(10) NOT NULL,
  `num_size_html` int(10) NOT NULL,
  `num_size_pic` int(10) NOT NULL,
  `config_pic` int(1) NOT NULL,
  `config_wap2wml` int(1) NOT NULL,
  `config_useragent` int(1) NOT NULL,
  `config_ipagent` varchar(30) NOT NULL,
  `config_cutpage` int(3) NOT NULL,
  `config_pic_wap` tinyint(1) NOT NULL DEFAULT '0',
  `config_ipagent_open` tinyint(1) NOT NULL DEFAULT '0',
  `template_foot` varchar(90) NOT NULL DEFAULT '[book]|[menu][br][size]',
  PRIMARY KEY (`id`,`name`),
  KEY `name` (`name`),
  KEY `pass` (`pass`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `disk_dir` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `oid` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `disk_file` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `title` varchar(50) NOT NULL,
  `mime` varchar(10) DEFAULT '',
  `file` varchar(50) NOT NULL,
  `oid` int(5) NOT NULL DEFAULT '0',
  `size` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `oid` (`oid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;