<?php

/**
 * Copyright (c) 2016-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   OperatingSystem/OsType/ValueBuilders
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\OsType\ValueBuilders;

use PHPUnit_Framework_TestCase;

use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\Debian;
use GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint;
use GanbaroDigital\OperatingSystem\OsType\Values\OSX;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;
use GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu;
use GanbaroDigital\OperatingSystem\OsType\Values\Unknown;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\OsType\ValueBuilders\EnsureOsType
 */
class EnsureOsTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     * @covers ::from
     *
     * @dataProvider provideOsTypesToTest
     */
    public function testCanUseAsObject($data, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new EnsureOsType;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($data);

        // ----------------------------------------------------------------
        // test the results

        $this->assertSame($expectedResult, $actualResult);
    }


    /**
     * @covers ::from
     *
     * @dataProvider provideOsTypesToTest
     */
    public function testCanCallStatically($data, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $actualResult = EnsureOsType::from($data);

        // ----------------------------------------------------------------
        // test the results

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @covers ::__invoke
     * @covers ::from
     */
    public function testReturnsUnknownWhenNullValueUsed()
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new EnsureOsType;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit(null);

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(Unknown::class, $actualResult);
    }

    public function provideOsTypesToTest()
    {
        $centos = new CentOS('6.7');
        $debian = new Debian("8.2");
        $linuxMint = new LinuxMint("17.3");
        $osx = new OSX("10.11.2");
        $ubuntu = new Ubuntu("15.04");

        return [
            [
                $centos,
                $centos
            ],
            [
                $debian,
                $debian,
            ],
            [
                $linuxMint,
                $linuxMint
            ],
            [
                $osx,
                $osx
            ],
            [
                $ubuntu,
                $ubuntu
            ]
        ];
    }
}
