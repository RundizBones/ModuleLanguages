<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\Languages\Tests\Helpers;


class multibyteTest extends \Rdb\Tests\BaseTestCase
{


    public static function setUpBeforeClass(): void
    {
        require_once dirname(__DIR__, 2) . '/Helpers/multibyte.php';
    }


    public function testLanguagesModuleReplaceURL()
    {
        // first, test with `substr_replace()`.
        $appBaseURL = '/myapp/';
        $currentURL = '/myapp/contact';
        $newPrefixURL = '/myapp/en-US/';

        $substrReplaceResult = substr_replace($currentURL, $newPrefixURL, 0, strlen($appBaseURL));
        $moduleReplaceURLResult = languagesModuleReplaceURL($currentURL, $newPrefixURL, $appBaseURL);
        $this->assertSame($substrReplaceResult, $moduleReplaceURLResult);

        // now, test with unicode characters.
        $currentURL = '/myapp/ติดต่อ';
        $newPrefixURL = '/myapp/th/';
        $moduleReplaceURLResult = languagesModuleReplaceURL($currentURL, $newPrefixURL, $appBaseURL);
        $expect = '/myapp/th/ติดต่อ';
        $this->assertSame($moduleReplaceURLResult, $expect);
        
        $currentURL = '/myapp/コンタクト';
        $newPrefixURL = '/myapp/jp/';
        $moduleReplaceURLResult = languagesModuleReplaceURL($currentURL, $newPrefixURL, $appBaseURL);
        $expect = '/myapp/jp/コンタクト';
        $this->assertSame($moduleReplaceURLResult, $expect);
        
        $currentURL = '/myapp/联系';
        $newPrefixURL = '/myapp/zh-CN/';
        $moduleReplaceURLResult = languagesModuleReplaceURL($currentURL, $newPrefixURL, $appBaseURL);
        $expect = '/myapp/zh-CN/联系';
        $this->assertSame($moduleReplaceURLResult, $expect);
    }// testLanguagesModuleReplaceURL


}
