<?php

declare(strict_types=1);

namespace App\Core\Helpers;

use App\Core\Exceptions\InvalidJsonException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

final class Utils
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return null|string
     */
    public static function getOriginHost(Request $request): ?string
    {
        $origin = $request->header('Origin');
        if (! $origin) {
            return null;
        }

        /** @var string $origin */

        $parse = \parse_url($origin);

        if (! $parse) {
            return null;
        }

        return $parse['host'] ?? $parse['path'];
    }

    /**
     * Check if string is binary.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isBinary(string $str): bool
    {
        return \preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

    /**
     * @param array<mixed,mixed> $array
     * @param \Closure $callback (mixed $value, mixed $key) => [$key => $value]
     *
     * @return array<mixed,mixed>
     */
    public static function mapToAssociativeArray(array $array, \Closure $callback): array
    {
        return \array_merge(...\array_map($callback, $array, \array_keys($array)));
    }

    /**
     * Decode json.
     *
     * @param string $json
     * @param bool $assoc
     * @param int<1, 2048> $depth
     * @param int $options
     *
     * @return mixed
     *
     * @throws \App\Core\Exceptions\InvalidJsonException
     */
    public static function jsonDecode(string $json, bool $assoc = false, int $depth = 512, int $options = 0): mixed
    {
        try {
            $result = \json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR | $options);
        } catch (\JsonException $e) {
            throw new InvalidJsonException($e->getMessage());
        }

        if (\json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonException(\json_last_error_msg());
        }

        return $result;
    }

    /**
     * Check if given string is class name.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isClassName(string $value): bool
    {
        return \str_contains($value, '\\');
    }

    /**
     * Join given paths with system directory separator.
     *
     * @param null|string ...$paths
     *
     * @return string
     */
    public static function joinPaths(?string ...$paths): string
    {
        return self::correctFilesystemPath(\implode(DIRECTORY_SEPARATOR, $paths));
    }

    /**
     * Create unique file name.
     *
     * @param string $fileName
     * @param null|string $fileExtension
     * @param string $subDirectoryName
     *
     * @return string
     */
    public static function createFileNameForStorageFromString(
        string $fileName,
        ?string $fileExtension = null,
        string $subDirectoryName = 'i'
    ): string {
        /** @var array<array-key, string> $result */
        $result = \preg_split('~\B(?=(..)+$)~', \sprintf('%0.0f', $fileName));

        $filename = \str_pad((string) \array_pop($result), 2, '0', \STR_PAD_LEFT);

        if ($fileExtension !== null) {
            $filename .= '.' . $fileExtension;
        }

        $directories = \array_map(
            static fn ($dir): string => 'd' . \str_pad($dir, 2, '0', \STR_PAD_LEFT),
            $result
        );

        return \implode(\DIRECTORY_SEPARATOR, [...$directories, $subDirectoryName, $filename]);
    }

    /**
     * Fix filesystem path using correct directory separator.
     *
     * @param string $path
     *
     * @return string
     */
    public static function correctFilesystemPath(string $path): string
    {
        return (string) \preg_replace('~[/\\\\]+~', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Make URL slug from given string.
     *
     * @param string $slug
     *
     * @return string
     */
    public static function slugify(string $slug): string
    {
        return Str::slug($slug);
    }

    /**
     * Make URL slug from given string.
     *
     * @param string $value
     * @param string $separator
     *
     * @return string
     */
    public static function urify(string $value, string $separator = '-'): string
    {
        $title = Str::ascii($value);

        // Replace @ with the word 'at'
        $title = \str_replace('@', $separator . 'at' . $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, whitespace or "/".
        $title = (string) \preg_replace(
            '![^' . \preg_quote($separator, $separator) . '\pL\pN\s\/]+!u',
            '',
            Str::lower($title)
        );

        // Replace all separator characters and whitespace by a single separator
        $title = (string) \preg_replace('![' . \preg_quote($separator, $separator) . '\s]+!u', $separator, $title);

        return \trim($title, $separator);
    }

    /**
     * @param string $urlSlug
     * @param null|int $limitLength
     *
     * @return string
     */
    public static function sanitizeUrlSlug(
        string $urlSlug,
        ?int $limitLength = null
    ): string {
        $urlSlug = self::slugify($urlSlug);
        if ($limitLength) {
            $urlSlug = Str::limit($urlSlug, $limitLength, '');
        }

        return $urlSlug;
    }

    /**
     * Remove accents from string.
     *
     * @param string $text
     *
     * @return string
     */
    public static function removeAccents(string $text): string
    {
        static $table = [
            'Š' => 'S',
            'š' => 's',
            'Ð' => 'Dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'C' => 'C',
            'c' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ě' => 'e',
            'Ě' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'č' => 'c',
            'Č' => 'C',
            'ř' => 'r',
            'Ř' => 'R',
            'ů' => 'u',
            'Ů' => 'U',
            'ň' => 'n',
            'Ň' => 'N',
            'ť' => 't',
            'Ť' => 'T',
            'ď' => 'd',
            'Ď' => 'D',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'R' => 'R',
            'r' => 'r',
        ];
        return \strtr($text, $table);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     *
     * @return string
     */
    public static function strLimit(string $value, int $limit, string $end = '…'): string
    {
        if (\mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return \mb_strcut($value, 0, $limit - \mb_strlen($end), 'UTF-8') . $end;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param null|string $value
     * @param int $limit
     * @param string $end
     *
     * @return null|string
     */
    public static function strLimitOrNull(?string $value, int $limit, string $end = '…'): ?string
    {
        return $value === null ? null : self::strLimit($value, $limit, $end);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isValidUrl(string $value): bool
    {
        if (! \preg_match('~^(#|//|https?://|mailto:|tel:)~', $value)) {
            return \filter_var($value, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * Replace empty string with given replacement.
     *
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    public static function replaceEmptyStrings(array $data, mixed $replacement = null): array
    {
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = $replacement;
            }
        }

        return $data;
    }

    /**
     * @param array $haystack
     * @param mixed $needle
     *
     * @return mixed
     */
    public static function recursiveFind(array $haystack, mixed $needle): mixed
    {
        $recursive = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($haystack),
            RecursiveIteratorIterator::SELF_FIRST
        );
        return $recursive[$needle] ?? null;
    }

    /**
     * @param array $haystack
     * @param string $pattern
     *
     * @return array
     */
    public static function recursivePregFindAll(array $haystack, string $pattern): array
    {
        $recursive = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($haystack),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $result = [];
        foreach ($recursive as $key => $value) {
            if (\preg_match($pattern, (string) $key)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Check if array value under given key is null.
     *
     * @param mixed[] $data
     * @param null|int|string $key
     *
     * @return bool
     */
    public static function isArrayValueNull(array $data, int | string | null $key): bool
    {
        return $key !== null && \array_key_exists($key, $data) && $data[$key] === null;
    }

    /**
     * @param mixed $data
     * @param bool $strict
     *
     * @return bool
     */
    public static function isSerialized(mixed $data, bool $strict = true): bool
    {
        if (! \is_string($data)) {
            return false;
        }

        $data = \trim($data);

        if ($data === 'N;') {
            return true;
        }

        if (\strlen($data) < 4 || $data[1] !== ':') {
            return false;
        }

        if ($strict) {
            $lastChar = \substr($data, -1);
            if ($lastChar !== ';' && $lastChar !== '}') {
                return false;
            }
        } else {
            $semicolon = \strpos($data, ';');
            $brace = \strpos($data, '}');
            if (
                ($semicolon === false && $brace === false) ||
                ($semicolon !== false && $semicolon < 3) ||
                ($brace !== false && $brace < 4)
            ) {
                return false;
            }
        }

        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ($data[\strlen($data) - 2] !== '"') {
                        return false;
                    }
                } elseif (! \str_contains($data, '"')) {
                    return false;
                }
                // Or else fall through.
                // no break
            case 'a':
            case 'O':
                return (bool) \preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) \preg_match("/^{$token}:[0-9.E+-]+;${end}/", $data);
        }

        return false;
    }

    /**
     * @param string $pattern
     * @param string $value
     * @param int $limit
     * @param int $flags
     *
     * @return array<array-key, string>
     */
    public static function strictPregSplit(string $pattern, string $value, int $limit = -1, int $flags = 0): array
    {
        $split = \preg_split($pattern, $value, $limit, $flags);

        if ($split === false) {
            throw new \RuntimeException("Value {$value} could not be split using pattern {$pattern}");
        }

        return $split;
    }
}
