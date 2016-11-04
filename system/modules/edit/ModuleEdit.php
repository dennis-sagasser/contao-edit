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
 * Class ModuleEdit
 *
 * Provide methods to render content element "edit".
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Controller
 */
class ModuleEdit extends Module
{

    /**
     * Primary key
     * @var string
     */
    protected $strPk = 'id';

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'edit_default';


    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### EDIT ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Return if the table or the fields have not been set
        if ($this->edit_table == '' || $this->edit_fields == '') {
            return '';
        }

        // Disable the details page
        if ($this->Input->get('edit') && $this->edit_info == '') {
            return '';
        }

        // Fallback to the default template
        if ($this->edit_layout == '') {
            $this->edit_layout = 'edit_default';
        }

        $this->strTemplate = $this->edit_layout;
        $this->edit_where  = $this->replaceInsertTags($this->edit_where);

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        // Add TinyMCE-Stuff to header
        $this->addTinyMCE($this->edit_tinMCEtemplate);

        $this->import('String');

        $this->loadLanguageFile($this->edit_table);
        $this->loadDataContainer($this->edit_table);

        // Edit a single record
        if ($this->Input->get('edit')) {
            $this->editSingleRecord($this->Input->get('edit'));
            return;
        }

        // Delete a single record
        if ($this->Input->get('delete')) {
            $this->deleteSingleRecord($this->Input->get('delete'));
            return;
        }

        /**
         * Add the search menu
         */
        if (FE_USER_LOGGED_IN) {
            $objUser = FrontendUser::getInstance();
            $userid  = $objUser->id;
        }

        $strWhere   = ' WHERE author = ?';
        $varKeyword = $userid . ', ';
        $strOptions = '';

        $this->Template->searchable = false;
        $arrSearchFields            = trimsplit(',', $this->edit_search);

