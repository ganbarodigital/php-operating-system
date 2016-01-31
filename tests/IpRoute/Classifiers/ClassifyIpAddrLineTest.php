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
 * @package   OperatingSystem/IpRoute/Classifiers
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\IpRoute\Classifiers;

use GanbaroDigital\OperatingSystem\NetInterfaces\Values\InetAddress;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\Inet6Address;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetInterface;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetLink;
use GanbaroDigital\OperatingSystem\IpRoute\Classifiers\ClassifyIpAddrLine;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\OperatingSystem\IpRoute\Classifiers\ClassifyIpAddrLine
 */
class ClassifyIpAddrLineTest extends PHPUnit_Framework_TestCase
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

        $unit = new ClassifyIpAddrLine;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ClassifyIpAddrLine::class, $unit);
    }

    /**
     * @covers ::__invoke
     * @covers ::from
     * @dataProvider provideLinesToClassify
     */
    public function testCanUseAsObject($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new ClassifyIpAddrLine;

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     * @dataProvider provideLinesToClassify
     */
    public function testCanCallStatically($output, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ClassifyIpAddrLine::from($output);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::from
     * @dataProvider provideUnsupportedTypes
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType
     */
    public function testThrowsExceptionForUnsupportedInputType($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ClassifyIpAddrLine::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    /**
     * @covers ::from
     * @dataProvider provideUnclassifiedLines
     * @expectedException GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotClassifyIpAddrLine
     */
    public function testThrowsExceptionForUnclassifiedLines($output)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = ClassifyIpAddrLine::from($output);

        // ----------------------------------------------------------------
        // test the results
    }

    public function provideLinesToClassify()
    {
        return [
            [ "1: lo: <LOOPBACK,UP,LOWER_UP> mtu 65536 qdisc noqueue state UNKNOWN group default", ClassifyIpAddrLine::LINK_START, ],
            [ "link/loopback 00:00:00:00:00:00 brd 00:00:00:00:00:00", ClassifyIpAddrLine::LINK_LOOPBACK, ],
            [ "link/ether b8:ae:ed:77:05:8e brd ff:ff:ff:ff:ff:ff", ClassifyIpAddrLine::LINK_ETHER, ],
            [ "link/none", ClassifyIpAddrLine::LINK_NONE, ],
            [ "inet 127.0.0.1/8 scope host lo", ClassifyIpAddrLine::INET_START, ],
            [ "valid_lft forever preferred_lft forever", ClassifyIpAddrLine::INET_OPTIONS, ],
            [ "inet6 ::1/128 scope host", ClassifyIpAddrLine::INET6_START, ],
        ];
    }

    public function provideUnclassifiedLines()
    {
        return [
            [ "hello, world" ],
            [ "veth659d46b Link encap:Ethernet  HWaddr 56:f4:0e:78:a9:8b" ],
            [ "inet6 addr: fe80::54f4:eff:fe78:a98b/64 Scope:Link" ],
            [ "UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1" ],
        ];
    }

    public function provideUnsupportedTypes()
    {
        return [
            [ null ],
            [ true ],
            [ false ],
            [ [], ],
            [ function(){ return []; } ],
            [ 0 ],
            [ 100 ],
            [ 0.0 ],
            [ 3.1415927 ],
            [ STDIN ],
            [ new ClassifyIpAddrLine ],
        ];
    }
}
