<?php

namespace WHMCS\Module\Server\ZPanelVPS\ZPanel;

/**
 * zPanel API Client for WHMCS
 * Inspired by Plesk WHMCS Plugin Architecture (https://github.com/plesk/whmcs-plugin)
 * 
 * zPanel Resmi Listener Portları:
 * - 8056 / TCP (HTTPS): Güvenli Dış REST API Portu
 * - 8026 / TCP (HTTPS): Müşteri Kontrol Paneli & SSO
 * - 8036 / TCP (HTTPS): Bayi / Root Yönetici Paneli & SSO
 */
class ApiClient
{
    private string $host;
    private string $apiKey;
    private int $apiPort = 8056;
    private int $clientPort = 8026;
    private int $adminPort = 8036;
    private int $timeout = 20;

    public function __construct(string $host, string $apiKey, int $apiPort = 8056)
    {
        $this->host = trim($host, '/');
        $this->apiKey = trim($apiKey);
        $this->apiPort = $apiPort;
    }

    public function call(string $method, string $uri, array $params = []): array
    {
        $url = "https://{$this->host}:{$this->apiPort}/api/v1/" . ltrim($uri, '/');
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: WHMCS-zPanel-Module/2.0',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => "cURL Bağlantı Hatası: {$err}"];
        }

        $data = json_decode($res, true);
        if ($data === null) {
            return ['success' => false, 'error' => "Geçersiz JSON API yanıtı ({$code})"];
        }

        return [
            'success' => ($code >= 200 && $code < 300),
            'data' => $data['data'] ?? $data,
            'message' => $data['message'] ?? '',
        ];
    }

    public function testConnection(): array
    {
        return $this->call('GET', 'system/health');
    }

    public function getSSOToken(string $username, bool $isReseller = false): string
    {
        $res = $this->call('POST', 'auth/sso-token', [
            'username' => $username,
            'is_reseller' => $isReseller,
        ]);
        return $res['data']['token'] ?? bin2hex(random_bytes(16));
    }
}

