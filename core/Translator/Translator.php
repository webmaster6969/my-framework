<?php

namespace Core\Translator;

use App\domain\Common\Domain\Exceptions\NotLoadFileTranslatorException;

class Translator
{
    /**
     * @var string
     */
    protected string $locale;

    /** @var array<string, string> */
    protected array $translations = [];

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var Translator|null
     */
    protected static ?Translator $instance = null;

    /**
     * @return Translator|null
     */
    public static function getInstance(): ?Translator
    {
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $path
     * @param string $locale
     * @return void
     */
    public static function init(string $path, string $locale = 'en'): void
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        self::$instance->locale = $locale;
        self::$instance->path = $path;
        self::$instance->loadTranslations();
    }

    /**
     * @return void
     */
    protected function loadTranslations(): void
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->locale . ".php";

        if (!file_exists($file)) {
            throw new NotLoadFileTranslatorException("Translation file not found: $file");
        }

        $rawTranslations = include $file;

        if (!is_array($rawTranslations)) {
            throw new \RuntimeException("Invalid translation format in file: $file");
        }

        $this->translations = [];
        foreach ($rawTranslations as $key => $value) {
            if (!is_string($value)) {
                throw new \RuntimeException("Translation value must be a string for key '$key'");
            }
            $this->translations[mb_strtolower((string)$key)] = $value;
        }
    }

    /** @param array<string, string> $replace */
    public static function get(string $key, array $replace = [], string $locale = 'en'): string
    {
        if (self::$instance === null) {
            throw new \LogicException("Translator not initialized.");
        }

        if (self::$instance->locale !== $locale) {
            self::$instance->locale = $locale;
            self::$instance->loadTranslations();
        }

        $lowerKey = mb_strtolower($key);
        $value = self::$instance->translations[$lowerKey] ?? null;
        $text = is_string($value) ? $value : $key;

        foreach ($replace as $k => $v) {
            $text = str_replace(":$k", (string)$v, $text);
        }

        return $text;
    }

    /**
     * @return void
     */
    public static function destroy(): void
    {
        self::$instance = null;
    }
}