<?php

namespace App\Events;

use App\Models\ControlePessoas;

class ControlePessoasEvent extends Event
{

    /**
     * Log de mensagem
     *
     * @var string $log
     */
    private $log;

    /**
     * Create a new event instance.
     *
     * @param string $log
     * @return void
     */
    public function __construct(string $log)
    {
        $this->log = $log;
    }

    /**
     *
     */
    public function logMensagem() {
        return $this->log;
    }

}
