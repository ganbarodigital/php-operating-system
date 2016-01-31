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

use GanbaroDigital\OperatingSystem\NetInterfaces\Values\Inet6Address;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\IpRoute\Parsers\ParseInet6Address
 */
class ParseInet6AddressTest extends PHPUnit_Framework_TestCase
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

        $unit = new ParseInet6Address;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ParseInet6Address::class, $unit);
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

        $unit = new ParseInet6Address;

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

        $actualResult = ParseInet6Address::from($output);

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

        $actualResult = ParseInet6Address::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    /**
     * @covers ::from
     * @covers ::fromString
     * @covers ::fromTraversable
     * @covers ::parseFirstLine
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseInet6AddressLine
     */
    public function testThrowsExceptionForUnparseableContent()
    {
        // ----------------------------------------------------------------
        // setup your test

        $output = "alfred: lo: <LOOPBACK,UP,LOWER_UP> mtu 65536 qdisc noqueue state UNKNOWN";

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseInet6Address::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    public function provideExamplesToParse()
    {
        return [
            [
                file_get_contents(__DIR__ . '/inet6-examples/example-1.txt'),
                new Inet6Address(
                    "3ffe:2400:0:1:2a0:ccff:fe66:1878",
                    "/64",
                    'global',
                    "dynamic",
                    null,
                    [
                        "valid_lft" => "forever",
                        "preferred_lft" => "604746sec"
                    ]
                )
            ],
            [
                file_get_contents(__DIR__ . '/inet6-examples/example-2.txt'),
                new Inet6Address(
                    "fe80::2a0:ccff:fe66:1878",
                    "/10",
                    'link',
                    null,
                    null,
                    []
                )
            ],
            [
                file_get_contents(__DIR__ . '/inet6-examples/example-3.txt'),
                new Inet6Address(
                    "::1",
                    "/128",
                    'host',
                    null,
                    null,
                    [
                        "valid_lft" => "forever",
                        "preferred_lft" => "forever"
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
