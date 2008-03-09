<?php
/**
 * Copyright (c) 2007 Martin Jansen
 * 
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS"" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * Test suite for File_DeliciousLibrary
 *
 * @author Martin Jansen <martin@divbyzero.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "File_DeliciousLibrary_AllTests::main");
}

require_once "PHPUnit/Framework/TestSuite.php";
require_once "PHPUnit/TextUI/TestRunner.php";

require_once "File/DeliciousLibrary/DeliciousLibraryTest.php";

/**
 * Class for running all test suites for File_DeliciousLibrary.
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_AllTests
{
    /**
     * Runs the test suite.
     *
     */
    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Adds the File_DeliciousLibraryTest suite.
     *
     * @return $suite
     */
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite("File_DeliciousLibrary tests");

        // Add more when they come  up
        $suite->addTestSuite("File_DeliciousLibraryTest");

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == "File_DeliciousLibrary_AllTests::main") {
    File_DeliciousLibrary_AllTests::main();
}
