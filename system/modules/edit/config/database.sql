-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `edit_table` varchar(64) NOT NULL default '',
  `edit_fields` varchar(255) NOT NULL default '',
  `edit_where` varchar(255) NOT NULL default '',
  `edit_sort` varchar(255) NOT NULL default '',
  `edit_search` varchar(255) NOT NULL default '',
  `edit_info` varchar(255) NOT NULL default '',
  `edit_info_where` varchar(255) NOT NULL default '',
  `edit_layout` varchar(32) NOT NULL default '',
  `edit_info_layout` varchar(32) NOT NULL default '',
  `edit_tinMCEtemplate` varchar(32) NOT NULL default '',
  `edit_jumpTo` int(10) unsigned NOT NULL default '0',
  `efgImagePerRow` smallint(5) unsigned NOT NULL default '0',
  `efgMultiSRC` text NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
