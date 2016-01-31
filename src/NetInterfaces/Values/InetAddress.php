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
 * @package   OperatingSystem/NetInterfaces/Values
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\NetInterfaces\Values;

/**
 * represents an IPv4 address assigned to a network interface
 */
class InetAddress
{
    /**
     * what is the address assigned to the network interface?
     *
     * this is in the classic A.B.C.D format
     *
     * @var string
     */
    protected $address;

    /**
     * what is the netmask assigned to the network interface?
     *
     * this is in the /X format
     *
     * @var string
     */
    protected $netmask;

    /**
     * what is the broadcast address assigned to the network interface?
     *
     * this is in the classic A.B.C.D format
     *
     * @var string
     */
    protected $broadcastAddress;

    /**
     * what is the scope of this address?
     *
     * On Linux, the following scopes are known to exist:
     *
     * - global
     * - link
     * - host
     * - site
     *
     * @var string
     */
    protected $scope;

    /**
     * what is the address label assigned to this IP address?
     *
     * On Linux, the following address labels are known to exist:
     *
     * - secondary
     * - dynamic
     * - deprecated
     * - tentative
     *
     * @var string
     */
    protected $addressLabel;

    /**
     * what is the name of the interface that this address is valid on?
     *
     * @var string
     */
    protected $linkDevice;

    /**
     * what additional properties have been advertised for this address?
     *
     * @var array
     */
    protected $properties;

    /**
     * create a value representing an IPv4 address attached to a network interface
     *
     * @param string $address
     *        the IPv4 address itself
     * @param string $netmask
     *        the netmask in /X format
     * @param string $broadcastAddress
     *        the broadcast address
     * @param string $scope
     *        the network scope of this address
     * @param string $addressLabel
     *        the address label assigned to this IP address
     * @param string $linkDevice
     *        the name of the interface that this address is available on
     * @param array $properties
     *        any additional properties declared on this network address
     */
    public function __construct($address, $netmask, $broadcastAddress, $scope, $addressLabel, $linkDevice, $properties = [])
    {
        $this->address = $address;
        $this->netmask = $netmask;
        $this->broadcastAddress = $broadcastAddress;
        $this->scope = $scope;
        $this->addressLabel = $addressLabel;
        $this->linkDevice = $linkDevice;
        $this->properties = $properties;
    }
}
