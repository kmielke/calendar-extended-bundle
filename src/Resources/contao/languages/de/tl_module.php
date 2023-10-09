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
$GLOBALS['TL_LANG']['tl_module']['use_horizontal'] = ['Horizontale Darstellung', 'Monate werden horizontal dargestellt.'];
$GLOBALS['TL_LANG']['tl_module']['use_navigation'] = ['Navigation anzeigen', 'Wochennavigation wird angezeigt, wenn aktiviert.'];
$GLOBALS['TL_LANG']['tl_module']['showDate'] = ['Datum anzeigen', 'Tagesdatum wird angezeigt, wenn aktiviert.'];
$GLOBALS['TL_LANG']['tl_module']['showRecurrences'] = ['Verkürzte Darstellung (Wiederholungen)', 'Events nur einmal anzeigen, auch wenn wiederholt werden. Template muss angepasst werden.'];
$GLOBALS['TL_LANG']['tl_module']['showOnlyNext'] = ['Nur nächste Wiederholung', 'Es wird nur die nächste Wiederholung angezeigt (Nur bei Wiederholungen).'];
$GLOBALS['TL_LANG']['tl_module']['linkCurrent'] = ['Link "Aktuelles Datum" anzeigen', 'Link für das aktuelle Datum wird angezeigt, wenn aktiviert.'];
$GLOBALS['TL_LANG']['tl_module']['hideEmptyDays'] = ['Leere Tage nicht anzeigen', 'Wochentage ohne Events werden ausgeblendet.'];
$GLOBALS['TL_LANG']['tl_module']['cal_holiday'] = ['Ferienkalender', 'Bitte wählen Sie einen oder mehrere Kalender für die Ferien und Feiertage.'];
$GLOBALS['TL_LANG']['tl_module']['show_holiday'] = ['Ferien ausblenden', 'Ferien und Feiertage nicht anzeigen.'];
$GLOBALS['TL_LANG']['tl_module']['cal_calendar_ext'] = ['Kalender', 'Bitte wählen Sie einen oder mehrere Kalender.'];
$GLOBALS['TL_LANG']['tl_module']['cal_times'] = ['Uhrzeiten anzeigen', 'Uhrzeiten werden rechts angezeigt, und Events gleicher Zeit auf gleicher Höhe angezeigt.'];
$GLOBALS['TL_LANG']['tl_module']['pubTimeRecurrences'] = ['Uhrzeit bei Wiederholungen berücksichtigen', 'Wiederholungen werden nur angezeigt, wenn die Zeit des Events innerhalb der Uhrzeit von "Anzeigen von/bis" liegt.'];
$GLOBALS['TL_LANG']['tl_module']['displayDuration'] = ['Anzeigedauer der Events', 'Anzeigedauer der Events wird begrenzt. Bitte "strtotime" Syntax (+7 days, +2 weeks) verwenden.'];
$GLOBALS['TL_LANG']['tl_module']['hide_started'] = ['Laufende Events nicht anzeigen', 'Events, die bereits gestartet sind, werden nicht mehr angezeigt.'];

$GLOBALS['TL_LANG']['tl_module']['cal_format_ext'] = ['Anzeigeformat (erweitert strtotime)', 'Standard Anzeigeformat wird ignoriert, wenn gesetzt. Bitte "strtotime" Syntax (+7 days, +2 weeks) verwenden. +2 days => aktueller Tag + 2 Tage. Kann nicht mit (erweitert Zeitraum) verwendet werden.'];

$GLOBALS['TL_LANG']['tl_module']['range_date'] = ['Anzeigeformat (erweitert Zeitraum)', 'Standard Anzeigeformat wird ignoriert, wenn gesetzt. Hier können die Events auf ein Start- und End-Datum eingegrenzt werden. Kann nicht mit (erweitert strtotime) verwendet werden.'];
$GLOBALS['TL_LANG']['tl_module']['range_from'] = ['Datum von', 'Start-Datum der Events.'];
$GLOBALS['TL_LANG']['tl_module']['range_to'] = ['Datum bis', 'End-Datum der Events.'];

