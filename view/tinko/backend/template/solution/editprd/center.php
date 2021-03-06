<?php
/**
 * Страница с формой для редактирования товара типового решения,
 * файл view/example/backend/template/solutions/editprd/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $require - должен быть обязательно в комплекте?
 * $code - код (артикул) товара
 * $name - торговое наименование изделия
 * $title - функциональное наименование изделия
 * $shortdescr - краткое описание
 * $price - цена
 * $unit - единица измерения
 * $units - единицы измерения для возможности выбора
 * $count - количество
 * $changeable - изменяемое количество?
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/editprd/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование товара</h1>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $code       = htmlspecialchars($code);
    $name       = htmlspecialchars($name);
    $title      = htmlspecialchars($title);
    $shortdescr = htmlspecialchars($shortdescr);
    if (isset($savedFormData)) {
        $group      = $savedFormData['group'];
        $code       = htmlspecialchars($savedFormData['code']);
        $name       = htmlspecialchars($savedFormData['name']);
        $title      = htmlspecialchars($savedFormData['title']);
        $shortdescr = htmlspecialchars($savedFormData['shortdescr']);
        $price      = $savedFormData['price'];
        $unit       = $savedFormData['unit'];
        $count      = $savedFormData['count'];
        $changeable = $savedFormData['changeable'];
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-product">
    <div>
        <div>Код (артикул)</div>
        <div>
            <input type="text" name="code" maxlength="16" value="<?php echo $code; ?>" />
            <span id="load-by-code">загрузить товар</span>
            <input type="checkbox" name="require"<?php echo $require ? ' checked="checked"' : ''; ?> value="1" /> обязательно должен быть
        </div>
    </div>
    <div>
        <div>Торговое наименование</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Функциональное наименование</div>
        <div><input type="text" name="title" maxlength="250" value="<?php echo $title; ?>" /></div>
    </div>
    <div>
        <div>Краткое описание</div>
        <div><textarea name="shortdescr"><?php echo $shortdescr; ?></textarea></div>
    </div>
    <div>
        <div>Группа (тип)</div>
        <div>
            <select name="group">
                <?php foreach ($groups as $item): ?>
                    <option value="<?php echo $item['id']; ?>"<?php if ($group == $item['id']) echo ' selected="selected"'; ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div id="price-count-note">
        <div>Цена, количество, сноска</div>
        <div>
            <span>цена <input type="text" name="price" value="<?php echo $price; ?>" /></span>
            <span>
                ед.изм.
                <?php if (!empty($units)): ?>
                    <select name="unit">
                        <?php foreach ($units as $key => $value): ?>
                            <option value="<?php echo $key; ?>"<?php if ($key == $unit) echo ' selected="selected"'; ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </span>
            <span>кол. <input type="text" name="count" value="<?php echo $count; ?>" /></span>
            <span><input type="checkbox" name="changeable"<?php echo $changeable ? ' checked="checked"' : ''; ?> value="1" /> изменяемое кол-во?</span>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/editprd/center.php -->
