<?php

declare(strict_types=1);

namespace App\Core\Services\RequestValidator;

use App\Core\Values\Translation;
use Illuminate\Support\MessageBag;

class ErrorMessageBag extends MessageBag
{
    /**
     * Add a message to the message bag.
     *
     * @param string $key
     * @param mixed $message
     *
     * @return $this|self
     */
    public function add($key, $message): static | self
    {
        if ($this->isUnique($key, $message)) {
            $this->messages[$key][] = $message;
        }

        return $this;
    }

    /**
     * Add a message to the message bag.
     *
     * @param string $key
     * @param mixed[]|string $message
     *
     * @return $this|self
     */
    public function set(string $key, string | array $message): static | self
    {
        $this->messages[$key] = [
            'message' => $message['message'] ?? $message,
        ];

        return $this;
    }

    /**
     * Get the number of messages in the message bag.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->messages);
    }

    /**
     * Add a message to the message bag.
     *
     * @param string $key
     * @param string $message
     * @param mixed[] $params
     *
     * @return $this
     */
    public function addWithParams(string $key, string $message, array $params): self
    {
        return $this->add($key, [
            'message' => $message,
            'params' => $params,
        ]);
    }

    /**
     * @param string $key
     * @param \App\Core\Values\Translation $translation
     *
     * @return \App\Core\Services\RequestValidator\ErrorMessageBag
     */
    public function setTranslation(string $key, Translation $translation): self
    {
        $this->messages[$key] = $translation;
        return $this;
    }

    /**
     * Format an array of messages.
     *
     * @param array $messages
     * @param string $format
     * @param string $messageKey
     *
     * @return array
     */
    protected function transform($messages, $format, $messageKey): array
    {
        return (array) $messages;
    }
}
