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
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
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
 * Test suite for the File_DeliciousLibrary class
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */

// Call File_DeliciousLibraryTest::main() if executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "File_DeliciousLibraryTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "File/DeliciousLibrary.php";

/**
 * Test class for File_DeliciousLibrary.
 *
 * @author Martin Jansen <martin@divbyzero.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibraryTest extends PHPUnit_Framework_TestCase
{

    protected $parser;

    /**
     * Runs the test methods of this class.
     */
    public static function main() {
        include_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("File_DeliciousLibraryTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp() {
        $this->parser = new File_DeliciousLibrary(dirname(__FILE__) . "/library.xml");
    }

    public function testFileNotFoundException() {
        $parser = new File_DeliciousLibrary("File/DeliciousLibrary/no-such-file.xml");
        try {
            $parser->parse();   
        } catch (Exception $e) {
            self::assertTrue($e instanceof File_DeliciousLibrary_Exception);
            self::assertEquals("Input file File/DeliciousLibrary/no-such-file.xml is not readable", $e->getMessage());
            return;
        }

        $this->fail("Expected exception not thrown");
    }

    public function testNonWellformedXML() {
        $parser = new File_DeliciousLibrary(dirname(__FILE__) . "/not-wellformed.xml");
        try {
            $parser->parse();   
        } catch (Exception $e) {
            self::assertTrue($e instanceof File_DeliciousLibrary_Exception);
            self::assertEquals("Unable to parse input file " . dirname(__FILE__) . "/not-wellformed.xml", $e->getMessage());
            return;
        }

        $this->fail("Expected exception not thrown");
    }
    
    public function testOnlyBooks() {
        $this->parser->parse();

        $set1  = $this->parser->getBooks();
        self::assertTrue(is_array($set1));
        self::assertEquals(3, count($set1));

        /* The following has to result in the same numbers as before. */
        
        $parser2 = new File_DeliciousLibrary(dirname(__FILE__) . "/library.xml");
        $parser2->include = File_DeliciousLibrary::INCLUDE_BOOKS;
        $parser2->parse();

        self::assertTrue(is_array($parser2->items));
        self::assertEquals(3, count($parser2->items));
    }

    public function testOnlyMovies() {
        $this->parser->parse();

        $set1  = $this->parser->getMovies();
        self::assertTrue(is_array($set1));
        self::assertEquals(1, count($set1));

        /* The following has to result in the same numbers as before. */
        
        $parser2 = new File_DeliciousLibrary(dirname(__FILE__) . "/library.xml");
        $parser2->include = File_DeliciousLibrary::INCLUDE_MOVIES;
        $parser2->parse();

        self::assertTrue(is_array($parser2->items));
        self::assertEquals(1, count($parser2->items));
    }

    public function testOnlyMusic() {
        $this->parser->parse();

        $set1  = $this->parser->getMusic();

        self::assertTrue(is_array($set1));
        self::assertEquals(1, count($set1));

        /* The following has to result in the same numbers as before. */
        
        $parser2 = new File_DeliciousLibrary(dirname(__FILE__) . "/library.xml");
        $parser2->include = File_DeliciousLibrary::INCLUDE_MUSIC;
        $parser2->parse();

        self::assertTrue(is_array($parser2->items));
        self::assertEquals(1, count($parser2->items));
    }

    public function testOnlyGames() {
        $this->parser->parse();

        $set1  = $this->parser->getGames();
        self::assertTrue(is_array($set1));
        self::assertEquals(1, count($set1));

        /* The following has to result in the same numbers as before. */
        
        $parser2 = new File_DeliciousLibrary(dirname(__FILE__) . "/library.xml");
        $parser2->include = File_DeliciousLibrary::INCLUDE_GAMES;
        $parser2->parse();

        self::assertTrue(is_array($parser2->items));
        self::assertEquals(1, count($parser2->items));
    }

    public function testShelves() {
        $this->parser->parse();


        $shelves = $this->parser->shelves;
        self::assertEquals(2, count($shelves));

        list($first, $second) = each($shelves);
        self::assertEquals("Favorites", $shelves['Favorites']->name);
        self::assertEquals("To read", $shelves['To read']->name);
    }

    public function testCoverLocations() {
        $path = dirname(__FILE__);
        foreach ($this->parser->items as $item) {
            self::assertEquals($path . "/Images/Small Covers/" . $item->uuid, $item->getCoverLocation());
            self::assertEquals($path . "/Images/Small Covers/" . $item->uuid, $item->getCoverLocation(File_DeliciousLibrary_Item::COVER_SMALL));
            self::assertEquals($path . "/Images/Medium Covers/" . $item->uuid, $item->getCoverLocation(File_DeliciousLibrary_Item::COVER_MEDIUM));
            self::assertEquals($path . "/Images/Plain Covers/" . $item->uuid, $item->getCoverLocation(File_DeliciousLibrary_Item::COVER_PLAIN));
            self::assertEquals($path . "/Images/Large Covers/" . $item->uuid, $item->getCoverLocation(File_DeliciousLibrary_Item::COVER_LARGE));
        }
    }
    
    public function testShelfContains() {
        $this->parser->parse();
        
        $item1 = $this->parser->items['702F75C0-7C6C-4537-8993-8ACA086F09C0'];
        $item2 = $this->parser->items['702F75C0-7C6C-4537-8993-8ACA086F09C4'];

        self::assertTrue($this->parser->shelves['Favorites']->contains($item1));
        self::assertFalse($this->parser->shelves['Favorites']->contains($item2));
    }
}
