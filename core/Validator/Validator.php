<?php

namespace Core\Validator;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function fails(): bool
    {
        $this->errors = []; // reset errors

        foreach ($this->rules as $field => $rules) {
            $rules = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $method = 'validate' . ucfirst($ruleName);

                if (method_exists($this, $method)) {
                    if (!$this->$method($value, ...$params)) {
                        $this->addError($field, $ruleName, $params);
                    }
                }
            }
        }

        return !empty($this->errors);
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $messages = [
            'required'     => 'Поле :field обязательно для заполнения.',
            'email'        => 'Поле :field должно быть валидным email адресом.',
            'min'          => 'Поле :field должно содержать минимум :param символов.',
            'max'          => 'Поле :field должно содержать максимум :param символов.',
            'date_format'  => 'Поле :field должно быть в формате :param.',
            'mimes'        => 'Файл в поле :field должен быть одного из следующих типов: :param.',
            'mimetypes'    => 'Файл в поле :field должен иметь MIME-тип: :param.',
        ];

        $message = $messages[$rule] ?? "Ошибка в поле :field.";

        $message = str_replace(':field', $field, $message);
        if (!empty($params)) {
            $message = str_replace(':param', $params[0], $message);
        }

        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    // === Правила ===

    protected function validateRequired($value): bool
    {
        return !is_null($value) && $value !== '';
    }

    protected function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin($value, $min): bool
    {
        return mb_strlen((string)$value) >= (int)$min;
    }

    protected function validateMax($value, $max): bool
    {
        return mb_strlen((string)$value) <= (int)$max;
    }

    protected function validateDateFormat($value, $format): bool
    {
        if (empty($value)) {
            return false;
        }

        $dt = \DateTime::createFromFormat($format, $value);
        return $dt && $dt->format($format) === $value;
    }

    protected function validateMimes($value, ...$extensions): bool
    {
        if (!isset($value['name'])) return false;

        $fileExtension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));
        return in_array($fileExtension, $extensions);
    }

    protected function validateMimetypes($value, ...$mimetypes): bool
    {
        if (!isset($value['tmp_name']) || !is_file($value['tmp_name'])) {
            return false;
        }

        $fInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fInfo, $value['tmp_name']);
        finfo_close($fInfo);

        return in_array($mime, $mimetypes);
    }
}