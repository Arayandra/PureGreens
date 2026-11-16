<?php

namespace Tests\Unit;

use App\Services\IpaymuSignatureVerifier;
use PHPUnit\Framework\TestCase;

class IpaymuSignatureVerifierTest extends TestCase
{
    public function test_it_verifies_a_signature_for_a_normalized_callback(): void
    {
        $verifier = new IpaymuSignatureVerifier();
        $payload = [
            'status' => 'berhasil',
            'reference_id' => 'ORDER-123',
            'amount' => '15000',
        ];

        $signature = $verifier->expectedSignature($payload, '1234567890');

        $this->assertTrue($verifier->verify($payload, $signature, '1234567890'));
    }

    public function test_it_rejects_a_tampered_callback_payload(): void
    {
        $verifier = new IpaymuSignatureVerifier();
        $payload = [
            'status' => 'berhasil',
            'reference_id' => 'ORDER-123',
            'amount' => '15000',
        ];
        $signature = $verifier->expectedSignature($payload, '1234567890');
        $payload['amount'] = '15001';

        $this->assertFalse($verifier->verify($payload, $signature, '1234567890'));
    }

    public function test_it_rejects_an_empty_secret_or_signature(): void
    {
        $verifier = new IpaymuSignatureVerifier();

        $this->assertFalse($verifier->verify(['reference_id' => 'ORDER-123'], '', '1234567890'));
        $this->assertFalse($verifier->verify(['reference_id' => 'ORDER-123'], 'signature', ''));
    }

    public function test_it_adds_an_empty_additional_info_array_before_signing(): void
    {
        $verifier = new IpaymuSignatureVerifier();

        $normalized = $verifier->normalize([
            'reference_id' => 'ORDER-123',
            'status' => 'success',
        ]);

        $this->assertSame([], $normalized['additional_info']);
        $this->assertSame(
            array_values($normalized),
            array_values(array_merge([], $normalized))
        );
    }
}
