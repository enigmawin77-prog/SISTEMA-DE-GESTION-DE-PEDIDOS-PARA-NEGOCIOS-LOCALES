<?php
namespace App\Validators;

class Validator {
    private array $data;
    private array $errors = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required(string $field, string $label): self {
        $val = trim($this->data[$field] ?? '');
        if ($val === '') {
            $this->errors[$field] = "El campo '{$label}' es obligatorio.";
        }
        return $this;
    }

    public function email(string $field, string $label): self {
        $val = trim($this->data[$field] ?? '');
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "El campo '{$label}' debe ser un correo electrónico válido.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label): self {
        $val = trim($this->data[$field] ?? '');
        if ($val !== '' && mb_strlen($val) < $min) {
            $this->errors[$field] = "El campo '{$label}' debe tener al menos {$min} caracteres.";
        }
        return $this;
    }

    public function numeric(string $field, string $label, float $min = 0): self {
        $val = $this->data[$field] ?? null;
        if ($val !== null && $val !== '') {
            if (!is_numeric($val) || (float)$val < $min) {
                $this->errors[$field] = "El campo '{$label}' debe ser un número igual o mayor a {$min}.";
            }
        }
        return $this;
    }

    public function inArray(string $field, array $allowed, string $label): self {
        $val = $this->data[$field] ?? '';
        if (!in_array($val, $allowed, true)) {
            $this->errors[$field] = "El valor seleccionado para '{$label}' no es válido.";
        }
        return $this;
    }

    public function isValid(): bool {
        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getFirstError(): ?string {
        return reset($this->errors) ?: null;
    }
}
