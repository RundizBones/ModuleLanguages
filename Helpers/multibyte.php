<?php
/** 
 * PHP additional multi-byte functions.
 * 
 * @license http://opensource.org/licenses/MIT MIT
 */


if (!function_exists('mb_substr_replace')) {
    /**
     * Multibyte version of `substr_replace()`.
     * 
     * @link https://shkspr.mobi/blog/2012/09/a-utf-8-aware-substr_replace-for-use-in-app-net/ Original source code.
     * @see https://www.php.net/manual/en/function.substr-replace.php for more info.
     * @param string $original The original string.
     * @param string $replacement The replacement string.
     * @param int $position The offset to begins.
     * @param mixed $length The length of the portion of string.
     * @return string Return result string.
     */
    function mb_substr_replace(string $original, string $replacement, int $position, $length = null): string
    {
       $startString = mb_substr($original, 0, $position, 'UTF-8');
       $endString = mb_substr($original, $position + $length, mb_strlen($original), 'UTF-8');

       $out = $startString . $replacement . $endString;

       return $out;
    }// mb_substr_replace
}