$GLOBALS['TL_LANG']['tl_module']['fc_editable'] = ['Events bearbeiten', 'Das Bearbeiten (Datum und/oder Zeit ändern) von Events ohne Wiederholungen erlauben.'];
$GLOBALS['TL_LANG']['tl_module']['businessHours'] = ['Arbeitszeiten', 'Arbeitszeiten hervorheben.'];
$GLOBALS['TL_LANG']['tl_module']['eventLimit'] = ['Events limitieren', 'Limitiert die Anzahl der Events eines Tages.'];
$GLOBALS['TL_LANG']['tl_module']['weekNumbers'] = ['Kalenderwochen', 'Kaldenderwochen werden im Kalendaer angezeigt.'];
$GLOBALS['TL_LANG']['tl_module']['weekNumbersWithinDays'] = ['Kalenderwochen in den Tagen', 'Anzeige der Kalenderwochen in den Tagen in der Monats- und Basisansicht.'];

$GLOBALS['TL_LANG']['tl_module']['cal_times_range'] = ['Zeitfenster für den Stundenplan.', 'Zeigt die Zeiten links als Label im Stundeninterval an.'];
$GLOBALS['TL_LANG']['tl_module']['time_range_from'] = ['Zeit von', 'Startzeit für den Stundenplan.'];
$GLOBALS['TL_LANG']['tl_module']['time_range_to'] = ['Zeit bis', 'Endzeit für den Stundenplan.'];

$GLOBALS['TL_LANG']['tl_module']['cellhight'] = ['Zellenhöhe eines Events', 'Höhe der Zelle eines Events in px pro Stunde. Standard ist 1px pro Minute und damit 60px bei einem Interval von 1 Stunde.'];
$GLOBALS['TL_LANG']['tl_module']['regform'] = ['Benachrichtigung', 'Wählen Sie eine Benachrichtigung aus.'];
$GLOBALS['TL_LANG']['tl_module']['regtype'] = ['Art der Bestätigung', 'Wählen Sie die art der Bestätigung aus.'];
$GLOBALS['TL_LANG']['tl_module']['regtypes'][1] = 'Bestätigung Anmeldung';
$GLOBALS['TL_LANG']['tl_module']['regtypes'][0] = 'Bestätigung Abmeldung';
$GLOBALS['TL_LANG']['tl_module']['ignore_urlparameter'] = ['URL Parameter nicht anhängen', 'Es werden keine Paramter (date, week, month, ...) an die Event URL gehängt.'];

$GLOBALS['TL_LANG']['tl_module']['filter_fields'] = ['Event Filterung', 'Felder auswählen, auf die im Frontend Template gefiltert werden kann.'];

/*
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['displayDurationError'] = 'strtotime Wert nicht lesbar.';
$GLOBALS['TL_LANG']['tl_module']['displayDurationError2'] = 'strtotime Wert flasch. Ergibt aktuelles Datum.';
$GLOBALS['TL_LANG']['tl_module']['config_ext_legend'] = 'Modul-Konfiguration (erweitert)';
$GLOBALS['TL_LANG']['tl_module']['registration_legend'] = 'Anmeldung / Abmeldung (Benachrichtigung und Typ)';
$GLOBALS['TL_LANG']['tl_module']['filter_legend'] = 'Filter';

$GLOBALS['TL_LANG']['tl_module']['regerror']['param'] = 'Es ist ein Fehler mit den Parametern aufgetreten.';
$GLOBALS['TL_LANG']['tl_module']['regerror']['noevt'] = 'Das Event ist nicht mehr vorhanden.';
$GLOBALS['TL_LANG']['tl_module']['regerror']['daevt'] = 'Das Event ist zurzeit nicht aktiv.';
$GLOBALS['TL_LANG']['tl_module']['regerror']['dline'] = 'Der Abmeldeschluss ist erreicht. Eine Abmeldung ist auf diesem Weg nicht mehr möglich.';
$GLOBALS['TL_LANG']['tl_module']['regerror']['admin'] = 'Bitte kontaktieren Sie den Administrator.';

$GLOBALS['TL_LANG']['tl_module']['fc_useGoCal'] = ['Google Kalendar', 'Einstellungen für die Nutzung eines Google Kalenders.'];
