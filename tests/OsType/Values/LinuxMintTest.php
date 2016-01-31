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
 * @package   OperatingSystem/OsType/Values
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\OsType\Values;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint
 */
class LinuxMintTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanInstantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new LinuxMint('17.3');

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(LinuxMint::class, $unit);
    }

    /**
     * @covers ::__construct
     */
    public function testIsLinuxDistro()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new LinuxMint('17.3');

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(LinuxDistro::class, $unit);
    }

    /**
     * @covers ::__construct
     */
    public function testIsOsType()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new LinuxMint('17.3');

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(OsType::class, $unit);
    }

    /**
     * @covers ::getName
     */
    public function testCanGetName()
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new LinuxMint('17.3');
        $expectedName = 'LinuxMint';

        // ----------------------------------------------------------------
        // perform the change

        $actualName = $unit->getName();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedName, $actualName);
    }

    /**
     * @covers ::getVersion
     */
    public function testCanGetVersion()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedVersion = '17.3';
        $unit = new LinuxMint($expectedVersion);

        // ----------------------------------------------------------------
        // perform the change

        $actualVersion = $unit->getVersion();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedVersion, $actualVersion);
    }

}