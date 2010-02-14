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
 * Parser for the library database of the Delicious Library software.
 *
 * This package provides a convenient interface to extract information out of
 * the XML based library database being used by Delicious Library.
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 * @see http://www.delicious-monster.com/
 */

require "DeliciousLibrary/Item.php";
require "DeliciousLibrary/Shelf.php";

class File_DeliciousLibrary_Exception extends Exception {}

/**
 * Main application class for parsing the library database of the Delicious Library Software
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD 
 */
class File_DeliciousLibrary
{
    const INCLUDE_BOOKS = 1;
    const INCLUDE_MOVIES = 2;
    const INCLUDE_MUSIC = 4;
    const INCLUDE_GAMES = 8;

    private $library = "";

    /**
     * Determines which sort of items to include in the parsing result
     *
     * This variable holds a bitmask made up of the INCLUDE_* class constants.
     * By default it is set to INCLUDE_BOOKS | INCLUDE_MOVIES | INCLUDE_MUSIC | INCLUDE_GAMES,
     * i.e. all items will be included.  If you e.g. only wish to include books
     * and games use
     *
     *      $parser->include = File_DeliciousLibrary::INCLUDE_BOOKS | File_DeliciousLibrary::INCLUDE_GAMES
     *
     * before calling $parser->parse().
     *
     * @access public
     * @var int
     */
    public $include;

    /**
     * Holds a list of all items that are part of the library
     *
     * This variable is an associative array where the key is a unique,
     * alphanumeric identifier and the value is an instance of a subclass of
     * File_DeliciousLibrary_Item containing information about the item.
     *
     * @access public
     * @var array
     */
    public $items = array();

    /**
     * Holds a list of all custom shelves and their items
     *
     * This variable is an associative array where the key is the name of the,
     * shelf and the value is an instance of File_DeliciousLibrary_Shelf 
     * containing information about the custom shelf and the items that are
     * part of it.
     *
     * @access public
     * @var array
     */
    public $shelves = array();
    
    /**
     * Constructor
     *
     * @param string Location of the library file. This file is usually called
     *               Library Media Data.xml and resides in the directory
     *               ~/Library/Application Support/Delicious Library.
     */
    public function __construct($library) {
        $this->library = $library;
        $this->include = self::INCLUDE_BOOKS | self::INCLUDE_MOVIES | self::INCLUDE_MUSIC | self::INCLUDE_GAMES;
    }
    
    /**
     * Returns an instance of a subclass of File_DeliciousLibrary_Item for an item from the library
     *
     * You are not expected to call this method directly.
     *
     * @param string Name of the item ("book", "movie", "music", "game")
     * @return File_DeliciousLibrary_Item
     * @throws File_DeliciousLibrary_Exception
     */
    public static function getItemInstance($ident) {
        $ident = ucfirst(strtolower($ident));
        $classname = "File_DeliciousLibrary_" . $ident;
        
        if (class_exists($classname)) {
            return new $classname;
        }
        
        throw new File_DeliciousLibrary_Exception("no such item class: " . $classname);
    }
    
    /**
     * Parses the Delicous Library library database
     *
     * @throws File_DeliciousLibrary_Exception
     */
    public function parse() {
        if (!is_readable($this->library)) {
            throw new File_DeliciousLibrary_Exception("Input file " . $this->library . " is not readable");
        }
        
        $xml = @simplexml_load_file($this->library);
        if ($xml === false) {
            throw new File_DeliciousLibrary_Exception("Unable to parse input file " . $this->library);
        }
        
        foreach ($xml->items->children() as $item) {
            if (!$this->shouldInclude($item->getName())) {
                continue;
            }
            
            $container = self::getItemInstance($item->getName());
            $container->loadInformation($this->library, $item);

            $this->items[(string)$item['uuid']] = $container;
        }

        foreach ($xml->shelves->shelf as $child) {
            $shelf = new File_DeliciousLibrary_Shelf;
            $shelf->name = (string)$child['name'];
            
            foreach ($child->linkto as $item) {
                $uuid = (string)$item['uuid'];
                
                if (isset($this->items[$uuid])) {
                    $shelf->items[$uuid] = $this->items[$uuid];
                }
            }
            
            $this->shelves[$shelf->name] = $shelf;
        }
    }
    
    /**
     * Returns all books from the library
     *
     * @return array Returns an associative array where the key is a unique,
     *               alphanumeric identifier and the value is an instance of
     *               File_DeliciousLibrary_Book containing information about 
     *               each book.
     */
    public function getBooks() {
        return $this->filterItems("File_DeliciousLibrary_Book");
    }
    
    /**
     * Returns all movies from the library
     *
     * @return array Returns an associative array where the key is a unique,
     *               alphanumeric identifier and the value is an instance of
     *               File_DeliciousLibrary_Movie containing information about 
     *               each movie.
     */
    public function getMovies() {
        return $this->filterItems("File_DeliciousLibrary_Movie");
    }
    
    /**
     * Returns all music from the library
     *
     * @return array Returns an associative array where the key is a unique,
     *               alphanumeric identifier and the value is an instance of
     *               File_DeliciousLibrary_Music containing information about 
     *               each album.
     */
    public function getMusic() {
        return $this->filterItems("File_DeliciousLibrary_Music");
    }
    
    /**
     * Returns all games from the library
     *
     * @return array Returns an associative array where the key is a unique,
     *               alphanumeric identifier and the value is an instance of
     *               File_DeliciousLibrary_Game containing information about 
     *               each game.
     */
    public function getGames() {
        return $this->filterItems("File_DeliciousLibrary_Game");
    }
    
    private function filterItems($class) {
        $list = $this->items;
        
        foreach ($list as $key => $value) {
            if (!$value instanceof $class) {
                unset($list[$key]);
            }
        }

        return $list;
    }
    
    private function shouldInclude($what) {
        if ($what == "book" && !($this->include & self::INCLUDE_BOOKS)) {
            return false;
        }
        if ($what == "movie" && !($this->include & self::INCLUDE_MOVIES)) {
            return false;
        }
        if ($what == "music" && !($this->include & self::INCLUDE_MUSIC)) {
            return false;
        }
        if ($what == "game" && !($this->include & self::INCLUDE_GAMES)) {
            return false;
        }
        
        return true;
    }
}
