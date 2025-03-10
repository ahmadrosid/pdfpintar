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

    public static function update_thread(array $thread): self
    {
        return new self('thread', $thread);
    }

    public static function token(string $token): self
    {
        return new self('token', $token);
    }
}
