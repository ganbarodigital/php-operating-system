<?php

/**
 * Copyright (c) 2015-present Ganbaro Digital Ltd
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
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-text-tools
 */

namespace GanbaroDigital\OperatingSystem\OsType\ValueBuilders;

use PHPUnit_Framework_TestCase;

use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\Debian;
use GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;
use GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromEtcIssue
 */
class BuildTypeFromEtcIssueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     * @covers ::from
     * @covers ::matchContentsToType
     * @covers ::matchTypeToRegex
     * @dataProvider provideEtcIssueFilesToTest
     */
    public function testCanUseAsObject($path, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new BuildTypeFromEtcIssue;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($path);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     * @covers ::matchContentsToType
     * @covers ::matchTypeToRegex
     * @dataProvider provideEtcIssueFilesToTest
     */
    public function testCanCallStatically($path, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $actualResult = BuildTypeFromEtcIssue::from($path);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     */
    public function testReturnsNullWhenNoFileExists()
    {
        // ----------------------------------------------------------------
        // setup your test

        $path = '/gobbledygook/will-not-exist';

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = BuildTypeFromEtcIssue::from($path);

        // ----------------------------------------------------------------
        // test the results

        $this->assertNull($actualResult);
    }

    /**
     * @covers ::from
     * @covers ::matchContentsToType
     * @covers ::matchTypeToRegex
     */
    public function testReturnsNullWhenNoMatchingOperatingSystemFound()
    {
        // ----------------------------------------------------------------
        // setup your test

        $path = __DIR__ . '/etc-issue-examples/invalid-issue.txt';

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = BuildTypeFromEtcIssue::from($path);

        // ----------------------------------------------------------------
        // test the results

        $this->assertNull($actualResult);
    }

    /**
     * @covers ::inDefaultLocation
     */
    public function testSupportsCheckingDefaultLocation()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectingType = false;
        if (file_exists('/etc/issue')) {
            $expectingType = true;
        }

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = BuildTypeFromEtcIssue::inDefaultLocation();

        // ----------------------------------------------------------------
        // test the results

        if ($expectingType) {
            $this->assertInstanceOf(OsType::class, $actualResult);
        }
        else {
            $this->assertNull($actualResult);
        }
    }

    public function provideEtcIssueFilesToTest()
    {
        return [
            [
                __DIR__ . '/etc-issue-examples/centos-6.7.txt',
                new CentOS('6.7')
            ],
            [
                __DIR__ . '/etc-issue-examples/ubuntu-15.04.txt',
                new Ubuntu('15.04')
            ]
        ];
    }
}