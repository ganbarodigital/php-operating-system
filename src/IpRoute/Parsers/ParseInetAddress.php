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
 * @package   OperatingSystem/NetInterface/Parsers
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\IpRoute\Parsers;

use GanbaroDigital\ArrayTools\Filters\ExtractFirstItem;
use GanbaroDigital\ArrayTools\Parsers\ConvertKeyValuePairsToArray;
use GanbaroDigital\ArrayTools\ValueBuilders\ConvertToArray;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseInetAddressLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\OperatingSystem\IpRoute\Classifiers\ClassifyIpAddrLine;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\InetAddress;
use GanbaroDigital\Reflection\Maps\MapTypeToMethod;
use GanbaroDigital\Reflection\ValueBuilders\SimpleType;

class ParseInetAddress
{
    /**
     * parse an inet (IPv4) address block from the output of 'ip addr show'
     *
     * @param  mixed $inetLines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    public function __invoke($inetLines)
    {
        return self::from($inetLines);
    }

    /**
     * parse an inet (IPv4) address block from the output of 'ip addr show'
     *
     * @param  mixed $inetLines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    public static function from($inetLines)
    {
        $method = MapTypeToMethod::using($inetLines, self::$dispatchMap);
        return self::$method($inetLines);
    }

    /**
     * a map of which types we support, and how to process them
     * @var array
     */
    private static $dispatchMap = [
        "String" => "fromString",
        "Traversable" => "fromTraversable",
    ];

    /**
     * called when we've been given a data type that we do not support
     *
     * @param  mixed $output
     *         the unsupported data
     * @return void
     * @throws E4xx_UnsupportedType
     */
    private static function nothingMatchesTheInputType($output)
    {
        throw new E4xx_UnsupportedType(SimpleType::from($output));
    }

    /**
     * parse an inet (IPv4) address block from the output of 'ip addr show'
     *
     * @param  string $inetLines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    private static function fromString($inetLines)
    {
        $lines = explode("\n", $inetLines);
        return self::fromTraversable($lines);
    }

    /**
     * parse an inet (IPv4) address block from the output of 'ip addr show'
     *
     * @param  array|Traversable $inetLines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    private static function fromTraversable($inetLines)
    {
        // we want a real PHP array for this
        $inetLines = ConvertToArray::from($inetLines);

        // line 0 is the inet line itself
        $inetDetails = self::parseFirstLine(array_shift($inetLines));

        // if there is a line 1, it contains additional flags
        while (!empty($inetLines)) {
            $inetDetails['properties'] = array_merge(self::parseSecondLine(array_shift($inetLines)), $inetDetails['properties']);
        }

        // now to convert this into an InetAddress value
        $retval = new InetAddress(
            $inetDetails['address'],
            $inetDetails['netmask'],
            $inetDetails['broadcast'],
            $inetDetails['scope'],
            $inetDetails['addressLabel'],
            $inetDetails['linkDevice'],
            $inetDetails['properties']
        );

        // all done
        return $retval;
    }

    /**
     * extract data from the first line of the inet address definition
     *
     * @param  string $line
     *         the line to parse
     * @return array
     *         the extracted data
     */
    private static function parseFirstLine($line)
    {
        // this regex will parse everything we know to expect from the first
        // line of an inet definition
        $regex = "~inet (?<addr>[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(?<netmask>/[0-9]{1,3})( brd (?<broadcast>[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})){0,1}( scope (?<scope>[a-z]+)){0,1}( (?<addressLabel>secondary|dynamic|deprecated|tentative)){0,1}( (?<linkDevice>.*)){0,1}$~";
        $matches = [];
        if (!preg_match_all($regex, $line, $matches)) {
            throw new E4xx_CannotParseInetAddressLine($line);
        }

        return [
            'address' => $matches['addr'][0],
            'netmask' => $matches['netmask'][0],
            'broadcast' => ExtractFirstItem::from($matches['broadcast'], null),
            'scope' => ExtractFirstItem::from($matches['scope'], null),
            'addressLabel' => ExtractFirstItem::from($matches['addressLabel'], null),
            'linkDevice' => ExtractFirstItem::from($matches['linkDevice'], null),
            'properties' => [],
        ];
    }

    /**
     * extract data from the second line of the inet address definition
     *
     * @param  string $line
     *         the line to parse
     * @return array
     *         the extracted data, as key/value pairs
     */
    private static function parseSecondLine($line)
    {
        return ConvertKeyValuePairsToArray::from($line, ' ', ' ');
    }
}
