<?php

function sanitize_string(string $value): string {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $value;
}

function hash_password(string $raw_password): string {
    return password_hash($raw_password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password(string $raw_password, string $hash): bool {
    return password_verify($raw_password, $hash);
}

function sanitize_email(string $value): string {
    $value = trim($value);
    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
    return strtolower($value);
}

function sanitize_phone(string $value): string {
    $value = preg_replace('/\D/', '', $value);
    if (strlen($value) === 11 && str_starts_with($value, '0')) {
        $value = substr($value, 1); // store as 10 chars to fit varchar(10)
    }
    return $value;
}

function sanitize_int(mixed $value): int {
    return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}

function sanitize_price(string $value): string {
    $value = trim($value);
    $value = preg_replace('/[^0-9.\-]/', '', $value);
    return $value;
}

function sanitize_image_filename(string $filename): string {
    $filename = basename($filename);
    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $base     = pathinfo($filename, PATHINFO_FILENAME);
    $base     = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $base);
    return $base . '.' . $ext;
}

function sanitize_text(string $value): string {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $value;
}

// ============================================================
//  OUTPUT ESCAPING
// ============================================================


function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}