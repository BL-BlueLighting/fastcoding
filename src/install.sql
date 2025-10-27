-- Fast Coding - Base on HustOJ - Installation SQL.
-- Just create a table for fastcoding.

-- fastcoding table creating
DROP TABLE IF EXISTS `fastcoding`;
CREATE TABLE `fastcoding` (
  `ID` int(11) NOT NULL AUTO_INCREMENT ,
  `problems` text NOT NULL,
  `join_id` text NOT NULL,
  `joiners` text NOT NULL,
  `Started` tinyint(1) NOT NULL,
  `started_times` text NOT NULL,
  `create_times` text NOT NULL,
  `finished_users` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- fastcoding_ranking table creating
DROP TABLE IF EXISTS `fastcoding_ranking`;
CREATE TABLE `fastcoding_ranking` (
  `user_id` text NOT NULL,
  `joined_fastcodings` int(11) NOT NULL,
  `cleared_fastcodings` int(11) NOT NULL,
  `joined_fastcodings_list` text NOT NULL COMMENT '通过逗号分割，不能有空格'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

-- OVER