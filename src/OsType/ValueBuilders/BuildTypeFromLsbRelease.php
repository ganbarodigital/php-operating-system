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
 * @package   OperatingSystem/OsType/ValueBuilders
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-text-tools
 */

namespace GanbaroDigital\OperatingSystem\OsType\ValueBuilders;

use GanbaroDigital\Filesystem\Checks\IsExecutableFile;
use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\Debian;
use GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;
use GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu;
use GanbaroDigital\ProcessRunner\ProcessRunners\PopenProcessRunner;
use GanbaroDigital\TextTools\Editors\TrimWhitespace;
use GanbaroDigital\TextTools\Filters\FilterForMatchingString;
use GanbaroDigital\TextTools\Filters\FilterColumns;

class BuildTypeFromLsbRelease implements BuildTypeFromFile
{
    /**
     * use the output of /usr/bin/lsb_release (if present) to determine which
     * Linux distro we are using
     *
     * @param  string $pathToBinary
     *         path to the binary to run
     * @return null|OsType
     *         OsType if we know which Linux distro we are using
     *         null otherwise
     */
    public function __invoke($pathToBinary = "/usr/bin/lsb_release")
    {
        return self::usingPath($pathToBinary);
    }

    /**
     * use the output of /usr/bin/lsb_release (if present) to determine which
     * Linux distro we are using
     *
     * @return null|OsType
     *         OsType if we know which Linux distro we are using
     *         null otherwise
     */
    public static function usingDefaultPath()
    {
        return self::usingPath("/usr/bin/lsb_release");
    }

    /**
     * use the output of /usr/bin/lsb_release (if present) to determine which
     * Linux distro we are using
     *
     * @param  string $pathToBinary
     *         path to the binary to run
     * @return null|OsType
     *         OsType if we know which Linux distro we are using
     *         null otherwise
     */
    public static function usingPath($pathToBinary)
    {
        list($distroName, $distroVersion) = self::getDistroDetails($pathToBinary);
        if ($distroName === null || $distroVersion === null) {
            return null;
        }

        // do we have a match?
        if (!isset(self::$osTypes[$distroName])) {
            return null;
        }

        // yes, we have a match
        /** @var OsType */
        $osType = new self::$osTypes[$distroName]($distroVersion);
        return $osType;
    }

    /**
     * get the Linux distro name & version from /usr/bin/lsb_release
     *
     * @param  string $pathToBinary
     *         the binary to call to get the LSB details
     * @return array
     *         [0] is the Linux distro name
     *         [1] is the Linux distro version
     */
    private static function getDistroDetails($pathToBinary)
    {
        $output = self::getOutputFromBinary($pathToBinary);
        if ($output === null) {
            return [null, null];
        }

        return self::extractDistroDetails($output);
    }

    /**
     * call /usr/bin/lsb_release and return the output
     *
     * @param  string $pathToBinary
     *         path to the binary to call
     * @return string
     *         output from the binary
     */
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

    /**
     * extract the info we need from the output of /usr/bin/lsb_release
     *
     * @param  string $output
     *         the output from running the command
     * @return array
     *         [0] is the Linux distro name
     *         [1] is the Linux distro version
     */
    private static function extractDistroDetails($output)
    {
        // what do we have?
        $lines = explode(PHP_EOL, $output);
        $distroName = self::extractField($lines, 'Distributor ID:');
        $distroVersion = self::extractField($lines, 'Release:');

        if ($distroVersion !== null) {
            $distroVersion = FilterColumns::from($distroVersion, '0-1', '.');
        }

        return [$distroName, $distroVersion];
    }

    /**
     * extract a named field from the output of /usr/bin/lsb_release
     *
     * @param  array $lines
     *         the output of /usr/bin/lsb_release
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
     * a map of distro names onto OsType classes
     * @var array
     */
    private static $osTypes = [
        'CentOS' => CentOS::class,
        'Debian' => Debian::class,
        'Ubuntu' => Ubuntu::class,
    ];
}
