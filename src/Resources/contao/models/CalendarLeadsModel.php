<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao;


/**
 * Reads leads
 *
 * @author    Kester Mielke
 */
class CalendarLeadsModel extends \Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTableMaster = 'tl_lead';
    protected static $strTableDetail = 'tl_lead_data';


    /**
     * @param $fid int formularid
     * @param $eid int eventid
     *
     * @return \Database\Result|object
     */
    public static function regCountByFormEvent($fid, $eid)
    {
        // SQL bauen
        $arrsql[] = 'select sum(ld3.value) as count';
        $arrsql[] = 'from ' . static::$strTableMaster . ' lm';
        $arrsql[] = 'left join ' . static::$strTableDetail . ' ld1 on ld1.pid = lm.id';
        $arrsql[] = 'left join ' . static::$strTableDetail . ' ld2 on ld2.pid = ld1.pid';
        $arrsql[] = 'left join ' . static::$strTableDetail . ' ld3 on ld3.pid = ld2.pid';
        $arrsql[] = 'where lm.form_id = ?';
        $arrsql[] = 'and ld1.name = "eventid" and ld1.value = ?';
        $arrsql[] = 'and ld2.name = "published" and ld2.value = 1';
        $arrsql[] = 'and ld3.name = "count";';
        $sql = implode(' ', $arrsql);

        // und ausführen
        $objResult = \Database::getInstance()->prepare($sql)->execute($fid, $eid);
        $count = ($objResult->count) ? $objResult->count : 0;

        return $count;
    }


    /**
     * @param $lid int leadid
     * @param $eid int eventid
     * @param $mail string email
     *
     * @return \Database\Result|object
     */
    public static function findByLeadEventMail($lid, $eid, $mail)
    {
        // SQL bauen
        $arrsql[] = 'select ld2.pid';
        $arrsql[] = 'from ' . static::$strTableMaster . ' lm';
        $arrsql[] = 'left join ' . static::$strTableDetail . ' ld1 on lm.id = ld1.pid';
        $arrsql[] = 'left join ' . static::$strTableDetail . ' ld2 on ld2.pid = ld1.pid';
        $arrsql[] = 'where lm.form_id = ?';
        $arrsql[] = 'and ld1.name = ?';
        $arrsql[] = 'and ld1.value = ?';
        $arrsql[] = 'and ld2.name = ?';
        $arrsql[] = 'and ld2.value = ?';
        $arrsql[] = 'order by pid desc limit 1';
        $sql = implode(' ', $arrsql);

        // und ausführen
        $objResult = \Database::getInstance()->prepare($sql)->execute((int)$lid, "eventid", (int)$eid, "email", $mail);
        if (!$objResult || $objResult->numRows === 0) {
            return false;
        }

        return self::findByPid($objResult->pid);
    }


    /**
     * @param $pid
     *
     * @return \Database\Result|object
     */
    public static function findByPid($pid)
    {
        // SQL bauen
        $sql = 'select pid, name, value from ' . static::$strTableDetail . ' where pid = ? order by id';
        // und ausführen
        return \Database::getInstance()->prepare($sql)->execute($pid);
    }


    /**
     * @param $lid int leadid
     * @param $eid int eventid
     * @param $mail string email
     * @param $published int published
     *
     * @return bool
     */
    public static function updateByLeadEventMail($lid, $eid, $mail, $published)
    {
        $objResult = self::findPidByLeadEventMail($lid, $eid, $mail);
        if (!$objResult || $objResult->numRows === 0) {
            return false;
        }

        $result = self::updateByPidField($objResult->pid, 'published', $published);
        if (!$result) {
            return false;
        }

        return true;
    }


    /**
     * @param $pid int pid
     * @param $field string fieldname
     * @param $value mixed value
     *
     * @return bool
     */
    public static function updateByPid($pid, $value)
    {
        // SQL bauen
        $sql = 'update ' . static::$strTableDetail . ' set value = ?, label = ? where pid = ? and name = "published"';
        // und ausführen
        $objResult = \Database::getInstance()->prepare($sql)->execute((int)$value, (int)$value, (int)$pid);

        return (bool)$objResult;
    }
}
