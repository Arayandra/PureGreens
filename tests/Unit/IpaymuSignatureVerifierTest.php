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

    public function test_it_normalizes_key_order_and_additional_info_before_signing(): void
    {
        $verifier = new IpaymuSignatureVerifier();
        $firstPayload = [
            'status' => 'success',
            'reference_id' => 'ORDER-123',
            'signature' => 'not-part-of-the-payload',
        ];
        $secondPayload = [
            'reference_id' => 'ORDER-123',
            'status' => 'success',
        ];

        $firstSignature = $verifier->expectedSignature($firstPayload, '1234567890');
        $secondSignature = $verifier->expectedSignature($secondPayload, '1234567890');

        $this->assertSame($firstSignature, $secondSignature);
        $this->assertSame([], $verifier->normalize($firstPayload)['additional_info']);
    }
}
