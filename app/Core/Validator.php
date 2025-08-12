<?php

namespace App\Core;

/**
 * Simple Form Validator.
 *
 * Provides a minimal validation system for common form rules.
 * Supported validation rules:
 * - `required` — Field must not be empty.
 * - `email` — Must be a valid email address.
 * - `min:N` — Minimum length of N characters.
 * - `max:N` — Maximum length of N characters.
 *
 * Example:
 * ```php
 * $result = Validator::make($_POST, [
 *     'name'  => 'required|min:3|max:50',
 *     'email' => 'required|email',
 * ]);
 *
 * if ($result['valid']) {
 *     // Process form
 * } else {
 *     // Show $result['errors']
 * }
 * ```
 *
 * @package App\Core
 */
final class Validator
{
    /**
     * Validate input data against a set of rules.
     *
     * @param array<string, mixed>  $input Input data (e.g., from `$_POST`).
     * @param array<string, string> $rules Validation rules for each field.
     *                                     Multiple rules can be separated by `|`.
     *
     * @return array{
     *     valid: bool,
     *     data: array<string, string>,
     *     errors: array<string, string>
     * }
     * Returns an associative array with:
     * - `valid`  → Boolean indicating if validation passed.
     * - `data`   → Sanitized field values.
     * - `errors` → Error messages for failed rules.
     */
    public static function make(array $input, array $rules): array
    {
        $errors = [];
        $data   = [];

        foreach ($rules as $field => $ruleStr) {
            $rulesArr = explode('|', $ruleStr);
            $val = trim((string) ($input[$field] ?? ''));

            foreach ($rulesArr as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);

                if ($name === 'required' && $val === '') {
                    $errors[$field] = 'This field is required.';
                }

                if ($val !== '' && $name === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email.';
                }

                if ($val !== '' && $name === 'max' && strlen($val) > (int) $arg) {
                    $errors[$field] = "Max {$arg} characters.";
                }

                if ($val !== '' && $name === 'min' && strlen($val) < (int) $arg) {
                    $errors[$field] = "Min {$arg} characters.";
                }
            }

            $data[$field] = $val;
        }

        return [
            'valid'  => empty($errors),
            'data'   => $data,
            'errors' => $errors
        ];
    }
}
