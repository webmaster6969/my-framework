<?php

declare(strict_types=1);

namespace Core\Validator;

use App\domain\Common\Domain\Exceptions\ValidationNoFindMethodException;

class Validator
{
    /**
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * @var array<string, string|string[]> $rules
     */
    protected array $rules;

    /**
     * @var array<string, string[]>
     */
    protected array $errors = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string|string[]> $rules
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * @return bool
     */
    public function fails(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $rules = is_array($rules) ? $rules : explode('|', (string)$rules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $method = 'validate' . ucfirst($ruleName);

                if (method_exists($this, $method)) {
                    if (!$this->$method($value, ...$params)) {
                        $this->addError($field, $ruleName, $params);
                    }
                } else {
                    throw new ValidationNoFindMethodException('Method ' . $method . ' does not exist.');
                }
            }
        }

        return !empty($this->errors);
    }

    /**
     * @param string $field
     * @param string $rule
     * @param array<int, string> $params
     */
    protected function addError(string $field, string $rule, array $params = []): void
    {
        $messages = [
            'required' => 'Поле :field обязательно для заполнения.',
            'email' => 'Поле :field должно быть валидным email адресом.',
            'min' => 'Поле :field должно содержать минимум :param символов.',
            'max' => 'Поле :field должно содержать максимум :param символов.',
            'date_format' => 'Поле :field должно быть в формате :param.',
            'mimes' => 'Файл в поле :field должен быть одного из следующих типов: :param.',
            'mimetypes' => 'Файл в поле :field должен иметь MIME-тип: :param.',
        ];

        $message = $messages[$rule] ?? "Ошибка в поле :field.";
        $message = str_replace(':field', $field, $message);

        if (!empty($params)) {
            $message = str_replace(':param', $params[0], $message);
        }

        $this->errors[$field][] = $message;
    }

    /**
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function validateRequired(mixed $value): bool
    {
        return !is_null($value) && $value !== '';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function validateEmail(mixed $value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param mixed $value
     * @param string $min
     * @return bool
     */
    protected function validateMin(mixed $value, string $min): bool
    {
        return is_string($value) && mb_strlen($value) >= (int)$min;
    }

    /**
     * @param mixed $value
     * @param string $max
     * @return bool
     */
    protected function validateMax(mixed $value, string $max): bool
    {
        return is_string($value) && mb_strlen($value) <= (int)$max;
    }

    /**
     * @param mixed $value
     * @param string $format
     * @return bool
     */
    protected function validateDateFormat(mixed $value, string $format): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $dt = \DateTime::createFromFormat($format, $value);
        return $dt && $dt->format($format) === $value;
    }

    /**
     * @param mixed $value
     * @param string ...$extensions
     * @return bool
     */
    protected function validateMimes(mixed $value, string ...$extensions): bool
    {
        if (!is_array($value) || !isset($value['name']) || !is_string($value['name'])) {
            return false;
        }

        $fileExtension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));
        return in_array($fileExtension, $extensions, true);
    }

    /**
     * @param mixed $value
     * @param string ...$mimetypes
     * @return bool
     */
    protected function validateMimetypes(mixed $value, string ...$mimetypes): bool
    {
        if (!is_array($value) || !isset($value['tmp_name']) || !is_string($value['tmp_name']) || !is_file($value['tmp_name'])) {
            return false;
        }

        $fInfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$fInfo) {
            return false;
        }

        $mime = finfo_file($fInfo, $value['tmp_name']);
        finfo_close($fInfo);

        return $mime !== false && in_array($mime, $mimetypes, true);
    }
}