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
 * @package   OperatingSystem/IpRoute/Parsers
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\IpRoute\Parsers;

use GanbaroDigital\OperatingSystem\NetInterfaces\Values\InetAddress;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\IpRoute\Parsers\ParseInetAddress
 */
class ParseInetAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * @coversNone
     */
    public function testCanInstantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new ParseInetAddress;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ParseInetAddress::class, $unit);
    }

    /**
     * @covers ::__invoke
     * @covers ::from
     * @covers ::fromString
     * @covers ::fromTraversable
     * @covers ::parseFirstLine
     * @covers ::parseSecondLine
     * @dataProvider provideExamplesToParse
     */
    public function testCanUseAsObject($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new ParseInetAddress;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     * @covers ::fromString
     * @covers ::fromTraversable
     * @covers ::parseFirstLine
     * @covers ::parseSecondLine
     * @dataProvider provideExamplesToParse
     */
    public function testCanCallStatically($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseInetAddress::from($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::nothingMatchesTheInputType
     * @dataProvider provideUnsupportedTypes
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType
     */
    public function testThrowsExceptionForUnsupportedInputType($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseInetAddress::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    /**
     * @covers ::from
     * @covers ::fromString
     * @covers ::fromTraversable
     * @covers ::parseFirstLine
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseInetAddressLine
     */
    public function testThrowsExceptionForUnparseableContent()
    {
        // ----------------------------------------------------------------
        // setup your test

        $output = "alfred: lo: <LOOPBACK,UP,LOWER_UP> mtu 65536 qdisc noqueue state UNKNOWN";

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseInetAddress::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    public function provideExamplesToParse()
    {
        return [
            [
                file_get_contents(__DIR__ . '/inet-examples/example-1.txt'),
                new InetAddress(
                    "127.0.0.1",
                    "/8",
                    null,
                    'host',
                    null,
                    'lo',
                    [
                        "valid_lft" => "forever",
                        "preferred_lft" => "forever"
                    ]
                )
            ],
            [
                file_get_contents(__DIR__ . '/inet-examples/example-2.txt'),
                new InetAddress(
                    "172.17.0.2",
                    "/16",
                    null,
                    'global',
                    null,
                    'eth0',
                    [
                        "valid_lft" => "forever",
                        "preferred_lft" => "forever"
                    ]
                )
            ],
            [
                file_get_contents(__DIR__ . '/inet-examples/example-3.txt'),
                new InetAddress(
                    "192.168.1.117",
                    "/24",
                    "192.168.1.255",
                    'global',
                    'dynamic',
                    'eth0',
                    [
                        "valid_lft" => "2890sec",
                        "preferred_lft" => "2890sec"
                    ]
                )
            ],
        ];
    }

    public function provideUnsupportedTypes()
    {
        return [
            [ null ],
            [ true ],
            [ false ],
            [ function(){ return []; } ],
            [ 0 ],
            [ 100 ],
            [ 0.0 ],
            [ 3.1415927 ],
            [ STDIN ],
            [ new ParseNetInterface ],
        ];
    }
}
