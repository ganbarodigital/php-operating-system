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

use GanbaroDigital\Filesystem\Requirements\RequireReadableFile;
use GanbaroDigital\OperatingSystem\OsType\Checks\HasEtcRedhatRelease;
use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;

class BuildTypeFromEtcRedhatRelease
{
    /**
     * use /etc/redhat-release (if it exists) to work out what flavour of
     * RedHat Linux we are looking at
     *
     * @param  string $path
     *         path to the file to parse
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    public function __invoke($path = '/etc/redhat-release')
    {
        return self::from($path);
    }

    /**
     * use /etc/redhat-release (if it exists) to work out what flavour of
     * RedHat Linux we are looking at
     *
     * @param  string $path
     *         path to the file to parse
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    public function inDefaultLocation()
    {
        return self::from('/etc/redhat-release');
    }

    /**
     * use /etc/redhat-release (if it exists) to work out what flavour of
     * RedHat Linux we are looking at
     *
     * @param  string $path
     *         path to the file to parse
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    public static function from($path)
    {
        // make sure we have the file!
        if (!HasEtcRedhatRelease::check($path)) {
            return null;
        }

        // make sure the file is readable
        RequireReadableFile::check($path);

        // what do we have?
        $fileContents = file_get_contents($path);

        // do we have a match?
        foreach (self::$osTypes as $regex => $type) {
            $matches=[];
            if (!preg_match($regex, $fileContents, $matches)) {
                continue;
            }

            // if we get here, we have a match
            $osType = new $type($matches['version']);
            return $osType;
        }

        return null;
    }

    /**
     * a map of regexes to OsType classes
     *
     * @var array
     */
    private static $osTypes = [
        "|^CentOS release (?<version>\d+\.\d+)|" => CentOS::class,
        "|^CentOS Linux release (?<version>\d+\.\d+)|" => CentOS::class,
    ];
}