<?php

declare(strict_types=1);

/*
 * This file is part of cgoit\calendar-extended-bundle.
 *
 * (c) Kester Mielke
 *
 * (c) Carsten Götzinger
 *
 * @license LGPL-3.0-or-later
 */

/**
 * Namespace.
 */

namespace Kmielke\CalendarExtendedBundle;

use Contao\StringUtil;
use Contao\Widget;

/**
 * Class TimePeriodExt.
 *
 * @copyright  Kester Mielke 2010-2013
 */
class TimePeriodExt extends Widget
{
    /**
     * Submit user input.
     *
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_widget';

    /**
     * Values.
     *
     * @var array
     */
    protected $arrValues = [];

    /**
     * Units.
     *
     * @var array
     */
    protected $arrUnits = [];

    /**
     * Add specific attributes.
     *
     * @param string $strKey
     * @param mixed  $varValue
     */
    public function __set($strKey, $varValue): void
    {
        switch ($strKey) {
            case 'value':
                $this->varValue = StringUtil::deserialize($varValue);
                break;

            case 'maxlength':
                $this->arrAttributes[$strKey] = $varValue > 0 ? $varValue : '';
                break;

            case 'mandatory':
                $this->arrConfiguration['mandatory'] = $varValue ? true : false;
                break;

            case 'options':
                $varValue = StringUtil::deserialize($varValue);
                $this->arrValues = $varValue[0];
                $this->arrUnits = $varValue[1];
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }

    /**
     * Generate the widget and return it as string.
     *
     * @return string
     */
    public function generate()
    {
        $arrValues = [];
        $arrUnits = [];

        //$arrValues[] = '<option value="">-</option>';
        foreach ($this->arrValues as $arrValue) {
            $arrValues[] = sprintf(
                '<option value="%s"%s>%s</option>',
                specialchars($arrValue['value']),
                (\is_array($this->varValue) && \in_array($arrValue['value'], $this->varValue, true) ? ' selected="selected"' : ''),
                $arrValue['label']
            );
        }

        foreach ($this->arrUnits as $arrUnit) {
            $arrUnits[] = sprintf(
                '<option value="%s"%s>%s</option>',
                specialchars($arrUnit['value']),
                (\is_array($this->varValue) && \in_array($arrUnit['value'], $this->varValue, true) ? ' selected="selected"' : ''),
                $arrUnit['label']
            );
        }

        if (!\is_array($this->varValue)) {
            $this->varValue = ['value' => $this->varValue];
        }

        return sprintf(
            '<select name="%s[value]" class="tl_select_interval" onfocus="Backend.getScrollOffset();">%s</select> <select name="%s[unit]" class="tl_select_interval" onfocus="Backend.getScrollOffset();">%s</select>%s',
            $this->strName,
            implode('', $arrValues),
            $this->strName,
            implode('', $arrUnits),
            $this->wizard
        );
    }

    /**
     * Do not validate unit fields.
     *
     * @param mixed $varInput
     *
     * @return mixed
     */
    protected function validator($varInput)
    {
        foreach ($varInput as $k => $v) {
            if ('unit' !== $k) {
                $varInput[$k] = parent::validator($v);
            }
        }

        return $varInput;
    }
}
