<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package   Contao
 * @author    Kester Mielke
 * @license   LGPL
 * @copyright Kester Mielke 2010-2013
 */


/**
 * Namespace
 */
namespace Contao;

/**
 * Class TimePeriodExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class TimePeriodExt extends \Widget
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_widget';

    /**
     * Values
     * @var array
     */
    protected $arrValues = array();

    /**
     * Units
     * @var array
     */
    protected $arrUnits = array();


    /**
     * Add specific attributes
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey) {
            case 'value':
                $this->varValue = deserialize($varValue);
                break;

            case 'maxlength':
                $this->arrAttributes[$strKey] = ($varValue > 0) ? $varValue : '';
                break;

            case 'mandatory':
                $this->arrConfiguration['mandatory'] = $varValue ? true : false;
                break;

            case 'options':
                $varValue = deserialize($varValue);
                $this->arrValues = $varValue[0];
                $this->arrUnits = $varValue[1];
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }


    /**
     * Do not validate unit fields
     * @param mixed
     * @return mixed
     */
    protected function validator($varInput)
    {
        foreach ($varInput as $k => $v) {
            if ($k != 'unit') {
                $varInput[$k] = parent::validator($v);
            }
        }

        return $varInput;
    }


    /**
     * Generate the widget and return it as string
     * @return string
     */
    public function generate()
    {
        $arrValues = array();
        $arrUnits = array();

        //$arrValues[] = '<option value="">-</option>';
        foreach ($this->arrValues as $arrValue) {
            $arrValues[] = sprintf('<option value="%s"%s>%s</option>',
                specialchars($arrValue['value']),
                ((is_array($this->varValue) && in_array($arrValue['value'], $this->varValue)) ? ' selected="selected"' : ''),
                $arrValue['label']);
        }

        foreach ($this->arrUnits as $arrUnit) {
            $arrUnits[] = sprintf('<option value="%s"%s>%s</option>',
                specialchars($arrUnit['value']),
                ((is_array($this->varValue) && in_array($arrUnit['value'], $this->varValue)) ? ' selected="selected"' : ''),
                $arrUnit['label']);
        }

        if (!is_array($this->varValue)) {
            $this->varValue = array('value' => $this->varValue);
        }

        return sprintf('<select name="%s[value]" class="tl_select_interval" onfocus="Backend.getScrollOffset();">%s</select> <select name="%s[unit]" class="tl_select_interval" onfocus="Backend.getScrollOffset();">%s</select>%s',
            $this->strName,
            implode('', $arrValues),
            $this->strName,
            implode('', $arrUnits),
            $this->wizard);
    }
}
