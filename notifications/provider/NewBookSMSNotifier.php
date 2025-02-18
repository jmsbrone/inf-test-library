<?php

namespace app\notifications\provider;

use app\models\Book;
use app\models\Subscription;
use app\notifications\NewBookNotifierInterface;
use app\notifications\presenters\NewBookNotificationPresenter;
use Exception;
use Yii;

/**
 * Уведомление по SMS о новой книге через сервис smspilot.ru
 */
class NewBookSMSNotifier implements NewBookNotifierInterface
{
    public function __construct(
        protected NewBookNotificationPresenter $presenter,
    ) { }

    /**
     * @param Book $book
     * @param Subscription $authorSubscription
     *
     * @return void
     * @throws Exception
     */
    public function notify(Book $book, Subscription $authorSubscription): void
    {
        $smspilotSendUrl = sprintf(
            'https://smspilot.ru/api.php?send=%s&from=%s&to=%s&apikey=%s&format=json',
            $this->presenter->getMessageText($book),
            'INFORM',
            trim($authorSubscription->phone_number, '+\s-'),
            Yii::$app->params['smspilotApiKey'],
        );

        $result = file_get_contents($smspilotSendUrl);
        $resultData = json_decode($result, true);

        if (isset($resultData->error)) {
            throw new Exception('Failed to send SMS');
        }
    }
}
