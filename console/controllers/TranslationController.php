<?php

namespace console\controllers;

use yii\console\Controller;
use yii\db\Migration;

class TranslationController extends Controller {
    public function actionUp()
    {
        $this->actionDown();
        $this->addMessages('app', $this->getTranslations());
    }

    public function actionDown()
    {
        $this->deleteMessages('app', $this->getTranslations());
    }

    private function getTranslations()
    {
        return [
            'Home' => ['ru' => 'Главная'],
            'Login' => ['ru' => 'Вход'],
            'Signup' => ['ru' => 'Регистрация'],
            'Logout' => ['ru' => 'Выход'],

            'Email' => ['ru' => 'Электронная почта'],
            'First name' => ['ru' => 'Имя'],
            'Last name' => ['ru' => 'Фамилия'],

            'Origin' => ['ru' => 'Место отправления'],
            'Destination' => ['ru' => 'Место назначения'],
            'Where are you from ...' => ['ru' => 'Откуда Вы хотите вылетать...'],
            'Where are you going to ...' => ['ru' => 'Куда Вы направляетесь'],
            'Flight dates range' => ['ru' => 'Интервал дат вылета'],
            'Travel period range' => ['ru' => 'Продолжительность пребывания'],
            'days' => ['ru' => 'дней'],
            'City, country, continent ...' => ['ru' => 'Город, страна, континент ...'],

            'Track now!' => ['ru' => 'Найти лучшие цены!'],
            'Send' => ['ru' => 'Отправить'],
            'New request' => ['ru' => 'Новый запрос'],

            'More details' => ['ru' => 'Дополнительно'],

            'Congratulations!' => ['ru' => 'Поздравляем!'],
            'Well done!' => ['ru' => 'Превосходно!'],

            'I need to know your email to send link to login' => ['ru' => 'Мне нужно знать email куда отправить ссылку для входа'],
            'Please fill out the following fields to signup' => ['ru' => 'Пожалуйста, заполните поля для регистрации'],
            'You are in one step to get the best air fares!' => ['ru' => 'Всего один шаг, чтобы получить лучшие цены!'],
            'You are successfully logged in' => ['ru' => 'Вы успешно авторизованы'],
            'Relax while I\'m doing my work!' => ['ru' => 'Отдохните, а я пока поработаю!'],
            'The request has been placed successfully' => ['ru' => 'Запрос успешно размещен'],
            'You are successfully logged out' => ['ru' => 'Вы успешно вышли'],
            'Please signup or login before request to let me know where to send search results' => ['ru' => 'Пожалуйста, зарегистрируйтесь или авторизуйтесь, чтобы я мог отправить результаты поиска'],
            'The authorization email has been sent' => ['ru' => 'Сообщение для авторизации отправлено'],
            'Could not find user with specified email' => ['ru' => 'Не могу найти пользователя с этим адресом'],

            'My requests' => ['ru' => 'Мои запросы'],
            'Created at' => ['ru' => 'Когда добавлен'],
            'Created by' => ['ru' => 'Кем добавлен'],
            'Origin place' => ['ru' => 'Место отправления'],
            'Destination place' => ['ru' => 'Место назначения'],
            'Starts at' => ['ru' => 'Вылет с'],
            'Ends at' => ['ru' => 'Вылет по'],
            'From, days' => ['ru' => 'От, дней'],
            'To, days' => ['ru' => 'До, дней'],
            'Status' => ['ru' => 'Статус'],
            'Mail sent' => ['ru' => 'Сообщение отправлено'],
            'Currency' => ['ru' => 'Валюта'],

            'Start request' => ['ru' => 'Возобновить'],
            'Pause request' => ['ru' => 'Приостановить'],

            'The request has been deleted' => ['ru' => 'Запрос был удален'],
            'The request has been paused'  => ['ru' => 'Запрос был приостановлен'],
            'The request has been started' => ['ru' => 'Запрос был возобновлен'],

            'Active' => ['ru' => 'Активен'],
            'Inactive' => ['ru' => 'Приостановлен'],
            'Deleted' => ['ru' => 'Удален'],
            'Outdated' => ['ru' => 'Просрочен'],

            'Wrong status' => ['ru' => 'Неверный статус'],

        ];
    }

    private function addMessages($category = 'app', array $translationsData)
    {
        foreach ($translationsData as $sourceMessage => $translationItem) {
            $this->addMessage($category, $sourceMessage, $translationItem);
        }
    }

    private function addMessage($category = 'app', $sourceMessage, array $translations)
    {
        $migration = new Migration();

        $migration->insert('source_message', [
            'category' => $category,
            'message' => $sourceMessage,
        ]);
        $sourceMessageId = $migration->db->getLastInsertID();
        foreach ($translations as $languageCode => $translation) {
            $migration->insert('message', [
                'id' => $sourceMessageId,
                'language' => $languageCode,
                'translation' => $translation,
            ]);
        }
    }

    private function deleteMessages($category = 'app', array $translationsData)
    {
        foreach ($translationsData as $sourceMessage => $translationItem) {
            $this->deleteMessage($category, $sourceMessage);
        }
    }

    private function deleteMessage($category = 'app', $sourceMessage)
    {
        $migration = new Migration();
        $migration->delete('source_message', [
            'category' => $category,
            'message' => $sourceMessage,
        ]);
    }
}