<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Modules\Languages\Controllers;


/**
 * Languages controller.
 * 
 * @since 0.1
 */
class LanguagesController extends \Modules\RdbAdmin\Controllers\BaseController
{


    /**
     * Get languages for render select box.
     * 
     * @return string
     */
    public function indexAction(): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        if ($this->Container->has('Config')) {
            /* @var $Config \System\Config */
            $Config = $this->Container->get('Config');
            $Config->setModule('');
        } else {
            $Config = new \System\Config();
        }

        $Url = new \System\Libraries\Url($this->Container);

        $output = [];
        $output['languages'] = $Config->get('languages', 'language', []);
        $output['defaultLanguage'] = $Config->getDefaultLanguage($output['languages']);
        $output['currentLanguage'] = $_SERVER['RUNDIZBONES_LANGUAGE'];
        $output['languageDetectMethod'] = $Config->get('languageMethod', 'language', 'url');
        if ($output['languageDetectMethod'] === 'url') {
            $output['languageUrlDefaultVisible'] = $Config->get('languageUrlDefaultVisible', 'language', false);
        }
        $output['setLanguage_method'] = 'PUT';
        $output['setLanguage_url'] = $Url->getDomainProtocol() . $Url->getAppBasedPath() . '/languages/update';

        unset($Config, $Url);

        // display, response part ---------------------------------------------------------------------------------------------
        if ($this->Input->isNonHtmlAccept() || $this->Input->isXhr()) {
            // if custom HTTP accept or XHR, response content.
            return $this->responseAcceptType($output);
        } else {
            // if not custom HTTP accept.
            if (isset($_SERVER['RUNDIZBONES_MODULEEXECUTE'])) {
                // if module execute.
                return serialize($output);
            } else {
                // if not module execute.
                http_response_code(403);
                return 'Sorry, this page is for request via XHR, REST API, or module execute.';
            }
        }
    }// indexAction


    /**
     * Multibyte version of `substr_replace()`.
     * 
     * @link https://shkspr.mobi/blog/2012/09/a-utf-8-aware-substr_replace-for-use-in-app-net/
     * @param string $original
     * @param string $replacement
     * @param int $position
     * @param type $length
     * @return string
     */
    protected function mb_substr_replace(string $original, string $replacement, int $position, $length = null): string
    {
        $startString = mb_substr($original, 0, $position, 'UTF-8');
        $endString = mb_substr($original, $position + $length, mb_strlen($original), 'UTF-8');

        $out = $startString . $replacement . $endString;

        return $out;
    }// mb_substr_replace


    /**
     * Update current language and get redirect URL.
     * 
     * The PUT data must contain 'currentUrl', 'rundizbones-languages'.<br>
     * The 'currentUrl' data should called using this code `$Url->getCurrentUrl() . $Url->getQuerystring()`.
     * 
     * @return string
     */
    public function updateAction(): string
    {
        // processing part ----------------------------------------------------------------------------------------------------
        if ($this->Container->has('Config')) {
            /* @var $Config \System\Config */
            $Config = $this->Container->get('Config');
            $Config->setModule('');
        } else {
            $Config = new \System\Config();
        }

        parse_str(file_get_contents('php://input'), $_PUT);

        $Url = new \System\Libraries\Url($this->Container);

        $output = [];

        $allLanguages = $Config->get('languages', 'language', []);
        $defaultLanguage = $Config->getDefaultLanguage($allLanguages);
        $languageID = $_PUT['rundizbones-languages'];
        if (!array_key_exists($languageID, $allLanguages)) {
            // if the language that was set is not exists in languages config.
            // use default.
            $languageID = $defaultLanguage;
        }
        unset($allLanguages);

        if ($Config->get('languageMethod', 'language', 'url') === 'cookie') {
            // if config is using cookie to set, detect language.
            $languageCookieName = 'rundizbones_language' . $Config->get('suffix', 'cookie');
            $cookieExpires = 90;// unit in days.
            setcookie($languageCookieName, $languageID, (time() + (60*60*24*$cookieExpires)), '/');
            unset($cookieExpires, $languageCookieName);
            $output['redirectUrl'] = $_PUT['currentUrl'];
        } else {
            // if config is using url to set, detect language.
            $appBase = $Url->getAppBasedPath() . '/';
            if ($Config->get('languageUrlDefaultVisible', 'language', false) === true) {
                // if config was set to show default language in the URL.
                $output['redirectUrl'] = $this->mb_substr_replace($_PUT['currentUrl'], $appBase . $languageID . '/', 0, mb_strlen($appBase));
            } else {
                // if config was set to NOT show default language in the URL.
                if ($languageID === $defaultLanguage) {
                    $output['redirectUrl'] = $_PUT['currentUrl'];
                } else {
                    $output['redirectUrl'] = $this->mb_substr_replace($_PUT['currentUrl'], $appBase . $languageID . '/', 0, mb_strlen($appBase));
                }
            }
            unset($appBase);
        }
        unset($defaultLanguage, $languageID, $Url);

        // display, response part ---------------------------------------------------------------------------------------------
        if ($this->Input->isNonHtmlAccept() || $this->Input->isXhr()) {
            // if custom HTTP accept or XHR, response content.
            return $this->responseAcceptType($output);
        } else {
            // if not custom HTTP accept.
            if (isset($_SERVER['RUNDIZBONES_MODULEEXECUTE'])) {
                // if module execute.
                return serialize($output);
            } else {
                // if not module execute.
                http_response_code(403);
                return 'Sorry, this page is for request via XHR, REST API, or module execute.';
            }
        }
    }// updateAction


}
