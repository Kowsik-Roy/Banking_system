<?php

namespace App\Services;

class DiffieHellman
{
    private string $prime;   // large prime number
    private string $base;    // generator
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
        // Small prime + generator for testing (you can use larger)
        $this->prime = "23"; // example prime
        $this->base = "5";   // example generator

        // Private key (random)
        $this->privateKey = (string) random_int(2, 100);

        // Public key = (g^a) mod p
        $this->publicKey = $this->modPow($this->base, $this->privateKey, $this->prime);
    }

    public function getPublicKey(): string
    {
        return dechex((int)$this->publicKey);
    }

    public function getSharedSecret(string $otherPublicKey): string
    {
        // Convert hex to decimal if needed
        $otherPublicKeyDecimal = ctype_xdigit($otherPublicKey) ? hexdec($otherPublicKey) : $otherPublicKey;
        
        // shared = (otherPublicKey ^ privateKey) mod prime
        return $this->modPow($otherPublicKeyDecimal, $this->privateKey, $this->prime);
    }

    public function getPrime(): string
    {
        return $this->prime;
    }

    private function modPow(string $base, string $exp, string $mod): string
    {
        // Ensure all arguments are valid decimal strings
        $base = (string)intval($base);
        $exp = (string)intval($exp);
        $mod = (string)intval($mod);
        
        $result = "1";
        $base = bcmod($base, $mod);

        while (bccomp($exp, "0") > 0) {
            if (bccomp(bcmod($exp, "2"), "1") === 0) {
                $result = bcmod(bcmul($result, $base), $mod);
            }
            $exp = bcdiv($exp, "2");
            $base = bcmod(bcmul($base, $base), $mod);
        }

        return $result;
    }
}
