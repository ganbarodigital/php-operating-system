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

use GanbaroDigital\OperatingSystem\IpRoute\Classifiers\ClassifyIpAddrLine;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_CannotParseIpAddrOutput;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_OperatingSystemException;
use GanbaroDigital\OperatingSystem\Exceptions\E4xx_UnsupportedType;
use GanbaroDigital\OperatingSystem\Exceptions\E5xx_CannotParseIpAddrOutput;
use GanbaroDigital\OperatingSystem\Exceptions\E5xx_OperatingSystemException;
use GanbaroDigital\Reflection\Maps\MapTypeToMethod;
use GanbaroDigital\Reflection\ValueBuilders\SimpleType;

/**
 * parser for the output of the 'ip addr show' command
 */
class ParseIpAddrOutput
{
    /**
     * parse the output of the 'ip addr show' command
     *
     * @param  mixed $output
     *         the output to parse
     * @return array<NetInterface>
     *         a list of the network interfaces extracted from the given
     *         command output
     */
    public function __invoke($output)
    {
        return self::from($output);
    }

    /**
     * parse the output of the 'ip addr show' command
     *
     * @param  mixed $output
     *         the output to parse
     * @return array<NetInterface>
     *         a list of the network interfaces extracted from the given
     *         command output
     */
    public static function from($output)
    {
        $method = MapTypeToMethod::using($output, self::$dispatchMap);
        return self::$method($output);
    }

    /**
     * parse the output of the 'ip addr show' command
     *
     * @param  Traversable|array $output
     *         the output to parse
     * @return array<NetInterface>
     *         a list of the network interfaces extracted from the given
     *         command output
     */
     private static function fromTraversable($output)
     {
         // our return value
         $retval = [];

         try {
             // group the output into smaller chunks for parsing
             $groups = self::groupOutputIntoInterfaces($output);

             foreach($groups as $interfaceLines) {
                 $interface = ParseNetInterface::from($interfaceLines);
                 $retval[] = $interface;
             }
         }
         catch (E4xx_OperatingSystemException $e) {
             throw new E4xx_CannotParseIpAddrOutput($output, $e);
         }
         catch (E5xx_OperatingSystemException $e) {
             throw new E5xx_CannotParseIpAddrOutput($output, $e);
         }

         // all done
         return $retval;
     }

     /**
      * convert the output of the 'ip addr show' command into smaller groups
      * that are easier to parse
      *
      * @param  array|Traversable $lines
      *         the output to group
      * @return array
      *         the grouped output
      */
     private static function groupOutputIntoInterfaces($lines)
     {
         $interfaces=[];

         // what do we have?
         foreach ($lines as $line) {
             // skip empty lines
             if (trim($line) === '') {
                 continue;
             }

             // what do we have?
             $lineType = ClassifyIpAddrLine::from($line);
             switch ($lineType) {
                 case ClassifyIpAddrLine::LINK_START:
                     $interfaces[] = [ $line ];
                     break;
                 default:
                     $interfaces[count($interfaces) - 1][] = $line;
             }
         }

         // all done
         return $interfaces;
     }

    /**
     * parse the output of the 'ip addr show' command
     *
     * @param  string $output
     *         the output to parse
     * @return array<NetInterface>
     *         a list of the network interfaces extracted from the given
     *         command output
     */
    private static function fromString($output)
    {
        $lines = explode("\n", (string)$output);
        return self::fromTraversable($lines);
    }

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
     * a map of which method to use for which type of data we're asked to
     * process
     *
     * @var array
     */
    private static $dispatchMap = [
        'Traversable' => 'fromTraversable',
        'String' => 'fromString',
    ];
}
