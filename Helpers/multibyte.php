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