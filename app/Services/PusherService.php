<?php

namespace App\Services;

use Pusher\Pusher;

class PusherService
{
    protected $pusher;

    public function __construct()
    {
        $options = [
            'cluster' => 'ap2',
            'useTLS' => true
        ];

        $this->pusher = new Pusher(
            '8e7dcbba961d12274052', // App Key
            'ed493c0128bba913cd18', // App Secret
            '2064757',              // App ID
            $options
        );
    }

    public function send(string $channel, string $event, array $data): \stdClass
    {
        return $this->pusher->trigger($channel, $event, $data);
    }

}
