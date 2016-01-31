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
 * @package   OperatingSystem/OsType/Checks
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-operating-system
 */

namespace GanbaroDigital\OperatingSystem\OsType\Checks;

use GanbaroDigital\Filesystem\Checks\IsFile;

class HasEtcRedhatRelease
{
    /**
     * does this operating system have the file '/etc/redhat-release'?
     *
     * /etc/redhat-release is a file that exists on RedHat Linux systems,
     * including derivatives such as Fedora and CentOS
     *
     * @param  string $path
     *         path to the file to check for
     *         override this if you're checking inside a chroot folder
     *         of some kind
     *
     * @return boolean
     *         TRUE if $path exists
     *         FALSE otherwise
     */
    public function __invoke($path = '/etc/redhat-release')
    {
        return self::check($path);
    }

    /**
     * does this operating system have the file '/etc/redhat-release'?
     *
     * /etc/redhat-release is a file that exists on RedHat Linux systems,
     * including derivatives such as Fedora and CentOS
     *
     * @param  string $path
     *         path to the file to check for
     *         override this if you're checking inside a chroot folder
     *         of some kind
     *
     * @return boolean
     *         TRUE if $path exists
     *         FALSE otherwise
     */
    public static function check($path = '/etc/redhat-release')
    {
        return IsFile::check($path);
    }
}