<?php

declare(strict_types=1);

namespace zonuexe;

use function base64_encode;
use function bin2hex;
use function chr;
use function explode;
use function hexdec;
use function is_null;
use function md5;
use function min;
use function pack;
use function random_bytes;
use function strlen;
use function strrev;
use function strtr;
use function substr;

class Apr1md5
{
    private const BASE64_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    private const APRMD5_ALPHABET = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Source/References for core algorithm:
    // http://www.cryptologie.net/article/126/bruteforce-apr1-hashes/
    // http://svn.apache.org/viewvc/apr/apr-util/branches/1.3.x/crypto/apr_md5.c?view=co
    // http://www.php.net/manual/en/function.crypt.php#73619
    // http://httpd.apache.org/docs/2.2/misc/password_encryptions.html
    // Wikipedia

    /**
     * @phpstan-return non-empty-string
     */
    public static function hash(string $mdp, ?string $salt = null): string
    {
        if (is_null($salt)) {
            $salt = self::salt();
        }

        $salt = substr($salt, 0, 8);
        $max = strlen($mdp);
        $context = "{$mdp}\$apr1\${$salt}";
        $binary = pack('H32', md5("{$mdp}{$salt}{$mdp}"));

        for ($i = $max; $i > 0; $i -= 16) {
            $context .= substr($binary, 0, min(16, $i));
        }

        for ($i = $max; $i > 0; $i >>= 1) {
            $context .= ($i & 1) ? chr(0) : $mdp[0];
        }

        $binary = pack('H32', md5($context));

        for ($i = 0; $i < 1000; $i++) {
            $new = ($i & 1) ? $mdp : $binary;

            if ($i % 3) {
                $new .= $salt;
            }

            if ($i % 7) {
                $new .= $mdp;
            }

            $new .= ($i & 1) ? $binary : $mdp;
            $binary = pack('H32', md5($new));
        }

        $hash = '';
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;

            if ($j === 16) {
                $j = 5;
            }

            $hash = "{$binary[$i]}{$binary[$k]}{$binary[$j]}{$hash}";
        }

        $hash = chr(0) . chr(0) . $binary[11] . $hash;
        $hash = strtr(
            strrev(substr(base64_encode($hash), 2)),
            self::BASE64_ALPHABET,
            self::APRMD5_ALPHABET
        );

        return "\$apr1\${$salt}\${$hash}";
    }

    // 8 character salts are the best. Don't encourage anything but the best.
    /**
     * @phpstan-return non-empty-string
     */
    public static function salt(): string
    {
        $alphabet = self::APRMD5_ALPHABET;
        $salt = '';

        for ($i = 0; $i < 8; $i++) {
            $offset = hexdec(bin2hex(random_bytes(1))) % 64;
            $salt .= $alphabet[$offset];
        }

        /** @var non-empty-string $salt */
        return $salt;
    }

    /**
     * @phpstan-param non-empty-string $hash
     */
    public static function check(string $plain, string $hash): bool
    {
        $parts = explode('$', $hash);

        return self::hash($plain, $parts[2]) === $hash;
    }
}
