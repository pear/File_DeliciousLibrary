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

/**
 * Class representing custom shelves containing a mix of books, movies, video games, and music
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_Shelf
{    
    /**
     * The name of the shelf
     *
     * @access public
     * @var string
     */
    public $name = "";

    /**
     * The items on the shelf
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
     * Checks if an item is part of the shelf
     *
     * This method takes an item as its input and determines if the shelf
     * contains this item.
     *
     * It is basically is a convenience helper so that one does not need to
     * work with the unique identifiers provided by Delicious Library but
     * directly with instances of the item classes.
     *
     * @param File_DeliciousLibrary_Item The item
     * @return boolean TRUE if the item is part of the shelf, FALSE otherwise.
     */
    public function contains(File_DeliciousLibrary_Item $item) {
        return !empty($this->items[$item->uuid]);
    }
}
