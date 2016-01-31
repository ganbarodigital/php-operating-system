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
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\Inet6Address;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetInterface;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetLink;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\IpRoute\Parsers\ParseNetInterface
 */
class ParseNetInterfaceTest extends PHPUnit_Framework_TestCase
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

        $unit = new ParseNetInterface;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ParseNetInterface::class, $unit);
    }

    /**
     * @covers ::__invoke
     * @covers ::from
     * @covers ::fromTraversable
     * @covers ::fromString
     * @covers ::breakupOutput
     * @covers ::convertToInetAddresses
     * @covers ::convertToInet6Addresses
     * @covers ::convertToLink
     * @dataProvider provideNetInterfacesToParse
     */
    public function testCanUseAsObject($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new ParseNetInterface;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     * @covers ::fromTraversable
     * @covers ::fromString
     * @covers ::breakupOutput
     * @covers ::convertToInetAddresses
     * @covers ::convertToInet6Addresses
     * @covers ::convertToLink
     * @dataProvider provideNetInterfacesToParse
     */
    public function testCanCallStatically($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseNetInterface::from($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::breakupOutput
     * @dataProvider provideUnsupportedContent
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseNetInterface
     */
    public function testThrowsExceptionForUnsupportedInputContent($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseNetInterface::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    /**
     * @covers ::breakupOutput
     * @dataProvider provideContentWithIncompleteSupport
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E5xx_CannotParseNetInterface
     */
    public function testThrowsExceptionForInputContentWhereSupportIsIncomplete($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseNetInterface::from($output);

        // ----------------------------------------------------------------
        // test the results
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

        $actualResult = ParseNetInterface::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    public function provideNetInterfacesToParse()
    {
        return [
            [
                file_get_contents(__DIR__ . '/ip-addr-one-interface-examples/example-1.txt'),
                new NetInterface(
                    new NetLink(
                        1,
                        'lo',
                        null,
                        [
                            'LOOPBACK' => true,
                            'UP' => true,
                            'LOWER_UP' => true,
                        ],
                        [
                            'mtu' => 65536,
                            'qdisc' => 'noqueue',
                            'state' => 'UNKNOWN'
                        ],
                        'link/loopback',
                        '00:00:00:00:00:00',
                        '00:00:00:00:00:00'
                    ),
                    [
                        new InetAddress(
                            '127.0.0.1',
                            '/8',
                            '',
                            'host',
                            '',
                            'lo',
                            [
                                'valid_lft' => 'forever',
                                'preferred_lft' => 'forever'
                            ]
                        )
                    ],
                    [
                        new Inet6Address(
                            '::1',
                            '/128',
                            'host',
                            null,
                            null,
                            [
                                'valid_lft' => 'forever',
                                'preferred_lft' => 'forever'
                            ]
                        )
                    ]
                )
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-one-interface-examples/example-2.txt'),
                new NetInterface(
                    new NetLink(
                        117,
                        'eth0',
                        null,
                        [
                            'BROADCAST' => true,
                            'MULTICAST' => true,
                            'UP' => true,
                            'LOWER_UP' => true,
                        ],
                        [
                            'mtu' => 1500,
                            'qdisc' => 'noqueue',
                            'state' => 'UP'
                        ],
                        'link/ether',
                        '02:42:ac:11:00:02',
                        'ff:ff:ff:ff:ff:ff'
                    ),
                    [
                        new InetAddress(
                            '172.17.0.2',
                            '/16',
                            '',
                            'global',
                            '',
                            'eth0',
                            [
                                'valid_lft' => 'forever',
                                'preferred_lft' => 'forever'
                            ]
                        )
                    ],
                    [
                        new Inet6Address(
                            'fe80::42:acff:fe11:2',
                            '/64',
                            'link',
                            null,
                            null,
                            [
                                'valid_lft' => 'forever',
                                'preferred_lft' => 'forever'
                            ]
                        )
                    ]
                )
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-one-interface-examples/example-3.txt'),
                new NetInterface(
                    new NetLink(
                        3,
                        'wlan0',
                        null,
                        [
                            'NO-CARRIER' => true,
                            'BROADCAST' => true,
                            'MULTICAST' => true,
                            'UP' => true,
                        ],
                        [
                            'mtu' => '1500',
                            'qdisc' => 'mq',
                            'state' => 'DOWN',
                            'group' => 'default',
                            'qlen' => '1000',
                        ],
                        'link/ether',
                        '34:13:e8:36:ad:df',
                        'ff:ff:ff:ff:ff:ff'
                    ),
                    [],
                    []
                )
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-one-interface-examples/example-4.txt'),
                new NetInterface(
                    new NetLink(
                        112,
                        'tun0',
                        null,
                        [
                            'POINTOPOINT' => true,
                            'MULTICAST' => true,
                            'NOARP' => true,
                            'UP' => true,
                            'LOWER_UP' => true,
                        ],
                        [
                            'mtu' => 1500,
                            'qdisc' => 'pfifo_fast',
                            'state' => 'UNKNOWN',
                            'group' => 'default',
                            'qlen' => '100'
                        ],
                        'link/none',
                        null,
                        null
                    ),
                    [
                        new InetAddress(
                            '192.168.177.209',
                            '/25',
                            '192.168.177.255',
                            'global',
                            '',
                            'tun0',
                            [
                                'valid_lft' => 'forever',
                                'preferred_lft' => 'forever'
                            ]
                        )
                    ],
                    []
                )
            ],
        ];
    }

    public function provideUnsupportedContent()
    {
        return [
            [ file_get_contents(__DIR__ . '/ip-addr-one-interface-bad-examples/example-1.txt') ],
        ];
    }

    public function provideContentWithIncompleteSupport()
    {
        return [
            [ file_get_contents(__DIR__ . '/ip-addr-one-interface-bad-examples/example-2.txt') ],
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
