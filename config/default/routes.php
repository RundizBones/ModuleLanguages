<?php
/** 
 * @license http://opensource.org/licenses/MIT MIT
 */


/* @var $Rc \FastRoute\RouteCollector */
/* @var $this \Rdb\System\Router */


$Rc->addGroup('/languages', function(\FastRoute\RouteCollector $Rc) {
    $Rc->addRoute('GET', '', '\\Rdb\\Modules\\Languages\\Controllers\\Languages:index');
    $Rc->addRoute('PUT', '/update', '\\Rdb\\Modules\\Languages\\Controllers\\Languages:update');
});