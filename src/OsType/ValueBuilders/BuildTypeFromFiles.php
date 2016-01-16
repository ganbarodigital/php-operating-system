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

use GanbaroDigital\OperatingSystems\OsType\Values\OsType;

class BuildTypeFromFiles
{
    /**
     * what kind of operating system are we running on?
     *
     * @param  array $pathsToFiles
     *         a list of paths to use for the supported builders
     *         use this to override the default paths where the builders
     *         search
     * @return OsType|null
     *         OsType if we know what kind of operating system we are running on
     *         null otherwise
     */
    public function __invoke($pathsToFiles = [])
    {
        return self::usingPaths($pathsToFiles);
    }

    /**
     * what kind of operating system are we running on?
     *
     * @return OsType|null
     *         OsType if we know what kind of operating system we are running on
     *         null otherwise
     */
    public static function usingDefaultPaths()
    {
        return self::usingPaths();
    }

    /**
     * what kind of operating system are we running on?
     *
     * @param  array $pathsToFiles
     *         a list of paths to use for the supported builders
     *         use this to override the default paths where the builders
     *         search
     * @return OsType|null
     *         OsType if we know what kind of operating system we are running on
     *         null otherwise
     */
    public static function usingPaths($pathsToFiles = [])
    {
        foreach (self::$builders as $builderClass) {
            $retval = self::buildUsingBuilder($builderClass, $pathsToFiles);
            if ($retval) {
                return $retval;
            }
        }

        return null;
    }

    /**
     * use a specific builder to try and work out which operating system
     * we are on
     *
     * @param  string $builderClass
     *         the class to use as a builder
     * @param  array $pathsToFiles
     *         a list of paths to override
     * @return OsType|null
     *         OsType if we know what kind of operating system we are running on
     *         null otherwise
     */
    private static function buildUsingBuilder($builderClass, $pathsToFiles)
    {
        /** @var OsType **/
        $builder = new $builderClass;
        if (isset($pathsToFiles[$builderClass])) {
            return $builder($pathsToFiles[$builderClass]);
        }
        return $builder();
    }

    /**
     * a list of builders to try in a specific order
     *
     * @var array
     */
    private static $builders = [
        BuildTypeFromLsbRelease::class,
        BuildTypeFromEtcRedhatRelease::class,
        BuildTypeFromEtcIssue::class,
        BuildTypeFromSwVers::class,
    ];
}
