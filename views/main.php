<?php
/*
Plugin Name: WP2FL. Lessons "Tabele Create" Part 1
Plugin URI: http://wp2fl.com/plugins
Description: Урок. Создание таблицы. Часть 1
Version: 2.0
Author: Pavel
Author URI: http://wp2fl.com
License: GPL2
*/

defined('ABSPATH') or die('No script kiddies please!');

if(class_exists('WP_List_Table') == FALSE)
{
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

if(is_admin() == TRUE)
{
	$table = new Table();
    $table->prepare_items();
		
    ?>
        <div class="wrap" style="position:absolute;">
            <h2>Список плагинов</h2>
            <a href="?page=plugin-constructor/create"><input type="submit" value="Создать новый плагин" style="position:absolute;"></a>
			<?php $table->display(); ?>
        </div>
    <?php
}

class Table extends WP_List_Table
{
	public function prepare_items()
	{
		$per_page = 5;
		
		$data = $this->table_data();

		$this->set_pagination_args( array(
			'total_items' => count($data),
			'per_page'    => $per_page
		));
		
		$data = array_slice(
			$data,
			(($this->get_pagenum() - 1) * $per_page),
			$per_page
		);

		
		$this->_column_headers = array(
			$this->get_columns(), 
			$this->get_hidden_columns(), 
			$this->get_sortable_columns() 
		);

		
		$this->items = $data;
	}
 
	public function get_columns()
	{
		return array(
			'ex_id'			=> 'ID',
			'ex_title'		=> 'Название',
			'ex_path'		=> 'Путь',
			'ex_created'    => 'Создан',
			'ex_updated'	=> 'Обновлён',
            'ex_actions'    => 'Действия',
		);
	}

	public function get_hidden_columns()
	{
		return array();
	}
 
	public function get_sortable_columns()
	{
		return array(
			'ex_id' => array('ex_id', false),
			'ex_title' => array('ex_title', true),
			'ex_path' => array('ex_path', false),
			'ex_updated' => array('ex_updated', false),
		);
	}

	private function table_data()
	{
		global $wpdb;
		
		$retn = array();
		$data = $wpdb->get_results( 
			"
			SELECT * FROM `{$wpdb->prefix}pconstructur`
			"
		);

		var_dump($data);

		date_default_timezone_set('UTC');
		
		foreach ($data as $elem) {
			$retn[] = array(
				'ex_id'			=> $elem->id,
                'ex_actions'    => "<a href=\"?page=plugin-constructor/edit?id=$elem->id\">Редактировать</a> | <a href=\"?page=plugin-constructor/delete?id=$elem->id\">Удалить</a>",
				'ex_title'		=> $elem->name,
				'ex_path'		=> "<span title='" . __DIR__ . "/$elem->path'>$elem->path</span>",
				'ex_created'    => date(DATE_RFC822, $elem->created_at),
				'ex_updated'	=> date(DATE_RFC822, $elem->updated_at)
			);
		}

		return $retn;
		/*return array(
			array(
				'ex_id'			=> 1,
                'ex_actions'    => '<a href="">Редактировать</a> | <a href="">Удалить</a>',
				'ex_title'		=> 'Plugin 1',
				'ex_path'		=> '<span title="' . __DIR__ . '/plugin-1">plugin-1</span>',
				'ex_created'    => '04.08.2017 16:30',
				'ex_updated'	=> '04.08.2017 16:32',
			),
			array(
				'ex_id'			=> 2,
                'ex_actions'    => '<a href="">Редактировать</a> | <a href="">Удалить</a>',
				'ex_title'		=> 'Plugin 2',
				'ex_path'		=> '<span title="' . __DIR__ . '/plugin-1">plugin-2</span>',
				'ex_created'    => '04.08.2017 16:30',
				'ex_updated'	=> '04.08.2017 16:32',
			),
			array(
				'ex_id'			=> 3,
                'ex_actions'    => '<a href="">Редактировать</a> | <a href="">Удалить</a>',
				'ex_title'		=> 'Plugin 3',
				'ex_path'		=> '<span title="' . __DIR__ . '/plugin-1">plugin-3</span>',
				'ex_created'    => '04.08.2017 16:30',
				'ex_updated'	=> '04.08.2017 16:32',
			),

		);*/
	}
 
    public function column_default($item, $column_name)
    {
        switch($column_name)
		{
			case 'ex_id':
			case 'ex_title':
			case 'ex_path':
			case 'ex_created':
			case 'ex_updated':
            case 'ex_actions':
				return $item[$column_name];
            default:
				return print_r($item, true);
        }
    }
}