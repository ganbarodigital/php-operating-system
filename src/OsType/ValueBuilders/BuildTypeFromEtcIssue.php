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
use GanbaroDigital\OperatingSystem\OsType\Checks\HasEtcIssue;
use GanbaroDigital\OperatingSystem\OsType\Values\CentOS;
use GanbaroDigital\OperatingSystem\OsType\Values\Debian;
use GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint;
use GanbaroDigital\OperatingSystem\OsType\Values\OsType;
use GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu;

class BuildTypeFromEtcIssue
{
    /**
     * use /etc/issue (if it exists) to work out what operating system we
     * are looking at
     *
     * @param  string $path
     *         path to the file to parse
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    public function __invoke($path = '/etc/issue')
    {
        return self::from($path);
    }

    /**
     * use /etc/issue (if it exists) to work out what operating system we
     * are looking at
     *
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    public function inDefaultLocation()
    {
        return self::from('/etc/issue');
    }

    /**
     * use /etc/issue (if it exists) to work out what operating system we
     * are looking at
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
        if (!HasEtcIssue::check($path)) {
            return null;
        }

        // make sure the file is readable
        RequireReadableFile::check($path);

        // what do we have?
        $fileContents = file_get_contents($path);

        // do we have a match?
        return self::matchContentsToType($fileContents);
    }

    /**
     * do we have a regex that matches the contents of our file?
     *
     * @param  string $fileContents
     *         the contents of the file to check
     * @return null|OsType
     *         OsType if we can determine the operating system
     *         null if we cannot
     */
    private static function matchContentsToType($fileContents)
    {
        foreach (self::$osTypes as $regex => $type) {
            if ($result = self::matchTypeToRegex($type, $regex, $fileContents)) {
                return $result;
            }
        }

        return null;
    }

    /**
     * does the given regex match our file
     *
     * @param  string $type
     *         the OsType class to return if the regex matches
     * @param  string $regex
     *         the regex to try
     * @param  string $fileContents
     *         the text that we apply the regex to
     * @return null|OsType
     *         OsType if the regex matches
     *         null otherwise
     */
    private static function matchTypeToRegex($type, $regex, $fileContents)
    {
        $matches=[];
        if (!preg_match($regex, $fileContents, $matches)) {
            return null;
        }

        // if we get here, we have a match
        /** @var OsType a type of operating system */
        $osType = new $type($matches['version']);
        return $osType;

    }

    /**
     * a map of regexes to OsType classes
     *
     * @var array
     */
    private static $osTypes = [
        "|^CentOS release (?<version>\d+\.\d+)|" => CentOS::class,
        "|^Ubuntu (?<version>\d+\.\d+)|" => Ubuntu::class,
    ];
}