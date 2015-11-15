<?php
/**
 * Класс Category_Solutions_Backend_Controller формирует страницу со списком типовых
 * решений выбранной категории. Получает данные от модели Solutions_Backend_Model,
 * административная часть сайта
 */
class Category_Solutions_Backend_Controller extends Solutions_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * со списком типовых решений выбранной категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Solutions_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Category_Solutions_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о категории
        $name = $this->solutionsBackendModel->getCategoryName($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        $this->title = $name . '. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->solutionsBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Типовые решения',
                'url'  => $this->solutionsBackendModel->getURL('backend/solutions/index')
            ),
            array(
                'name' => 'Категории',
                'url' => $this->solutionsBackendModel->getURL('backend/solutions/allctgs')
            ),
        );

        // получаем от модели массив типовых решений выбранной категории
        $solutions = $this->solutionsBackendModel->getCategorySolutions($this->params['id']);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // URL страницы с формой для добавления типового решения
            'addSltnUrl'  => $this->solutionsBackendModel->getURL('backend/solutions/addsltn'),
            // уникальный идентификатор категории
            'id'          => $this->params['id'],
            // наименование категории
            'name'        => $name,
            // массив типовых решений выбранной категории
            'solutions'   => $solutions,
        );

    }

}