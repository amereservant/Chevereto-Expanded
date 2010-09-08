<?php
/**
 * Initialize/Loader
 *
 * This file is responsible for loading everything.
 * NOTHING should be executed prior to this since it's responsible for ensuring
 * everything is loaded and executed in the correct order.
 */

// Add all system hooks
add_hooks(array('parse_url', 'set_var', 'set_system_var'));

if( file_exists( CHEV_PATH . CHEV_SEP .'install'. CHEV_SEP .'install.php' ) )
{
    require_once CHEV_PATH . CHEV_SEP .'install'. CHEV_SEP .'install.php';
}

Router::load();
//Registry::dump_vars();
