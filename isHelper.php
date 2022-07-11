<?php declare(strict_types=1);

/**
 * isHelper
 * --------------------------------------------------------
 * This content is released under the MIT License (MIT)
 * --------------------------------------------------------
 * @author Roman Kelesidis
 * @license https://opensource.org/licenses/MIT	(MIT License)
 * @filesource
 */
final class isHelper
{
    protected static $validUrlPrefixes = array('http://', 'https://', 'ftp://'); // Array with url prefixes

    public static function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    public static function isArray($value): bool
    {
        return is_array($value);
    }

    public static function isInteger($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_INT);
    }

    public static function isAlpha($value): bool
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    public static function isAlphaNum($value): bool
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    public static function isFloat($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_FLOAT);
    }

    public static function isBool($value): bool
    {
        return is_bool(filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE));
    }

    public static function isURL($value): bool
    {
        foreach (self::$validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                return filter_var($value, \FILTER_VALIDATE_URL) !== false;
            }
        }
        return false;
    }

    public static function isURLActive($value): bool
    {
        foreach (self::$validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                $host = parse_url(strtolower($value), PHP_URL_HOST);
                return checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME');
            }
        }
        return false;
    }

    public static function isURI($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")));
    }

    public static function isEmail($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL);
    }

    public static function isEmailDNS($value): bool
    {
        if (self::IsEmail($value)) {
            $domain = ltrim(stristr($value, '@'), '@') . '.';
            if (function_exists('idn_to_ascii') && defined('INTL_IDNA_VARIANT_UTS46')) {
                $domain = idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
            }
            return checkdnsrr($domain, 'MX');
        }
        return false;
    }

    public static function isIP($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_IP);
    }

    public static function isIPv4($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
    }

    public static function isIPv6($value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6);
    }

    public static function isASCII($value): bool
    {
        if (function_exists('mb_detect_encoding')) {
            return mb_detect_encoding($value, 'ASCII', true);
        }
        return preg_match('/[^\x00-\x7F]/', $value) === 0;
    }

    public static function isContainsUnique($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        return $value === array_unique($value, \SORT_REGULAR);
    }

    public static function isSlug($value): bool
    {
        if (is_array($value)) {
            return false;
        }
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    public static function isDate($value): bool
    {
        $result = false;
        if ($value instanceof \DateTime) {
            $result = true;
        } else {
            $result = strtotime($value) !== false;
        }
        return $result;
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public static function isPHP(string $value): bool
    {
        static $_is_php;
        if (!isset($_is_php[$value])) {
            $_is_php[$value] = version_compare(PHP_VERSION, $value, '>=');
        }
        return $_is_php[$value];
    }
}
