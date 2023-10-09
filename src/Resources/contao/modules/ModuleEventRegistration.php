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

namespace Kmielke\CalendarExtendedBundle;

use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\System;
use NotificationCenter\Model\Notification;

/**
 * Class ModuleEventRegistration.
 */
class ModuleEventRegistration extends Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_evr_registration';

    /**
     * Do not show the module if no calendar has been selected.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['evr_registration'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate module.
     */
    protected function compile(): void
    {
        System::loadLanguageFile('tl_module');

        /** @var FrontendTemplate|object $objTemplate */
        $objTemplate = new FrontendTemplate('evr_registration');

        $objTemplate->hasError = false;
        $msgError = [];
        $reg_type = (int) $this->regtype;

        $objTemplate->type = $GLOBALS['TL_LANG']['tl_module']['regtypes'][$reg_type];

        // Id der Benachrichtigung
        $ncid = $this->nc_notification;

        // Get the input parameter
        $lead_id = Input::get('lead') ?: false;
        $event_id = Input::get('event') ?: false;
        $email = Input::get('email') ?: false;

        // Fehler anzeigen, wenn parameter fehlen
        if (!$lead_id || !$event_id || !$email) {
            $objTemplate->hasError = true;
            $msgError[] = $GLOBALS['TL_LANG']['tl_module']['regerror']['param'];
        }

        // Event auf Existens prüfen
        $objEvent = CalendarEventsModelExt::findById($event_id);

        if (!$objEvent) {
            $objTemplate->hasError = true;
            $msgError[] = $GLOBALS['TL_LANG']['tl_module']['regerror']['noevt'];
        } else {
            // ist das Event da, sollte es published sein
            if (!$objEvent->published) {
                $objTemplate->hasError = true;
                $msgError[] = $GLOBALS['TL_LANG']['tl_module']['regerror']['daevt'];
            }

            // ist der Abmeldeschluss erreicht?
            if (0 === $reg_type && $objEvent->regenddate > 0 && $objEvent->regenddate < time()) {
                $objTemplate->hasError = true;
                $msgError[] = $GLOBALS['TL_LANG']['tl_module']['regerror']['dline'];
            }
        }

        if ($objTemplate->hasError) {
            // Sind Fehler aufgetreten, dann die Meldungen zuweisen...
            $objTemplate->msgError = $msgError;
        } else {
            // Sind keine aufgetreten, dann weiter
            global $objPage;

            // Jetzt noch die notification_center mais raus
            $objNotification = Notification::findByPk($ncid);

            if (null !== $objNotification) {
                $arrTokens = [];
                $objResult = CalendarLeadsModel::findByLeadEventMail($lead_id, $event_id, $email);

                if (false !== $objResult) {
                    // zuerst den entsprechenden Datensatz updaten...
                    $published = $this->regtype;
                    $result = CalendarLeadsModel::updateByPid($objResult->pid, $published);

                    if ($result) {
                        // Dann bauen wir arrTokens für die Nachrichten
                        $arrRawData = [];

                        while ($objResult->next()) {
                            $arrTokens['recipient_'.$objResult->name] = $objResult->value;
                            $arrRawData[] = ucfirst($objResult->name).': '.$objResult->value;
                        }
                        $arrTokens['raw_data'] = implode('<br>', $arrRawData);
                        unset($arrRawData);
                        $arrTokens['recipient_published'] = $reg_type;
                        $arrTokens['recipients'] = [$email, $objPage->adminEmail];
                        $arrTokens['page_title'] = $objPage->pageTitle;
                        $arrTokens['admin_email'] = $objPage->adminEmail;

                        // und dann senden wir die Nachrichten
                        $objNotification->send($arrTokens, $GLOBALS['TL_LANGUAGE']);
                    } else {
                        $objTemplate->hasError = true;
                        $msgError[] = 'Fehler bei DB Update.';
                    }
                } else {
                    $objTemplate->hasError = true;
                    $msgError[] = 'Keine Daten gefunden.';
                }
            }
        }

        $this->Template->event_registration = $objTemplate->parse();
    }
}
