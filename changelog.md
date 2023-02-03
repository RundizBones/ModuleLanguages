# Rundizbones Languages module.
This module is require "RdbAdmin" module.

## v1.0.3
2022-02-03

* Accept `currentLanguageID` parameter in the `/languages/update` route and `PUT` method.
* Fix plugin check returned value.
* Use new function `languagesModuleReplaceURL()` instead of `mb_substr_replace()`. There are minor improvements.

## v1.0.1
2021-03-24

* Add plugin hook on update (change) language.

## v1.0
2021-01-27

* Use class to retrieve PUT method data.
* Add docblock comment to `\Rdb\Modules\Languages\Controllers\LanguagesController->mb_substr_replace()` method.

## v0.2.0
2020-02-07

* Use new namespace.

## v0.1.0
2019-06-17

* Initial version.