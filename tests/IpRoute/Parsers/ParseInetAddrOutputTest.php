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
 * @coversDefaultClass GanbaroDigital\OperatingSystem\IpRoute\Parsers\ParseIpAddrOutput
 */
class ParseIpAddrOutputTest extends PHPUnit_Framework_TestCase
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

        $unit = new ParseIpAddrOutput;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ParseIpAddrOutput::class, $unit);
    }

    /**
     * @covers ::__invoke
     * @covers ::from
     * @covers ::fromTraversable
     * @covers ::fromString
     * @covers ::groupOutputIntoInterfaces
     * @dataProvider provideOutputToParse
     */
    public function testCanUseAsObject($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new ParseIpAddrOutput;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
    * @covers ::__invoke
    * @covers ::from
    * @covers ::fromTraversable
    * @covers ::fromString
    * @covers ::groupOutputIntoInterfaces
    * @dataProvider provideOutputToParse
     */
    public function testCanCallStatically($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseIpAddrOutput::from($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::fromTraversable
     * @dataProvider provideUnsupportedContent
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseIpAddrOutput
     */
    public function testThrowsExceptionForUnsupportedInputContent($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseIpAddrOutput::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    /**
     * @covers ::fromTraversable
     * @dataProvider provideContentWithIncompleteSupport
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E5xx_CannotParseIpAddrOutput
     */
    public function testThrowsExceptionForInputContentWhereSupportIsIncomplete($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ParseIpAddrOutput::from($output);

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

        $actualResult = ParseIpAddrOutput::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    public function provideOutputToParse()
    {
        return [
            [
                file_get_contents(__DIR__ . '/ip-addr-examples/centos-6.7.txt'),
                [
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
                                'mtu' => '65536',
                                'qdisc' => 'noqueue',
                                'state' => 'UNKNOWN',
                            ],
                            'link/loopback',
                            '00:00:00:00:00:00',
                            '00:00:00:00:00:00'
                        ),
                        [
                            new InetAddress(
                                '127.0.0.1',
                                '/8',
                                null,
                                'host',
                                null,
                                'lo',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            117,
                            'eth0',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                            ],
                            'link/ether',
                            '02:42:ac:11:00:02',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.17.0.2',
                                '/16',
                                null,
                                'global',
                                null,
                                'eth0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),

                ]
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-examples/centos-7.2.txt'),
                [
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
                                'mtu' => '65536',
                                'qdisc' => 'noqueue',
                                'state' => 'UNKNOWN',
                            ],
                            'link/loopback',
                            '00:00:00:00:00:00',
                            '00:00:00:00:00:00'
                        ),
                        [
                            new InetAddress(
                                '127.0.0.1',
                                '/8',
                                null,
                                'host',
                                null,
                                'lo',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            119,
                            'eth0',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                            ],
                            'link/ether',
                            '02:42:ac:11:00:02',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.17.0.2',
                                '/16',
                                null,
                                'global',
                                null,
                                'eth0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),

                ]
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-examples/linuxmint-17.3.txt'),
                [
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
                                'mtu' => '65536',
                                'qdisc' => 'noqueue',
                                'state' => 'UNKNOWN',
                                'group' => 'default',
                            ],
                            'link/loopback',
                            '00:00:00:00:00:00',
                            '00:00:00:00:00:00'
                        ),
                        [
                            new InetAddress(
                                '127.0.0.1',
                                '/8',
                                null,
                                'host',
                                null,
                                'lo',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            121,
                            'eth0',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                            ],
                            'link/ether',
                            '02:42:ac:11:00:02',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.17.0.2',
                                '/16',
                                null,
                                'global',
                                null,
                                'eth0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),

                ]
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-examples/ubuntu-15.04.txt'),
                [
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
                                'mtu' => '65536',
                                'qdisc' => 'noqueue',
                                'state' => 'UNKNOWN',
                                'group' => 'default',
                            ],
                            'link/loopback',
                            '00:00:00:00:00:00',
                            '00:00:00:00:00:00'
                        ),
                        [
                            new InetAddress(
                                '127.0.0.1',
                                '/8',
                                null,
                                'host',
                                null,
                                'lo',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            2,
                            'eth0',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'pfifo_fast',
                                'state' => 'UP',
                                'group' => 'default',
                                'qlen' => '1000',
                            ],
                            'link/ether',
                            'b8:ae:ed:77:05:8e',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '192.168.1.117',
                                '/24',
                                '192.168.1.255',
                                'global',
                                'dynamic',
                                'eth0',
                                [
                                    'valid_lft' => '2890sec',
                                    'preferred_lft' => '2890sec',
                                ]
                            )
                        ],
                        [
                            new Inet6Address(
                                'fe80::baae:edff:fe77:58e',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            3,
                            'wlan0',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'NO-CARRIER' => true,
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
                    ),
                    new NetInterface(
                        new NetLink(
                            4,
                            'br-fff97f656194',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                            ],
                            'link/ether',
                            '02:42:39:8c:05:ca',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.18.0.1',
                                '/16',
                                null,
                                'global',
                                null,
                                'br-fff97f656194',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ],
                        [
                            new Inet6Address(
                                'fe80::42:39ff:fe8c:5ca',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            5,
                            'docker0',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'NO-CARRIER' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'DOWN',
                                'group' => 'default',
                            ],
                            'link/ether',
                            '02:42:9a:d7:a4:0f',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.17.0.1',
                                '/16',
                                null,
                                'global',
                                null,
                                'docker0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ],
                        [
                            new Inet6Address(
                                'fe80::42:9aff:fed7:a40f',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            11,
                            'veth659d46b',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'LOWER_UP' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                                'master' => 'br-fff97f656194',
                            ],
                            'link/ether',
                            '56:f4:0e:78:a9:8b',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        [
                            new Inet6Address(
                                'fe80::54f4:eff:fe78:a98b',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            13,
                            'veth1f1f872',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'LOWER_UP' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                                'master' => 'br-fff97f656194',
                            ],
                            'link/ether',
                            '36:06:22:bd:e5:e1',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        [
                            new Inet6Address(
                                'fe80::3406:22ff:febd:e5e1',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            58,
                            'veth8aaf615',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'LOWER_UP' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                                'master' => 'br-fff97f656194',
                            ],
                            'link/ether',
                            'ba:7e:e6:b7:27:af',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        [
                            new Inet6Address(
                                'fe80::b87e:e6ff:feb7:27af',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            104,
                            'veth16ee8a8',
                            null,
                            [
                                'UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                                'LOWER_UP' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                                'master' => 'br-fff97f656194',
                            ],
                            'link/ether',
                            '06:ec:6f:08:38:09',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        [
                            new Inet6Address(
                                'fe80::4ec:6fff:fe08:3809',
                                '/64',
                                'link',
                                null,
                                null,
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            105,
                            'vboxnet0',
                            null,
                            [
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'pfifo_fast',
                                'state' => 'DOWN',
                                'group' => 'default',
                                'qlen' => '1000',
                            ],
                            'link/ether',
                            '0a:00:27:00:00:00',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        []
                    ),
                    new NetInterface(
                        new NetLink(
                            106,
                            'vboxnet1',
                            null,
                            [
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noop',
                                'state' => 'DOWN',
                                'group' => 'default',
                                'qlen' => '1000'
                            ],
                            'link/ether',
                            '0a:00:27:00:00:01',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [],
                        []
                    ),
                    new NetInterface(
                        new NetLink(
                            112,
                            'tun0',
                            null,
                            [
                                'UP' => true,
                                'MULTICAST' => true,
                                'POINTOPOINT' => true,
                                'NOARP' => true,
                                'LOWER_UP' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'pfifo_fast',
                                'state' => 'UNKNOWN',
                                'group' => 'default',
                                'qlen' => '100',
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
                                null,
                                'tun0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ],
                        []
                    ),
                ]
            ],
            [
                file_get_contents(__DIR__ . '/ip-addr-examples/ubuntu-15.10.txt'),
                [
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
                                'mtu' => '65536',
                                'qdisc' => 'noqueue',
                                'state' => 'UNKNOWN',
                                'group' => 'default',
                            ],
                            'link/loopback',
                            '00:00:00:00:00:00',
                            '00:00:00:00:00:00'
                        ),
                        [
                            new InetAddress(
                                '127.0.0.1',
                                '/8',
                                null,
                                'host',
                                null,
                                'lo',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                    new NetInterface(
                        new NetLink(
                            115,
                            'eth0',
                            null,
                            [
                                'UP' => true,
                                'LOWER_UP' => true,
                                'BROADCAST' => true,
                                'MULTICAST' => true,
                            ],
                            [
                                'mtu' => '1500',
                                'qdisc' => 'noqueue',
                                'state' => 'UP',
                                'group' => 'default',
                            ],
                            'link/ether',
                            '02:42:ac:11:00:02',
                            'ff:ff:ff:ff:ff:ff'
                        ),
                        [
                            new InetAddress(
                                '172.17.0.2',
                                '/16',
                                null,
                                'global',
                                null,
                                'eth0',
                                [
                                    'valid_lft' => 'forever',
                                    'preferred_lft' => 'forever',
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
                                    'preferred_lft' => 'forever',
                                ]
                            )
                        ]
                    ),
                ]
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
            [ new ParseIpAddrOutput ],
        ];
    }
}
