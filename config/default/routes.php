<?php
/** 
 * @license http://opensource.org/licenses/MIT MIT
 */


/* @var $Rc \FastRoute\RouteCollector */
/* @var $this \System\Router */


$Rc->addGroup('/languages', function(\FastRoute\RouteCollector $Rc) {
    $Rc->addRoute('GET', '', '\\Modules\\Languages\\Controllers\\Languages:index');
    $Rc->addRoute('PUT', '/update', '\\Modules\\Languages\\Controllers\\Languages:update');
});