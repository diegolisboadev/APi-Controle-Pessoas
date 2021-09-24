<?php

namespace App\Listeners;

use App\Events\ControlePessoasEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Services\AMQPRabbitMQ;

class ControlePessoasListener
{

    private $amqprabbit;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->amqprabbit = new AMQPRabbitMQ();
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ControlePessoasEvent  $event
     * @return void
     */
    public function handle(ControlePessoasEvent $event)
    {
        //
        $this->amqprabbit->sendMessage($event->logMensagem());
    }
}