        if (is_array($arrSearchFields) && !empty($arrSearchFields)) {
            $this->Template->searchable = true;

            if ($this->Input->get('search') && $this->Input->get('for')) {
                $varKeyword = '%' . $this->Input->get('for') . '%';
                $strWhere   = (!$this->edit_where ? " WHERE " : " AND ") . $this->Input->get('search') . " LIKE ?";
            }

            foreach ($arrSearchFields as $field) {
                $strOptions .= '  <option value="' . $field . '"' . (($field == $this->Input->get('search')) ? ' selected="selected"' : '') . '>' . (strlen($label = $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$field]['label'][0]) ? $label : $field) . '</option>' . "\n";
            }
        }

        $this->Template->search_fields = $strOptions;


        /**
         * Get the total number of records
         */
        $strQuery = "SELECT COUNT(*) AS count FROM " . $this->edit_table;

        if ($this->edit_where) {
            $strQuery .= " WHERE " . $this->edit_where;
        }

        $strQuery .= $strWhere;
        $objTotal = $this->Database->prepare($strQuery)->execute($varKeyword);


        /**
         * Validate the page count
         */
        $page     = $this->Input->get('page') ? $this->Input->get('page') : 1;
        $per_page = $this->Input->get('per_page') ? $this->Input->get('per_page') : $this->perPage;

        // Thanks to Hagen Klemp (see #4485)
        if ($per_page > 0) {
            if ($page < 1 || $page > max(ceil($objTotal->count / $per_page), 1)) {
                global $objPage;
                $objPage->noSearch = 1;
                $objPage->cache    = 0;

                $this->Template->thead = array();
                $this->Template->tbody = array();

                // Send a 404 header
                header('HTTP/1.1 404 Not Found');
                return;
            }
        }


        /**
         * Get the selected records
         */
        $strQuery = "SELECT " . $this->strPk . "," . $this->edit_fields . " FROM " . $this->edit_table;

        if ($this->edit_where) {
            $strQuery .= " WHERE " . $this->edit_where;
        }

        $strQuery .= $strWhere;

        // Order by
        if ($this->Input->get('order_by')) {
            $strQuery .= " ORDER BY " . $this->Input->get('order_by') . ' ' . $this->Input->get('sort');
        } elseif ($this->edit_sort) {
            $strQuery .= " ORDER BY " . $this->edit_sort;
        }

        $objDataStmt = $this->Database->prepare($strQuery);

        // Limit
        if ($this->Input->get('per_page')) {
            $objDataStmt->limit($this->Input->get('per_page'), (($page - 1) * $per_page));
        } elseif ($this->perPage) {
            $objDataStmt->limit($this->perPage, (($page - 1) * $per_page));
        }

        $objData = $objDataStmt->execute($varKeyword);


        /**
         * Prepare the URL
         */
        $strUrl   = preg_replace('/\?.*$/', '', $this->Environment->request);
        $blnQuery = false;

        foreach (preg_split('/&(amp;)?/', $_SERVER['QUERY_STRING']) as $fragment) {
            if ($fragment != '' && strncasecmp($fragment, 'order_by', 8) !== 0 && strncasecmp($fragment, 'sort', 4) !== 0 && strncasecmp($fragment, 'page', 4) !== 0) {
                $strUrl .= ((!$blnQuery && !$GLOBALS['TL_CONFIG']['disableAlias']) ? '?' : '&amp;') . $fragment;
                $blnQuery = true;
            }
        }

        $this->Template->url = $strUrl;
        $strVarConnector     = ($blnQuery || $GLOBALS['TL_CONFIG']['disableAlias']) ? '&amp;' : '?';


        /**
         * Prepare the data arrays
         */
        $arrTh     = array();
        $arrTd     = array();
        $arrFields = trimsplit(',', $this->edit_fields);

        // THEAD
        for ($i = 0; $i < count($arrFields); $i++) {
            // Never show passwords
            if ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$arrFields[$i]]['inputType'] == 'password') {
                continue;
            }

            $class    = '';
            $sort     = 'asc';
            $strField = strlen($label = $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$arrFields[$i]]['label'][0]) ? $label : $arrFields[$i];

            // Add a CSS class to the order_by column
            if ($this->Input->get('order_by') == $arrFields[$i]) {
                $sort  = ($this->Input->get('sort') == 'asc') ? 'desc' : 'asc';
                $class = ' sorted ' . $this->Input->get('sort');
            }

            $arrTh[] = array
            (
                'link'  => $strField,
                'href'  => (ampersand($strUrl) . $strVarConnector . 'order_by=' . $arrFields[$i]) . '&amp;sort=' . $sort,
                'title' => specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['edit_orderBy'], $strField)),
                'class' => $class . (($i == 0) ? ' col_first' : '') //. ((($i + 1) == count($arrFields)) ? ' col_last' : '')
            );
        }

        $arrRows = $objData->fetchAllAssoc();

        // TBODY
        for ($i = 0; $i < count($arrRows); $i++) {
            $j     = 0;
            $class = 'row_' . $i . (($i == 0) ? ' row_first' : '') . ((($i + 1) == count($arrRows)) ? ' row_last' : '') . ((($i % 2) == 0) ? ' even' : ' odd');

            foreach ($arrRows[$i] as $k => $v) {
                // Skip the primary key
                if ($k == $this->strPk && !in_array($this->strPk, $arrFields)) {
                    continue;
                }

                // Never show passwords
                if ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['inputType'] == 'password') {
                    continue;
                }

                $value = $this->formatValue($k, $v);

                $arrTd[$class][$k] = array
                (
                    'raw'     => $v,
                    'content' => ($value ? $value : '&nbsp;'),
                    'class'   => 'col_' . $j . (($j++ == 0) ? ' col_first' : '') . ($this->edit_info ? '' : (($j >= (count($arrRows[$i]) - 1)) ? ' col_last' : '')),
                    'id'      => $arrRows[$i][$this->strPk],
                    'field'   => $k,
                    'url'     => $strUrl . $strVarConnector . 'edit=' . $arrRows[$i][$this->strPk],
                    'del'     => $strUrl . $strVarConnector . 'delete=' . $arrRows[$i][$this->strPk]
                );
            }
        }

        $this->Template->thead = $arrTh;
        $this->Template->tbody = $arrTd;


        /**
         * Pagination
         */
        $objPagination              = new Pagination($objTotal->count, $per_page);
        $this->Template->pagination = $objPagination->generate("\n  ");
        $this->Template->per_page   = $per_page;


        /**
         * Template variables
         */
        $this->Template->action           = $this->getIndexFreeRequest();
        $this->Template->details          = strlen($this->edit_info) ? true : false;
        $this->Template->search_label     = specialchars($GLOBALS['TL_LANG']['MSC']['search']);
        $this->Template->per_page_label   = specialchars($GLOBALS['TL_LANG']['MSC']['list_perPage']);
        $this->Template->fields_label     = $GLOBALS['TL_LANG']['MSC']['all_fields'][0];
        $this->Template->keywords_label   = $GLOBALS['TL_LANG']['MSC']['keywords'];
        $this->Template->search           = $this->Input->get('search');
        $this->Template->for              = $this->Input->get('for');
        $this->Template->order_by         = $this->Input->get('order_by');
        $this->Template->sort             = $this->Input->get('sort');
        $this->Template->col_last         = 'col_' . $j;
        $this->Template->no_entries       = empty($arrRows) ? $GLOBALS['TL_LANG']['MSC']['strNoEntries'] : false;
        $this->Template->strConfirmDelete = $GLOBALS['TL_LANG']['MSC']['strConfirmDelete'];
    }


    /**
     * Edit a single record
     * @param integer
     */
    protected function editSingleRecord($id)
    {
        // Fallback template
        if (!strlen($this->edit_info_layout)) {
            $this->edit_info_layout = 'edit_info_default';
        }

        $this->Template = new FrontendTemplate($this->edit_info_layout);

        $this->Template->record  = array();
        $this->Template->referer = 'javascript:history.go(-1)';
        $this->Template->back    = $GLOBALS['TL_LANG']['MSC']['goBack'];
        $this->edit_info         = deserialize($this->edit_info);
        $this->edit_info_where   = $this->replaceInsertTags($this->edit_info_where);

        $objRecord = $this->Database->prepare(
            "SELECT " . $this->edit_info .
            " FROM " . $this->edit_table .
            " WHERE " . (strlen($this->edit_info_where) ? $this->edit_info_where .
                " AND " : "") . $this->strPk . "=?")
            ->limit(1)
            ->execute($id);

        if ($objRecord->numRows < 1) {
            return;
        }

        $arrFields    = array();
        $arrRow       = $objRecord->fetchAssoc();
        $limit        = count($arrRow);
        $count        = -1;
        $arrSetValues = array();

        foreach ($arrRow as $k => $v) {

            // Never show passwords
            if ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['inputType'] == 'password') {
                --$limit;
                continue;
            }
            $class = 'row_' . ++$count . (($count == 0) ? ' row_first' : '') . (($count >= ($limit - 1)) ? ' row_last' : '') . ((($count % 2) == 0) ? ' even' : ' odd');

            switch ($k) {
                case 'date':
                    $objWidget            = new FormTextField();
                    $objWidget->mandatory = true;
                    $objWidget->required  = true;
//                    $objWidget->dateImage = true;
                    $objWidget->rgxp = 'date';
                    break;
                case 'time':
                    $objWidget            = new FormTextField();
                    $objWidget->mandatory = true;
                    $objWidget->required  = true;
                    $objWidget->dateImage = true;
                    $objWidget->rgxp      = 'time';
                    break;
                case 'start':
                case 'stop':
                    $objWidget                = new FormCalendarField();
                    $objWidget->dateImage     = true;
                    $objWidget->dateDirection = '+1';
                    $objWidget->rgxp          = 'date';
                    break;
                case 'teaser':
                case 'text':
                    $objWidget               = new FormTextArea();
                    $objWidget->mandatory    = true;
                    $objWidget->required     = true;
                    $objWidget->allowHtml    = true;
                    $objWidget->preserveTags = true;
                    break;
                default:
                    $objWidget       = new FormTextField();
                    $objWidget->rgxp = 'extnd';

            }

            $objWidget->id    = $k;
            $objWidget->class = $class;
            $objWidget->label = (strlen($label = $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['label'][0]) ?
                $label : $k);
            $objWidget->name  = $k;
            $objWidget->value = $this->formatValue($k, $v, true);

            if ($this->Input->post('FORM_SUBMIT') === 'form_edit') {
                $objWidget->validate();
                if (!$objWidget->hasErrors()) {
                    $arrSetValues[$k] = (strtotime($this->Input->postRaw($k)) != false) ?
                        strtotime($this->Input->postRaw('date') . " " . $this->Input->postRaw('time')) :
                        $this->Input->postRaw($k);
                }

            }

            $arrFields[$count] = $objWidget;

        }

        if (count($arrFields) === count($arrSetValues)) {
            $objUpdate = $this->Database->prepare("UPDATE " . $this->edit_table . " %s WHERE id=?")
                ->set($arrSetValues)
                ->execute($id);

            $this->redirectToPage();
        }

        $objWidgetSubmit                 = new FormSubmit();
        $objWidgetSubmit->id             = 'submit';
        $objWidgetSubmit->slabel         = specialchars($GLOBALS['TL_LANG']['MSC']['save']);
        $this->Template->objWidgetSubmit = $objWidgetSubmit;

        $this->Template->fields = $arrFields;

    }


    /**
     * Edit a single record
     * @param integer
     */
    protected function deleteSingleRecord($id)
    {
        $objDelete = $this->Database->prepare("DELETE FROM " . $this->edit_table . " WHERE id=?")
            ->execute($id);

        $this->redirectToPage();
    }

    /**
     * Redirect
     */
    protected function redirectToPage()
    {
        // after this: jump to "jumpTo-Page"
        $jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
        // Get current "jumpTo" page
        $objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
            ->limit(1)
            ->execute($this->edit_jumpTo);

        if ($objPage->numRows) {
            $jt = $this->generateFrontendUrl($objPage->row());
        }
        $this->redirect($jt, 301);

    }

    /**
     * Format a value
     * @param string
     * @param mixed
     * @param boolean
     * @return mixed
     */
    protected
    function formatValue($k, $value, $blnEditSingle = false)
    {
        $value = deserialize($value);

        // Return if empty
        if (empty($value)) {
            return '';
        }

        global $objPage;

        // Array
        if (is_array($value)) {
            $value = implode(', ', $value);
        } // Date
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['rgxp'] == 'date') {
            $value = $this->parseDate($objPage->dateFormat, $value);
        } // Time
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['rgxp'] == 'time') {
            $value = $this->parseDate($objPage->timeFormat, $value);
        } // Date and time
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['rgxp'] == 'datim') {
            $value = $this->parseDate($objPage->datimFormat, $value);
        } // URLs
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['rgxp'] == 'url' && preg_match('@^(https?://|ftp://)@i', $value)) {
            global $objPage;
            $value = '<a href="' . $value . '"' . (($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"') . '>' . $value . '</a>';
        } // E-mail addresses
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['rgxp'] == 'email') {
            $value = $this->String->encodeEmail($value);
            $value = '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;' . $value . '">' . $value . '</a>';
        } // Reference
        elseif (is_array($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['reference'])) {
            $value = $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['reference'][$value];
        } // Associative array
        elseif ($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['eval']['isAssociative'] || array_is_assoc($GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['options'])) {
            if ($blnEditSingle) {
                $value = $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['options'][$value];
            } else {
                $value = '<span class="value">[' . $value . ']</span> ' . $GLOBALS['TL_DCA'][$this->edit_table]['fields'][$k]['options'][$value];
            }
        }

        return $value;
    }

    /**
     * add the selected TinyMCE into the header of the page
     */
    public
    function addTinyMCE($str)
    {
        if (!empty($str)) {
            $strFile         = sprintf('%s/system/config/%s.php', TL_ROOT, $str);
            $this->rteFields = 'ctrl_teaser,ctrl_text';
            $this->language  = 'en';
            // Fallback to English if the user language is not supported
            if (file_exists(TL_ROOT . '/plugins/tinyMCE/langs/' . $GLOBALS['TL_LANGUAGE'] . '.js')) {
                $this->language = $GLOBALS['TL_LANGUAGE'];
            }

            if (!file_exists($strFile)) {
                echo(sprintf('Cannot find rich text editor configuration file "%s"', $strFile));
            } else {
                ob_start();
                include($strFile);
                $GLOBALS['TL_HEAD']['rte'] = ob_get_contents();
                ob_end_clean();
                $GLOBALS['TL_JAVASCRIPT']['rte'] = 'contao/contao.js';
            }
        }
    }
}

?>