<?php
defined('C5_EXECUTE') or die("Access Denied.");
if ((!$c->isAdminArea()) && ($c->getCollectionPath() != '/login')) {

    $smm = Config::get('SITE_MAINTENANCE_MODE');
    if ($smm == 1) {
        $v = View::getInstance();
        $v->render('/maintenance_mode/');
        exit;
    }

}
