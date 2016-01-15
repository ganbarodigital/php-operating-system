<?php

/**
 * Copyright (c) 2015-present Ganbaro Digital Ltd
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
 * @package   OperatingSystem/OsType/ValueBuilders
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-text-tools
 */

namespace GanbaroDigital\OperatingSystem\OsType\ValueBuilders;

use GanbaroDigital\Filesystem\Checks\IsExecutableFile;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;
use GanbaroDigital\OperatingSystem\OsType\Values\OSX;
use GanbaroDigital\ProcessRunner\ProcessRunners\PopenProcessRunner;
use GanbaroDigital\TextTools\Editors\TrimWhitespace;
use GanbaroDigital\TextTools\Filters\FilterForMatchingString;
use GanbaroDigital\TextTools\Filters\FilterColumns;

class BuildTypeFromSwVers implements BuildTypeFromFile
{
    /**
     * use the output of /usr/bin/sw_vers (if present) to determine which
     * operating system we are using
     *
     * @param  string $pathToBinary
     *         path to the binary to run
     * @return null|OsType
     *         OsType if we know which operating system we are using
     *         null otherwise
     */
    public function __invoke($pathToBinary = "/usr/bin/sw_vers")
    {
        return self::usingPath($pathToBinary);
    }

    /**
     * use the output of /usr/bin/sw_vers (if present) to determine which
     * operating system we are using
     *
     * @return null|OsType
     *         OsType if we know which operating system we are using
     *         null otherwise
     */
    public static function usingDefaultPath()
    {
        return self::usingPath("/usr/bin/sw_vers");
    }

    /**
     * use the output of /usr/bin/sw_vers (if present) to determine which
     * operating system we are using
     *
     * @param  string $pathToBinary
     *         path to the binary to run
     * @return null|OsType
     *         OsType if we know which operating system we are using
     *         null otherwise
     */
    public static function usingPath($pathToBinary)
    {
        list($productName, $productVersion) = self::getOsDetails($pathToBinary);
        if ($productName === null || $productVersion === null) {
            return null;
        }

        // do we have a match?
        if (!isset(self::$osTypes[$productName])) {
            return null;
        }

        $osType = new self::$osTypes[$productName]($productVersion);
        return $osType;
    }

    /**
     * call /usr/bin/sw_vers to get details about this operating system
     *
     * @param  string $pathToBinary
     *         path to the binary to run
     * @return array
     *         [0] is the operating system name
     *         [1] is the operating system version
     */
    private static function getOsDetails($pathToBinary)
    {
        $output = self::getOutputFromBinary($pathToBinary);
        if ($output === null) {
            return [null, null];
        }

        return self::extractOsDetails($output);
    }

    /**
     * call /usr/bin/sw_vers and return the output
     *
     * @param  string $pathToBinary
     *         path to the binary to call
     * @return string|null
     *         output from the binary
     */
    private static function getOutputFromBinary($pathToBinary)
    {
        // make sure we have an executable binary
        if (!IsExecutableFile::check($pathToBinary)) {
            return null;
        }

        // get the info
        $result = PopenProcessRunner::run([$pathToBinary]);
        if ($result->getReturnCode() !== 0) {
            return null;
        }

        // at this point, return the output
        return $result->getOutput();
    }

    /**
     * parse the output of /usr/bin/sw_vers to get details about
     * this operating system
     *
     * @param  string $output
     *         output from /usr/bin/sw_vers
     * @return array
     *         [0] is the operating system name
     *         [1] is the operating system version
     */
    private static function extractOsDetails($output)
    {
        // what do we have?
        $lines = explode(PHP_EOL, $output);
        $productName = self::extractField($lines, 'ProductName:');
        $productVersion = self::extractField($lines, 'ProductVersion:');

        return [$productName, $productVersion];
    }

    /**
     * extract a named field from the output of /usr/bin/sw_vers
     *
     * @param  array $lines
     *         the output of /usr/bin/sw_vers
     * @param  string $fieldName
     *         the field that we are looking for
     * @return string|null
     *         the value of the field (if found)
     */
    private static function extractField($lines, $fieldName)
    {
        $matches = FilterForMatchingString::against($lines, $fieldName);
        if (empty($matches)) {
            return null;
        }
        return TrimWhitespace::from(FilterColumns::from($matches[0], '1', ':'));
    }

    /**
     * map of operating system names to OsTypes
     * @var array
     */
    private static $osTypes = [
        'Mac OS X' => OSX::class,
    ];
}
