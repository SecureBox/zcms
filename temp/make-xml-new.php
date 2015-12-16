<?php
/**
 * Для запуска из командной строки для формирования xml каталога
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

chdir('..');

// поддержка кодировки UTF-8
require 'app/include/utf8.php';
// автоматическая загрузка классов
require 'app/include/autoload.php';
// правила маршрутизации
require 'app/routing.php';
// настройки приложения
require 'app/settings.php';
Config::init($settings);
// реестр, для хранения всех объектов приложения
$register = Register::getInstance();
// настройки приложения
$register->config = Config::getInstance();
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();

$handle = fopen('catalog-temp.xml', 'w');
fwrite($handle, '<?xml version="1.0" encoding="utf-8" ?><catalog>');

// получаем все категории
fwrite($handle, '<categories>');
$query = "SELECT `id`, `parent`, `name`, `sortorder` FROM `categories` WHERE 1 ORDER BY `id`";
$categories = $register->database->fetchAll($query);
foreach ($categories as $item) {
    echo 'category id='.$item['id'].PHP_EOL;
    $parent = md5($item['parent']);
    if (empty($item['parent'])) {
        $parent = '';
    }
    $text = '<category id="' . md5($item['id']) . '" parent="' . $parent . '" sortorder="' . $item['sortorder'] . '">';
    $text = $text . '<![CDATA[' . $item['name'] . ']]>';
    $text = $text . '</category>';
    fwrite($handle, $text);
}
fwrite($handle, '</categories>');

// получаем всех производителей
fwrite($handle, '<makers>');
$query = "SELECT `id`, `name` FROM `makers` WHERE 1 ORDER BY `id`";
$makers = $register->database->fetchAll($query);
foreach ($makers as $item) {
    echo 'maker id='.$item['id'].PHP_EOL;
    $text = '<maker id="' . md5($item['id']) . '">';
    $text = $text . '<![CDATA[' . $item['name'] . ']]>';
    $text = $text . '</maker>';
    fwrite($handle, $text);
}
fwrite($handle, '</makers>');

// получаем все параметры и все значения
fwrite($handle, '<params>');
// получаем параметры
fwrite($handle, '<names>');
$query = "SELECT `id`, `name` FROM `params` WHERE 1 ORDER BY `id`";
$names = $register->database->fetchAll($query);
foreach ($names as $item) {
    echo 'param name id='.$item['id'].PHP_EOL;
    $text = '<name id="' . md5($item['id']) . '">';
    $text = $text . '<![CDATA[' . $item['name'] . ']]>';
    $text = $text . '</name>';
    fwrite($handle, $text);
}
fwrite($handle, '</names>');
// получаем значения
fwrite($handle, '<values>');
$query = "SELECT `id`, `name` FROM `values` WHERE 1 ORDER BY `id`";
$values = $register->database->fetchAll($query);
foreach ($values as $item) {
    echo 'param value id='.$item['id'].PHP_EOL;
    $text = '<value id="' . md5($item['id']) . '">';
    $text = $text . '<![CDATA[' . $item['name'] . ']]>';
    $text = $text . '</value>';
    fwrite($handle, $text);
}
fwrite($handle, '</values>');
fwrite($handle, '</params>');

// получаем функциональные группы
fwrite($handle, '<groups>');
$query = "SELECT `id`, `name` FROM `groups` WHERE 1 ORDER BY `id`";
$groups = $register->database->fetchAll($query);
foreach ($groups as $item) {
    echo 'group id='.$item['id'].PHP_EOL;
    $text = '<group id="' . md5($item['id']) . '">';
    $text = $text . '<name><![CDATA[' . $item['name'] . ']]></name>';
    // параметры подбора
    $text = $text . '<params>';
    $query = "SELECT `param_id`, `value_id` FROM `group_param_value` WHERE `group_id` = :id ORDER BY `param_id`, `value_id`";
    $params = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($params as $param) {
        $text = $text . '<param name="'.md5($param['param_id']).'" value="'.md5($param['value_id']).'" />';
    }
    $text = $text . '</params>';
    $text = $text . '</group>';
    fwrite($handle, $text);
}
fwrite($handle, '</groups>');

// все единицы измерения
fwrite($handle, '<units><unit id="1">руб/шт</unit><unit id="2">руб/компл</unit><unit id="3">руб/упак</unit>');
fwrite($handle, '<unit id="4">руб/метр</unit><unit id="5">руб/пара</unit></units>');

// получаем все товары
fwrite($handle, '<products>');
$query = "SELECT * FROM `products` WHERE 1 ORDER BY `id`";
$products = $register->database->fetchAll($query);
$i = 0;
foreach ($products as $item) {
    $i++;
    echo $i . ' product id=' . $item['id'] . PHP_EOL;
    $category = md5($item['category']);
    if (!empty($item['category2'])) $category = $category.','.md5($item['category2']);
    $text = '<product code="' . $item['code'] . '" category="' . $category . '" group="' . md5($item['group']) . '" maker="' . md5($item['maker']) . '"  hit="' . $item['hit'] . '" new="' . $item['new'] . '" sortorder="' . $item['sortorder'] . '">';
    $text = $text . '<name><![CDATA[' . $item['name'] . ']]></name>';
    if (!empty($item['title'])) {
        $text = $text . '<title><![CDATA[' . $item['title'] . ']]></title>';
    } else {
        $text = $text . '<title/>';
    }
    $text = $text . '<price>' . $item['price'] .  '</price>';
    $text = $text . '<price2>' . $item['price2'] .  '</price2>';
    $text = $text . '<price3>' . $item['price3'] .  '</price3>';
    $text = $text . '<price4>' . $item['price4'] .  '</price4>';
    $text = $text . '<price5>' . $item['price5'] .  '</price5>';
    $text = $text . '<price6>' . $item['price6'] .  '</price6>';
    $text = $text . '<price7>' . $item['price7'] .  '</price7>';
    $text = $text . '<unit>' . $item['unit'] .  '</unit>';
    if (!empty($item['shortdescr'])) {
        $text = $text . '<shortdescr><![CDATA[' . $item['shortdescr'] . ']]></shortdescr>';
    } else {
        $text = $text . '<shortdescr/>';
    }
    if (!empty($item['purpose'])) {
        $text = $text . '<purpose><![CDATA[' . $item['purpose'] . ']]></purpose>';
    } else {
        $text = $text . '<purpose/>';
    }
    $techdata = array();
    if (!empty($item['techdata'])) {
        $techdata = unserialize($item['techdata']);
    }
    $text = $text . '<techdata>';
    foreach ($techdata as $data) {
        $text = $text . '<item>';
        $text = $text . '<name><![CDATA[' . $data[0] . ']]></name>';
        $text = $text . '<value><![CDATA[' . $data[1] . ']]></value>';
        $text = $text . '</item>';
    }
    $text = $text . '</techdata>';
    if (!empty($item['features'])) {
        $text = $text . '<features><![CDATA[' . $item['features'] . ']]></features>';
    } else {
        $text = $text . '<features/>';
    }
    if (!empty($item['complect'])) {
        $text = $text . '<complect><![CDATA[' . $item['complect'] . ']]></complect>';
    } else {
        $text = $text . '<complect/>';
    }
    if (!empty($item['equipment'])) {
        $text = $text . '<equipment><![CDATA[' . $item['equipment'] . ']]></equipment>';
    } else {
        $text = $text . '<equipment/>';
    }
    if (!empty($item['padding'])) {
        $text = $text . '<padding><![CDATA[' . $item['padding'] . ']]></padding>';
    } else {
        $text = $text . '<padding/>';
    }
    $text = $text . '<image>' . $item['image'] . '</image>';
    // параметры подбора
    $text = $text . '<params>';
    $query = "SELECT `param_id`, `value_id` FROM `product_param_value` WHERE `product_id` = :id ORDER BY `param_id`, `value_id`";
    $params = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($params as $param) {
        $text = $text . '<param name="'.md5($param['param_id']).'" value="'.md5($param['value_id']).'" />';
    }
    $text = $text . '</params>';
    // файлы документации
    $text = $text . '<docs>';
    $query = "SELECT `doc_id` FROM `doc_prd` WHERE `prd_id` = :id";
    $docs = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($docs as $doc) {
        $text = $text . '<doc id="' . $doc['doc_id'] . '" />';
    }
    $text = $text . '</docs>';
    // сертификаты
    $text = $text . '<certs>';
    $query = "SELECT `cert_id` FROM `cert_prod` WHERE `prod_id` = :id";
    $certs = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($certs as $cert) {
        $text = $text . '<cert id="' . $cert['cert_id'] . '">';
    }
    $text = $text . '</certs>';
    // связанные товары
    $text = $text . '<linked>';
    $query = "SELECT `id2`, `sortorder` FROM `related` WHERE `id1` = :id ORDER BY `sortorder`";
    $prds = $register->database->fetchAll($query, array('id' => $item['id']));
    foreach ($prds as $prd) {
        $code = $prd['id2'];
        if (strlen($code) == 4) $code = '00'.$code;
        if (strlen($code) == 5) $code = '0'.$code;
        $count = 11 - $prd['sortorder'];
        $text = $text . '<prd code="' . $code . '" count="' . $count . '" />';
    }
    $text = $text . '</linked>';
    $text = $text . '</product>';
    fwrite($handle, $text);
}
fwrite($handle, '</products>');

// получаем все файлы документации
fwrite($handle, '<docs>');
$query = "SELECT `id`, `title`, `filename`, `md5` FROM `docs` WHERE 1 ORDER BY `id`";
$docs = $register->database->fetchAll($query);
$i = 0;
foreach ($docs as $item) {
    $i++;
    echo $i.' doc id='.$item['id'].PHP_EOL;
    $text = '<doc id="' . $item['id'] . '">';
    $text = $text . '<title><![CDATA[' . $item['title'] . ']]></title>';
    $text = $text . '<file>' . $item['filename'] . '</file>';
    $text = $text . '<md5>' . $item['md5'] . '</md5>';
    $text = $text . '</doc>';
    fwrite($handle, $text);
}
fwrite($handle, '</docs>');

fwrite($handle, '<certs></certs>');

fwrite($handle, '</catalog>');

fclose($handle);