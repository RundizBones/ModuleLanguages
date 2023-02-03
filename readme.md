Languages module for [RundizBones] framework.

This module is for switch to selected URL by cookie or URL segment (depend on configuration on your framework installation).

If the new language ID is not available in framework's configuration, the default will be use instead.

### Installation
Download and extract files into **/Modules/Languages** folder.

### REST API URL.
| Method | URL | Params | Description |
| --- | --- | --- | --- |
| `GET` | **/languages** | None | Get all languages to render in HTML such as select box. |
| `PUT` | **/languages/update** | `currentUrl` (string) Your current URL. Must not contains any language ID.<br>`rundizbones-languages` (string) The new language ID same as in configuration that you want to change to.<br>`currentLanguageID` (string) Current language ID before changing. | Change to selected language |

Please note that the URL should begins with your installation path. Example your installation is in **/myapp** then the URL must changed to **/myapp/languages**.

Example work flow:<br>
`PUT` **/languages/update** with params: `currentUrl='/contact'` `rundizbones-languages='en-US'` `currentLanguageID='th'`<br>
Result will be returned: `redirectUrl` **/en-US/contact**

[RundizBones]:https://github.com/RundizBones/framework
