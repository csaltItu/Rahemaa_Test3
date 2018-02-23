# Host: 127.0.0.1  (Version 5.6.24)
# Date: 2017-07-31 16:44:08
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "actions"
#

DROP TABLE IF EXISTS `actions`;
CREATE TABLE `actions` (
  `action_id` int(9) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `new_action` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

#
# Structure for table "calls"
#

DROP TABLE IF EXISTS `calls`;
CREATE TABLE `calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `call_id` int(9) NOT NULL,
  `u_id` int(9) NOT NULL,
  `action_id` int(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_id` varchar(90) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9613 DEFAULT CHARSET=latin1;

#
# Structure for table "comment"
#

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `story_id` int(9) NOT NULL,
  `file_id` int(9) NOT NULL,
  `u_id` int(9) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_recorded` int(11) NOT NULL DEFAULT '0',
  `approved` int(11) NOT NULL DEFAULT '0',
  `Call_ID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `story_id` (`story_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

#
# Structure for table "cost"
#

DROP TABLE IF EXISTS `cost`;
CREATE TABLE `cost` (
  `cost_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` varchar(100) NOT NULL,
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

#
# Structure for table "cost_more_info"
#

DROP TABLE IF EXISTS `cost_more_info`;
CREATE TABLE `cost_more_info` (
  `info_id` int(9) NOT NULL AUTO_INCREMENT,
  `cost_id` int(9) NOT NULL,
  `file_id` int(9) NOT NULL,
  PRIMARY KEY (`info_id`),
  KEY `cost_id` (`cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Structure for table "doctor_tip"
#

DROP TABLE IF EXISTS `doctor_tip`;
CREATE TABLE `doctor_tip` (
  `tip_id` int(9) NOT NULL AUTO_INCREMENT,
  `file_id` int(9) NOT NULL,
  PRIMARY KEY (`tip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

#
# Structure for table "dt_more_info"
#

DROP TABLE IF EXISTS `dt_more_info`;
CREATE TABLE `dt_more_info` (
  `info_id` int(9) NOT NULL AUTO_INCREMENT,
  `tip_id` int(9) NOT NULL,
  `file_id` int(9) NOT NULL,
  PRIMARY KEY (`info_id`),
  KEY `tip_id` (`tip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Structure for table "forward"
#

DROP TABLE IF EXISTS `forward`;
CREATE TABLE `forward` (
  `forward_id` int(9) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `file_id` varchar(64) NOT NULL,
  `u_id` int(9) NOT NULL,
  `dest` varchar(255) NOT NULL,
  `call_id` int(9) NOT NULL,
  `info` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`forward_id`)
) ENGINE=InnoDB AUTO_INCREMENT=528 DEFAULT CHARSET=latin1;

#
# Structure for table "main_feedback"
#

DROP TABLE IF EXISTS `main_feedback`;
CREATE TABLE `main_feedback` (
  `feedback_id` int(9) NOT NULL AUTO_INCREMENT,
  `u_id` int(9) NOT NULL,
  `file_id` int(9) NOT NULL,
  `is_recorded` int(11) DEFAULT '0',
  `Call_ID` int(11) NOT NULL,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;

#
# Structure for table "recordings_info"
#

DROP TABLE IF EXISTS `recordings_info`;
CREATE TABLE `recordings_info` (
  `file_id` int(9) NOT NULL AUTO_INCREMENT,
  `call_id` int(9) NOT NULL,
  `u_id` int(9) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Structure for table "story"
#

DROP TABLE IF EXISTS `story`;
CREATE TABLE `story` (
  `story_id` int(9) NOT NULL AUTO_INCREMENT,
  `file_id` int(9) NOT NULL,
  PRIMARY KEY (`story_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

#
# Structure for table "story_tip"
#

DROP TABLE IF EXISTS `story_tip`;
CREATE TABLE `story_tip` (
  `tip_id` int(9) NOT NULL AUTO_INCREMENT,
  `story_id` int(9) NOT NULL,
  `file_id` int(9) NOT NULL,
  PRIMARY KEY (`tip_id`),
  KEY `story_id` (`story_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Structure for table "suggestion"
#

DROP TABLE IF EXISTS `suggestion`;
CREATE TABLE `suggestion` (
  `sugg_id` int(9) NOT NULL AUTO_INCREMENT,
  `is_recorded` int(9) NOT NULL DEFAULT '0',
  `Call_ID` int(16) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`sugg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=latin1;

#
# Structure for table "user_story"
#

DROP TABLE IF EXISTS `user_story`;
CREATE TABLE `user_story` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `is_recorded` int(11) DEFAULT '0',
  `approved` int(11) NOT NULL DEFAULT '0',
  `Call_ID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
