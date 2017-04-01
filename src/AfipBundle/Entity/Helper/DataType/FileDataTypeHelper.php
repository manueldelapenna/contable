<?php

namespace AfipBundle\Entity\Helper\DataType;

/**
 * Helper for File types.
 *
 
 * @author Eduardo Casey
 */
abstract class FileDataTypeHelper {

    /**
     * @var string
     */
    private static $defaultMimeType = "";

    /**
     * @var array
     */
    private static $mimeTypes = array();

    /**
     * Returns the MIME type of given file name.
     *
     * @param string $filename
     * @return string
     * @throws Afip_Exception_Lib_Exception Throws an exception whether the given filename is NULL or empty.
     */
    public static function getMimeType($filename) {
        $filename = trim($filename);
        if (isset($filename) && ($filename != "")) {
            self::loadMimeTypeCollection();

            $temp = explode(".", $filename);
            $counter = count($temp);

            if ($counter > 1) {
                $extension = $temp[$counter - 1];
                if (array_key_exists($extension, self::$mimeTypes))
                    return self::$mimeTypes[$extension];
            }

            return self::$defaultMimeType;
        }
        else {
            ExceptionFactory::throwFor(__CLASS__ . ": No given filename.");
        }
    }

    private static function loadMimeTypeCollection() {
        if (empty(self::$mimeTypes)) {
            $dom = new \DOMDocument("1.0", "UTF-8");

            if ($dom->load(__DIR__. "/../../etc/dataTypes/qFileHelper.config.xml", LIBXML_NOBLANKS) && $dom->validate()) {
                self::$mimeTypes = array();

                $mimeTypeCollection = $dom->getElementsByTagName("mimeType");
                for ($x = 0; $x < $mimeTypeCollection->length; $x++) {
                    foreach (explode("|", $mimeTypeCollection->item($x)->getAttribute("extensions")) as $extension)
                        self::$mimeTypes[$extension] = $mimeTypeCollection->item($x)->getAttribute("type");
                }

                self::$defaultMimeType = $dom->getElementById($dom->getElementsByTagName("defaultMimeType")->item(0)->getAttribute("id"))->getAttribute("type");
            } else
                ExceptionFactory::throwFor(__CLASS__ . ": Cannot load the MIME database.");
        }
    }

}

?>