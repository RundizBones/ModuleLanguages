<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rdb\Modules\Languages\Controllers;


/**
 * Languages controller.
 * 
 * @since 0.1
 */
class LanguagesController extends \Rdb\Modules\RdbAdmin\Controllers\BaseController
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
            /* @var $Config \Rdb\System\Config */
            $Config = $this->Container->get('Config');
            $Config->setModule('');
        } else {
            $Config = new \Rdb\System\Config();
        }

        $Url = new \Rdb\System\Libraries\Url($this->Container);

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
            /* @var $Config \Rdb\System\Config */
            $Config = $this->Container->get('Config');
            $Config->setModule('');
        } else {
            $Config = new \Rdb\System\Config();
        }

        $Url = new \Rdb\System\Libraries\Url($this->Container);

        $output = [];

        $allLanguages = $Config->get('languages', 'language', []);
        $defaultLanguage = $Config->getDefaultLanguage($allLanguages);
        $languageID = $this->Input->put('rundizbones-languages');
        if (!array_key_exists($languageID, $allLanguages)) {
            // if the language that was set is not exists in languages config.
            // use default.
            $languageID = $defaultLanguage;
        }
        unset($allLanguages);
        $currentUrl = $this->Input->put('currentUrl');
        $currentLanguageID = $this->Input->put('currentLanguageID');

        if ($Config->get('languageMethod', 'language', 'url') === 'cookie') {
            // if config is using cookie to set, detect language.
            $languageCookieName = 'rundizbones_language' . $Config->get('suffix', 'cookie');
            $cookieExpires = 90;// unit in days.
            setcookie($languageCookieName, $languageID, (time() + (60*60*24*$cookieExpires)), '/');
            unset($cookieExpires, $languageCookieName);
            $output['redirectUrl'] = $currentUrl;
        } else {
            // if config is using url to set, detect language.
            require_once MODULE_PATH . DIRECTORY_SEPARATOR . 'Languages' . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . 'multibyte.php';
            $appBase = $Url->getAppBasedPath() . '/';
            if ($Config->get('languageUrlDefaultVisible', 'language', false) === true) {
                // if config was set to show default language in the URL.
                $output['redirectUrl'] = mb_substr_replace($currentUrl, $appBase . $languageID . '/', 0, mb_strlen($appBase));
            } else {
                // if config was set to NOT show default language in the URL.
                if ($languageID === $defaultLanguage) {
                    $output['redirectUrl'] = $currentUrl;
                } else {
                    $output['redirectUrl'] = mb_substr_replace($currentUrl, $appBase . $languageID . '/', 0, mb_strlen($appBase));
                }
            }
            unset($appBase);
        }

        if ($this->Container->has('Plugins')) {
            /* @var $Plugins \Rdb\Modules\RdbAdmin\Libraries\Plugins */
            $Plugins = $this->Container->get('Plugins');
            if ($this->Container->has('Logger')) {
                /* @var $Logger \Rdb\System\Libraries\Logger */
                $Logger = $this->Container->get('Logger');
                $logChannel = 'modules/languages/controllers/languagescontroller/updateaction';
            }
            /*
             * PluginHook: Rdb\Modules\Languages\Controllers\LanguagesController->updateAction.afterGetRedirectUrl
             * PluginHookDescription: Hook after get redirect URL on change language.
             * PluginHookParam: associative array:<br>
             *              `redirectUrl` (string) The redirect URL.<br>
             *              `currentUrl` (string) Current URL before redirect.<br>
             *              `configLanguageMethod` (string) Config value of language detection method.<br>
             *              `configLanguageUrlDefaultVisible` (bool) Config value of default language URL will be visible or not (if language method is URL).<br>
             *              `defaultLanguage` (string) Default language.<br>
             *              `languageID` (string) Selected language ID.<br>
             *              `currentLanguageID` (string) Current language ID before change.<br>
             * PluginHookReturn: Expect return redirect URL as string.
             * PluginHookSince: 1.0.1
             */
            $redirectUrl = $Plugins->doHook(
                __CLASS__ . '->' . __FUNCTION__ . '.afterGetRedirectUrl',
                [
                    'redirectUrl' => $output['redirectUrl'],
                    'currentUrl' => $currentUrl,
                    'configLanguageMethod' => $Config->get('languageMethod', 'language', 'url'),
                    'configLanguageUrlDefaultVisible' => $Config->get('languageUrlDefaultVisible', 'language', false),
                    'defaultLanguage' => $defaultLanguage,
                    'languageID' => $languageID,
                    'currentLanguageID' => $currentLanguageID,
                ]
            );

            if (isset($Logger)) {
                $Logger->write($logChannel, 0, 'Do hook for update (change) language. Here is the result {redirectUrl}. The last array will be use.', ['redirectUrl' => $redirectUrl]);
            }

            if (is_array($redirectUrl)) {
                $lastRedirectUrl = '';
                foreach ($redirectUrl as $eachRedirectUrl) {
                    if (is_string($eachRedirectUrl) && !empty(trim($eachRedirectUrl))) {
                        $lastRedirectUrl = $eachRedirectUrl;
                    }
                }
                unset($eachRedirectUrl);

                if (!empty($lastRedirectUrl)) {
                    $output['redirectUrl'] = $lastRedirectUrl;
                }
                unset($lastRedirectUrl);
            }
            unset($Plugins, $redirectUrl);
        }// endif; plugins
        unset($currentLanguageID, $currentUrl, $defaultLanguage, $languageID, $Url);

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
