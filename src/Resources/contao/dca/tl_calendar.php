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
 * Table tl_calendar
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace
(
    '{title_legend},title,jumpTo;',
    '{title_legend},title,jumpTo,uniqueEvents;{extended_type_legend},isHolidayCal;{extended_legend},bg_color,fg_color;',
    $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']
);

array_insert($GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'], 99, 'isHolidayCal');
array_insert($GLOBALS['TL_DCA']['tl_calendar']['subpalettes'], 99, array('isHolidayCal' => 'allowEvents'));

// Hinzufügen der Feld-Konfiguration
$GLOBALS['TL_DCA']['tl_calendar']['fields']['bg_color'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['bg_color'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => array('maxlength' => 6, 'multiple' => true, 'size' => 2, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'),
    'sql' => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['fg_color'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['fg_color'],
    'inputType' => 'text',
    'exclude' => true,
    'eval' => array('maxlength' => 6, 'multiple' => true, 'size' => 2, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'),
    'sql' => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['isHolidayCal'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['isHolidayCal'],
    'default' => 0,
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true, 'tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['allowEvents'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['allowEvents'],
    'default' => 0,
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['uniqueEvents'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['uniqueEvents'],
    'default' => 0,
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''"
);
