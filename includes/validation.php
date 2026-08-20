<?php
/**
 * Input Validation Helpers
 * Kamadenu Goushala
 */

class Validator {
    private array $errors = [];
    private array $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    /**
     * Validate that a field is not empty
     */
    public function required(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if ($value === '') {
            $this->errors[$field] = "{$label} is required.";
        }
        
        return $this;
    }
    
    /**
     * Validate email format
     */
    public function email(string $field, string $label = 'Email'): self {
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }
        
        return $this;
    }
    
    /**
     * Validate phone number (Indian format)
     */
    public function phone(string $field, string $label = 'Phone'): self {
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && !preg_match('/^(\+91[\-\s]?)?[6-9]\d{9}$/', preg_replace('/[\s\-]/', '', $value))) {
            $this->errors[$field] = "{$label} must be a valid Indian phone number.";
        }
        
        return $this;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength(string $field, int $min, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = "{$label} must be at least {$min} characters.";
        }
        
        return $this;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength(string $field, int $max, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }
        
        return $this;
    }
    
    /**
     * Validate numeric value
     */
    public function numeric(string $field, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field] = "{$label} must be a number.";
        }
        
        return $this;
    }
    
    /**
     * Validate minimum numeric value
     */
    public function min(string $field, float $min, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && is_numeric($value) && (float)$value < $min) {
            $this->errors[$field] = "{$label} must be at least {$min}.";
        }
        
        return $this;
    }
    
    /**
     * Validate that value is in a given list
     */
    public function in(string $field, array $allowed, string $label = ''): self {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim($this->data[$field] ?? '');
        
        if (!empty($value) && !in_array($value, $allowed)) {
            $this->errors[$field] = "{$label} contains an invalid value.";
        }
        
        return $this;
    }
    
    /**
     * Check if validation passed
     */
    public function passes(): bool {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     */
    public function fails(): bool {
        return !$this->passes();
    }
    
    /**
     * Get all validation errors
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Get the first error message
     */
    public function getFirstError(): string {
        return reset($this->errors) ?: '';
    }
    
    /**
     * Add a custom error
     */
    public function addError(string $field, string $message): self {
        $this->errors[$field] = $message;
        return $this;
    }
}
