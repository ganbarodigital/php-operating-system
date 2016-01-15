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
use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\Debian;
use GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint;
use GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu;
use GanbaroDigital\ProcessRunner\ProcessRunners\PopenProcessRunner;
use GanbaroDigital\TextTools\Editors\TrimWhitespace;
use GanbaroDigital\TextTools\Filters\FilterForMatchingString;
use GanbaroDigital\TextTools\Filters\FilterColumns;

class BuildTypeFromLsbRelease
{
    public function __invoke($path = "/usr/bin/lsb_release")
    {
        return self::usingBinary($path);
    }

    public static function usingDefaultPath()
    {
        return self::usingBinary("/usr/bin/lsb_release");
    }

    public static function usingBinary($pathToBinary)
    {
        $output = self::getOutputFromBinary($pathToBinary);
        if ($output === null) {
            return null;
        }

        list($distroName, $distroVersion) = self::extractDistroDetails($output);
        if ($distroName === null || $distroVersion === null) {
            return null;
        }

        // do we have a match?
        if (!isset(self::$osTypes[$distroName])) {
            return null;
        }

        $osType = new self::$osTypes[$distroName]($distroVersion);
        return $osType;
    }

    private static function getOutputFromBinary($pathToBinary)
    {
        // make sure we have an executable binary
        if (!IsExecutableFile::check($pathToBinary)) {
            return null;
        }

        // get the info
        $result = PopenProcessRunner::run([$pathToBinary, '-a']);
        if ($result->getReturnCode() !== 0) {
            return null;
        }

        // at this point, return the output
        return $result->getOutput();
    }

    private static function extractDistroDetails($output)
    {
        // what do we have?
        $lines = explode(PHP_EOL, $output);
        $distroName = self::extractField($lines, 'Distributor ID:');
        $distroVersion = self::extractField($lines, 'Release:');

        if ($distroName === null || $distroVersion === null) {
            return null;
        }

        $distroVersion = FilterColumns::from($distroVersion, '0-1', '.');

        return [$distroName, $distroVersion];
    }

    private static function extractField($lines, $fieldName)
    {
        $matches = FilterForMatchingString::against($lines, $fieldName);
        if (empty($matches)) {
            return null;
        }
        return TrimWhitespace::from(FilterColumns::from($matches[0], '1', ':'));
    }

    private static $osTypes = [
        'CentOS' => CentOS::class,
        'Debian' => Debian::class,
        'Ubuntu' => Ubuntu::class,
    ];
}