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

use GanbaroDigital\ArrayTools\ValueBuilders\ConvertToArray;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotClassifyIpAddrLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseNetInterface;
use GanbaroDigital\OperatingSystem\Exceptions\E5xx_CannotParseNetInterface;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\OperatingSystem\Exceptions\E5xx_UnsupportedIpAddrLine;
use GanbaroDigital\OperatingSystem\IpRoute\Classifiers\ClassifyIpAddrLine;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetInterface;
use GanbaroDigital\Reflection\Maps\MapTypeToMethod;
use GanbaroDigital\Reflection\ValueBuilders\SimpleType;
use GanbaroDigital\TextTools\Filters\FilterOutEmptyValues;

/**
 * parse a single network interface definition, as found in the output of
 * the 'ip addr' and 'ip link' commands on Linux
 */
class ParseNetInterface
{
    /**
     * parse a single network interface from the output of the Linux 'ip'
     * command
     *
     * @param  mixed $output
     *         the command output to parse
     * @return NetInterface
     *         the network interface definition obtained from the command
     *         output
     */
    public function __invoke($output)
    {
        return self::from($output);
    }

    /**
     * parse a single network interface from the output of the Linux 'ip'
     * command
     *
     * @param  mixed $output
     *         the command output to parse
     * @return NetInterface
     *         the network interface definition obtained from the command
     *         output
     */
    public static function from($output)
    {
        $method = MapTypeToMethod::using($output, self::$dispatchMap);
        return self::$method($output);
    }

    /**
     * parse a single network interface from the output of the Linux 'ip'
     * command
     *
     * @param  mixed $output
     *         the command output to parse
     * @return NetInterface
     *         the network interface definition obtained from the command
     *         output
     */
    private static function fromTraversable($output)
    {
        // strip out any empty values
        $lines = FilterOutEmptyValues::from($output);

        // group the output into smaller chunks for parsing
        $breakdown = self::breakupOutput($lines);

        $inets = self::convertToInetAddresses($breakdown['inet']);
        $inet6s = self::convertToInet6Addresses($breakdown['inet6']);
        $link = self::convertToLink($breakdown['link']);

        // we can build our complete NetInterface now
        $retval = new NetInterface($link, $inets, $inet6s);

        // all done
        return $retval;
    }

    /**
     * take the output from 'ip addr show <interface>' and break it up into
     * the individual groups that we can parse
     *
     * @param  array $lines
     *         the output to break up
     * @return array
     *         the grouped output
     */
    private static function breakupOutput($lines)
    {
        $linkLines = [];
        $inetLines = [];
        $inet6Lines = [];
        $optionsTarget = 'inetLines';

        // what do we have?
        try {
            foreach ($lines as $line) {
                $lineType = ClassifyIpAddrLine::from($line);

                switch ($lineType) {
                    case ClassifyIpAddrLine::LINK_START:
                    case ClassifyIpAddrLine::LINK_ETHER:
                    case ClassifyIpAddrLine::LINK_LOOPBACK:
                    case ClassifyIpAddrLine::LINK_NONE:
                        $linkLines[] = $line;
                        break;
                    case ClassifyIpAddrLine::INET_START:
                        $inetLines[] = [ $line ];
                        $optionsTarget = 'inetLines';
                        break;
                    case ClassifyIpAddrLine::INET6_START:
                        $inet6Lines[] = [ $line ];
                        $optionsTarget = 'inet6Lines';
                        break;
                    case ClassifyIpAddrLine::INET_OPTIONS:
                        switch ($optionsTarget) {
                            case 'inetLines':
                                $inetLines[count($inetLines) - 1][] = $line;
                                break;
                            case 'inet6Lines':
                                $inet6Lines[count($inet6Lines) - 1][] = $line;
                                break;
                        }
                        break;

                    default:
                        // important that we catch any line types that we do not
                        // yet know what to do with
                        throw new E5xx_UnsupportedIpAddrLine($line, $lineType);
                }
            }
        }
        catch (E4xx_CannotClassifyIpAddrLine $e) {
            throw new E4xx_CannotParseNetInterface($lines, $e);
        }
        catch (E5xx_UnsupportedIpAddrLine $e) {
            throw new E5xx_CannotParseNetInterface($lines, $e);
        }

        // all done
        return [
            'link' => $linkLines,
            'inet' => $inetLines,
            'inet6' => $inet6Lines,
        ];
    }

    /**
     * convert the isolated lines of text for inet (IPv4) addresses into a
     * list of InetAddress objects
     *
     * @param  array $inets
     *         the 'ip addr' lines for the inet addresses
     * @return array<InetAddress>
     *         a list of InetAddress entries
     */
    private static function convertToInetAddresses($inets)
    {
        // our return value
        $retval = [];

        foreach ($inets as $inetLines) {
            $retval[] = ParseInetAddress::from($inetLines);
        }

        // all done
        return $retval;
    }

    /**
     * convert the isolated lines of text for inet6 (IPv6) addresses into a
     * list of Inet6Address objects
     *
     * @param  array $inet6s
     *         the 'ip addr' lines for the inet6 addresses
     * @return array<Inet6Address>
     *         a list of Inet6Address entries
     */
    private static function convertToInet6Addresses($inet6s)
    {
        // our return value
        $retval = [];

        foreach ($inet6s as $inet6Lines) {
            $retval[] = ParseInet6Address::from($inet6Lines);
        }

        // all done
        return $retval;
    }

    /**
     * convert the isolated lines of text for the physical link into a
     * single NetLink object
     *
     * @param  array $lines
     *         the 'ip addr' lines for the physical link
     * @return NetLink
     *         the value object extracted from the lines of output
     */
    private static function convertToLink($lines)
    {
        return ParseNetLink::from($lines);
    }

    /**
     * parse a single network interface from the output of the Linux 'ip'
     * command
     *
     * @param  mixed $output
     *         the command output to parse
     * @return NetInterface
     *         the network interface definition obtained from the command
     *         output
     */
    private static function fromString($output)
    {
        $lines = explode("\n", $output);
        return self::fromTraversable($lines);
    }

    /**
     * called when we've been asked to parse a datatype that we do not support
     *
     * @param  mixed $output
     *         the command output to parse
     * @return void
     * @throws E4xx_UnsupportedType
     */
    private static function nothingMatchesTheInputType($output)
    {
        throw new E4xx_UnsupportedType(SimpleType::from($output));
    }

    /**
     * our map of how to handle each data type we are passed
     * @var array
     */
    private static $dispatchMap = [
        'String' => 'fromString',
        'Traversable' => 'fromTraversable',
    ];
}
