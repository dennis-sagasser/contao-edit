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
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['edit'] = '{title_legend},name,headline,type;{config_legend},edit_table,edit_fields,edit_where,edit_search,edit_sort,perPage,edit_info,edit_info_where,edit_jumpTo;{image_legend:hide},efgMultiSRC,efgImagePerRow;{template_legend:hide},edit_layout,edit_info_layout,edit_tinMCEtemplate;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['edit_table'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['edit_table'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('tl_module_edit', 'getAllTables'),
    'eval'             => array('chosen' => true, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_fields'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_fields'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('mandatory' => true, 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_where'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_where'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('preserveTags' => true, 'maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_search'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_search'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_sort'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_sort'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_info'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_info'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_info_where'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_info_where'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array('preserveTags' => true, 'maxlength' => 255, 'tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_jumpTo'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['edit_jumpTo'],
    'exclude'   => true,
    'inputType' => 'pageTree',
    'eval'      => array('fieldType' => 'radio')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['efgMultiSRC'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['efgMultiSRC'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('fieldType'=>'checkbox', 'files'=>true, 'mandatory'=>true, 'extensions' => 'gif,jpg,png')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['efgImagePerRow'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['efgImagePerRow'],
    'default'                 => 1,
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array(1, 2, 3, 4),
    'eval'                    => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_layout'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['edit_layout'],
    'default'          => 'edit_default',
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('tl_module_edit', 'geteditTemplates'),
    'eval'             => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_info_layout'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['edit_info_layout'],
    'default'          => 'edit_info_default',
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('tl_module_edit', 'getInfoTemplates'),
    'eval'             => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['edit_tinMCEtemplate'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['edit_tinMCEtemplate'],
    'default'          => 'tinyFrontendMinimal',
    'inputType'        => 'select',
    'options_callback' => array('tl_module_edit', 'getConfigFiles'),
    'eval'             => array('includeBlankOption' => true, 'tl_class' => 'w50'),
);


/**
 * Class tl_module_edit
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class tl_module_edit extends Backend
{

    /**
     * Get all tables and return them as array
     * @return array
     */
    public function getAllTables()
    {
        return $this->Database->listTables();
    }


    /**
     * Return all edit templates as array
     * @param DataContainer
     * @return array
     */
    public function getEditTemplates(DataContainer $dc)
    {
        $intPid = $dc->activeRecord->pid;

        if ($this->Input->get('act') == 'overrideAll') {
            $intPid = $this->Input->get('id');
        }

        return $this->getTemplateGroup('edit_', $intPid);
    }


    /**
     * Return all info templates as array
     * @param DataContainer
     * @return array
     */
    public function getInfoTemplates(DataContainer $dc)
    {
        $intPid = $dc->activeRecord->pid;

        if ($this->Input->get('act') == 'overrideAll') {
            $intPid = $this->Input->get('id');
        }

        return $this->getTemplateGroup('edit_info_', $intPid);
    }

    /**
     * Return a list of tinyMCE config files in this system.
     * copied from "FormRTE", @copyright  Andreas Schempp 2009
     */
    public function getConfigFiles()
    {
        $arrConfigs = array();
        $arrFiles   = scan(TL_ROOT . '/system/config/');

        foreach ($arrFiles as $file) {
            if (substr($file, 0, 4) == 'tiny') {
                $arrConfigs[] = basename($file, '.php');
            }
        }
        return $arrConfigs;
    }

}

?>