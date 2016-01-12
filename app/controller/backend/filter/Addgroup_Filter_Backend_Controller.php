<?php
/**
 * Класс Addgroup_Filter_Backend_Controller формирует страницу с формой
 * для добавления функциональной группы. Получает данные от модели
 * Filter_Backend_Model, административная часть сайта
 */
class Addgroup_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления функциональной группы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addgroup_Filter_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->filterBackendModel->getURL('backend/filter/addgroup'));
            } else {
                $this->redirect($this->filterBackendModel->getURL('backend/filter/allgroups'));
            }
        }

        $this->title = 'Новая группа. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->filterBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Фильтр',
                'url'  => $this->filterBackendModel->getURL('backend/filter/index')
            ),
            array(
                'name' => 'Группы',
                'url'  => $this->filterBackendModel->getURL('backend/filter/allgroups')
            ),
        );

        // получаем от модели массив параметров подбора для возможности привязки
        // параметров к новой функциональной группе
        $allParams = $this->filterBackendModel->getParams();

        // получаем от модели массив значений параметров подбора для возможности
        // привязки допустимых значений параметра к параметру
        $allValues = $this->filterBackendModel->getValues();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->filterBackendModel->getURL('backend/filter/addgroup'),
            // массив параметров подбора
            'allParams'   => $allParams,
            // массив значений параметров подбора
            'allValues'   => $allValues,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные администратором данные
        if ($this->issetSessionData('addFilterGroupForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addFilterGroupForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addFilterGroupForm');
        }

    }

    /**
     * Функция проверяет корректность введенных администратором данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция добавляет
     * функциональную группу и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование группы
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 100));
        // параметры, привязанные к группе
        $data['params_values'] = array();
        if (isset($_POST['params_values']) && is_array($_POST['params_values'])) {
            foreach ($_POST['params_values'] as $key => $value) {
                $data['params_values'][$key] = array();
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $data['params_values'][$key][] = $v;
                    }
                }
            }
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addFilterGroupForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новой группы
        $this->filterBackendModel->addGroup($data);

        return true;

    }

}