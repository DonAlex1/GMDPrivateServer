
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 11-07-2018 a las 15:19:15
-- Versión del servidor: 10.1.24-MariaDB
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `u800066094_gd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accComments`
--

CREATE TABLE IF NOT EXISTS `accComments` (
  `accountID` int(11) NOT NULL,
  `userName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentID`),
  KEY `userID` (`accountID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `accountID` int(11) NOT NULL AUTO_INCREMENT,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `isBanned` tinyint(1) NOT NULL DEFAULT '0',
  `userID` int(11) NOT NULL DEFAULT '0',
  `friends` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `blockedBy` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `blocked` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unused',
  `mS` int(11) NOT NULL DEFAULT '0',
  `frS` int(11) NOT NULL DEFAULT '0',
  `cS` int(11) NOT NULL DEFAULT '0',
  `youtubeurl` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitch` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `registerDate` int(11) NOT NULL DEFAULT '0',
  `usernameChangeDate` int(11) NOT NULL DEFAULT '0',
  `usernameCount` int(11) NOT NULL DEFAULT '0',
  `friendsCount` int(11) NOT NULL DEFAULT '0',
  `hash` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `discordID` bigint(20) NOT NULL DEFAULT '0',
  `discordLinkReq` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountID`),
  UNIQUE KEY `userName` (`username`),
  KEY `isAdmin` (`isAdmin`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=102 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `value2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value3` int(11) NOT NULL DEFAULT '0',
  `value4` int(11) NOT NULL DEFAULT '0',
  `value5` int(11) NOT NULL DEFAULT '0',
  `value6` int(11) NOT NULL DEFAULT '0',
  `account` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artists`
--

CREATE TABLE IF NOT EXISTS `artists` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `author` text COLLATE utf8_unicode_ci NOT NULL,
  `YouTube` varchar(69) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bannedArtists`
--

CREATE TABLE IF NOT EXISTS `bannedArtists` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `authorID` int(11) NOT NULL,
  `authorName` varchar(69) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bannedIPs`
--

CREATE TABLE IF NOT EXISTS `bannedIPs` (
  `hostname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blocks`
--

CREATE TABLE IF NOT EXISTS `blocks` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `person1` int(11) NOT NULL,
  `person2` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `accountID` int(11) NOT NULL,
  `userName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `levelID` int(11) NOT NULL,
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `percent` int(11) NOT NULL DEFAULT '0',
  `isSpam` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`commentID`),
  KEY `levelID` (`levelID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cpshares`
--

CREATE TABLE IF NOT EXISTS `cpshares` (
  `shareID` int(11) NOT NULL AUTO_INCREMENT,
  `levelID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`shareID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dailyFeatures`
--

CREATE TABLE IF NOT EXISTS `dailyFeatures` (
  `feaID` int(11) NOT NULL AUTO_INCREMENT,
  `levelID` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`feaID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `demonDiffSuggestions`
--

CREATE TABLE IF NOT EXISTS `demonDiffSuggestions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `levelID` int(11) NOT NULL DEFAULT '0',
  `accountID` int(11) NOT NULL DEFAULT '0',
  `diff` int(11) NOT NULL DEFAULT '0',
  `isMod` int(1) NOT NULL DEFAULT '0',
  `IP` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `suggestionDate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friendreqs`
--

CREATE TABLE IF NOT EXISTS `friendreqs` (
  `accountID` int(11) NOT NULL,
  `toAccountID` int(11) NOT NULL,
  `comment` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `uploadDate` int(11) NOT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `isNew` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `toAccountID` (`toAccountID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friendships`
--

CREATE TABLE IF NOT EXISTS `friendships` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `person1` int(11) NOT NULL,
  `person2` int(11) NOT NULL,
  `isNew1` tinyint(1) NOT NULL,
  `isNew2` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `person1` (`person1`),
  KEY `person2` (`person2`),
  KEY `isNew1` (`isNew1`),
  KEY `isNew2` (`isNew2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gauntlets`
--

CREATE TABLE IF NOT EXISTS `gauntlets` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `levels` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levels`
--

CREATE TABLE IF NOT EXISTS `levels` (
  `gameVersion` int(11) NOT NULL,
  `binaryVersion` int(11) NOT NULL DEFAULT '0',
  `userName` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `levelID` int(11) NOT NULL AUTO_INCREMENT,
  `levelName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `levelDesc` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `levelVersion` int(11) NOT NULL DEFAULT '1',
  `levelLength` int(11) NOT NULL DEFAULT '0',
  `audioTrack` int(11) NOT NULL DEFAULT '0',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `password` int(11) NOT NULL DEFAULT '0',
  `original` int(11) NOT NULL DEFAULT '0',
  `twoPlayer` tinyint(1) NOT NULL DEFAULT '0',
  `songID` int(11) NOT NULL DEFAULT '0',
  `objects` int(11) NOT NULL DEFAULT '0',
  `coins` int(11) NOT NULL DEFAULT '0',
  `requestedStars` int(11) NOT NULL DEFAULT '0',
  `extraString` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `levelString` longtext COLLATE utf8_unicode_ci,
  `levelInfo` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `starDifficulty` int(11) NOT NULL DEFAULT '0' COMMENT '0=N/A 10=EASY 20=NORMAL 30=HARD 40=HARDER 50=INSANE 50=AUTO 50=DEMON',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `starDemon` tinyint(1) NOT NULL DEFAULT '0',
  `starAuto` tinyint(1) NOT NULL DEFAULT '0',
  `starStars` int(11) NOT NULL DEFAULT '0',
  `uploadDate` int(11) NOT NULL DEFAULT '0',
  `updateDate` int(11) NOT NULL DEFAULT '0',
  `rateDate` bigint(20) NOT NULL DEFAULT '0',
  `ratedBy` int(11) NOT NULL DEFAULT '0',
  `starCoins` tinyint(1) NOT NULL DEFAULT '0',
  `starFeatured` tinyint(1) NOT NULL DEFAULT '0',
  `starHall` tinyint(1) NOT NULL DEFAULT '0',
  `starEpic` tinyint(1) NOT NULL DEFAULT '0',
  `starDemonDiff` int(11) NOT NULL DEFAULT '0',
  `userID` int(11) NOT NULL,
  `extID` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unlisted` tinyint(1) NOT NULL DEFAULT '0',
  `originalReup` int(11) NOT NULL DEFAULT '0' COMMENT 'used for levelReupload.php',
  `hostname` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `isCPShared` tinyint(1) NOT NULL DEFAULT '0',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `isLDM` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`levelID`),
  KEY `levelID` (`levelID`),
  KEY `levelName` (`levelName`),
  KEY `starDifficulty` (`starDifficulty`),
  KEY `starFeatured` (`starFeatured`),
  KEY `starEpic` (`starEpic`),
  KEY `starDemonDiff` (`starDemonDiff`),
  KEY `userID` (`userID`),
  KEY `likes` (`likes`),
  KEY `downloads` (`downloads`),
  KEY `starStars` (`starStars`),
  KEY `songID` (`songID`),
  KEY `audioTrack` (`audioTrack`),
  KEY `levelLength` (`levelLength`),
  KEY `twoPlayer` (`twoPlayer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `levelscores`
--

CREATE TABLE IF NOT EXISTS `levelscores` (
  `scoreID` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` int(11) NOT NULL,
  `levelID` int(11) NOT NULL,
  `percent` int(11) NOT NULL,
  `uploadDate` int(11) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `coins` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`scoreID`),
  KEY `levelID` (`levelID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` int(11) NOT NULL,
  `targetAccountID` int(11) NOT NULL,
  `server` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `targetUserID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mapPacks`
--

CREATE TABLE IF NOT EXISTS `mapPacks` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `levels` varchar(512) COLLATE utf8_unicode_ci NOT NULL COMMENT 'entered as "ID of level 1, ID of level 2, ID of level 3" for example "13,14,15" (without the "s)',
  `stars` int(11) NOT NULL,
  `coins` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `rgbcolors` varchar(11) COLLATE utf8_unicode_ci NOT NULL COMMENT 'entered as R,G,B',
  `colors2` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `userID` int(11) NOT NULL,
  `userName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `subject` longtext COLLATE utf8_unicode_ci NOT NULL,
  `accID` int(11) NOT NULL,
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `toAccountID` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `isNew` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageID`),
  KEY `toAccountID` (`toAccountID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modactions`
--

CREATE TABLE IF NOT EXISTS `modactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `value2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value3` int(11) NOT NULL DEFAULT '0',
  `value4` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `value5` int(11) NOT NULL DEFAULT '0',
  `value6` int(11) NOT NULL DEFAULT '0',
  `account` int(11) NOT NULL DEFAULT '0',
  `value7` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modipperms`
--

CREATE TABLE IF NOT EXISTS `modipperms` (
  `categoryID` int(11) NOT NULL AUTO_INCREMENT,
  `actionFreeCopy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modips`
--

CREATE TABLE IF NOT EXISTS `modips` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(69) COLLATE utf8_unicode_ci NOT NULL,
  `isMod` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `modipCategory` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quests`
--

CREATE TABLE IF NOT EXISTS `quests` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `reward` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rateSuggestions`
--

CREATE TABLE IF NOT EXISTS `rateSuggestions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `levelID` int(11) NOT NULL DEFAULT '0',
  `accountID` int(11) NOT NULL DEFAULT '0',
  `stars` int(11) NOT NULL DEFAULT '0',
  `feature` tinyint(1) NOT NULL DEFAULT '0',
  `isMod` int(11) NOT NULL DEFAULT '0',
  `IP` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `suggestionDate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `levelID` int(11) NOT NULL,
  `hostname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roleassign`
--

CREATE TABLE IF NOT EXISTS `roleassign` (
  `assignID` bigint(20) NOT NULL AUTO_INCREMENT,
  `roleID` bigint(20) NOT NULL,
  `accountID` bigint(20) NOT NULL,
  PRIMARY KEY (`assignID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `roleID` bigint(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL DEFAULT '0',
  `roleName` varchar(255) NOT NULL,
  `commandUnrate` tinyint(1) NOT NULL DEFAULT '0',
  `commandEpic` tinyint(1) NOT NULL DEFAULT '0',
  `commandUnepic` tinyint(1) NOT NULL DEFAULT '0',
  `commandVerifycoins` tinyint(1) NOT NULL DEFAULT '0',
  `commandUnverifycoins` tinyint(1) NOT NULL DEFAULT '0',
  `commandDaily` tinyint(1) NOT NULL DEFAULT '0',
  `commandWeekly` tinyint(1) NOT NULL DEFAULT '0',
  `commandDelete` tinyint(1) NOT NULL DEFAULT '0',
  `commandSetacc` tinyint(1) NOT NULL DEFAULT '0',
  `commandSharecpOwn` tinyint(1) NOT NULL DEFAULT '1',
  `commandSharecpAll` tinyint(1) NOT NULL DEFAULT '0',
  `profilecommandDiscord` tinyint(1) NOT NULL DEFAULT '1',
  `actionRateDemon` tinyint(1) NOT NULL DEFAULT '0',
  `actionRateStars` tinyint(1) NOT NULL DEFAULT '0',
  `actionRateDifficulty` tinyint(1) NOT NULL DEFAULT '0',
  `actionRequestMod` int(11) NOT NULL DEFAULT '0',
  `toolLeaderboardsban` tinyint(1) NOT NULL DEFAULT '0',
  `toolPackcreate` tinyint(1) NOT NULL DEFAULT '0',
  `toolModactions` tinyint(1) NOT NULL DEFAULT '0',
  `dashboardModTools` tinyint(1) NOT NULL DEFAULT '0',
  `modipCategory` int(11) NOT NULL DEFAULT '0',
  `isDefault` tinyint(1) NOT NULL DEFAULT '0',
  `commentColor` varchar(11) NOT NULL DEFAULT '255,255,255',
  `modBadgeLevel` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `songs`
--

CREATE TABLE IF NOT EXISTS `songs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `authorID` int(11) NOT NULL,
  `authorName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `download` varchar(1337) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0',
  `levelsCount` int(11) NOT NULL DEFAULT '0',
  `reuploadTime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=803393 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `isRegistered` tinyint(1) NOT NULL,
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `extID` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `userName` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'undefined',
  `stars` int(11) NOT NULL DEFAULT '0',
  `demons` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  `color1` int(11) NOT NULL DEFAULT '0',
  `color2` int(11) NOT NULL DEFAULT '3',
  `iconType` int(11) NOT NULL DEFAULT '0',
  `coins` int(11) NOT NULL DEFAULT '0',
  `userCoins` int(11) NOT NULL DEFAULT '0',
  `special` int(11) NOT NULL DEFAULT '0',
  `gameVersion` int(11) NOT NULL DEFAULT '0',
  `accIcon` int(11) NOT NULL DEFAULT '0',
  `accShip` int(11) NOT NULL DEFAULT '0',
  `accBall` int(11) NOT NULL DEFAULT '0',
  `accBird` int(11) NOT NULL DEFAULT '0',
  `accDart` int(11) NOT NULL DEFAULT '0',
  `accRobot` int(11) DEFAULT '0',
  `accGlow` int(11) NOT NULL DEFAULT '0',
  `creatorPoints` double NOT NULL DEFAULT '0',
  `hostname` varchar(69) COLLATE utf8_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `lastPlayed` int(11) NOT NULL DEFAULT '0',
  `diamonds` int(11) NOT NULL DEFAULT '0',
  `orbs` int(11) NOT NULL DEFAULT '0',
  `completedLvls` int(11) NOT NULL DEFAULT '0',
  `accSpider` int(11) NOT NULL DEFAULT '0',
  `accExplosion` int(11) NOT NULL DEFAULT '0',
  `chest1time` int(11) NOT NULL DEFAULT '0',
  `chest2time` int(11) NOT NULL DEFAULT '0',
  `chest1count` int(11) NOT NULL DEFAULT '0',
  `chest2count` int(11) NOT NULL DEFAULT '0',
  `isBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isLevelBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isLikeBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isRatingBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isReportingBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isMessageBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isCommentBanned` tinyint(1) NOT NULL DEFAULT '0',
  `isCreatorBanned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`),
  KEY `userID` (`userID`),
  KEY `userName` (`userName`),
  KEY `stars` (`stars`),
  KEY `demons` (`demons`),
  KEY `coins` (`coins`),
  KEY `userCoins` (`userCoins`),
  KEY `gameVersion` (`gameVersion`),
  KEY `creatorPoints` (`creatorPoints`),
  KEY `diamonds` (`diamonds`),
  KEY `orbs` (`orbs`),
  KEY `completedLvls` (`completedLvls`),
  KEY `isBanned` (`isBanned`),
  KEY `isCreatorBanned` (`isCreatorBanned`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
