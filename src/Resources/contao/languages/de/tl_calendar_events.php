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
$GLOBALS['TL_LANG']['tl_calendar_events']['showOnFreeDay'] = ['Event immer anzeigen', 'Event wird auch an freien Tagen angezeigt, wenn es der Kalender erlaubt.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['hideOnWeekend'] = ['Nur an Werktagen', 'Event wird an Wochenenden nicht angezeigt.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['recurringExt'] = ['Event wiederholen (erweitert)', 'Ein wiederkehrendes Event erstellen.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEachExt'] = ['Erweitertes Intervall', 'Hier können Sie das Wiederholungsintervall festlegen.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions'] = ['Ausnahmen nach Datum', 'Bitte geben Sie die Termine an, die sich ändern. Die Anzahl der Wiederholungen muß eventuell angepasst werden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsInt'] = ['Ausnahmen nach Intervall', 'Bitte geben Sie einen Intervall an. Beispiel: "jeden ersten", berücksichtigt jeden ersten gewählten Wochentag im Monat.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptionsPer'] = ['Ausnahmen nach Zeitraum', 'Bitte geben Sie einen Zeitraum an, in dem sich die Termine ändern.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['ignoreEndTime'] = ['Endzeit ignorieren', 'Die Endzeit des Events wird immer auf die Startzeit gesetzt.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['useExceptions'] = ['Ausnahmen definieren', 'Möchten sie Ausnahmen für Wiederholungen angeben?.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatEnd'] = ['Ende der Wiederholungen', 'Datum der letzten Wiederholung dieses Events. (automatisch berechnet)'];
$GLOBALS['TL_LANG']['tl_calendar_events']['weekday'] = ['Wochentag', 'Wochentag, an dem das Event stattfindet'];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates'] = ['Unregelmäßige Wiederholungen', 'Datum und Zeit für unregelmäßige Wiederholungen.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_legend'] = 'Veranstaltungsort';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_name'] = ['Veranstaltung', 'Name, oder kurze Beschreibung des Veranstaltungsorts.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_str'] = ['Straße', 'Straße des Veranstaltungsorts.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_plz'] = ['Postleitzahl', 'PLZ des Veranstaltungsorts.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_ort'] = ['Ort', 'Ort / Stadt des Veranstaltungsorts.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['contact_legend'] = 'Kontaktinformation';
$GLOBALS['TL_LANG']['tl_calendar_events']['location_link'] = ['Link auf Veranstaltungsort', 'z.B. ein Link auf eine Webseite des Veranstaltungsorts (http://www.link.de)'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_contact'] = ['Kontaktperson', 'Name einer Kontaktperson.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['location_mail'] = ['E-Mail', 'E-Mail Adresse der Kontaktperson.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['useRegistration'] = ['Anmeldung', 'Aktivierung der Anmeldung für dieses Event.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regconfirm'] = ['Anmeldung / Abmeldung mit Bestätigung', 'Es muss je eine Seite mit einem Modul "Bestätigung Anmeldung / Abmeldung" erstellt werden.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regform'] = ['Anmeldeformular', 'Das gewählte Anmeldeformular wird im angepassten Event Template eingefügt.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regperson'] = ['Anzahl Teilnehmer', 'Anzahl der min. und max, Teilnehmer, Anmeldungen und freie Plätze. '];
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatWeekday'] = ['Wochentag', 'Auswahl der Wochentage bei täglicher Wiederholung. Alle Tage, wenn keine Auswahl getroffen wird.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['mini'] = 'minimal';
$GLOBALS['TL_LANG']['tl_calendar_events']['maxi'] = 'maximal';
$GLOBALS['TL_LANG']['tl_calendar_events']['curr'] = 'aktuell';
$GLOBALS['TL_LANG']['tl_calendar_events']['free'] = 'frei';
$GLOBALS['TL_LANG']['tl_calendar_events']['regstartdate'] = ['Anmeldeschluss', 'Nach diesem Zeitpunkt ist keine Anmeldung mehr möglich.'];
$GLOBALS['TL_LANG']['tl_calendar_events']['regenddate'] = ['Abmeldeschluss', 'Nach diesem Zeitpunkt ist keine kostenlose Abmeldung mehr möglich.'];

$GLOBALS['TL_LANG']['tl_calendar_events']['first'] = 'jeden ersten';
$GLOBALS['TL_LANG']['tl_calendar_events']['second'] = 'jeden zweiten';
$GLOBALS['TL_LANG']['tl_calendar_events']['third'] = 'jeden dritten';
$GLOBALS['TL_LANG']['tl_calendar_events']['fourth'] = 'jeden vierten';
$GLOBALS['TL_LANG']['tl_calendar_events']['fifth'] = 'jeden fünften';
$GLOBALS['TL_LANG']['tl_calendar_events']['sixth'] = 'jeden sechsten';
$GLOBALS['TL_LANG']['tl_calendar_events']['seventh'] = 'jeden siebten';
$GLOBALS['TL_LANG']['tl_calendar_events']['last'] = 'jeden letzten';

$GLOBALS['TL_LANG']['tl_calendar_events']['sunday'] = 'Sonntag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['monday'] = 'Montag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['tuesday'] = 'Dienstag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['wednesday'] = 'Mittwoch im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['thursday'] = 'Donnerstag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['friday'] = 'Freitag im Monat';
$GLOBALS['TL_LANG']['tl_calendar_events']['saturday'] = 'Samstag im Monat';

$GLOBALS['TL_LANG']['tl_calendar_events']['recurring_legend_ext'] = 'Wiederholungen (erweitert)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatFixedDates_legend'] = 'Wiederholungen (unregelmäßig)';
$GLOBALS['TL_LANG']['tl_calendar_events']['repeatExceptions_legend'] = 'Ausnahmen für Wiederholungen';
$GLOBALS['TL_LANG']['tl_calendar_events']['exception_legend'] = 'Ausnahmen für Wiederholungen';
$GLOBALS['TL_LANG']['tl_calendar_events']['regform_legend'] = 'Anmeldung über ein Formular';

$GLOBALS['TL_LANG']['tl_calendar_events']['checkRecurring'] = 'Es kann nur eine der Optionen für die Wiederholungen aktiv sein.';
$GLOBALS['TL_LANG']['tl_calendar_events']['checkExceptions'] = 'Keine Option für Wiederholungen aktiv.';
$GLOBALS['TL_LANG']['tl_calendar_events']['nonUniqueEvents'] = 'Es gibt eine Zeitüberschneidung mit einem anderen Event.';

$GLOBALS['TL_LANG']['tl_calendar_events']['new_exception'] = 'verschieben um';
$GLOBALS['TL_LANG']['tl_calendar_events']['exception'] = 'Datum';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionFr'] = 'Von';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionTo'] = 'Bis';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionInt'] = 'Jeden X ';
$GLOBALS['TL_LANG']['tl_calendar_events']['exceptionPer'] = 'Zeitraum';
$GLOBALS['TL_LANG']['tl_calendar_events']['action'] = 'Aktion';
$GLOBALS['TL_LANG']['tl_calendar_events']['move'] = 'verschieben';
$GLOBALS['TL_LANG']['tl_calendar_events']['hide'] = 'nicht anzeigen';
$GLOBALS['TL_LANG']['tl_calendar_events']['cssclass'] = 'CSS Class';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_start'] = 'Startzeit';
$GLOBALS['TL_LANG']['tl_calendar_events']['new_end'] = 'Endzeit';
$GLOBALS['TL_LANG']['tl_calendar_events']['reason'] = 'Grund';
