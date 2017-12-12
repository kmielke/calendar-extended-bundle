<?php

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  Kester Mielke 2011 
 * @author     Kester Mielke 
 * @package    Language
 * @license    LGPL 
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['showOnFreeDay']		= array('Event immer anzeigen', 'Event wird auch an freien Tagen angezeigt, wenn es der Kalender erlaubt.');
$GLOBALS['TL_LANG']['tl_calendar_events']['hideOnWeekend']		= array('Nur an Werktagen', 'Event wird an Wochenenden nicht angezeigt.');
$GLOBALS['TL_LANG']['tl_calendar_events']['recurringExt']		= array('Event wiederholen (erweitert)', 'Ein wiederkehrendes Event erstellen.');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEachExt']		= array('Erweitertes Intervall', 'Hier können Sie das Wiederholungsintervall festlegen.');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions']	= array('Ausnahmen nach Datum', 'Bitte geben Sie die Termine an, die sich ändern. Die Anzahl der Wiederholungen muß eventuell angepasst werden.');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsInt']= array('Ausnahmen nach Intervall', 'Bitte geben Sie einen Intervall an. Beispiel: "jeden ersten", berücksichtigt jeden ersten gewählten Wochentag im Monat.');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsPer']= array('Ausnahmen nach Zeitraum', 'Bitte geben Sie einen Zeitraum an, in dem sich die Termine ändern.');
$GLOBALS['TL_LANG']['tl_calendar_events']['ignoreEndTime']      = array('Endzeit ignorieren', 'Die Endzeit des Events wird immer auf die Startzeit gesetzt.');
$GLOBALS['TL_LANG']['tl_calendar_events']['useExceptions']      = array('Ausnahmen definieren', 'Möchten sie Ausnahmen für Wiederholungen angeben?.');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEnd']			= array('Ende der Wiederholungen', 'Datum der letzten Wiederholung dieses Events. (automatisch berechnet)');
$GLOBALS['TL_LANG']['tl_calendar_events']['weekday']			= array('Wochentag', 'Wochentag, an dem das Event stattfindet');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates']   = array('Unregelmäßige Wiederholungen', 'Datum und Zeit für unregelmäßige Wiederholungen.');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_legend']    = 'Veranstaltungsort';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_name']      = array('Veranstaltung', 'Name, oder kurze Beschreibung des Veranstaltungsorts.');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_str']       = array('Straße', 'Straße des Veranstaltungsorts.');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_plz']       = array('Postleitzahl', 'PLZ des Veranstaltungsorts.');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_ort']       = array('Ort', 'Ort / Stadt des Veranstaltungsorts.');
$GLOBALS['TL_LANG']['tl_calendar_events']['contact_legend']     = 'Kontaktinformation';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_link']      = array('Link auf Veranstaltungsort', 'z.B. ein Link auf eine Webseite des Veranstaltungsorts (http://www.link.de)');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_contact']   = array('Kontaktperson', 'Name einer Kontaktperson.');
$GLOBALS['TL_LANG']['tl_calendar_events']['location_mail']      = array('E-Mail', 'E-Mail Adresse der Kontaktperson.');
$GLOBALS['TL_LANG']['tl_calendar_events']['useRegistration']    = array('Anmeldung', 'Aktivierung der Anmeldung für dieses Event.');
$GLOBALS['TL_LANG']['tl_calendar_events']['regconfirm']         = array('Anmeldung / Abmeldung mit Bestätigung', 'Es muss je eine Seite mit einem Modul "Bestätigung Anmeldung / Abmeldung" erstellt werden.');
$GLOBALS['TL_LANG']['tl_calendar_events']['regform']            = array('Anmeldeformlar', 'Das gewählte Anmeldeformular wird im angepassten Event Template eingefügt.');
$GLOBALS['TL_LANG']['tl_calendar_events']['regperson']          = array('Anzahl Teilnehmer', 'Anzahl der min. und max, Teilnehmer, Anmeldungen und freie Plätze. ');
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatWeekday']      = array('Wochentag', 'Auswahl der Wochentage bei täglicher Wiederholung. Alle Tage, wenn keine Auswahl getroffen wird.');
$GLOBALS['TL_LANG']['tl_calendar_events']['mini']               = 'minimal';
$GLOBALS['TL_LANG']['tl_calendar_events']['maxi']               = 'maximal';
$GLOBALS['TL_LANG']['tl_calendar_events']['curr']               = 'aktuell';
$GLOBALS['TL_LANG']['tl_calendar_events']['free']               = 'frei';
$GLOBALS['TL_LANG']['tl_calendar_events']['regstartdate']       = array('Anmeldeschluss', 'Nach diesem Zeitpunkt ist keine Anmeldung mehr möglich.');
$GLOBALS['TL_LANG']['tl_calendar_events']['regenddate']         = array('Abmeldeschluss', 'Nach diesem Zeitpunkt ist keine kostenlose Abmeldung mehr möglich.');

$GLOBALS['TL_LANG']['tl_calendar_events']['first']		= 'jeden ersten';
$GLOBALS['TL_LANG']['tl_calendar_events']['second']		= 'jeden zweiten';
$GLOBALS['TL_LANG']['tl_calendar_events']['third']		= 'jeden dritten';
$GLOBALS['TL_LANG']['tl_calendar_events']['fourth']		= 'jeden vierten';
$GLOBALS['TL_LANG']['tl_calendar_events']['fifth']		= 'jeden fünften';
$GLOBALS['TL_LANG']['tl_calendar_events']['sixth']		= 'jeden sechsten';
$GLOBALS['TL_LANG']['tl_calendar_events']['seventh']	= 'jeden siebten';
$GLOBALS['TL_LANG']['tl_calendar_events']['last']		= 'jeden letzten';

$GLOBALS['TL_LANG']['tl_calendar_events']['sunday']		= 'Sonntag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['monday']		= 'Montag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['tuesday']	= 'Dienstag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['wednesday']	= 'Mittwoch im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['thursday']	= 'Donnerstag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['friday']		= 'Freitag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['saturday']	= 'Samstag im Monat';

$GLOBALS['TL_LANG']['tl_calendar_events']['recurring_legend_ext']	    = 'Wiederholungen (erweitert)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates_legend']	= 'Wiederholungen (unregelmäßig)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions_legend']    = 'Ausnahmen für Wiederholungen';
$GLOBALS['TL_LANG']['tl_calendar_events']['exception_legend']	        = 'Ausnahmen für Wiederholungen';
$GLOBALS['TL_LANG']['tl_calendar_events']['regform_legend']             = 'Anmeldung über ein Formular';

