<?php
define('PLUGINS_DIR', __DIR__ . '/../../');
global $name, $path, $version, $description;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $info = [];

    $name = filter_input(INPUT_POST, 'name');
    $path = filter_input(INPUT_POST, 'path');
    $version = filter_input(INPUT_POST, 'version');
    $description = filter_input(INPUT_POST, 'description');
    $delete = filter_input(INPUT_POST, 'delete');
    $bases = $_POST['db'];

    if ($delete) {
        $info['bases'] = [];

        foreach ($bases as $key => $base) {
            $matched = [];
            if (preg_match("/create\s*table\s*(?:`|)([\w-]+)/im", $base, $matched)) {
                $info['bases'][] = $matched[1];
            }
        }
    }

    // var_dump($bases);
    //exit;

    if (!$name) {
        render("Введите название плагина.");
    }
    if (strlen($name) > 64) {
        render("В названии плагина не должно быть более 64 символов.");
    }

    if (!$path) {
        render("Введите имя пути.");
    }
    if (strlen($path) > 64) {
        render("В имени пути не должно быть более 64 символов.");
    }
    
    $dir = scandir(PLUGINS_DIR);

    foreach ($dir as $value) {
        if ($value == $path) {
            render("Путь \"$value\" занят.");
        }
    }
    
    if (!$path) {
        $version = '1.0.0';
    }
    if (strlen($path) > 16) {
        render("В версии плагина не должно быть более 64 символов.");
    }

    // //

    global $wpdb;

    $wpdb->insert(
        $wpdb->prefix . 'pconstructur',
        array(
            'name' => $name,
            'path' => $path, 
            'version' => $version,
            'created_at' => time(),
            'updated_at' => time() 
        )
    );

    $id = $wpdb->insert_id;

    // //
    
    $plugin_dir = PLUGINS_DIR . $path . '';
    try {
        $info['name'] = $name;
        $info['version'] = $version;
        
        echo mkdir($plugin_dir) ? 1 : 0;
        mkdir($plugin_dir . '/views');
        touch($plugin_dir . '/index.php');
        $file = fopen($plugin_dir . '/config.json', 'w');
        $test = fwrite($file, json_encode($info));
        // var_dump($_SERVER);
        fclose($file);
        $file = fopen($plugin_dir . '/index.php', 'w');

        $bases = join($bases, '", "');
        $test = fwrite($file, "<?php
/*
Plugin Name: $name
Plugin URI: {$_SERVER['HTTP_ORIGIN']}/{$_SERVER['DOCUMENT_URI']}?page=plugin-constructor/edit?id=$id
Description: $description
Version: $version
Author: " . wp_get_current_user()->display_name . "
Author URI: {$_SERVER['HTTP_ORIGIN']}/{$_SERVER['DOCUMENT_URI']}
*/

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

register_activation_hook(__FILE__, 'msp_activation');
register_deactivation_hook(__FILE__, 'msp_deactivation');

function msp_activation() {
    global \$wpdb;

    \$bases = [\"$bases\"];

    foreach (\$bases as \$base) {
        dbDelta(\$base);
    }

    register_uninstall_hook(__FILE__, 'msp_uninstall');
}

function msp_deactivation() {

}

function msp_uninstall() {

}
");
    fclose($file);
        echo $plugin_dir;
    } catch (Exception $e) {
        echo $e;
    }
    
} else {
    render('');
}

function render($error) {
    global $name, $path, $version;
?><div class="wrap" style="position:absolute;">
    <h2>Создать плагин</h2>

    <?php if ($error):?> <p class="error"><?php echo $error; ?></p>

    <?php endif; ?>
    <form method="POST">
    <table class="wp-list-table widefat fixed striped toplevel_page_plugin-constructormain">
        <thead>
            <tr>
                <td>Название:</td>
                <td><input type="text" name="name" size="100%" maxlength="64" value="<?php echo $name; ?>"></td>
            </tr>
            <tr>
                <td>Имя пути:</td>
                <td><input type="text" name="path" size="100%" maxlength="64" value="<?php echo $path; ?>"></td>
            </tr>
            <tr>
                <td>Версия:</td>
                <td><input type="text" name="version" size="100%" value="1.0.0" maxlength="16" value="<?php echo $version; ?>"></td>
            </tr>
            <tr>
                <td>Краткое описание:</td>
                <td><input type="text" name="description" size="100%" value="" maxlength="256" value="<?php echo $description; ?>"></td>
            </tr>
            <tr>
                <td>База данных:</td>
                <td>
                    <p><input type="checkbox" class="createdb-c" id="ch_1"> <label for="ch_1">SQL</label></p>
                    <div class="createdb-d" style="display:none;">
                        <p><input type="submit" class="createdb-add" value="Добавить"></p>
                        <div class="createdb-base">
                        
                        </div>
                        <input type="hidden" name="_db" value="">
                        <p><input type="checkbox" name="delete" id="ch_2"><label for="ch_2"> Удалять базы при деактивации плагина</label></p>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Создать" size="20"></td>
            </tr>
        </thrad>
    </table>
    </form>
</div>

<script>
var idx = 0;
document.getElementsByClassName('createdb-c')[0].onclick = function () {
    if (document.getElementsByClassName('createdb-c')[0].checked) {
        document.getElementsByClassName('createdb-d')[0].style.display="";
    } else {
        document.getElementsByClassName('createdb-d')[0].style.display="none";
    }
}
document.getElementsByClassName('createdb-add')[0].onclick = function () {
    document.getElementsByClassName('createdb-base')[0].innerHTML += `<div><textarea name="db[]" class="createdb-area" id="createdb-area-${++idx}"></textarea><button class="createdb-cc" id="createdb-cc-${idx}">Конструктор</button></div>`;
    for (const o of document.getElementsByClassName(`createdb-cc`)) {
        o.onclick = function () {
            const id = this.id.split('-')[2]-1;
            window.open(`/wp-content/plugins/plugin-constructor/views/db-create.html#${id}`, `c_${id}`, "width=900,height=500");
            return false;
        }
    }
    return false;
}
</script>
<?php
    
    exit;
}
?>