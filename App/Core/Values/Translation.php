<?php

declare(strict_types=1);

namespace App\Core\Values;

use Illuminate\Contracts\Translation\Translator;

final class Translation implements \Stringable
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var mixed[]
     */
    private array $replacements;

    /**
     * @var null|int
     */
    private ?int $choiceBy = null;

    private function __construct()
    {
        // not directly instantiable
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Make trans instance.
     *
     * @param \App\Core\Values\Translation|string $key
     * @param mixed[] $replacements
     *
     * @return \App\Core\Values\Translation
     */
    public static function make(string | self $key, array $replacements = []): self
    {
        if ($key instanceof self) {
            $key->replacements = \array_merge($replacements, $key->replacements);
            return $key;
        }

        $instance = new self();

        $instance->key = $key;
        $instance->replacements = $replacements;

        return $instance;
    }

    /**
     * Make trans instance with choice.
     *
     * @param \App\Core\Values\Translation|string $key
     * @param mixed[] $replacements
     * @param int $count
     *
     * @return \App\Core\Values\Translation
     */
    public static function makeChoice(
        string | self $key,
        int $count,
        array $replacements = []
    ): self {
        $instance = self::make($key, $replacements);

        $instance->choiceBy = $count;

        return $instance;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed[]
     */
    public function getReplacements(): array
    {
        return $this->replacements;
    }

    /**
     * To string using translator.
     *
     * @param null|\Illuminate\Contracts\Translation\Translator $translator
     *
     * @return string
     */
    public function toString(?Translator $translator = null): string
    {
        if (! $translator) {
            return $this->getKey();
        }

        if ($this->choiceBy !== null) {
            return $translator->choice($this->getKey(), $this->choiceBy, $this->getReplacements());
        }

        return $translator->get($this->getKey(), $this->getReplacements());
    }
}
