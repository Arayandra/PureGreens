<?php

namespace App\Services;

class IpaymuSignatureVerifier
{
    /**
     * Normalize the callback payload before signature calculation, following
     * the provider's PHP callback signing rules.
     */
    public function normalize(array $raw): array
    {
        $normalized = [];

        foreach ($raw as $key => $value) {
            if ($key === 'additional_info') {
                if ($value === null || $value === '[]') {
                    $normalized[$key] = [];
                } elseif (is_string($value)) {
                    $decoded = json_decode($value, true);
                    $normalized[$key] = is_array($decoded) ? $decoded : $value;
                } else {
                    $normalized[$key] = $value;
                }

                continue;
            }

            if (in_array($key, ['trx_id', 'status_code', 'transaction_status_code', 'paid_off'], true)) {
                $normalized[$key] = is_numeric($value) ? (int) $value : $value;
                continue;
            }

            if ($key === 'is_escrow') {
                $normalized[$key] = in_array($value, [true, 1, '1', 'true'], true);
                continue;
            }

            $normalized[$key] = is_array($value) ? $value : (string) $value;
        }

        if (!array_key_exists('additional_info', $normalized)) {
            $normalized['additional_info'] = [];
        }

        unset($normalized['signature']);
        ksort($normalized, SORT_STRING);

        return $normalized;
    }

    public function expectedSignature(array $raw, string $secret): string
    {
        $payload = $this->normalize($raw);
        $jsonPayload = json_encode($payload);

        if ($jsonPayload === false) {
            throw new \InvalidArgumentException('Unable to encode callback payload.');
        }

        return hash_hmac('sha256', $jsonPayload, $secret);
    }

    public function verify(array $raw, string $receivedSignature, string $secret): bool
    {
        if ($secret === '' || $receivedSignature === '') {
            return false;
        }

        return hash_equals($this->expectedSignature($raw, $secret), $receivedSignature);
    }
}
