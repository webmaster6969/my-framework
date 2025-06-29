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
            $rules = is_array($rules) ? $rules : explode('|', $rules);
            $valueExists = array_key_exists($field, $this->data);
            $value = $this->data[$field] ?? null;

            $hasRequired = in_array('required', $rules);

            foreach ($rules as $rule) {
                $params = [];

                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $method = 'validate' . ucfirst($ruleName);

                if (!method_exists($this, $method)) {
                    throw new ValidationNoFindMethodException("Method {$method} does not exist.");
                }

                if (!$hasRequired && (!$valueExists || empty($value))) {
                    break;
                }

                if (!$this->$method($value, ...$params)) {
                    $this->addError($field, $ruleName, $params);

                    if ($ruleName === 'required') {
                        break;
                    }
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
            'required' => 'Field :field is required',
            'email' => 'The :field must be a valid email address',
            'min' => 'The :field must contain at least :param characters',
            'max' => 'The :field must contain a maximum of :param characters.',
            'date_format' => 'The :field field must be in the :param format',
            'mimes' => 'The file in the :field must be one of the following formats: :param',
            'mimetypes' => 'The file in the :field must have a MIME type of: :param',
            'in' => 'The :field must be one of the following values: :param',
        ];

        $message = t($messages[$rule] ?? "Error in field :field");
        $message = str_replace(':field', t($field), $message);

        if (!empty($params)) {
            $translatedParams = array_map(fn($p) => t($p), $params);
            $message = str_replace(':param', implode(', ', $translatedParams), $message);
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

    /**
     * @param mixed $value
     * @param string ...$allowed
     * @return bool
     */
    protected function validateIn(mixed $value, string ...$allowed): bool
    {
        return is_string($value) && in_array($value, $allowed, true);
    }
}