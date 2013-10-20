SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `certifiedStudents`;
CREATE TABLE IF NOT EXISTS `certifiedStudents` (
  `certifiedStudentKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `studentKey` int(10) unsigned NOT NULL,
  `classNumber` char(4) NOT NULL,
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lastTested` datetime DEFAULT NULL,
  `currentTesting` datetime DEFAULT NULL,
  PRIMARY KEY (`certifiedStudentKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=548 ;

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `classKey` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `semester` smallint(5) unsigned NOT NULL DEFAULT '20093',
  `classNumber` char(4) NOT NULL,
  `sectionNumber` smallint(8) unsigned NOT NULL,
  `courseTitle` varchar(100) NOT NULL,
  `instructor` varchar(50) NOT NULL,
  `endDate` date NOT NULL,
  PRIMARY KEY (`classKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

DROP TABLE IF EXISTS `classQuestionList`;
CREATE TABLE IF NOT EXISTS `classQuestionList` (
  `questionKey` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `classNumber` char(4) NOT NULL,
  `questionText` varchar(1000) NOT NULL,
  `option1` varchar(500) NOT NULL,
  `option2` varchar(500) NOT NULL,
  `option3` varchar(500) NOT NULL,
  `option4` varchar(500) NOT NULL,
  `option5` varchar(500) NOT NULL,
  `correctOption` tinyint(4) NOT NULL,
  PRIMARY KEY (`questionKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2221 ;

DROP TABLE IF EXISTS `classStatusUpdates`;
CREATE TABLE IF NOT EXISTS `classStatusUpdates` (
  `classStatusKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classKey` smallint(5) unsigned NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `studentKey` smallint(5) unsigned NOT NULL,
  `dateUpdated` datetime NOT NULL,
  `keyRepliedTo` int(10) unsigned DEFAULT NULL,
  `statusText` varchar(5000) NOT NULL,
  PRIMARY KEY (`classStatusKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4597 ;

DROP TABLE IF EXISTS `conversationContent`;
CREATE TABLE IF NOT EXISTS `conversationContent` (
  `contentKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conversationKey` int(10) unsigned NOT NULL,
  `fromStudentKey` int(10) unsigned NOT NULL,
  `toStudentKey` int(10) unsigned NOT NULL,
  `messageSent` datetime NOT NULL,
  `messageContent` varchar(5000) NOT NULL,
  PRIMARY KEY (`contentKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `conversationKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fromStudentKey` smallint(5) unsigned NOT NULL,
  `toStudentKey` smallint(5) unsigned NOT NULL,
  `lastMessage` datetime NOT NULL,
  `lastToRead` datetime DEFAULT NULL,
  `lastFromRead` datetime DEFAULT NULL,
  PRIMARY KEY (`conversationKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

DROP TABLE IF EXISTS `instructors`;
CREATE TABLE IF NOT EXISTS `instructors` (
  `instructorKey` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `studentKey` smallint(5) unsigned NOT NULL,
  `classKey` smallint(5) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`instructorKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

DROP TABLE IF EXISTS `mailQueue`;
CREATE TABLE IF NOT EXISTS `mailQueue` (
  `mailQueueKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to` tinytext NOT NULL,
  `subject` tinytext NOT NULL,
  `message` text NOT NULL,
  `headers` tinytext NOT NULL,
  `dateAdded` datetime DEFAULT NULL,
  `dateSent` datetime DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailQueueKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=185049 ;

DROP TABLE IF EXISTS `mentorList`;
CREATE TABLE IF NOT EXISTS `mentorList` (
  `mentorKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `studentKey` smallint(5) unsigned NOT NULL,
  `classNumber` char(4) NOT NULL,
  `mentorRequestType` tinyint(1) NOT NULL,
  PRIMARY KEY (`mentorKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=361 ;

DROP TABLE IF EXISTS `miniMarkers`;
CREATE TABLE IF NOT EXISTS `miniMarkers` (
  `studentKey` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bashful` tinyint(4) DEFAULT NULL,
  `bold` tinyint(4) DEFAULT NULL,
  `careless` tinyint(4) DEFAULT NULL,
  `cold` tinyint(4) DEFAULT NULL,
  `complex` tinyint(4) DEFAULT NULL,
  `cooperative` tinyint(4) DEFAULT NULL,
  `creative` tinyint(4) DEFAULT NULL,
  `deep` tinyint(4) DEFAULT NULL,
  `disorganized` tinyint(4) DEFAULT NULL,
  `efficient` tinyint(4) DEFAULT NULL,
  `energetic` tinyint(4) DEFAULT NULL,
  `envious` tinyint(4) DEFAULT NULL,
  `extraverted` tinyint(4) DEFAULT NULL,
  `fretful` tinyint(4) DEFAULT NULL,
  `harsh` tinyint(4) DEFAULT NULL,
  `imaginative` tinyint(4) DEFAULT NULL,
  `inefficient` tinyint(4) DEFAULT NULL,
  `intellectual` tinyint(4) DEFAULT NULL,
  `jealous` tinyint(4) DEFAULT NULL,
  `kind` tinyint(4) DEFAULT NULL,
  `moody` tinyint(4) DEFAULT NULL,
  `organized` tinyint(4) DEFAULT NULL,
  `philosophical` tinyint(4) DEFAULT NULL,
  `practical` tinyint(4) DEFAULT NULL,
  `quiet` tinyint(4) DEFAULT NULL,
  `relaxed` tinyint(4) DEFAULT NULL,
  `rude` tinyint(4) DEFAULT NULL,
  `shy` tinyint(4) DEFAULT NULL,
  `sloppy` tinyint(4) DEFAULT NULL,
  `sympathetic` tinyint(4) DEFAULT NULL,
  `systematic` tinyint(4) DEFAULT NULL,
  `talkative` tinyint(4) DEFAULT NULL,
  `temperamental` tinyint(4) DEFAULT NULL,
  `touchy` tinyint(4) DEFAULT NULL,
  `uncreative` tinyint(4) DEFAULT NULL,
  `unenvious` tinyint(4) DEFAULT NULL,
  `unintellectual` tinyint(4) DEFAULT NULL,
  `unsympathetic` tinyint(4) DEFAULT NULL,
  `warm` tinyint(4) DEFAULT NULL,
  `withdrawn` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`studentKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `posttest_classes`;
CREATE TABLE IF NOT EXISTS `posttest_classes` (
  `cK` smallint(5) unsigned DEFAULT NULL,
  `sicK` mediumint(8) unsigned DEFAULT NULL,
  `sK` smallint(5) unsigned DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `retake` tinyint(4) DEFAULT NULL,
  `reaction1` tinyint(4) DEFAULT NULL,
  `reaction2` tinyint(4) DEFAULT NULL,
  `satisf1` tinyint(4) DEFAULT NULL,
  `satisf2` tinyint(4) DEFAULT NULL,
  `satisf3` tinyint(4) DEFAULT NULL,
  `satisf4` tinyint(4) DEFAULT NULL,
  `satisf5` tinyint(4) DEFAULT NULL,
  `satisf6` tinyint(4) DEFAULT NULL,
  `satisf7` tinyint(4) DEFAULT NULL,
  `satisf8` tinyint(4) DEFAULT NULL,
  `satisf9` tinyint(4) DEFAULT NULL,
  `satisf10` tinyint(4) DEFAULT NULL,
  `satisf11` tinyint(4) DEFAULT NULL,
  `satisf12` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `posttest_main`;
CREATE TABLE IF NOT EXISTS `posttest_main` (
  `sK` int(11) DEFAULT NULL,
  `id` char(32) DEFAULT NULL,
  `classes` tinyint(4) DEFAULT NULL,
  `commit1` tinyint(4) DEFAULT NULL,
  `commit2` tinyint(4) DEFAULT NULL,
  `commit3` tinyint(4) DEFAULT NULL,
  `commit4` tinyint(4) DEFAULT NULL,
  `commit5` tinyint(4) DEFAULT NULL,
  `commit6` tinyint(4) DEFAULT NULL,
  `commit7` tinyint(4) DEFAULT NULL,
  `commit8` tinyint(4) DEFAULT NULL,
  `commit9` tinyint(4) DEFAULT NULL,
  `commit10` tinyint(4) DEFAULT NULL,
  `commit11` tinyint(4) DEFAULT NULL,
  `commit12` tinyint(4) DEFAULT NULL,
  `commit13` tinyint(4) DEFAULT NULL,
  `commit14` tinyint(4) DEFAULT NULL,
  `commit15` tinyint(4) DEFAULT NULL,
  `commit16` tinyint(4) DEFAULT NULL,
  `commit17` tinyint(4) DEFAULT NULL,
  `commit18` tinyint(4) DEFAULT NULL,
  `commit19` tinyint(4) DEFAULT NULL,
  `commit20` tinyint(4) DEFAULT NULL,
  `commit21` tinyint(4) DEFAULT NULL,
  `commit22` tinyint(4) DEFAULT NULL,
  `commit23` tinyint(4) DEFAULT NULL,
  `commit24` tinyint(4) DEFAULT NULL,
  `commit25` tinyint(4) DEFAULT NULL,
  `commit26` tinyint(4) DEFAULT NULL,
  `commit27` tinyint(4) DEFAULT NULL,
  `commit28` tinyint(4) DEFAULT NULL,
  `commit29` tinyint(4) DEFAULT NULL,
  `commit30` tinyint(4) DEFAULT NULL,
  `commit31` tinyint(4) DEFAULT NULL,
  `commit32` tinyint(4) DEFAULT NULL,
  `commit33` tinyint(4) DEFAULT NULL,
  `commit34` tinyint(4) DEFAULT NULL,
  `commit35` tinyint(4) DEFAULT NULL,
  `commit36` tinyint(4) DEFAULT NULL,
  `commit37` tinyint(4) DEFAULT NULL,
  `commit38` tinyint(4) DEFAULT NULL,
  `commit39` tinyint(4) DEFAULT NULL,
  `commit40` tinyint(4) DEFAULT NULL,
  `commit41` tinyint(4) DEFAULT NULL,
  `commit42` tinyint(4) DEFAULT NULL,
  `commit43` tinyint(4) DEFAULT NULL,
  `commit44` tinyint(4) DEFAULT NULL,
  `commit45` tinyint(4) DEFAULT NULL,
  `commit46` tinyint(4) DEFAULT NULL,
  `commit47` tinyint(4) DEFAULT NULL,
  `commit48` tinyint(4) DEFAULT NULL,
  `commit49` tinyint(4) DEFAULT NULL,
  `commit50` tinyint(4) DEFAULT NULL,
  `commit51` tinyint(4) DEFAULT NULL,
  `commit52` tinyint(4) DEFAULT NULL,
  `commit53` tinyint(4) DEFAULT NULL,
  `commit54` tinyint(4) DEFAULT NULL,
  `commit55` tinyint(4) DEFAULT NULL,
  `commit56` tinyint(4) DEFAULT NULL,
  `commit57` tinyint(4) DEFAULT NULL,
  `commit58` tinyint(4) DEFAULT NULL,
  `commit59` tinyint(4) DEFAULT NULL,
  `commit60` tinyint(4) DEFAULT NULL,
  `doagainyes` tinyint(4) DEFAULT NULL,
  `doagainno` tinyint(4) DEFAULT NULL,
  `social1` tinyint(4) DEFAULT NULL,
  `social2` tinyint(4) DEFAULT NULL,
  `social3` tinyint(4) DEFAULT NULL,
  `social4` tinyint(4) DEFAULT NULL,
  `social5` tinyint(4) DEFAULT NULL,
  `social6` tinyint(4) DEFAULT NULL,
  `social7` tinyint(4) DEFAULT NULL,
  `social8` tinyint(4) DEFAULT NULL,
  `social9` tinyint(4) DEFAULT NULL,
  `social10` tinyint(4) DEFAULT NULL,
  `social11` tinyint(4) DEFAULT NULL,
  `social12` tinyint(4) DEFAULT NULL,
  `social13` tinyint(4) DEFAULT NULL,
  `social14` tinyint(4) DEFAULT NULL,
  `social15` tinyint(4) DEFAULT NULL,
  `social16` tinyint(4) DEFAULT NULL,
  `social17` tinyint(4) DEFAULT NULL,
  `social18` tinyint(4) DEFAULT NULL,
  `social19` tinyint(4) DEFAULT NULL,
  `social20` tinyint(4) DEFAULT NULL,
  `social21` tinyint(4) DEFAULT NULL,
  `social22` tinyint(4) DEFAULT NULL,
  `social23` tinyint(4) DEFAULT NULL,
  `social24` tinyint(4) DEFAULT NULL,
  `social25` tinyint(4) DEFAULT NULL,
  `best` varchar(5000) DEFAULT NULL,
  `worst` varchar(5000) DEFAULT NULL,
  `changeit` varchar(5000) DEFAULT NULL,
  `comments` varchar(5000) DEFAULT NULL,
  `focus` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `statusUpdates`;
CREATE TABLE IF NOT EXISTS `statusUpdates` (
  `updateKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `studentKey` int(10) unsigned NOT NULL,
  `statusDateUpdated` datetime NOT NULL,
  `statusText` varchar(1000) NOT NULL,
  PRIMARY KEY (`updateKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=633 ;

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `studentKey` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `signupStage` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `participationConsent` tinyint(1) NOT NULL DEFAULT '0',
  `gradeConsent` tinyint(1) NOT NULL DEFAULT '0',
  `lastLogin` datetime DEFAULT NULL,
  `totalLogins` int(10) unsigned NOT NULL DEFAULT '0',
  `anon` tinyint(1) NOT NULL DEFAULT '0',
  `anonNick` varchar(50) DEFAULT NULL,
  `first` varchar(25) DEFAULT NULL,
  `last` varchar(25) DEFAULT NULL,
  `signature` varchar(50) DEFAULT NULL,
  `username` varchar(25) NOT NULL,
  `password` char(32) NOT NULL,
  `tempPass` char(32) DEFAULT NULL,
  `tempExpire` datetime DEFAULT NULL,
  `studentEmail` varchar(50) DEFAULT NULL,
  `emailDirect` tinyint(4) NOT NULL DEFAULT '1',
  `emailMentoring` tinyint(4) NOT NULL DEFAULT '0',
  `emailClasses` tinyint(4) NOT NULL DEFAULT '0',
  `lastMentorEmail` datetime DEFAULT NULL,
  `profilePicture` mediumblob,
  `profilePictureThumb` mediumblob,
  `instructorFlag` tinyint(1) NOT NULL DEFAULT '0',
  `openness` decimal(3,2) DEFAULT NULL,
  `conscientiousness` decimal(3,2) DEFAULT NULL,
  `extraversion` decimal(3,2) DEFAULT NULL,
  `agreeableness` decimal(3,2) DEFAULT NULL,
  `emotionalStability` decimal(3,2) DEFAULT NULL,
  `gpa` varchar(25) DEFAULT NULL,
  `hours` tinyint(4) DEFAULT NULL,
  `sat` varchar(25) DEFAULT NULL,
  `act` varchar(25) DEFAULT NULL,
  `major` varchar(25) NOT NULL,
  `age` varchar(25) DEFAULT NULL,
  `year` tinyint(3) unsigned DEFAULT NULL,
  `sex` tinyint(3) unsigned DEFAULT NULL,
  `race` tinyint(3) unsigned DEFAULT NULL,
  `ethnicity` tinyint(3) unsigned DEFAULT NULL,
  `commit1` tinyint(4) DEFAULT NULL,
  `commit2` tinyint(4) DEFAULT NULL,
  `commit3` tinyint(4) DEFAULT NULL,
  `commit4` tinyint(4) DEFAULT NULL,
  `commit5` tinyint(4) DEFAULT NULL,
  `commit6` tinyint(4) DEFAULT NULL,
  `commit7` tinyint(4) DEFAULT NULL,
  `commit8` tinyint(4) DEFAULT NULL,
  `commit9` tinyint(4) DEFAULT NULL,
  `commit10` tinyint(4) DEFAULT NULL,
  `commit11` tinyint(4) DEFAULT NULL,
  `commit12` tinyint(4) DEFAULT NULL,
  `commit13` tinyint(4) DEFAULT NULL,
  `commit14` tinyint(4) DEFAULT NULL,
  `commit15` tinyint(4) DEFAULT NULL,
  `commit16` tinyint(4) DEFAULT NULL,
  `commit17` tinyint(4) DEFAULT NULL,
  `commit18` tinyint(4) DEFAULT NULL,
  `commit19` tinyint(4) DEFAULT NULL,
  `commit20` tinyint(4) DEFAULT NULL,
  `commit21` tinyint(4) DEFAULT NULL,
  `commit22` tinyint(4) DEFAULT NULL,
  `commit23` tinyint(4) DEFAULT NULL,
  `commit24` tinyint(4) DEFAULT NULL,
  `commit25` int(11) DEFAULT NULL,
  `commit26` int(11) DEFAULT NULL,
  `commit27` int(11) DEFAULT NULL,
  `commit28` int(11) DEFAULT NULL,
  `commit29` int(11) DEFAULT NULL,
  `commit30` int(11) DEFAULT NULL,
  `commit31` int(11) DEFAULT NULL,
  `commit32` int(11) DEFAULT NULL,
  `commit33` int(11) DEFAULT NULL,
  `commit34` int(11) DEFAULT NULL,
  `commit35` int(11) DEFAULT NULL,
  `commit36` int(11) DEFAULT NULL,
  `commit37` int(11) DEFAULT NULL,
  `commit38` int(11) DEFAULT NULL,
  `commit39` int(11) DEFAULT NULL,
  `commit40` int(11) DEFAULT NULL,
  `commit41` int(11) DEFAULT NULL,
  `commit42` int(11) DEFAULT NULL,
  `commit43` int(11) DEFAULT NULL,
  `commit44` int(11) DEFAULT NULL,
  `commit45` int(11) DEFAULT NULL,
  `commit46` int(11) DEFAULT NULL,
  `commit47` int(11) DEFAULT NULL,
  `commit48` int(11) DEFAULT NULL,
  `commit49` int(11) DEFAULT NULL,
  `commit50` int(11) DEFAULT NULL,
  `commit51` int(11) DEFAULT NULL,
  `commit52` int(11) DEFAULT NULL,
  `commit53` int(11) DEFAULT NULL,
  `commit54` int(11) DEFAULT NULL,
  `commit55` int(11) DEFAULT NULL,
  `commit56` int(11) DEFAULT NULL,
  `commit57` int(11) DEFAULT NULL,
  `commit58` int(11) DEFAULT NULL,
  `commit59` int(11) DEFAULT NULL,
  `commit60` int(11) DEFAULT NULL,
  PRIMARY KEY (`studentKey`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `studentEmail` (`studentEmail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=612 ;

DROP TABLE IF EXISTS `studentProfile`;
CREATE TABLE IF NOT EXISTS `studentProfile` (
  `studentKey` smallint(5) unsigned NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `age` varchar(50) NOT NULL,
  `relationshipStatus` tinyint(1) unsigned NOT NULL,
  `hometown` varchar(50) NOT NULL,
  `schoolStatus` tinyint(1) unsigned NOT NULL,
  `employer` varchar(100) NOT NULL,
  `workStatus` tinyint(1) unsigned NOT NULL,
  `major` varchar(50) NOT NULL,
  `favorite` varchar(50) NOT NULL,
  `clubs` varchar(500) NOT NULL,
  `activities` varchar(500) NOT NULL,
  `interests` varchar(500) NOT NULL,
  `faveMusic` varchar(500) NOT NULL,
  `faveTV` varchar(500) NOT NULL,
  `faveBooks` varchar(500) NOT NULL,
  `bigFiveUse` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`studentKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `studentsInClasses`;
CREATE TABLE IF NOT EXISTS `studentsInClasses` (
  `studentInClassKey` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `studentKey` smallint(5) unsigned NOT NULL,
  `pastStudent` tinyint(1) unsigned DEFAULT '0',
  `classKey` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`studentInClassKey`),
  UNIQUE KEY `studentClassCombo` (`studentKey`,`classKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=820 ;

DROP TABLE IF EXISTS `surveyAnswers`;
CREATE TABLE IF NOT EXISTS `surveyAnswers` (
  `responseKey` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `itemRecorded` datetime NOT NULL,
  `studentKey` smallint(5) unsigned NOT NULL,
  `questionKey` smallint(5) unsigned NOT NULL,
  `response` tinyint(3) unsigned NOT NULL,
  `correct` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`responseKey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9861 ;

DROP TABLE IF EXISTS `url_log`;
CREATE TABLE IF NOT EXISTS `url_log` (
  `click_id` int(11) NOT NULL AUTO_INCREMENT,
  `click_time` datetime NOT NULL,
  `shorturl` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `referrer` varchar(200) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(41) NOT NULL,
  `country_code` char(2) NOT NULL,
  PRIMARY KEY (`click_id`),
  KEY `shorturl` (`shorturl`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2725 ;

DROP TABLE IF EXISTS `url_options`;
CREATE TABLE IF NOT EXISTS `url_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  PRIMARY KEY (`option_id`,`option_name`),
  KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `url_url`;
CREATE TABLE IF NOT EXISTS `url_url` (
  `keyword` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `url` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(41) NOT NULL,
  `clicks` int(10) unsigned NOT NULL,
  PRIMARY KEY (`keyword`),
  KEY `timestamp` (`timestamp`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `vote_bestpost`;
CREATE TABLE IF NOT EXISTS `vote_bestpost` (
  `voteID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateSubmitted` datetime NOT NULL,
  `IDnum` char(32) NOT NULL,
  `vote` tinyint(4) NOT NULL,
  PRIMARY KEY (`voteID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;
