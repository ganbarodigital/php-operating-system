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

use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotClassifyIpAddrLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\Reflection\Requirements\RequireStringy;

class ClassifyIpAddrLine
{
    const LINK_START = 1;
    const LINK_ETHER = 2;
    const LINK_LOOPBACK = 3;
    const LINK_NONE = 4;
    const INET_START = 50;
    const INET6_START = 51;
    const INET_OPTIONS = 52;

    /**
     * a list of regexes to use to classify an address line
     * @var array
     */
    private static $typeMap = [
        '|^\d+:|' => self::LINK_START,
        '|link/ether|' => self::LINK_ETHER,
        '|link/loopback|' => self::LINK_LOOPBACK,
        '|link/none|' => self::LINK_NONE,
        '|^\s{0,}inet [0-9{1,3}\.]+/|' => self::INET_START,
        '|^\s{0,}inet6 [0-9a-f:]+/|' => self::INET6_START,
        '|^\s{0,}valid_lft |' => self::INET_OPTIONS,
    ];

    /**
     * given a single line from the output of the 'ip addr show' or 'ip link show'
     * commands, tell us what kind of line we are looking at
     *
     * @param  string $line
     *         the line to classify
     * @return int
     *         one of this class's constants
     */
    public function __invoke($line)
    {
        return self::from($line);
    }

    /**
     * given a single line from the output of the 'ip addr show' or 'ip link show'
     * commands, tell us what kind of line we are looking at
     *
     * @param  string $line
     *         the line to classify
     * @return int
     *         one of this class's constants
     */
    public static function from($line)
    {
        // robustness!
        RequireStringy::check($line, E4xx_UnsupportedType::class);
        $line = (string)$line;

        // what do we have?
        foreach (self::$typeMap as $regex => $lineType) {
            if (preg_match($regex, $line)) {
                return $lineType;
            }
        }

        // if we get here, we do not understand what we are looking at
        throw new E4xx_CannotClassifyIpAddrLine($line);
    }
}
