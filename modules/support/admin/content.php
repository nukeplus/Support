<?php

/**
 * @Project PaCop 1x
 * @Author PaCorp Co,Ltd (contact@PaCop.vn)
 * @Copyright (C) 2014 PaCorp Co,Ltd. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$id = $nv_Request->get_int( 'id', 'post,get', 0 );

if( $id )
{
	$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id;
	$row = $db->query( $sql )->fetch();

	if(empty( $row ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
		die();
	}

	$page_title = $lang_module['edit'];
	$action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $id;
}
else
{
	$page_title = $lang_module['add'];
	$action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
}


$error = '';
$groups_list = nv_groups_list();

if( $nv_Request->get_int( 'save', 'post' ) == '1' )
{
	$row['title'] = $nv_Request->get_title( 'title', 'post', '', 1 );
	$row['alias'] = $nv_Request->get_title( 'alias', 'post', '', 1 );
	$row['phone'] = $nv_Request->get_title( 'phone', 'post', '', 1 );
	$row['phone1'] = $nv_Request->get_title( 'phone1', 'post', '', 1 );
	$image = $nv_Request->get_string( 'image', 'post', '' );
	if( is_file( NV_DOCUMENT_ROOT . $image ) )
	{
		$lu = strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' );
		$row['image'] = substr( $image, $lu );
	}
	else
	{
		$row['image'] = '';
	}
	$row['imagealt'] = $nv_Request->get_title( 'imagealt', 'post', '', 1 );

	
	if( empty( $row['title'] ) )
	{
		$error = $lang_module['empty_title'];
	}
	
	
		$row['alias'] = empty( $row['alias'] ) ? change_alias( $row['title'] ) : change_alias( $row['alias'] );

	
		if( $id )
		{
			$_sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET title = :title, alias = :alias, image = :image, imagealt = :imagealt,  phone = :phone, phone1 = :phone1,  admin_id = :admin_id, edit_time = ' . NV_CURRENTTIME . ' WHERE id =' . $id;
			$publtime = $row['add_time'];
		}
		else
		{
			if( $page_config['news_first'] )
			{
				$weight = 1;
			}
			else
			{
				$weight = $db->query( "SELECT MAX(weight) FROM " . NV_PREFIXLANG . "_" . $module_data )->fetchColumn();
				$weight = intval( $weight ) + 1;
			}

			$_sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '
				(title, alias, image, imagealt, phone,phone1, weight,admin_id, add_time, edit_time, status) VALUES
				(:title, :alias, :image, :imagealt,:phone,:phone1, ' . $weight . ', :admin_id, ' . NV_CURRENTTIME . ', ' . NV_CURRENTTIME . ', 1)';

			$publtime = NV_CURRENTTIME;
		}

		try
		{
			$sth = $db->prepare( $_sql );
			$sth->bindParam( ':title', $row['title'], PDO::PARAM_STR );
			$sth->bindParam( ':alias', $row['alias'], PDO::PARAM_STR );
			$sth->bindParam( ':image', $row['image'], PDO::PARAM_STR );
			$sth->bindParam( ':imagealt', $row['imagealt'], PDO::PARAM_STR );
			$sth->bindParam( ':phone', $row['phone'], PDO::PARAM_STR );
			$sth->bindParam( ':phone1', $row['phone1'], PDO::PARAM_STR );
			$sth->bindParam( ':admin_id', $admin_info['admin_id'], PDO::PARAM_INT );
			$sth->execute();

			if( $sth->rowCount() )
			{
				if( $id )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit', 'ID: ' . $id, $admin_info['userid'] );
				}
				else
				{
					if( $page_config['news_first'] )
					{
						$id = $db->lastInsertId();
						$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET weight=weight+1 WHERE id!=' . $id );
					}

					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add', ' ', $admin_info['userid'] );
				}

				nv_del_moduleCache( $module_name );
				Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main' );
				die();
			}
			else
			{
				$error = $lang_module['errorsave'];
			}
		}
		catch( PDOException $e )
		{
			$error = $lang_module['errorsave'];
		}
	
}
elseif( empty( $id ) )
{
	$row['image'] = '';

}



if( ! empty( $row['image'] ) and is_file( NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $row['image'] ) )
{
	$row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $row['image'];
}
$lang_global['title_suggest_max'] = sprintf( $lang_global['length_suggest_max'], 65 );

$xtpl = new XTemplate( 'content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'FORM_ACTION', $action );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'UPLOADS_DIR_USER', NV_UPLOADS_DIR . '/' . $module_upload );
$xtpl->assign( 'DATA', $row );



if( empty( $row['alias'] ) ) $xtpl->parse( 'main.get_alias' );

if( $error )
{
	$xtpl->assign( 'ERROR', $error );
	$xtpl->parse( 'main.error' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';