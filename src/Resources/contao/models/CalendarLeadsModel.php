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

use Contao\Database;
use Contao\Database\Result;
use Contao\Model;

/**
 * Reads leads.
 */
class CalendarLeadsModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTableMaster = 'tl_lead';
    protected static $strTableDetail = 'tl_lead_data';

    /**
     * @param int    $fid   formularid
     * @param int    $eid   eventid
     * @param string $email email
     *
     * @return bool
     */
    public static function regCheckByFormEventMail($fid, $eid, $email)
    {
        // SQL bauen
        $arrsql[] = 'select ld3.value as email';
        $arrsql[] = 'from '.static::$strTableMaster.' lm';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld1 on ld1.pid = lm.id';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld2 on ld2.pid = ld1.pid';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld3 on ld3.pid = ld2.pid';
        $arrsql[] = 'where lm.form_id = ?';
        $arrsql[] = 'and ld1.name = "eventid" and ld1.value = ?';
        $arrsql[] = 'and ld2.name = "published" and ld2.value = 1';
        $arrsql[] = 'and ld3.name = "email" and ld3.value = ?;';
        $sql = implode(' ', $arrsql);

        // und ausführen
        $objResult = Database::getInstance()->prepare($sql)->execute($fid, $eid, $email);

        return $objResult->email === $email ? false : true;
    }

    /**
     * @param int $fid formularid
     * @param int $eid eventid
     *
     * @return Result|object
     */
    public static function regCountByFormEvent($fid, $eid)
    {
        // SQL bauen
        $arrsql[] = 'select sum(ld3.value) as count';
        $arrsql[] = 'from '.static::$strTableMaster.' lm';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld1 on ld1.pid = lm.id';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld2 on ld2.pid = ld1.pid';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld3 on ld3.pid = ld2.pid';
        $arrsql[] = 'where lm.form_id = ?';
        $arrsql[] = 'and ld1.name = "eventid" and ld1.value = ?';
        $arrsql[] = 'and ld2.name = "published" and ld2.value = 1';
        $arrsql[] = 'and ld3.name = "count";';
        $sql = implode(' ', $arrsql);

        // und ausführen
        $objResult = Database::getInstance()->prepare($sql)->execute($fid, $eid);

        return $objResult->count ? $objResult->count : 0;
    }

    /**
     * @param int    $lid  leadid
     * @param int    $eid  eventid
     * @param string $mail email
     *
     * @return Result|object
     */
    public static function findByLeadEventMail($lid, $eid, $mail)
    {
        // SQL bauen
        $arrsql[] = 'select ld2.pid';
        $arrsql[] = 'from '.static::$strTableMaster.' lm';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld1 on lm.id = ld1.pid';
        $arrsql[] = 'left join '.static::$strTableDetail.' ld2 on ld2.pid = ld1.pid';
        $arrsql[] = 'where lm.form_id = ?';
        $arrsql[] = 'and ld1.name = ?';
        $arrsql[] = 'and ld1.value = ?';
        $arrsql[] = 'and ld2.name = ?';
        $arrsql[] = 'and ld2.value = ?';
        $arrsql[] = 'order by pid desc limit 1';
        $sql = implode(' ', $arrsql);

        // und ausführen
        $objResult = Database::getInstance()->prepare($sql)->execute((int) $lid, 'eventid', (int) $eid, 'email', $mail);

        if (!$objResult || 0 === $objResult->numRows) {
            return false;
        }

        return self::findByPid($objResult->pid);
    }

    /**
     * @param $pid
     *
     * @return Result|object
     */
    public static function findByPid($pid)
    {
        // SQL bauen
        $sql = 'select pid, name, value from '.static::$strTableDetail.' where pid = ? order by id';
        // und ausführen
        return Database::getInstance()->prepare($sql)->execute($pid);
    }

    /**
     * @param int    $lid       leadid
     * @param int    $eid       eventid
     * @param string $mail      email
     * @param int    $published published
     *
     * @return bool
     */
    public static function updateByLeadEventMail($lid, $eid, $mail, $published)
    {
        $objResult = self::findPidByLeadEventMail($lid, $eid, $mail);

        if (!$objResult || 0 === $objResult->numRows) {
            return false;
        }

        $result = self::updateByPidField($objResult->pid, 'published', $published);

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * @param int    $pid   pid
     * @param string $field fieldname
     * @param mixed  $value value
     *
     * @return bool
     */
    public static function updateByPid($pid, $value)
    {
        // SQL bauen
        $sql = 'update '.static::$strTableDetail.' set value = ?, label = ? where pid = ? and name = "published"';
        // und ausführen
        $objResult = Database::getInstance()->prepare($sql)->execute((int) $value, (int) $value, (int) $pid);

        return (bool) $objResult;
    }
}
