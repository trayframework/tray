<?php
namespace Tray\Lib\Http;
class Request
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key];
    }

    public static function postDecoded(string $key, mixed $default = null): mixed
    {
        $encoded = $_POST[$key] ?? $default;
        if (!is_string($encoded)) return $default;
        $decoded = base64_decode($encoded, true);
        return $decoded !== false ? $decoded : $default;
    }

    public static function postEncoded(string $key, mixed $default = null): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? base64_encode($value) : '';
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return self::clean($_POST[$key] ?? $_GET[$key] ?? $default);
    }

    public static function file(string $key): mixed
    {
        return $_FILES[$key] ?? null;
    }

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function json(): mixed
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    public static function header(string $key, mixed $default = null): mixed
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        foreach ($headers as $name => $value) {
            if (strcasecmp($name, $key) === 0) {
                return $value;
            }
        }

        return $default;
    }

    public static function bearerToken(): ?string
    {
        $auth = self::header('Authorization');
        if ($auth && preg_match('/^Bearer\s+(.*)$/i', $auth, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public static function validate(array $rules, string $source = 'get', bool $nested = false): array
    {
        $data = $source === 'get' ? $_GET : $_POST;
        $result = [];

        foreach ($rules as $field => $type) {
            $value = self::getNested($data, $field);
            $result[$field] = self::validateField($value, $type);
        }

        return $nested ? self::expandDotKeys($result) : $result;
    }

    protected static function validateField(mixed $value, string $type): mixed
    {
        $value = is_string($value) ? trim($value) : $value;
        return match ($type) {
            'int'    => self::isIntegerLike($value) ? (int)$value : 0,
            'float'  => is_numeric($value) ? (float)$value : 0.0,
            'email'  => filter_var($value, FILTER_VALIDATE_EMAIL) ?: '',
            'bool'   => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'string' => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'),
            'html'   => self::sanitizeHtml($value),
            'base64' => base64_decode($value, true) ?: '',
            'raw'    => $value,
            default  => null,
        };
    }

    public static function isIntegerLike($value): bool
    {
        return is_numeric($value) && (string)(int)$value === (string)trim($value);
    }
    public static function sanitizeHtml(?string $html, array $allowedTags = null): string
    {
        if ($html === null) return '';

        $allowed = $allowedTags ?? [
            'p', 'br', 'b', 'strong', 'i', 'em', 'u', 'ul', 'ol', 'li',
            'a', 'img', 'h1', 'h2', 'h3', 'blockquote', 'code', 'table',
            'thead', 'tbody', 'tr', 'th', 'td', 'span', 'div'
        ];

        $allowedList = '<' . implode('><', $allowed) . '>';

        $cleaned = strip_tags($html, $allowedList);
        $cleaned = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $cleaned);
        $cleaned = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $cleaned);
        $cleaned = preg_replace('#on[a-z]+=".*?"#i', '', $cleaned);
        $cleaned = preg_replace('#javascript:#i', '', $cleaned);

        return $cleaned;
    }
    public static function set(string $key, mixed $default = null,$method='get'): mixed
    {
        $key = self::name2key($name);
        if($method == 'post') {
            $_POST[$key] = self::clean($default);
        }
        else {
            $_GET[$key] = self::clean($default);
        }
    }
    protected static function name2key(string $name): string
    {
        $name = trim($name);
        if($name == ''){
            print_r('The stack name cannot be empty.');
            exit;
        }
        return strtolower($name);
    }
    protected static function clean(mixed $value): mixed
    {
        if (is_array($value)) return self::cleanArray($value);
        return is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
    }

    protected static function cleanArray(array $array): array
    {
        return array_map([self::class, 'clean'], $array);
    }

    protected static function getNested(array $array, string $key): mixed
    {
        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return null;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    protected static function expandDotKeys(array $flat): array
    {
        $output = [];
        foreach ($flat as $dotKey => $value) {
            $segments = explode('.', $dotKey);
            $ref = &$output;
            foreach ($segments as $segment) {
                if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref = &$ref[$segment];
            }
            $ref = $value;
        }
        return $output;
    }
}
