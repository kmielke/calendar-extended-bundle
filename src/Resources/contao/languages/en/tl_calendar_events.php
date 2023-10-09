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

/*
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['showOnFreeDay'] = ['Show Event always', 'Event will be displayed if allowed by the calendar.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['hideOnWeekend'] = ['Not on weekends', 'Event will not be displayed on weekends.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['recurringExt'] = ['Repeat event (extended)', 'Create a recurring event.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEachExt'] = ['Extended Interval', 'Here you can set the recurrence interval.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions'] = ['Exception by date', 'Please add changing events here. Adjust the count of the recurrences if needed.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsInt'] = ['Exception by interval', 'Please set an interval here. E.g. "every first" will take every first weekday of the month.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsPer'] = ['Exception by period', 'Please add an time period. Recurrences in this period are effected.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['ignoreEndTime'] = ['Ignore endtime', 'Endtime is set to starttime.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['useExceptions'] = ['Use exceptions', 'Do you like to define exceptions?'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEnd'] = ['End of the recurrences', 'Date of the last recurrence. (calc. automatically)'];
$GLOBALS['TL_LANG']['tl_calendar_events']['weekday'] = ['Weekday', 'Day of week'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates'] = ['Repeat event (irregular)', 'Create a irregular recurring event.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_legend'] = 'Location information';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_name'] = ['Location', 'Name or short description of the location.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_str'] = ['Street', 'Street of the location.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_plz'] = ['Zipcode', 'Zipcode of the location.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_ort'] = ['City', 'City of the location.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['contact_legend'] = 'Contact information';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_link'] = ['Link to location', 'e.g. a link to a webpage of the location. (http://www.link.de)'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_contact'] = ['Contact', 'Name of a contact.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_mail'] = ['E-Mail', 'E-Mail address of the contact.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['useRegistration'] = ['Registration', 'Activate registration for this Event.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regconfirm'] = ['Register / Unregister with confirmation', 'You need to create pages with a module "Confirmation register/unregister".'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regform'] = ['Registerform', 'The selected form will be inserted into the modified event template.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regperson'] = ['Number of participants', 'Count of min and max participants, registrations and free places.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatWeekday'] = ['Weekday', 'Select the weekday if event is recurring daily. No selection means all.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['mini'] = 'min';
$GLOBALS['TL_LANG']['tl_calendar_events']['maxi'] = 'max';
$GLOBALS['TL_LANG']['tl_calendar_events']['curr'] = 'current';
$GLOBALS['TL_LANG']['tl_calendar_events']['free'] = 'free';
$GLOBALS['TL_LANG']['tl_calendar_events']['regstartdate'] = ['Deadline register', 'After that date, no more registration is possible.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regenddate'] = ['Deadline unregister', 'After this date, is not possible to remove a registration for free.'];

$GLOBALS['TL_LANG']['tl_calendar_events']['first'] = 'every first';
$GLOBALS['TL_LANG']['tl_calendar_events']['second'] = 'every second';
$GLOBALS['TL_LANG']['tl_calendar_events']['third'] = 'every third';
$GLOBALS['TL_LANG']['tl_calendar_events']['fourth'] = 'every fourth';
$GLOBALS['TL_LANG']['tl_calendar_events']['fifth'] = 'every fifth';
$GLOBALS['TL_LANG']['tl_calendar_events']['sixth'] = 'every sixth';
$GLOBALS['TL_LANG']['tl_calendar_events']['seventh'] = 'every seventh';
$GLOBALS['TL_LANG']['tl_calendar_events']['last'] = 'every last';

$GLOBALS['TL_LANG']['tl_calendar_events']['sunday'] = 'sunday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['monday'] = 'monday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['tuesday'] = 'tuesday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['wednesday'] = 'wednesday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['thursday'] = 'thursday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['friday'] = 'friday of a month';
$GLOBALS['TL_LANG']['tl_calendar_events']['saturday'] = 'saturday of a month';

$GLOBALS['TL_LANG']['tl_calendar_events']['recurring_legend_ext'] = 'Recurrence settings (extended)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates_legend'] = 'Recurrence settings (irregular)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions_legend'] = 'Define exceptions for the recurrences';
$GLOBALS['TL_LANG']['tl_calendar_events']['exception_legend'] = 'Exception settings (extended)';
$GLOBALS['TL_LANG']['tl_calendar_events']['regform_legend'] = 'Register via a form';

$GLOBALS['TL_LANG']['tl_calendar_events']['checkRecurring'] = 'Only one option can be active for recurrences.';
$GLOBALS['TL_LANG']['tl_calendar_events']['checkExceptions'] = 'No option for recurrences is active.';
$GLOBALS['TL_LANG']['tl_calendar_events']['nonUniqueEvents'] = 'Time overlapping with another event.';

$GLOBALS['TL_LANG']['tl_calendar_events']['new_exception'] = 'move by';
$GLOBALS['TL_LANG']['tl_calendar_events']['exception'] = 'Date';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionInt'] = 'Every X ';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionFr'] = 'From';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionTo'] = 'To';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionPer'] = 'Period';
$GLOBALS['TL_LANG']['tl_calendar_events']['action'] = 'Action';
$GLOBALS['TL_LANG']['tl_calendar_events']['move'] = 'move';
$GLOBALS['TL_LANG']['tl_calendar_events']['hide'] = 'don\'t show';
$GLOBALS['TL_LANG']['tl_calendar_events']['cssclass'] = 'Css class';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_start'] = 'Start time';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_end'] = 'End time';
$GLOBALS['TL_LANG']['tl_calendar_events']['reason'] = 'Reason';
