SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Create tables
--
CREATE TABLE `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `page_current_rev` int(11) NOT NULL,
  `page_views` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_current_rev` (`page_current_rev`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

CREATE TABLE `rev` (
  `rev_id` int(11) NOT NULL AUTO_INCREMENT,
  `rev_content` text NOT NULL,
  `rev_comment` tinytext NOT NULL,
  `rev_user` int(11) NOT NULL,
  `rev_timestamp` datetime NOT NULL,
  `rev_page` int(11) NOT NULL,
  PRIMARY KEY (`rev_id`),
  KEY `rev_user` (`rev_user`),
  KEY `rev_page` (`rev_page`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE `rev_tags` (
  `rt_rev` int(11) NOT NULL,
  `rt_tag` varchar(255) NOT NULL,
  KEY `rt_rev` (`rt_rev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` tinytext NOT NULL,
  `user_password` varchar(150) NOT NULL,
  `user_timestamp` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

CREATE TABLE IF NOT EXISTS `user_groups` (
  `ug_user` int(11) NOT NULL,
  `ug_group` varchar(255) NOT NULL,
  KEY `ug_user` (`ug_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Populate with initial data
--
INSERT INTO `page` 
(`page_id`, `page_title`, `page_current_rev`)
VALUES (1, 'Home', 1);

INSERT INTO `rev` 
(`rev_id`, `rev_content`, `rev_comment`, `rev_user`, `rev_timestamp`, `rev_page`)
VALUES (1, 'This is the **home page** of your wiki.', 'Initial revision', 1, NOW(), 1);

INSERT INTO `rev_tags`
(`rt_rev`, `rt_tag`)
VALUES (1, 'new page');

INSERT INTO `user`
(`user_id`, `user_name`, `user_password`, `user_timestamp`)
VALUES (1, 'StickyWiki default', '', NOW());

INSERT INTO `user_groups`
(`ug_user`, `ug_group`)
VALUES (1, 'system');

--
-- Constraints
--
ALTER TABLE `rev`
  ADD CONSTRAINT `rev_ibfk_1` FOREIGN KEY (`rev_user`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `rev_ibfk_2` FOREIGN KEY (`rev_page`) REFERENCES `page` (`page_id`);

ALTER TABLE `rev_tags`
  ADD CONSTRAINT `rev_tags_ibfk_1` FOREIGN KEY (`rt_rev`) REFERENCES `rev` (`rev_id`);

ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`ug_user`) REFERENCES `user` (`user_id`);


--
-- 
--
CREATE TABLE `siteinfo` (
  `db_version` INT(11) NOT NULL
);
INSERT INTO `siteinfo`
(`db_version`)
VALUES (3);