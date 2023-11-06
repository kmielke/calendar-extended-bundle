<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Kmielke\CalendarExtendedBundle;

use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\Environment;

use Contao\PageModel;
use Contao\System;
use Kmielke\CalendarExtendedBundle\CalendarEventsModelExt;
use Kmielke\CalendarExtendedBundle\CalendarLeadsModel;
use Kmielke\CalendarExtendedBundle\EventsExt;

/**
 * Front end module "event reader".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModuleEventReader extends EventsExt
{

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'mod_event';


  /**
   * Display a wildcard in the back end
   *
   * @return string
   */
  public function generate()
  {
    if (TL_MODE == 'BE') {
      /** @var \BackendTemplate|object $objTemplate */
      $objTemplate = new \BackendTemplate('be_wildcard');

      $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['eventreader'][0]) . ' ###';
      $objTemplate->title = $this->headline;
      $objTemplate->id = $this->id;
      $objTemplate->link = $this->name;
      $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

      return $objTemplate->parse();
    }

    // Set the item from the auto_item parameter
    if (!isset($_GET['events']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
      \Input::setGet('events', \Input::get('auto_item'));
    }

    // Do not index or cache the page if no event has been specified
    if (!\Input::get('events')) {
      /** @var \PageModel $objPage */
      global $objPage;

      $objPage->noSearch = 1;
      $objPage->cache = 0;

      return '';
    }

    $cals = ($this->cal_holiday)
      ? array_merge(deserialize($this->cal_calendar), deserialize($this->cal_holiday))
      : deserialize($this->cal_calendar);
    $this->cal_calendar = $this->sortOutProtected($cals);

    // Do not index or cache the page if there are no calendars
    if (!is_array($this->cal_calendar) || empty($this->cal_calendar)) {
      /** @var \PageModel $objPage */
      global $objPage;

      $objPage->noSearch = 1;
      $objPage->cache = 0;

      return '';
    }

    return parent::generate();
  }


  /**
   * Generate the module
   */
  protected function compile()
  {
    /** @var \PageModel $objPage */
    global $objPage;

    $this->Template->event = '';

    if ($this->overviewPage)
    {
      $this->Template->referer = PageModel::findById($this->overviewPage)->getFrontendUrl();
      $this->Template->back = $this->customLabel ?: $GLOBALS['TL_LANG']['MSC']['eventOverview'];
    }
    else
    {
      trigger_deprecation('contao/calendar-bundle', '4.13', 'If you do not select an overview page in the event reader module, the "go back" link will no longer be shown in Contao 5.0.');

      $this->Template->referer = 'javascript:history.go(-1)';
      $this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
    }

    // Get the current event
    $objEvent = CalendarEventsModelExt::findPublishedByParentAndIdOrAlias(\Input::get('events'), $this->cal_calendar);

    // The event does not exist (see #33)
    if ($objEvent === null) {
      throw new PageNotFoundException('Page not found: ' . Environment::get('uri'));
    }

    // Add author info
    $objEvent->author_name = ($objEvent->getRelated("author")->name) ? $objEvent->getRelated("author")->name : null;
    $objEvent->author_mail = ($objEvent->getRelated("author")->email) ? $objEvent->getRelated("author")->email : null;

    // Overwrite the page title (see #2853 and #4955)
    if ($objEvent->title != '') {
      $objPage->pageTitle = strip_tags(strip_insert_tags($objEvent->title));
    }

    // Overwrite the page description
    if ($objEvent->teaser != '') {
      $objPage->description = $this->prepareMetaDescription($objEvent->teaser);
    }

    $intStartTime = $objEvent->startTime;
    $intEndTime = $objEvent->endTime;
    $span = \Calendar::calculateSpan($intStartTime, $intEndTime);

    // Save original times...
    $orgStartTime = $objEvent->startTime;
    $orgEndTime = $objEvent->endTime;

    // Do not show dates in the past if the event is recurring (see #923)
    if ($objEvent->recurring) {
      $arrRange = deserialize($objEvent->repeatEach);

      while ($intStartTime < time() && $intEndTime < $objEvent->repeatEnd) {
        $intStartTime = strtotime('+' . $arrRange['value'] . ' ' . $arrRange['unit'], $intStartTime);
        $intEndTime = strtotime('+' . $arrRange['value'] . ' ' . $arrRange['unit'], $intEndTime);
      }
    }

    // Do not show dates in the past if the event is recurringExt
    if ($objEvent->recurringExt) {
      $arrRange = deserialize($objEvent->repeatEachExt);

      // list of months we need
      $arrMonth = array(
        1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june',
        7 => 'july', 8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
      );

      // month and year of the start date
      $month = date('n', $intStartTime);
      $year = date('Y', $intEndTime);
      while ($intStartTime < time() && $intEndTime < $objEvent->repeatEnd) {
        // find the next date
        $nextValueStr = $arrRange['value'] . ' ' . $arrRange['unit'] . ' of ' . $arrMonth[$month] . ' ' . $year;
        $nextValueDate = strtotime($nextValueStr, $intStartTime);
        // add time to the new date
        $intStartTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", $intStartTime));
        $intEndTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", $intEndTime));

        $month++;
        if (($month % 13) == 0) {
          $month = 1;
          $year += 1;
        }
      }
    }

    // Do not show dates in the past if the event is recurring irregular
    if (!is_null($objEvent->repeatFixedDates)) {
      $arrFixedDates = deserialize($objEvent->repeatFixedDates);

      // Check if there are valid data in the array...
      if (is_array($arrFixedDates) && strlen($arrFixedDates[0]['new_repeat'])) {
        foreach ($arrFixedDates as $fixedDate) {
          $nextValueDate = ($fixedDate['new_repeat']) ? strtotime($fixedDate['new_repeat']) : $intStartTime;
          if (strlen($fixedDate['new_start'])) {
            $nextStartTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", strtotime($fixedDate['new_start'])));
            $nextValueDate = $nextStartTime;
          } else {
            $nextStartTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", $intStartTime));
          }
          if (strlen($fixedDate['new_end'])) {
            $nextEndTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", strtotime($fixedDate['new_end'])));
          } else {
            $nextEndTime = strtotime(date("Y-m-d", $nextValueDate) . ' ' . date("H:i:s", $intEndTime));
          }

          if ($nextValueDate > time() && $nextEndTime <= $objEvent->repeatEnd) {
            $intStartTime = $nextStartTime;
            $intEndTime = $nextEndTime;
            break;
          }
        }
      }
    }

    // Replace the date an time with the correct ones from the recurring event
    if (\Input::get('times')) {
      list($intStartTime, $intEndTime) = explode(",", \Input::get('times'));
    }

    $strDate = \Date::parse($objPage->dateFormat, $intStartTime);

    if ($span > 0) {
      $strDate = \Date::parse($objPage->dateFormat, $intStartTime) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . \Date::parse($objPage->dateFormat, $intEndTime);
    }

    $strTime = '';

    if ($objEvent->addTime) {
      if ($span > 0) {
        $strDate = \Date::parse($objPage->datimFormat, $intStartTime) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . \Date::parse($objPage->datimFormat, $intEndTime);
      } elseif ($intStartTime == $intEndTime) {
        $strTime = \Date::parse($objPage->timeFormat, $intStartTime);
      } else {
        $strTime = \Date::parse($objPage->timeFormat, $intStartTime) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . \Date::parse($objPage->timeFormat, $intEndTime);
      }
    }

    // Fix date if we have to ignore the time
    if ((int)$objEvent->ignoreEndTime === 1) {
      // $strDate = \Date::parse($objPage->datimFormat, $objEvent->startTime) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . \Date::parse($objPage->dateFormat, $objEvent->endTime);
      // $strTime = null;
      $strDate = \Date::parse($objPage->dateFormat, $objEvent->startTime);
      $objEvent->endTime = '';
      $objEvent->time = '';
    }

    $until = '';
    $recurring = '';

    // Recurring event
    if ($objEvent->recurring) {
      $arrRange = deserialize($objEvent->repeatEach);

      if (is_array($arrRange) && isset($arrRange['unit']) && isset($arrRange['value'])) {
        $strKey = 'cal_' . $arrRange['unit'];
        $recurring = sprintf($GLOBALS['TL_LANG']['MSC'][$strKey] ?? null, $arrRange['value']);

        if ($objEvent->recurrences > 0) {
          $until = sprintf($GLOBALS['TL_LANG']['MSC']['cal_until'], \Date::parse($objPage->dateFormat, $objEvent->repeatEnd));
        }
      }
    }

    // Recurring eventExt
    if ($objEvent->recurringExt) {
      $arrRange = deserialize($objEvent->repeatEachExt);
      $strKey = 'cal_' . $arrRange['value'];
      $strVal = $GLOBALS['TL_LANG']['DAYS'][$GLOBALS['TL_LANG']['DAYS'][$arrRange['unit']]];
      $recurring = sprintf($GLOBALS['TL_LANG']['MSC'][$strKey], $strVal);

      if ($objEvent->recurrences > 0) {
        $until = sprintf($GLOBALS['TL_LANG']['MSC']['cal_until'], \Date::parse($objPage->dateFormat, $objEvent->repeatEnd));
      }
    }

    // moveReason fix...
    $moveReason = null;

    // get moveReason from exceptions
    if ($objEvent->useExceptions) {
      $exceptions = deserialize($objEvent->exceptionList);
      if ($exceptions) {
        foreach ($exceptions as $fixedDate) {
          // look for the reason only if we have a move action
          if ($fixedDate['action'] === "move") {
            // value to add to the old date
            $addToDate = $fixedDate['new_exception'];
            $newDate = strtotime($addToDate, $fixedDate['exception']);
            if (date("Ymd", $newDate) == date("Ymd", $intStartTime)) {
              $moveReason = ($fixedDate['reason']) ? $fixedDate['reason'] : null;
            }
          }
        }
      }
    }

    // get moveReason from fixed dates if exists...
    if (!is_null($objEvent->repeatFixedDates)) {
      $arrFixedDates = deserialize($objEvent->repeatFixedDates);
      if (is_array($arrFixedDates)) {
        foreach ($arrFixedDates as $fixedDate) {
          if (date("Ymd", strtotime($fixedDate['new_repeat'])) == date("Ymd", $intStartTime)) {
            $moveReason = ($fixedDate['reason']) ? $fixedDate['reason'] : null;
          }
        }
      }
    }

    // check the repeat values
    $unit = '';
    if ($objEvent->recurring) {
      $arrRepeat = deserialize($objEvent->repeatEach) ? deserialize($objEvent->repeatEach) : null;
      $unit = $arrRepeat['unit'];
    }
    if ($objEvent->recurringExt) {
      $arrRepeat = deserialize($objEvent->repeatEachExt) ? deserialize($objEvent->repeatEachExt) : null;
      $unit = $arrRepeat['unit'];
    }

    // get the configured weekdays if any
    $useWeekdays = ($weekdays = deserialize($objEvent->repeatWeekday)) ? true : false;

    // Set the next date
    $nextDate = null;
    if ($objEvent->repeatDates) {
      $arrNext = deserialize($objEvent->repeatDates);
      if (is_array($arrNext)) {
        foreach ($arrNext as $k => $nextDate) {
          if (strtotime($nextDate) > time()) {
            // check if we have the correct weekday
            if ($useWeekdays && $unit === 'days') {
              if (!in_array(date('w', $k), $weekdays)) {
                continue;
              }
            }
            $nextDate = \Date::parse($objPage->datimFormat, $k);
            break;
          }
        }
      }
      $event['nextDate'] = $nextDate;
    }

    if ($objEvent->allRecurrences) {
      $objEvent->allRecurrences = deserialize($objEvent->allRecurrences);
    }

    /** @var \FrontendTemplate|object $objTemplate */
    $objTemplate = new \FrontendTemplate($this->cal_template ?: 'event_full');
    $objTemplate->setData($objEvent->row());

    $objTemplate->date = $strDate;
    $objTemplate->time = $strTime;
    $objTemplate->datetime = $objEvent->addTime ? date('Y-m-d\TH:i:sP', $intStartTime) : date('Y-m-d', $intStartTime);
    $objTemplate->begin = $intStartTime;
    $objTemplate->end = $intEndTime;
    $objTemplate->class = ($objEvent->cssClass != '') ? ' ' . $objEvent->cssClass : '';
    $objTemplate->recurring = $recurring;
    $objTemplate->until = $until;
    $objTemplate->locationLabel = $GLOBALS['TL_LANG']['MSC']['location'];
    $objTemplate->calendar = $objEvent->getRelated('pid');
    $objTemplate->details = '';
    $objTemplate->hasDetails = false;
    $objTemplate->hasTeaser = false;

    $objTemplate->nextDate = $nextDate;
    $objTemplate->moveReason = ($moveReason) ? $moveReason : null;

    // Formular für Anmeldung, wenn contao-leads installiert ist...
    $objTemplate->regform = null;

    // Event und Formular ID
    $eid = (int)$objEvent->id;
    $fid = (int)$objEvent->regform;

    // Prüfen, ob sich ein angemeldeter Benutzer schon registriert hat
    $showToUser = true;
    if (FE_USER_LOGGED_IN) {
      $this->import('FrontendUser', 'User');
      $email = $this->User->email;
      $showToUser = CalendarLeadsModel::regCheckByFormEventMail($fid, $eid, $email);
    }

    if (class_exists('leads\leads') && $objEvent->useRegistration && $showToUser) {
      // ... und im Event ein Formular ausgewählt wurde
      if ($objEvent->regform) {
        $values = deserialize($objEvent->regperson);

        // Anmeldungen ermittlen und anzeigen
        $regCount = CalendarLeadsModel::regCountByFormEvent($fid, $eid);

        // Werte setzen
        $values[0]['curr'] = (int)$regCount;
        $values[0]['mini'] = (int)$values[0]['mini'];
        $values[0]['maxi'] = (int)$values[0]['maxi'];

        $useMaxi = ($values[0]['maxi'] === 0) ? false : true;

        $values[0]['free'] = ($useMaxi) ? $values[0]['maxi'] - $values[0]['curr'] : 0;
        $values[0]['info'] = $GLOBALS['TL_LANG']['MSC']['reginfo'];

        // Prüfen, ob ein Anmeldeschluss gesetzt ist
        $showForm = true;
        if ($objEvent->regstartdate) {
          // und ob dieser erreicht ist...
          $showForm = ($objEvent->regstartdate > time()) ? true : false;
        }
        if (!$showForm) {
          // wenn ja, dann entsprechende Meldung ausgeben
          $values[0]['info'] = $GLOBALS['TL_LANG']['MSC']['regdone'];
        } else {
          // Formular auf null setzen
          $objTemplate->regform = null;

          // Maximale Anzahl noch nicht erreicht. Dann Formluar setzen
          if (($useMaxi && $values[0]['free'] > 0) || (!$useMaxi && $values[0]['free'] == 0)) {
            $regform = \Form::getForm((int)$objEvent->regform);

            // Wenn bestätigt werden soll, dann published auf 0, sonst direkt auf 1
            $published = ($objEvent->regconfirm) ? 0 : 1;

            // Einsetzen der aktuell Event ID, damit diese mit dem Formular gespeichert wird.
            $regform = str_replace('input type="number" name="count" ', 'input type="number" name="count" max="' . $values[0]['free'] . '"', $regform);
            $regform = str_replace('value="eventid"', 'value="' . $objEvent->id . '"', $regform);
            $regform = str_replace('value="eventtitle"', 'value="' . \StringUtil::specialchars($objEvent->title) . '"', $regform);
            $regform = str_replace('value="eventstart"', 'value="' . \Date::parse($objPage->datimFormat, $intStartTime) . '"', $regform);
            $regform = str_replace('value="eventend"', 'value="' . \Date::parse($objPage->datimFormat, $intEndTime) . '"', $regform);
            $regform = str_replace('value="location_contact"', 'value="' . \StringUtil::specialchars($objEvent->location_contact) . '"', $regform);
            $regform = str_replace('value="location_mail"', 'value="' . $objEvent->location_mail . '"', $regform);
            $regform = str_replace('value="published"', 'value="' . $published . '"', $regform);
            $objTemplate->regform = $regform;
          }

          // Maximale Anzahl erreicht.
          if ($useMaxi && $values[0]['free'] == 0) {
            $values[0]['info'] = $GLOBALS['TL_LANG']['MSC']['regmaxi'];
          }

          // Info darüber, ob die minimal Anzahl erreicht ist.
          if ($values[0]['mini'] > 0 && $values[0]['curr'] < $values[0]['mini']) {
            $values[0]['info'] = $GLOBALS['TL_LANG']['MSC']['regmini'];
          }
        }

        // Reg Info's für die Ausgabe
        $objTemplate->reginfo = $values[0];

        unset($values);
      }
    }

    // Restore event times...
    $objEvent->startTime = $orgStartTime;
    $objEvent->endTime = $orgEndTime;

    // Clean the RTE output
    if ($objEvent->teaser != '') {
      $objTemplate->hasTeaser = true;

      if ($objPage->outputFormat == 'xhtml') {
        $objTemplate->teaser = \StringUtil::toXhtml($objEvent->teaser);
      } else {
        $objTemplate->teaser = \StringUtil::toHtml5($objEvent->teaser);
      }

      $objTemplate->teaser = \StringUtil::encodeEmail($objTemplate->teaser);
    }

    // Display the "read more" button for external/article links
    if ($objEvent->source != 'default') {
      $objTemplate->details = true;
      $objTemplate->hasDetails = true;
    } // Compile the event text
    else {
      $id = $objEvent->id;

      $objTemplate->details = function () use ($id) {
        $strDetails = '';
        $objElement = \ContentModel::findPublishedByPidAndTable($id, 'tl_calendar_events');

        if ($objElement !== null) {
          while ($objElement->next()) {
            $strDetails .= $this->getContentElement($objElement->current());
          }
        }

        return $strDetails;
      };

      $objTemplate->hasDetails = function () use ($id) {
        return \ContentModel::countPublishedByPidAndTable($id, 'tl_calendar_events') > 0;
      };
    }

    $objTemplate->addImage = false;

    // Add an image
    if ($objEvent->addImage && $objEvent->singleSRC != '') {
      $objModel = \FilesModel::findByUuid($objEvent->singleSRC);

      if ($objModel === null) {
        if (!\Validator::isUuid($objEvent->singleSRC)) {
          $objTemplate->text = '<p class="error">' . $GLOBALS['TL_LANG']['ERR']['version2format'] . '</p>';
        }
      } elseif (is_file(TL_ROOT . '/' . $objModel->path)) {
        // Do not override the field now that we have a model registry (see #6303)
        $arrEvent = $objEvent->row();

        // Override the default image size
        if ($this->imgSize != '') {
          $size = deserialize($this->imgSize);

          if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_') {
            $arrEvent['size'] = $this->imgSize;
          }
        }

        $arrEvent['singleSRC'] = $objModel->path;
        $this->addImageToTemplate($objTemplate, $arrEvent);
      }
    }

    $objTemplate->enclosure = array();

    // Add enclosures
    if ($objEvent->addEnclosure) {
      $this->addEnclosuresToTemplate($objTemplate, $objEvent->row());
    }

    // schema.org information
    if (method_exists(\Events::class, 'getSchemaOrgData')) {
        $objTemplate->getSchemaOrgData = static function () use ($objTemplate, $objEvent): array{
            $jsonLd = \Events::getSchemaOrgData($objEvent);

            if ($objTemplate->addImage && $objTemplate->figure){
                $jsonLd['image'] = $objTemplate->figure->getSchemaOrgData();
            }

            return $jsonLd;
        };
    }

    $this->Template->event = $objTemplate->parse();

    // Tag the event (see #2137)
    if (System::getContainer()->has('fos_http_cache.http.symfony_response_tagger'))
    {
      $responseTagger = System::getContainer()->get('fos_http_cache.http.symfony_response_tagger');
      $responseTagger->addTags(array('contao.db.tl_calendar_events.' . $objEvent->id));
    }

    // HOOK: comments extension required
    if ($objEvent->noComments || !in_array('comments', \ModuleLoader::getActive())) {
      $this->Template->allowComments = false;

      return;
    }

    /** @var \CalendarModel $objCalendar */
    $objCalendar = $objEvent->getRelated('pid');
    $this->Template->allowComments = $objCalendar->allowComments;

    // Comments are not allowed
    if (!$objCalendar->allowComments) {
      return;
    }

    // Adjust the comments headline level
    $intHl = min(intval(str_replace('h', '', $this->hl)), 5);
    $this->Template->hlc = 'h' . ($intHl + 1);

    $this->import('Comments');
    $arrNotifies = array();

    // Notify the system administrator
    if ($objCalendar->notify != 'notify_author') {
      $arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
    }

    // Notify the author
    if ($objCalendar->notify != 'notify_admin') {
      /** @var \UserModel $objAuthor */
      if (($objAuthor = $objEvent->getRelated('author')) !== null && $objAuthor->email != '') {
        $arrNotifies[] = $objAuthor->email;
      }
    }

    $objConfig = new \stdClass();

    $objConfig->perPage = $objCalendar->perPage;
    $objConfig->order = $objCalendar->sortOrder;
    $objConfig->template = $this->com_template;
    $objConfig->requireLogin = $objCalendar->requireLogin;
    $objConfig->disableCaptcha = $objCalendar->disableCaptcha;
    $objConfig->bbcode = $objCalendar->bbcode;
    $objConfig->moderate = $objCalendar->moderate;

    $this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_calendar_events', $objEvent->id, $arrNotifies);
  }
}
