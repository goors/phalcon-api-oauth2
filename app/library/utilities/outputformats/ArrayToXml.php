<?php

namespace Utilities\Outputformats;

class ArrayToXML
{
    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     * Based on: http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXml($data, $rootNodeName = 'data', &$xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
        if ( is_null( $xml ) ) {
            $xml = simplexml_load_string(stripslashes("<?xml version='1.0' encoding='utf-8'?><root xmlns:example='http://example.namespace.com' version='1.0'></root>"));
        }

        // loop through the data passed in.
        foreach( $data as $key => $value ) {

            // no numeric keys in our xml please!
            $numeric = false;
            if ( is_numeric( $key ) ) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            //check to see if there should be an attribute added (expecting to see _id_)
            $attrs = false;

            //if there are attributes in the array (denoted by attr_**) then add as XML attributes
            if ( is_array( $value ) ) {
                foreach($value as $i => $v ) {
                    $attr_start = false;
                    $attr_start = stripos($i, 'attr_');
                    if ($attr_start === 0) {
                        $attrs[substr($i, 5)] = $v; unset($value[$i]);
                    }
                }
            }


            // if there is another array found recursively call this function
            if ( is_array( $value ) ) {

                if ( \Utilities\Outputformats\ArrayToXML::is_assoc( $value ) || $numeric ) {

                    // older SimpleXMLElement Libraries do not have the addChild Method
                    if (method_exists('SimpleXMLElement','addChild'))
                    {
                        $node = $xml->addChild( $key, null, 'http://www.lcc.arts.ac.uk/' );
                        if ($attrs) {
                            foreach($attrs as $key => $attribute) {
                                $node->addAttribute($key, $attribute);
                            }
                        }
                    }

                }else{
                    $node =$xml;
                }

                // recrusive call.
                if ( $numeric ) $key = 'anon';
                \Utilities\Outputformats\ArrayToXML::toXml( $value, $key, $node );
            } else {

                // older SimplXMLElement Libraries do not have the addChild Method
                if (method_exists('SimpleXMLElement','addChild'))
                {
                    $childnode = $xml->addChild( $key, $value, 'http://www.lcc.arts.ac.uk/' );
                    if ($attrs) {
                        foreach($attrs as $key => $attribute) {
                            $childnode->addAttribute($key, $attribute);
                        }
                    }
                }
            }
        }

        // pass back as unformatted XML
        //return $xml->asXML('data.xml');

        // if you want the XML to be formatted, use the below instead to return the XML
        return \Utilities\Outputformats\ArrayToXML::fixCDATA($xml->asXML());
    }

    public static function fixCDATA($string) {
        //fix CDATA tags
        $find[]     = '&lt;![CDATA[';
        $replace[] = '<![CDATA[';
        $find[]     = ']]&gt;';
        $replace[] = ']]>';

        $string = str_ireplace($find, $replace, $string);
        return $string;
    }

    /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
     *
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function toArray( $xml ) {
        if ( is_string( $xml ) ) $xml = new \SimpleXMLElement( $xml );
        $children = $xml->children();
        if ( !$children ) return (string) $xml;
        $arr = array();
        foreach ( $children as $key => $node ) {
            $node = \Utilities\Outputformats\ArrayToXML::toArray( $node );

            // support for 'anon' non-associative arrays
            if ( $key == 'anon' ) $key = count( $arr );

            // if the node is already set, put it into an array
            if ( isset( $arr[$key] ) ) {
                if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }
        return $arr;
    }

    // determine if a variable is an associative array
    public static function is_assoc( $array ) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
} 