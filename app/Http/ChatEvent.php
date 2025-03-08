<?php

namespace App\Http;

class ChatEvent
{
    public function __construct(
        public string $type,
        public mixed $data
    ) {}

    public function emit(): void
    {
        // todo: investigate if type can be echoed as event:
        if ($this->type === 'error') {
            echo "event: error\n";
        }
        echo 'data: '.json_encode($this->toArray())."\n\n";

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
        ];
    }

    public static function notice(string $token): self
    {
        return new self('notice', $token);
    }

    public static function update_thread_id(string $id): self
    {
        return new self('thread_id', $id);
    }

    public static function token(string $token): self
    {
        return new self('token', $token);
    }
}
