<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Edit
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['edit_table']               = array('Tabelle', 'Bitte wählen Sie die Quelltabelle.');
$GLOBALS['TL_LANG']['tl_module']['edit_fields']              = array('Felder', 'Bitte geben Sie eine kommagetrennte Liste der Felder ein, die Sie auflisten möchten.');
$GLOBALS['TL_LANG']['tl_module']['edit_where']               = array('Bedingung', 'Hier können Sie eine Bedingung eingeben, um die Ergebnisse zu filtern (z.B. <em>published=1</em> oder <em>type!="admin"</em>).');
$GLOBALS['TL_LANG']['tl_module']['edit_search']              = array('Durchsuchbare Felder', 'Hier können Sie eine kommagetrennte Liste der Felder eingeben, die durchsuchbar sein sollen.');
$GLOBALS['TL_LANG']['tl_module']['edit_sort']                = array('Sortieren nach', 'Hier können Sie eine kommagetrennte Liste der Felder eingeben, nach denen die Ergebnisse sortiert werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['edit_info']                = array('Felder der Bearbeitungsseite', 'Geben Sie eine kommagetrennte Liste der Felder ein, die Sie auf der Detailseite bearbeiten möchten. Lassen Sie das Feld leer, um das Feature zu deaktivieren.');
$GLOBALS['TL_LANG']['tl_module']['edit_info_where']          = array('Detailseitenbedingung', 'Hier können Sie eine Bedingung eingeben, um die Ergebnisse zu filtern (z.B. <em>published=1</em> oder <em>type!="admin"</em>).');
$GLOBALS['TL_LANG']['tl_module']['edit_layout']              = array('Listentemplate', 'Hier können Sie das Listentemplate auswählen.');
$GLOBALS['TL_LANG']['tl_module']['edit_info_layout']         = array('Detailseitentemplate', 'Hier können Sie das Detailseitentemplate auswählen.');
$GLOBALS['TL_LANG']['tl_module']['edit_tinMCEtemplate']['0'] = "Richtext Editor";
$GLOBALS['TL_LANG']['tl_module']['edit_tinMCEtemplate']['1'] = "Wählen Sie eine Konfigurations-Datei, falls Sie den TinyMCE Richtext-Editor verwenden möchten. Sie können weitere Konfigurationen durch Hinzufügen einer Datei \"tinyXXX\" im Ordner system/config.";
$GLOBALS['TL_LANG']['tl_module']['edit_jumpTo']['0']         = "Weiterleitungsseite zum Bearbeiten";
$GLOBALS['TL_LANG']['tl_module']['edit_jumpTo']['1']         = "Bitte wählen Sie eine Seite mit dem Event-Editor, auf den der User bei einem Klick auf einen Bearbeiten-Link weitergeleitet wird.";
?>