-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2013 at 08:38 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `page_current_rev` int(11) NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `page_current_rev` (`page_current_rev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `rev` (
  `rev_id` int(11) NOT NULL AUTO_INCREMENT,
  `rev_content` text NOT NULL,
  `rev_comment` tinytext NOT NULL,
  `rev_user` int(11) NOT NULL,
  `rev_timestamp` datetime NOT NULL,
  `rev_page` int(11) NOT NULL,
  PRIMARY KEY (`rev_id`),
  KEY `rev_user` (`rev_user`),
  KEY `rev_page` (`rev_page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='storing actual revision content here for now, unlike MediaWiki' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` tinytext NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Constraints
--
ALTER TABLE `page`
  ADD CONSTRAINT `page_ibfk_1` FOREIGN KEY (`page_current_rev`) REFERENCES `rev` (`rev_id`);

ALTER TABLE `rev`
  ADD CONSTRAINT `rev_ibfk_2` FOREIGN KEY (`rev_page`) REFERENCES `page` (`page_id`),
  ADD CONSTRAINT `rev_ibfk_1` FOREIGN KEY (`rev_user`) REFERENCES `user` (`user_id`);

--
-- Initial entries (first page and users)
--