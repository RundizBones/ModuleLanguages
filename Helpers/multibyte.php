<?php
/** 
 * PHP additional multi-byte functions.
 * 
 * @license http://opensource.org/licenses/MIT MIT
 */


/**
 * Replace current URL with new prefix URL which contain new language ID.
 * 
 * This will not use `mb_substr_replace()` function name because in case PHP release this function it may be broken.
 * 
 * @link https://www.php.net/manual/en/function.substr-replace.php#90146 Original source code.
 * @see https://www.php.net/manual/en/function.substr-replace.php for more info.
 * @since 1.0.3
 * @param string $currentURL Current URL (included app base URL but without any language ID).
 * @param string $newPrefixURL New prefix URL with app base URL and new language ID.
 * @param string $appBaseURL The app base URL.
 * @return string Return replaced URL with new prefix URL which contains new language ID.
 */
function languagesModuleReplaceURL(string $currentURL, string $newPrefixURL, string $appBaseURL): string
{
    $encoding = 'UTF-8';
    $currentURLLength = mb_strlen($currentURL, $encoding);
    $start = 0;
    $appBaseURLLength = mb_strlen($appBaseURL, $encoding);

    if ($appBaseURLLength > $currentURLLength) {
        $appBaseURLLength = $currentURLLength;
    }

    $startString = mb_substr($currentURL, 0, $start, $encoding);
    $endString = mb_substr($currentURL, $start + $appBaseURLLength, $currentURLLength - $start - $appBaseURLLength, $encoding);
    return  $startString . $newPrefixURL . $endString;
}// languagesModuleReplaceURL


if (!function_exists('mb_substr_replace')) {
    /**
     * Multibyte version of `substr_replace()`.
     * 
     * There are many modules/plugins use this function. Do not remove it.
     * 
     * @link https://www.php.net/manual/en/function.substr-replace.php#90146 Original source code.
     * @see https://www.php.net/manual/en/function.substr-replace.php for more info.
     * @since 1.0.1
     * @param string $string The original string.
     * @param string $replacement The replacement string.
     * @param int $start The offset to begins.
     * @param int|null $length The length of the portion of string.
     * @param string|null $encoding The multibyte functions encoding.
     * @return string Return result string.
     */
    function mb_substr_replace(string $string, string $replacement, int $start, $length = null, $encoding = null): string
    {
        $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);
            
        if ($start < 0) {
            $start = max(0, $string_length + $start);
        } else if ($start > $string_length) {
            $start = $string_length;
        }

        if ($length < 0) {
            $length = max(0, $string_length - $start + $length);
        } else if ((is_null($length) === true) || ($length > $string_length)) {
            $length = $string_length;
        }

        if (($start + $length) > $string_length) {
            $length = $string_length - $start;
        }

        if (is_null($encoding) === true) {
            return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length);
        }

        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
    }// mb_substr_replace
}