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

namespace Cgoit\BfvWidgetBundle\Tests;

use Kmielke\CalendarExtendedBundle\CalendarExtendedBundle;
use PHPUnit\Framework\TestCase;

class CalendarExtendedBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new CalendarExtendedBundle();

        $this->assertInstanceOf('Kmielke\CalendarExtendedBundle\CalendarExtendedBundle', $bundle);
    }
}
