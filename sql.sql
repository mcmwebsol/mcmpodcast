CREATE TABLE IF NOT EXISTS `Author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `firstName` (`firstName`,`lastName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `Book_Of_Bible` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `Book_Of_Bible` (`id`, `name`) VALUES
(1, '1 Chronicles'),
(2, '1 Corinthians'),
(3, '1 John'),
(4, '1 Kings'),
(5, '1 Peter'),
(6, '1 Samuel'),
(7, '1 Thessalonians'),
(8, '1 Timothy'),
(9, '2 Chronicles'),
(10, '2 Corinthians'),
(11, '2 John'),
(12, '2 Kings'),
(13, '2 Peter'),
(14, '2 Samuel'),
(15, '2 Thessalonians'),
(16, '2 Timothy'),
(17, '3 John'),
(18, 'Acts'),
(19, 'Amos'),
(20, 'Colossians'),
(21, 'Daniel'),
(22, 'Deuteronomy'),
(23, 'Ecclesiastes'),
(24, 'Ephesians'),
(25, 'Esther'),
(26, 'Exodus'),
(27, 'Ezekiel'),
(28, 'Ezra'),
(29, 'Galatians'),
(30, 'Genesis'),
(31, 'Habakkuk'),
(32, 'Haggai'),
(33, 'Hebrews'),
(34, 'Hosea'),
(36, 'Isaiah'),
(37, 'James'),
(38, 'Jeremiah'),
(39, 'Job'),
(40, 'Joel'),
(41, 'John'),
(42, 'Jonah'),
(43, 'Joshua'),
(44, 'Jude'),
(45, 'Judges'),
(46, 'Lamentations'),
(47, 'Leviticus'),
(48, 'Luke'),
(49, 'Malachi'),
(50, 'Mark'),
(51, 'Matthew'),
(52, 'Micah'),
(53, 'Nahum'),
(54, 'Nehemiah'),
(55, 'Numbers'),
(56, 'Obadiah'),
(57, 'Philemon'),
(58, 'Philippians'),
(59, 'Proverbs'),
(60, 'Psalms'),
(61, 'Revelation'),
(62, 'Romans'),
(63, 'Ruth'),
(64, 'Song Of Solomon'),
(65, 'Titus'),
(66, 'Zechariah'),
(67, 'Zephaniah');


CREATE TABLE IF NOT EXISTS `Podcast_Cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` longtext NOT NULL,
  `modDateTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


INSERT INTO `Podcast_Cache` (`id`, `value`, `modDateTime`) VALUES
(1, '', '2012-12-11 13:45:19');



CREATE TABLE IF NOT EXISTS `Series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `Sermon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `authorID` int(11) NOT NULL,
  `bookOfBibleID` int(11) NOT NULL,
  `myDate` date NOT NULL,
  `chapterStart` mediumint(9) NOT NULL,
  `verseStart` int(11) NOT NULL,
  `chapterEnd` mediumint(9) NOT NULL,
  `verseEnd` int(11) NOT NULL,
  `description` text NOT NULL,
  `audioFile` varchar(255) NOT NULL,
  `manuscriptFile` varchar(255) NOT NULL,
  `seriesID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `authorID` (`authorID`),
  KEY `title` (`title`),
  KEY `verseEnd` (`verseEnd`),
  KEY `verseStart` (`verseStart`),
  KEY `bookOfBibleID` (`bookOfBibleID`),
  KEY `myDate` (`myDate`),
  KEY `chapterStart` (`chapterStart`),
  KEY `chapterEnd` (`chapterEnd`),
  KEY `seriesID` (`seriesID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
