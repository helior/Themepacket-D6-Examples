<?php

/**
 * @file
 *  This fictitious theme "Kawaii" is an example of how to implement
 *  Themepacket into your own themes. This file may seem convoluted, but it is
 *  mainly because of the extensive documentation.
 * 
 *  There are 3 primary things to keep in mind regarding what Themepacket is
 *  trying to accomplish:
 *  
 *  1)  A scalable organization of files/folder that encapsulate your theme
 *      implementations along with their assets in order to keep your code
 *      tidy. As well as eliminate the need to keep track of paths in your
 *      code, such as for adding js/css, loading include files, and to some
 *      extent, url references in your css.
 *  2)  Automatically discover the existence of assets (js/css) in the same
 *      location as your theme implementations, and automatically drupal_add_*
 *      each item. This mechanism also correctly handles "library" files that
 *      are added along theme implementations as symbolic links from your
 *      configured "libraries" directory (the actual path is configurable in
 *      admin/settings/themepacket.)
 *  3)  Eliminates the need to manually register theme implementations in
 *      hook_theme().
 */

/**
 * Implementation of hook_theme().
 */
function kawaii_theme($existing, $type, $theme, $path) {
  $hooks = array();
  
  // Themes in Drupal 6 cannot have module dependencies, so it would be a good
  // idea to use module_exists() before attempting to use themepacket's API.
  if (module_exists('themepacket')) {
    
    // Drupal 6 allows you to override templates such as node.tpl.php and even
    // further override that with node-story.tpl.php, however, this will only
    // work when both files are in the same hierarchy. This, however,
    // introduces a problem if we want to organize the theme layer in a way
    // that makes more sense, however, if we override the existing node theme
    // implementation in the registry and add a 'pattern' to it, we can achieve
    // some pretty interesting things.
    $hooks['node'] = array(
       'arguments' => array('node' => NULL, 'teaser' => FALSE, 'page' => FALSE),
       'function' => 'node_delegator',
       'pattern' => 'node__',
     );
    
    // Phptemplate engine only searches for patterns against theme hooks that
    // were implemented in the module layer. themepacket_find_patterns gives
    // themes an opportunity to define (or override) theme hooks and have the
    // registry discover their patterns on it's own. You may have already seen
    // patterns implemented in the views module. An example is the
    // views-view.tpl.php. It's pattern can discover templates such as
    // views-view--unformatted.tpl.php, views-view--unformatted--block.tpl.php,
    // and so on. There is no restriction as to where these template files can
    // be located in your theme.
    $hooks += themepacket_find_patterns($hooks, '.tpl.php', $path);
    
    // Themepacket will scan the "theme" directory (this is configurable in
    // admin/settings/themepacket) for any theme implementations and register
    // them on your behalf.
    $hooks += themepacket_discover($existing, $type, $theme, $path);
  }
  
  else {
    // If Themepacket is not enabled and you're using a theme that heavily 
    // depends on it, you may get a completely unstyled page. It's a good idea
    // to let watchdog know about it so you may easily track and fix the error.
    watchdog('kawaii', 'Themepacket module is expected to be enabled for the Kawaii theme.', array(), WATCHDOG_ERROR);
  }
  
  return $hooks;
}

