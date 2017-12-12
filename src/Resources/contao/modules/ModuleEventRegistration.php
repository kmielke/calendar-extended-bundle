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

use NotificationCenter\Model\Notification;


/**
 * Class ModuleEventRegistration
 *
 * @author     Kester Mielke
 */
class ModuleEventRegistration extends \Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_evr_registration';


    /**
     * Do not show the module if no calendar has been selected
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['evr_registration'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate module
     */
    protected function compile()
    {
        \System::loadLanguageFile('tl_module');

        /** @var \FrontendTemplate|object $objTemplate */
        $objTemplate = new \FrontendTemplate('evr_registration');

        $objTemplate->hasError = false;
        $msgError = array();
        $reg_type = (int)$this->regtype;

        $objTemplate->type = $GLOBALS['TL_LANG']['tl_module']['regtypes'][$reg_type];

        // Id der Benachrichtigung
        $ncid = $this->nc_notification;

        // Get the input parameter
        $lead_id = (\Input::get('lead')) ? \Input::get('lead') : false;
        $event_id = (\Input::get('event')) ? \Input::get('event') : false;
        $email = (\Input::get('email')) ? \Input::get('email') : false;

        // Fehler anzeigen, wenn parameter fehlen
        if (!$lead_id || !$event_id || !$email ) {
            $objTemplate->hasError = true;
            $msgError[] = $GLOBALS['TL_LANG']['tl_module']['regerror']['param'];
        }

        // Event auf Existens prüfen
        $objEvent = \CalendarEventsModelExt::findById($event_id);
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
            if ($reg_type === 0 && $objEvent->regenddate > 0 && $objEvent->regenddate < time()) {
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
            $objNotification = \NotificationCenter\Model\Notification::findByPk($ncid);
            if (null !== $objNotification) {
                $arrTokens = array();
                $objResult = \CalendarLeadsModel::findByLeadEventMail($lead_id, $event_id, $email);

                if ($objResult !== null) {
                    // zuerst den entsprechenden Datensatz updaten...
                    $published = $this->regtype;
                    $result = \CalendarLeadsModel::updateByPid($objResult->pid, $published);

                    if ($result) {
                        // Dann bauen wir arrTokens für die Nachrichten
                        $arrRawData = array();
                        while ($objResult->next()) {
                            $arrTokens['recipient_' . $objResult->name] = $objResult->value;
                            $arrRawData[] = ucfirst($objResult->name) . ': ' . $objResult->value;
                        }
                        $arrTokens['raw_data'] = implode('<br>', $arrRawData);
                        unset($arrRawData);
                        $arrTokens['recipient_published'] = $reg_type;
                        $arrTokens['recipients'] = array($email, $objPage->adminEmail);
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