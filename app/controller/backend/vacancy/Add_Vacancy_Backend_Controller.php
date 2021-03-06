<?php
/**
 * Класс Add_Vacancy_Backend_Controller для добавления новой вакансии, формирует страницу с
 * формой для добавления вакансии, добавляет запись в таблицу БД vacancies, работает с моделью
 * Vacancy_Backend_Model, административная часть сайта
 */
class Add_Vacancy_Backend_Controller extends Vacancy_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы с формой
     * для добавления вакансии; в данном случае никаких данных получать не нужно
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Vacancy_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Add_Vacancy_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление вакансии прошло успешно
                $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/index'));
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->vacancyBackendModel->getURL('backend/vacancy/add'));
            }
        }

        $this->title = 'Новая вакансия. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->vacancyBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->vacancyBackendModel->getURL('backend/vacancy/index'), 'name' => 'Вакансии'),
        );

        $details = array(
            array(
                'name'  => 'Требования',
                'items' => array()
            ),
            array(
                'name'  => 'Обязанности',
                'items' => array()
            ),
            array(
                'name'  => 'Условия',
                'items' => array()
            ),
        );

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->vacancyBackendModel->getURL('backend/vacancy/add'),
            // подробная информация о вакансии
            'details'     => $details,

        );
        // если были ошибки при заполнении формы, передаем в шаблон сохраненные
        // данные формы и массив сообщений об ошибках
        if ($this->issetSessionData('addVacancyForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addVacancyForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addVacancyForm');
        }
    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет вакансию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name'] = trim(iconv_substr($_POST['name'], 0, 100));     // название вакансии

        $data['visible'] = 0;
        if (isset($_POST['visible'])) {
            $data['visible'] = 1;
        }

        // подробная информация о вакансии
        $details = array();
        $error = 0;
        if (isset($_POST['names']) && is_array($_POST['names'])) {
            $count = 0;
            foreach ($_POST['names'] as $key => $name) {
                $name = trim(iconv_substr($name, 0, 100));
                $items = array();
                if (isset($_POST['items'][$key]) && is_array($_POST['items'][$key])) {
                    foreach ($_POST['items'][$key] as $item) {
                        if (empty($item)) continue;
                        $items[] = trim(iconv_substr($item, 0, 100));
                    }
                }
                if ( (empty($name) && !empty($items)) || (!empty($name) && empty($items))) {
                    $error = $error + 1;
                }
                if (empty($name) && empty($items)) {
                    continue;
                }
                $details[$count] = array(
                    'name' => $name,
                    'items' => $items,
                );
                $count++;
            }
        }

        if (empty($details)) {
            $details = array(
                array(
                    'name'  => 'Требования',
                    'items' => array()
                ),
                array(
                    'name'  => 'Обязанности',
                    'items' => array()
                ),
                array(
                    'name'  => 'Условия',
                    'items' => array()
                ),
            );
        }
        $data['details'] = $details;

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название вакансии»';
        }
        if ($error) {
            $errorMessage[] = 'Не заполнены обязательные поля «Условия и требования»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addVacancyForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новой вакансии
        $this->vacancyBackendModel->addVacancy($data);

        return true;

    }

}