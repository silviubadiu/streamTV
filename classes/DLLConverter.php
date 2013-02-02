<?php

/**
 * streamTV DLLConverter
 *
 * It fetches the DLL file from AGlotze and decodes it back to a readable Array. Then formats the URL from each
 * stream and returns it back from the Object. You may use it with e.g. JSON to a website or etc..
 *
 * Written by: djcrackhome (Sebastian Graebner) <sgraebner@my.canyons.edu>
 * License: I dedicate any and all copyright interest in this software to the public domain. I make this dedication for
 * the benefit of the public at large and to the detriment of my heirs and successors. I intend this dedication to be
 * an overt act of relinquishment in perpetuity of all present and future rights to this software under copyright law.
 */

class DLLConverter {

    private $dllContent;
    private $streamTVList = array();

    public function __construct($dllURL) {
        $headers = get_headers($dllURL);
        if (substr($headers[0], 9, 3) == 200) {
            if (ini_get('allow_url_fopen')) {
                $this->dllContent = file_get_contents($dllURL);
            }
            else {
                $curlSession = curl_init();
                curl_setopt($curlSession, CURLOPT_URL, $dllURL);
                curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
                $this->dllContent = curl_exec($curlSession);
                curl_close($curlSession);
            }
        }
    }

    public function getRawDLLContent() {
        return $this->dllContent;
    }
    public function getStreamTVList() {
        $streamList = array_filter($this->streamTVList);
        if (!empty($streamList)) {
            return $this->streamTVList;
        }
        else {
            throw new Exception('Please call decodeDLL() first!');
        }
    }

    public function decodeDLL() {
        $encodedChar = array(' < w > ', ' < x > ', ' < u > ', ' < v > ', ' < _ > ', ' < ~ > ', ' < q > ', ' < o > ', ' < $ > ', ' < '."\xA7".' > ', ' < % > ', ' < & > ', ' < * > ', ' < # > ', ' < + > ', ' < - > ', ' < z > ', ' < , > ', ' < '."\x80".' > ', ' < @ > ', ' < ` > ', ' < ? > ', ' < | > ', " < ' > ", ' < '."\xE4".' > ', ' < '."\xFC".' > ', ' < '."\xDF".' > ', ' < '."\xF6".' > ', ' < ; > ');
        $decodedChar = array('://', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '.', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'r', 's', 't' );
        $decodedContent = str_replace($encodedChar, $decodedChar, $this->dllContent);
        for($i = 1; $i<=140; $i++) {
            $decodedContent = str_replace(':'.$i."a\r\nrem ", ':'.($i+1), $decodedContent);
            $pushContent = explode(':'.$i."\r\nrem " ,trim($decodedContent));
            $pushContent = explode('goto', $pushContent[1]);
            $this->convertEntry($pushContent[0]);
        }
    }

    private function convertEntry($channel) {
        $channelArray = explode("\n", $channel);
        if ($channelArray[0]) {
            preg_match_all('/\b(?:(?:https?|rtmpe?):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $channelArray[1], $result, PREG_PATTERN_ORDER);
            if (!is_null($result[0][0]))
                array_push($this->streamTVList, array(trim($channelArray[0]) => $result[0][0]));
        }
    }

}