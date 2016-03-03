<?php
/**
 * Страница со списком всех функциональных групп,
 * файл view/example/frontend/template/catalog/groups/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $groups - массив всех функциональных групп
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/groups/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
    <?php foreach ($breadcrumbs as $item): ?>
        <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
    <?php endforeach; ?>
    </div>
<?php endif; ?>


<div class="center-block">
    <div><h1>Функционал</h1></div>
    <div class="no-padding">
        <div id="all-groups">
            <?php $divide = ceil(count($groups)/2); ?>
            <ul>
            <?php foreach ($groups as $key => $item): ?>
                <li>
                    <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                </li>
                <?php if ($divide == ($key+1)): ?>
                    </ul><ul>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Конец шаблона view/example/frontend/template/catalog/groups/center.php -->