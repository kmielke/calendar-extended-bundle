<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   Contao
 * @author    Kester Mielke
 * @license   LGPL
 * @copyright Kester Mielke 2010-2013
 */

namespace Contao;


/**
 * Class ModuleYearViewExt
 *
 * @copyright  Kester Mielke 2010-2013
 * @author     Kester Mielke
 * @package    Devtools
 */
class ModuleYearView extends \EventsExt
{

    /**
     * Current date object
     * @var \Date
     */
    protected $Date;
    protected $yearBegin;
    protected $yearEnd;
    protected $calConf = array();

    /**
     * Redirect URL
     * @var string
     */
    protected $strLink;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_calendar';


    /**
     * Do not show the module if no calendar has been selected
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['yearview'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar, true));
        $this->cal_holiday = $this->sortOutProtected(deserialize($this->cal_holiday, true));

        // Return if there are no calendars
        if (!is_array($this->cal_calendar) || empty($this->cal_calendar)) {
            return '';
        }

        // Calendar filter
        if (\Input::get('cal')) {
            // Create array of cal_id's to filter
            $cals1 = explode(',', \Input::get('cal'));
            // Check if the cal_id's are valid for this module
            $cals2 = array_intersect($cals1, $this->cal_calendar);
            if ($cals2) {
                $this->cal_calendar = array_intersect($cals2, $this->cal_calendar);
            }
        }

        // Get the background and foreground colors of the calendars
        foreach (array_merge($this->cal_calendar, $this->cal_holiday) as $cal) {
            $objBG = $this->Database->prepare("select title, bg_color, fg_color from tl_calendar where id = ?")
                ->limit(1)->execute($cal);

            $this->calConf[$cal]['calendar'] = $objBG->title;

            if ($objBG->bg_color) {
                list($cssColor, $cssOpacity) = deserialize($objBG->bg_color);

                if (!empty($cssColor)) {
                    $this->calConf[$cal]['background'] .= 'background-color:#' . $cssColor . ';';
                }
                if (!empty($cssOpacity)) {
                    $this->calConf[$cal]['background'] .= 'opacity:' . ($cssOpacity / 100) . ';';
                }
            }

            if ($objBG->fg_color) {
                list($cssColor, $cssOpacity) = deserialize($objBG->fg_color);

                if (!empty($cssColor)) {
                    $this->calConf[$cal]['foreground'] .= 'color:#' . $cssColor . ';';
                }
                if (!empty($cssOpacity)) {
                    $this->calConf[$cal]['foreground'] .= 'opacity:' . ($cssOpacity / 100) . ';';
                }
            }
        }

        $this->strUrl = preg_replace('/\?.*$/', '', \Environment::get('request'));
        $this->strLink = $this->strUrl;

        if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null) {
            /** @var \PageModel $objTarget */
            $this->strLink = $objTarget->getFrontendUrl();
        }

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        // Create the date object
        try {
            if (\Input::get('year')) {
                $intYear = \Input::get('year');
                $this->yearBegin = mktime(0, 0, 0, 1, 1, $intYear);
                $this->Date = new \Date($this->yearBegin);
            } else {
                $this->Date = new \Date();
            }
        } catch (\OutOfBoundsException $e) {
            /** @var \PageModel $objPage */
            global $objPage;

            /** @var \PageError404 $objHandler */
            $objHandler = new $GLOBALS['TL_PTY']['error_404']();
            $objHandler->generate($objPage->id);
        }

        // Get the Year and the week of the given date
        $intYear = date('Y', $this->Date->tstamp);
        $this->yearBegin = mktime(0, 0, 0, 1, 1, $intYear);
        $this->yearEnd = mktime(23, 59, 59, 12, 31, $intYear);

        // Get total count of weeks of the year
        if (($weeksTotal = date('W', mktime(0, 0, 0, 12, 31, $intYear))) == 1) {
            $weeksTotal = date('W', mktime(0, 0, 0, 12, 24, $intYear));
        }

        $time = \Date::floorToMinute();

        // Find the boundaries
        $objMinMax = $this->Database->query("SELECT MIN(startTime) AS dateFrom, MAX(endTime) AS dateTo, MAX(repeatEnd) AS repeatUntil FROM tl_calendar_events WHERE pid IN(" . implode(',', array_map('intval', $this->cal_calendar)) . ")" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<='$time') AND (stop='' OR stop>'" . ($time + 60) . "') AND published='1'" : ""));
        $intLeftBoundary = date('Y', $objMinMax->dateFrom);
        $intRightBoundary = date('Y', max($objMinMax->dateTo, $objMinMax->repeatUntil));

        /** @var \FrontendTemplate|object $objTemplate */
        $objTemplate = new \FrontendTemplate(($this->cal_ctemplate ? $this->cal_ctemplate : 'cal_yearview'));

        $objTemplate->intYear = $intYear;
        $objTemplate->use_horizontal = $this->use_horizontal;
        $objTemplate->use_navigation = $this->use_navigation;
        $objTemplate->linkCurrent = $this->linkCurrent;

        // display the navigation if selected
        if ($this->use_navigation) {
            // Get the current year and the week
            if ($this->linkCurrent) {
                $currYear = date('Y', time());
                $lblCurrent = $GLOBALS['TL_LANG']['MSC']['curr_year'];

                $objTemplate->currHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'year=' . $currYear;
                $objTemplate->currTitle = $currYear;
                $objTemplate->currLink = $lblCurrent;
                $objTemplate->currLabel = $GLOBALS['TL_LANG']['MSC']['cal_previous'];
            }

            // Previous week
            $prevYear = $intYear - 1;
            $lblPrevious = $GLOBALS['TL_LANG']['MSC']['calendar_year'] . ' ' . $prevYear;
            // Only generate a link if there are events (see #4160)
//            if ($prevYear >= $intLeftBoundary)
//            {
            $objTemplate->prevHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'year=' . $prevYear;
            $objTemplate->prevTitle = $prevYear;
            $objTemplate->prevLink = $GLOBALS['TL_LANG']['MSC']['cal_previous'] . ' ' . $lblPrevious;
            $objTemplate->prevLabel = $GLOBALS['TL_LANG']['MSC']['cal_previous'];
//            }
            // Current week
            $objTemplate->current = $GLOBALS['TL_LANG']['MSC']['calendar_year'] . ' ' . $intYear;

            // Next month
            $nextYear = $intYear + 1;
            $lblNext = $GLOBALS['TL_LANG']['MSC']['calendar_year'] . ' ' . $nextYear;

            // Only generate a link if there are events (see #4160)
//            if ($nextYear <= $intRightBoundary)
//            {
            $objTemplate->nextHref = $this->strUrl . (\Config::get('disableAlias') ? '?id=' . \Input::get('id') . '&amp;' : '?') . 'year=' . $nextYear;
            $objTemplate->nextTitle = $nextYear;
            $objTemplate->nextLink = $lblNext . ' ' . $GLOBALS['TL_LANG']['MSC']['cal_next'];
            $objTemplate->nextLabel = $GLOBALS['TL_LANG']['MSC']['cal_next'];
//            }
        }

        // Set week start day
        if (!$this->cal_startDay) {
            $this->cal_startDay = 0;
        }

        $objTemplate->months = $this->compileMonths();
        $objTemplate->yeardays = $this->compileDays($intYear);
        $objTemplate->substr = $GLOBALS['TL_LANG']['MSC']['dayShortLength'];

        $this->Template->calendar = $objTemplate->parse();
    }


    /**
     * Return the name of the months
     * @return array
     */
    protected function compileMonths()
    {
        $arrDays = array();

        for ($m = 0; $m < 12; $m++) {
            $arrDays[$m]['label'] = $GLOBALS['TL_LANG']['MONTHS'][$m];
            $arrDays[$m]['class'] = 'head';
        }

        return $arrDays;
    }


    /**
     * Return the week days and labels as array
     * @return array
     */
    protected function compileDays($currYear)
    {
        $arrDays = array();

        //Get all events
        $arrAllEvents = $this->getAllEventsExt($this->cal_calendar, $this->yearBegin, $this->yearEnd, array($this->cal_holiday));

        for ($m = 1; $m <= 12; $m++) {
            for ($d = 1; $d <= 31; $d++) {
                if (checkdate($m, $d, $currYear)) {
                    $day = mktime(12, 00, 00, $m, $d, $currYear);

                    $intCurrentDay = (int)date('w', $day);
                    $intCurrentWeek = (int)date('W', $day);

                    $intKey = date("Ymd", strtotime(date("Y-m-d", $day)));
                    $currDay = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime(date("Y-m-d", $day)));
                    $class = ($intCurrentDay == 0 || $intCurrentDay == 6) ? 'weekend' : 'weekday';
                    $class .= (($d % 2) == 0) ? ' even' : ' odd';
                    $class .= ' ' . strtolower($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay]);

                    if ($currDay == \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], strtotime(date("Y-m-d")))) {
                        $class .= ' today';
                    }

                    if ($this->use_horizontal) {
                        $arrDays[$m][0]['label'] = $GLOBALS['TL_LANG']['MONTHS'][$m - 1];
                        $arrDays[$m][0]['class'] = 'head';
                        $arrDays[$m][$d]['label'] = strtoupper(substr($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay], 0, 2)) . ' ' . $d;
                        $arrDays[$m][$d]['weekday'] = strtoupper(substr($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay], 0, 2));
                        $arrDays[$m][$d]['day'] = $d;
                        $arrDays[$m][$d]['class'] = $class;
                    } else {
                        $arrDays[$d][$m]['label'] = strtoupper(substr($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay], 0, 2)) . ' ' . $d;
                        $arrDays[$d][$m]['weekday'] = strtoupper(substr($GLOBALS['TL_LANG']['DAYS'][$intCurrentDay], 0, 2));
                        $arrDays[$d][$m]['day'] = $d;
                        $arrDays[$d][$m]['class'] = $class;
                    }
                } else {
                    if ($this->use_horizontal) {
                        $arrDays[$m][0]['label'] = $GLOBALS['TL_LANG']['MONTHS'][$m - 1];
                        $arrDays[$m][0]['class'] = 'head';
                        $arrDays[$m][$d]['label'] = '';
                        $arrDays[$m][$d]['weekday'] = '';
                        $arrDays[$m][$d]['day'] = '';
                        $arrDays[$m][$d]['class'] = 'empty';
                    } else {
                        $arrDays[$d][$m]['label'] = '';
                        $arrDays[$d][$m]['weekday'] = '';
                        $arrDays[$d][$m]['day'] = '';
                        $arrDays[$d][$m]['class'] = 'empty';
                    }
                    $intKey = 'empty';
                }

                // Get all events of a day
                $arrEvents = array();
                if (is_array($arrAllEvents[$intKey])) {
                    foreach ($arrAllEvents[$intKey] as $v) {
                        foreach ($v as $vv) {
                            // set class recurring
                            if ($vv['recurring'] || $vv['recurringExt']) {
                                $vv['class'] .= ' recurring';
                            }

                            // set color from calendar
                            $vv['calendar_title'] = $this->calConf[$vv['pid']]['calendar'];

                            if ($this->calConf[$vv['pid']]['background']) {
                                $vv['bgstyle'] = $this->calConf[$vv['pid']]['background'];
                            }
                            if ($this->calConf[$vv['pid']]['foreground']) {
                                $vv['fgstyle'] = $this->calConf[$vv['pid']]['foreground'];
                            }
                            $arrEvents[] = $vv;
                        }
                    }
                }
                if ($this->use_horizontal) {
                    $arrDays[$m][$d]['events'] = $arrEvents;
                } else {
                    $arrDays[$d][$m]['events'] = $arrEvents;
                }
            }
        }

        return $arrDays;
    }
}