$GLOBALS['TL_LANG']['tl_calendar_events']['checkRecurring']     = "Es kann nur eine der Optionen für die Wiederholungen aktiv sein.";
$GLOBALS['TL_LANG']['tl_calendar_events']['checkExceptions']    = "Keine Option für Wiederholungen aktiv.";
$GLOBALS['TL_LANG']['tl_calendar_events']['nonUniqueEvents']    = "Es gibt eine Zeitüberschneidung mit einem anderen Event.";

$GLOBALS['TL_LANG']['tl_calendar_events']['new_exception']      = "verschieben um";
$GLOBALS['TL_LANG']['tl_calendar_events']['exception']	        = 'Datum';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionFr']	    = 'Von';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionTo']	    = 'Bis';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionInt']	    = 'Jeden X ';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionPer']	    = 'Zeitraum';
$GLOBALS['TL_LANG']['tl_calendar_events']['action']		        = 'Aktion';
$GLOBALS['TL_LANG']['tl_calendar_events']['move']		        = 'verschieben';
$GLOBALS['TL_LANG']['tl_calendar_events']['hide']		        = 'nicht anzeigen';
$GLOBALS['TL_LANG']['tl_calendar_events']['cssclass']	        = 'CSS Class';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_start']	        = 'Startzeit';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_end']	        = 'Endzeit';
$GLOBALS['TL_LANG']['tl_calendar_events']['reason']	            = 'Grund';