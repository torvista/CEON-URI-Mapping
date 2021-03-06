<?php

/**
 * Observer for Ceon URI Mapping link creation for admin-generated emails, BISN etc.
 * Watches html_output.php function zen_href_catalog_link
 *
 * @package     ceon_uri_mapping
 * @author      Conor Kerr <zen-cart.uri-mapping@ceon.net>
 * @author      torvista
 * @copyright   Copyright 2008-2012 Ceon
 * @copyright   Copyright 2003-2007 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        https://github.com/torvista/CEON-URI-Mapping
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     2016
 */
class CeonURIMappingLinkBuildAdmin extends base
{
    function __construct()
    {
        $this->attach($this, array('NOTIFY_SEFU_INTERCEPT_ADMCATHREF'));
    }

    function update(&$callingClass, $notifier, $p1, &$link, $page, $parameters, $connection)//can use "update" or camelized notifier name. & required for &$link to modify it inside here

    {
        if (defined('CEON_URI_MAPPING_ENABLED') && CEON_URI_MAPPING_ENABLED == 1) {

            if (!isset($ceon_uri_mapping_href_link_builder)) {
                static $ceon_uri_mapping_href_link_builder;

                require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingHREFLinkBuilder.php');

                $ceon_uri_mapping_href_link_builder = new CeonURIMappingHREFLinkBuilder();
            }

            if ($connection == 'NONSSL') {
                $link = HTTP_SERVER;
            } elseif ($connection == 'SSL') {
                if (ENABLE_SSL == 'true') {
                    $link = HTTPS_SERVER;
                } else {
                    $link = HTTP_SERVER;
                }
            }

            if ($ceon_uri_mapping_href_link_builder->buildHREFLink($link, $page, $parameters, $connection, false)) {
                $link = $ceon_uri_mapping_href_link_builder->getHREFLink();
            } else {
                $link = null;
            }
        }
    }
}