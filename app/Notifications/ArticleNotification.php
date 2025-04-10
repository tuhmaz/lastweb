<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Services\OneSignalService;

class ArticleNotification extends Notification
{
    use Queueable;

    public $article;
    protected $oneSignalService;

    public function __construct($article)
    {
        $this->article = $article;
        $this->oneSignalService = new OneSignalService();
    }

    public function via($notifiable)
    {
        return ['database', 'onesignal'];
    }

    public function toOneSignal($notifiable)
    {
        $country = session('country', 'jordan');
        $url = route('dashboard.articles.show', ['article' => $this->article->id, 'country' => $country]);
        
        return $this->oneSignalService->sendNotification(
            'New Article Published',
            $this->article->title,
            $url,
            [
                'article_id' => $this->article->id,
                'type' => 'article'
            ]
        );
    }

    public function toArray($notifiable)
    {
        $country = session('country', 'jordan');
        return [
            'title' => 'New Article: ' . $this->article->title,
            'article_id' => $this->article->id,
            'url' => route('dashboard.articles.show', ['article' => $this->article->id, 'country' => $country]),
        ];
    }
}
