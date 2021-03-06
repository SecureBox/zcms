<?php
/**
 * Класс Rmvprd_Wished_Frontend_Controller отвечает за удаление товара из списка
 * отложенных, взаимодействует с моделью Wished_Frontend_Model, общедоступная часть
 * сайта
 */
class Rmvprd_Wished_Frontend_Controller extends Wished_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы.
     * В данном случае страницу нам формировать не нужно, и от модели ничего
     * получать не надо. Только удаление товара из списка отложенных и редирект
     * либо на страницу со списком отложенных товаров, либо обратно на страницу,
     * где была нажата кнопка «Удалить из отложенных».
     */
    protected function input() {

        // данные должны быть отправлены методом POST
        if ( ! $this->isPostMethod()) {
            $this->notFoundRecord = true;
            return;
        }

        // если не передан id товара или id товара не число
        if ( ! (isset($_POST['product_id']) && ctype_digit($_POST['product_id']) && $_POST['product_id'] > 0)) {
            $this->notFoundRecord = true;
            return;
        } else {
            $product_id = (int)$_POST['product_id'];
        }

        // удаляем товар из списка отложенных
        $this->wishedFrontendModel->RemoveFromWished($product_id);

        // куда перенаправить посетителя после удаления товара из списка отложенных?
        if ( ! isset($_POST['return'])) {
            $this->redirect($this->wishedFrontendModel->getURL('frontend/wished/index'));
        }

        $url = 'frontend/wished/index';
        if ($_POST['return'] == 'product') { // перенаправляем на страницу товара
            if (isset($_POST['return_prd_id']) && ctype_digit($_POST['return_prd_id'])) {
                $url = 'frontend/catalog/product/id/' . $_POST['return_prd_id'];
            }
        } elseif ($_POST['return'] == 'category') { // перенаправляем на страницу категории
            if (isset($_POST['return_ctg_id']) && ctype_digit($_POST['return_ctg_id'])) {
                $url = 'frontend/catalog/category/id/' . $_POST['return_ctg_id'];
                if (isset($_POST['group']) && ctype_digit($_POST['group']) && $_POST['group'] > 0) {
                    $url = $url . '/group/' . $_POST['group'];
                }
                if (isset($_POST['maker']) && ctype_digit($_POST['maker']) && $_POST['maker'] > 0) {
                    $url = $url . '/maker/' . $_POST['maker'];
                }
                if (isset($_POST['hit']) && $_POST['hit'] == 1) {
                    $url = $url . '/hit/1';
                }
                if (isset($_POST['new']) && $_POST['new'] == 1) {
                    $url = $url . '/new/1';
                }
                if (isset($_POST['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $_POST['param'])) {
                    $url = $url . '/param/' . $_POST['param'];
                }
                if (isset($_POST['sort']) && ctype_digit($_POST['sort']) && $_POST['sort'] > 0) {
                    $url = $url . '/sort/' . $_POST['sort'];
                }
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'maker') { // перенаправляем на страницу производителя
            if (isset($_POST['return_mkr_id']) && ctype_digit($_POST['return_mkr_id'])) {
                $url = 'frontend/catalog/maker/id/' . $_POST['return_mkr_id'];
                if (isset($_POST['group']) && ctype_digit($_POST['group']) && $_POST['group'] > 0) {
                    $url = $url . '/group/' . $_POST['group'];
                }
                if (isset($_POST['hit']) && $_POST['hit'] == 1) {
                    $url = $url . '/hit/1';
                }
                if (isset($_POST['new']) && $_POST['new'] == 1) {
                    $url = $url . '/new/1';
                }
                if (isset($_POST['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $_POST['param'])) {
                    $url = $url . '/param/' . $_POST['param'];
                }
                if (isset($_POST['sort']) && ctype_digit($_POST['sort']) && $_POST['sort'] > 0) {
                    $url = $url . '/sort/' . $_POST['sort'];
                }
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'group') { // перенаправляем на страницу функциональной группы
            if (isset($_POST['return_grp_id']) && ctype_digit($_POST['return_grp_id'])) {
                $url = 'frontend/catalog/group/id/' . $_POST['return_grp_id'];
                if (isset($_POST['maker']) && ctype_digit($_POST['maker']) && $_POST['maker'] > 0) {
                    $url = $url . '/maker/' . $_POST['maker'];
                }
                if (isset($_POST['hit']) && $_POST['hit'] == 1) {
                    $url = $url . '/hit/1';
                }
                if (isset($_POST['new']) && $_POST['new'] == 1) {
                    $url = $url . '/new/1';
                }
                if (isset($_POST['param']) && preg_match('~^\d+\.\d+(-\d+\.\d+)*$~', $_POST['param'])) {
                    $url = $url . '/param/' . $_POST['param'];
                }
                if (isset($_POST['sort']) && ctype_digit($_POST['sort']) && $_POST['sort'] > 0) {
                    $url = $url . '/sort/' . $_POST['sort'];
                }
                if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'wished') { // перенаправляем на страницу отложенных товаров
            $url = 'frontend/wished/index';
            if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                // если удален последний отложенный товар на странице, мы
                // дожны посетителя перенаправить на предыдущую страницу
                $totalPages = $this->wishedFrontendModel->getTotalPages();
                if ($_POST['page'] > $totalPages) {
                    if ($totalPages > 1) {
                        $url = $url . '/page/' . $totalPages;
                    }
                } else {
                    $url = $url . '/page/' . $_POST['page'];
                }
            }
        } elseif ($_POST['return'] == 'compare') { // перенаправляем на страницу сравнения товаров
            $url = 'frontend/compare/index';
        } elseif ($_POST['return'] == 'viewed') { // перенаправляем на страницу просмотрненных товаров
            $url = 'frontend/viewed/index';
            if (isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page'] > 1) {
                $url = $url . '/page/' . $_POST['page'];
            }
        }

        $this->redirect($this->wishedFrontendModel->getURL($url));

    }

}