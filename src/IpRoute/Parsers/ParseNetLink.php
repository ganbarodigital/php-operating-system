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
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseNetLinkLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\OperatingSystem\NetInterfaces\Values\NetLink;
use GanbaroDigital\Reflection\Maps\MapTypeToMethod;
use GanbaroDigital\Reflection\ValueBuilders\SimpleType;
use GanbaroDigital\TextTools\Filters\FilterOutEmptyValues;

class ParseNetLink
{
    /**
     * extract a NetLink from the output of 'ip addr show' or 'ip link show'
     *
     * @param  mixed $linkLines
     *         the output to parse
     * @return NetLink
     */
    public function __invoke($linkLines)
    {
        return self::from($linkLines);
    }

    /**
     * extract a NetLink from the output of 'ip addr show' or 'ip link show'
     *
     * @param  mixed $linkLines
     *         the output to parse
     * @return NetLink
     */
    public static function from($linkLines)
    {
        $method = MapTypeToMethod::using($linkLines, self::$dispatchMap);
        return self::$method($linkLines);
    }

    /**
     * extract a NetLink from the output of 'ip addr show' or 'ip link show'
     *
     * @param  string $linkLines
     *         the output to parse
     * @return NetLink
     */
    private static function fromString($linkLines)
    {
        $lines = explode("\n", $linkLines);
        return self::fromTraversable($lines);
    }

    /**
     * extract a NetLink from the output of 'ip addr show' or 'ip link show'
     *
     * @param  mixed $linkLines
     *         the output to parse
     * @return NetLink
     */
    private static function fromTraversable($linkLines)
    {
        // we want a real PHP array for this
        $linkLines = ConvertToArray::from($linkLines);

        // get rid of any empty lines of text
        $linkLines = FilterOutEmptyValues::from($linkLines);

        // line 0 has the majority interface properties
        $linkDetails = self::parseFirstLine(array_shift($linkLines));

        // if there is a line 1, it contains link layer type and (optional) addresses
        while (!empty($linkLines)) {
            $additionalDetails = self::parseSecondLine(array_shift($linkLines));
            $linkDetails = array_merge($linkDetails, $additionalDetails);
        }

        // now to convert this into an NetLink value
        $retval = new NetLink(
            $linkDetails['index'],
            $linkDetails['name'],
            $linkDetails['master'],
            $linkDetails['flags'],
            $linkDetails['properties'],
            $linkDetails['linkType'],
            $linkDetails['physicalAddress'],
            $linkDetails['broadcastAddress']
        );

        // all done
        return $retval;
    }

    /**
     * called when we have been given a data type that we do not support
     *
     * @param  mixed $linkLines
     * @return void
     * @throws E4xx_UnsupportedType
     */
    private static function nothingMatchesTheInputType($linkLines)
    {
        throw new E4xx_UnsupportedType(SimpleType::from($linkLines));
    }

    /**
     * extract data from the first line of the link definition
     *
     * @param  string $line
     *         the line to parse
     * @return array
     *         the extracted data
     */
    private static function parseFirstLine($line)
    {
        // this regex should parse every permutation of the link definition
        // that we know about
        $regex = "~(?<index>[0-9]+): (?<name>[^:@]+)(@(?<master>[^:]+)){0,1}: \\<(?<flags>[^>]+)\\>( (?<extra>.*)){0,1}~";
        $matches = [];
        if (!preg_match_all($regex, $line, $matches)) {
            throw new E4xx_CannotParseNetLinkLine($line);
        }

        // the 'master' tells us which interface that this interface is linked to
        $master = self::extractMasterFromMatches($matches);

        // a list of key/value pairs of interface settings
        $properties = self::extractPropertiesFromMatches($matches);

        // a list of flags that are set on this interface
        $flags = self::extractFlagsFromMatches($matches);

        // all done
        return [
            'index' => (int)$matches['index'][0],
            'name' => $matches['name'][0],
            'master' => $master,
            'flags' => $flags,
            'properties' => $properties,
            'linkType' => null,
            'physicalAddress' => null,
            'broadcastAddress' => null
        ];
    }

    /**
     * extract the master interface (if there is one) from the first line of
     * the link definition
     *
     * @param  array $matches
     *         the match results from running a regex against the first line
     *         of the link definition
     * @return string|null
     */
    private static function extractMasterFromMatches($matches)
    {
        // extract whatever value is there
        $master = ExtractFirstItem::from($matches['master'], null);

        // make sure we return NULL if there was no actual value
        if (empty($master)) {
            $master = null;
        }

        // all done
        return $master;
    }

    /**
     * convert the key/value pairs from the first line of the link definition
     * into a list of key/value pairs
     *
     * @param  array $matches
     *         the match results from running a regex against the first line
     *         of the link definition
     * @return array
     *         the extracted key/value pairs
     */
    private static function extractPropertiesFromMatches($matches)
    {
        // turn the properties into a list of key/value pairs
        return ConvertKeyValuePairsToArray::from($matches['extra'][0], ' ', ' ');
    }

    /**
     * convert the interface flags from the first line of the link definition
     * into a list of flags that are set
     *
     * @param  array $matches
     *         the match results from running a regex against the first line
     *         of the link definition
     * @return array
     *         the extracted flags - the flag name is the key, and the value
     *         is always TRUE
     */
    private static function extractFlagsFromMatches($matches)
    {
        // turn the flags into a list, where the name of the flag is the key,
        // and the value is always TRUE
        return array_fill_keys(explode(',', $matches['flags'][0]), true);
    }

    /**
     * extract the data from the second line of the link definition
     *
     * @param  string $line
     *         the second line of the link definition
     * @return array
     *         a list of the extracted link properties
     */
    private static function parseSecondLine($line)
    {
        // our return value
        $retval = [];

        $parts = explode(" ", trim($line));

        // the link type is always the first entry on the line
        $retval['linkType'] = $parts[0];

        // the second entry is always the MAC address
        if (isset($parts[1])) {
            $retval['physicalAddress'] = $parts[1];
        }

        // the third entry (if it exists) is always the hardware broadcast
        // address
        if (isset($parts[3]) && $parts[2] == 'brd') {
            $retval['broadcastAddress'] = $parts[3];
        }

        // all done
        return $retval;
    }

    /**
     * a map of how to handle supported data types
     *
     * @var array
     */
    private static $dispatchMap = [
        'String' => 'fromString',
        'Traversable' => 'fromTraversable',
    ];
}
