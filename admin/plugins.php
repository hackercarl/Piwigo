<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_plugins.inc.php');
check_status(ACCESS_ADMINISTRATOR);

$my_base_url = PHPWG_ROOT_PATH.'admin.php?page=plugins';

if ( isset($_REQUEST['action']) and isset($_REQUEST['plugin'])  )
{
  if ( $_REQUEST['action']=='deactivate')
  {
    $result = deactivate_plugin( $_REQUEST['plugin'] );
  }
  else
  {
    $result = activate_plugin( $_REQUEST['plugin'] );
  }
  if ($result)
  { // we need a redirect so that we really reload it
    redirect($my_base_url.'&amp;'.$_REQUEST['action'].'='.$result);
  }
  else
  {
    array_push( $page['errors'], 'Plugin activation/deactivation error' );
  }
}


$active_plugins = get_active_plugins();
$active_plugins = array_flip($active_plugins);

$plugins = get_plugins();

$template->set_filenames(array('plugins' => 'admin/plugins.tpl'));


trigger_event('plugin_admin_menu');

$template->assign_block_vars('plugin_menu.menu_item',
    array(
      'NAME' => l10n('Plugins'),
      'URL' => PHPWG_ROOT_PATH.'admin.php?page=plugins'
    )
  );

if ( isset($page['plugin_admin_menu']) )
{
  $plug_base_url = PHPWG_ROOT_PATH.'admin.php?page=plugin&amp;section=';
  foreach ($page['plugin_admin_menu'] as $menu)
  {
    $template->assign_block_vars('plugin_menu.menu_item',
        array(
          'NAME' => $menu['title'],
          'URL' => $plug_base_url.$menu['uid']
        )
      );
  }
}

$num=0;
foreach( $plugins as $plugin_id => $plugin )
{
  $action_url = $my_base_url.'&amp;plugin='.$plugin_id;
  if ( isset( $active_plugins[$plugin_id] ) )
  {
    $action_url .= '&amp;action=deactivate';
    $action_name = l10n('Deactivate');
  }
  else
  {
    $action_url .= '&amp;action=activate';
    $action_name = l10n('Activate');
  }
  $display_name = $plugin['name'];
  if ( !empty($plugin['uri']) )
  {
    $display_name='<a href="'.$plugin['uri'].'">'.$display_name.'</a>';
  }
  $template->assign_block_vars( 'plugins.plugin',
      array(
        'NAME' => $display_name,
        'VERSION' => $plugin['version'],
        'DESCRIPTION' => $plugin['description'],
        'CLASS' => ($num++ % 2 == 1) ? 'row2' : 'row1',
        'L_ACTION' => $action_name,
        'U_ACTION' => $action_url,
        )
     );
}


$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>
