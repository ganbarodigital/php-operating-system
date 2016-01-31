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
 * @link      http://code.ganbarodigital.com/php-array-tools
 */

namespace GanbaroDigital\OperatingSystem\IpRoute\Parsers;

use GanbaroDigital\ArrayTools\Parsers\ConvertKeyValuePairsToArray;
use GanbaroDigital\ArrayTools\Filters\ExtractFirstItem;
use GanbaroDigital\ArrayTools\ValueBuilders\ConvertToArray;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseInet6AddressLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\Inet6Address;
use GanbaroDigital\Reflection\Maps\MapTypeToMethod;
use GanbaroDigital\Reflection\ValueBuilders\SimpleType;

class ParseInet6Address
{
    /**
     * parse an inet6 (IPv6) address block from the output of 'ip addr show'
     *
     * @param  mixed $inet6Lines
     *         the line(s) to parse
     * @return Inet6Address
     *         the inet address defined in the output
     */
    public function __invoke($inet6Lines)
    {
        return self::from($inet6Lines);
    }

    public static function from($inet6Lines)
    {
        $method = MapTypeToMethod::using($inet6Lines, self::$dispatchMap);
        return self::$method($inet6Lines);
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
     * parse an inet6 (IPv6) address block from the output of 'ip addr show'
     *
     * @param  string $inet6Lines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    private static function fromString($inet6Lines)
    {
        $lines = explode("\n", $inet6Lines);

        return self::fromTraversable($lines);
    }

    /**
     * parse an inet6 (IPv6) address block from the output of 'ip addr show'
     *
     * @param  array|Traversable $inet6Lines
     *         the line(s) to parse
     * @return InetAddress
     *         the inet address defined in the output
     */
    private static function fromTraversable($inet6Lines)
    {
        // we want a real PHP array for this
        $inet6Lines = ConvertToArray::from($inet6Lines);

        // line 0 is the inet line itself
        $inet6Details = self::parseFirstLine(array_shift($inet6Lines));

        // if there is a line 1, it contains additional flags
        while (!empty($inet6Lines)) {
            $inet6Details['properties'] = array_merge(self::parseSecondLine(array_shift($inet6Lines)), $inet6Details['properties']);
        }

        // now to convert this into an InetAddress value
        $retval = new Inet6Address(
            $inet6Details['address'],
            $inet6Details['netmask'],
            $inet6Details['scope'],
            $inet6Details['addressLabel'],
            $inet6Details['linkDevice'],
            $inet6Details['properties']
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
        $regex = "~inet6 (?<addr>.+)(?<netmask>/[0-9]{1,5})( scope (?<scope>[a-z]+)){0,1}( (?<addressLabel>secondary|dynamic|deprecated|tentative)){0,1}( (?<linkDevice>.*)){0,1}$~";
        $matches = [];
        if (!preg_match_all($regex, $line, $matches)) {
            throw new E4xx_CannotParseInet6AddressLine($line);
        }

        return [
            'address' => $matches['addr'][0],
            'netmask' => $matches['netmask'][0],
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
