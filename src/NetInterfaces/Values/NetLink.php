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
 * @package   OperatingSystem/NetInterface/Values
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\NetInterfaces\Values;

class NetLink
{
    /**
     * the unique ID of this interface
     *
     * on Linux, administrators and management tools can change the name of
     * a network interface. when they do, this ID remains the same
     *
     * @var int
     */
    protected $interfaceIndex;

    /**
     * the name of this interface
     *
     * this is guaranteed to be unique at any one time. On Linux (at least) it
     * can be changed at any time by administrators and management tools
     *
     * @var string
     */
    protected $interfaceName;

    /**
     * a list of the properties advertised for this interface
     *
     * @var array
     */
    protected $interfaceProperties = [];

    /**
     * a list of the flags that have been set on this interface
     *
     * @var array
     */
    protected $interfaceFlags;

    /**
     * what kind of link does this interface use?
     *
     * examples (on Linux) include:
     *
     * - link/loopback
     * - link/ether
     * - link/none
     *
     * @var string
     */
    protected $linkType;

    /**
     * what is the MAC address of this interface?
     *
     * interfaces attached to real devices should have a MAC address
     *
     * some interfaces (e.g. VPN endpoints) will not have a MAC address
     *
     * @var string|null
     */
    protected $physicalAddress;

    /**
     * what is the hardware broadcast address of this interface?
     *
     * interfaces that do not have a MAC address will not have a hardware
     * broadcast address either
     *
     * @var string|null
     */
    protected $hardwareBroadcastAddress;

    public function __construct($interfaceIndex, $interfaceName, $interfaceMaster, $interfaceFlags, $interfaceProperties, $linkType, $physicalAddress, $hardwareBroadcastAddress)
    {
        $this->interfaceIndex = $interfaceIndex;
        $this->interfaceName = $interfaceName;
        $this->interfaceMaster = $interfaceMaster;
        $this->interfaceProperties = $interfaceProperties;
        $this->interfaceFlags = $interfaceFlags;
        $this->linkType = $linkType;
        $this->physicalAddress = $physicalAddress;
        $this->hardwareBroadcastAddress = $hardwareBroadcastAddress;
    }
}
