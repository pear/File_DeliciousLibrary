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
 * Abstract class for all items from the library
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
abstract class File_DeliciousLibrary_Item
{
    const COVER_LARGE = 1;
    const COVER_MEDIUM = 2;
    const COVER_PLAIN = 3;
    const COVER_SMALL = 4;
    
    public $recommendations = array();
    private $library = "";
    
    /**
     * Extracts information from an XML element and loads it into the item
     *
     * @param string Location of the XML library file
     * @param SimpleXMLElement XML element defining the item
     */
    public function loadInformation($library, SimpleXMLElement $information) {
        $this->library = $library;
        
        foreach ($information->attributes() as $key => $value) {
            $this->$key = (string)$value;
        }

        $this->description = (isset($information->description) ? (string)$information->description : "");
        $this->notes = (isset($information->notes) ? (string)$information->notes : "");

        foreach ($information->xpath("recommendations/*") as $recommendation) {
            $container = File_DeliciousLibrary::getItemInstance($recommendation->getName());
            $container->loadInformation($library, $recommendation);
            $this->recommendations[] = $container;
        }
    }
    
    /**
     * Returns the location of the cover image for the item
     *
     * Depending on the value of the parameter this method constructs the
     * location of the file that contains the cover image associated with the
     * item.  Note that the method does not check if the image exists at the
     * returned location.
     *
     * @param int Size of the cover image. Can be self::COVER_LARGE; self::COVER_MEDIUM, self::COVER_PLAIN, and self::COVER_SMALL. Defaults to self::COVER_SMALL.
     * @return string Location of the cover image
     */
    public function getCoverLocation($size = self::COVER_SMALL) {
        switch ($size) {
            case self::COVER_LARGE :
                $path = "Images/Large Covers";
                break;
                
            case self::COVER_MEDIUM :
                $path = "Images/Medium Covers";
                break;
                
            case self::COVER_PLAIN :
                $path = "Images/Plain Covers";
                break;

            default :
            case self::COVER_SMALL :
                $path =  "Images/Small Covers";
                break;
        }

        return dirname($this->library) . "/" . $path . "/" . $this->uuid;
    }
}

/**
 * Class representing books from the library
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_Book extends File_DeliciousLibrary_Item
{
}

/**
 * Class representing movies from the library
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_Movie extends File_DeliciousLibrary_Item
{
}

/**
 * Class representing music from the library
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_Music extends File_DeliciousLibrary_Item
{
}

/**
 * Class representing games from the library
 *
 * @author Martin Jansen <mj@php.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD
 */
class File_DeliciousLibrary_Game extends File_DeliciousLibrary_Item
{
}
