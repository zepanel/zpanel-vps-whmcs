<?php

namespace WHMCS\Module\Server\ZPanelVPS\ZPanel;

/**
 * zPanel API Client for WHMCS
 * zPanel Core API v1 Entegrasyonu
 * 
 * zPanel Resmi Listener Portları:
 * - 8056 / TCP (HTTPS): Güvenli Core REST API Portu
 * - 8026 / TCP (HTTPS): Müşteri Kontrol Paneli
 * - 8036 / TCP (HTTPS): Root / Yönetici Paneli
 * - 8046 / TCP (HTTPS): Webmail
 */
class ApiClient
{
    private string $host;
    private string $apiKey;
    private int $apiPort = 8056;
    private int $timeout = 25;

    public function __construct(string $host, string $apiKey, int $apiPort = 8056)
    {
        $this->host = trim($host, '/');
        $this->apiKey = trim($apiKey);
        $this->apiPort = $apiPort > 0 ? $apiPort : 8056;
    }

    public function call(string $method, string $uri, array $params = []): array
    {
        $cleanUri = ltrim($uri, '/');
        // Eğer uri zaten v1/ ile başlamıyorsa başına v1/ ekle
        if (!str_starts_with($cleanUri, 'v1/')) {
            $cleanUri = 'v1/' . $cleanUri;
        }

        $url = "https://{$this->host}:{$this->apiPort}/" . $cleanUri;
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: WHMCS-zPanel-VPS/1.0',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method === 'PATCH' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $res = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => "cURL Bağlantı Hatası: {$err}", 'http_code' => 0];
        }

        $data = json_decode((string)$res, true);
        $isOk = ($code >= 200 && $code < 300);

        return [
            'success' => $isOk,
            'http_code' => $code,
            'data' => $data,
            'message' => $data['message'] ?? ($isOk ? 'Başarılı' : "HTTP Hata {$code}"),
            'error' => !$isOk ? ($data['message'] ?? "HTTP Hata {$code}") : null,
        ];
    }

    public function testConnection(): array
    {
        return $this->call('GET', 'health');
    }

    public function rebootServer(): array
    {
        return $this->call('POST', 'system/reboot');
    }

    public function startServer(): array
    {
        return $this->call('POST', 'system/start');
    }

    public function stopServer(): array
    {
        return $this->call('POST', 'system/stop');
    }

    public function resetRootPassword(?string $newPassword = null): array
    {
        $payload = [];
        if (!empty($newPassword)) {
            $payload['password'] = $newPassword;
        }
        return $this->call('POST', 'system/reset-root-password', $payload);
    }

    public function reinstallOS(string $osTemplate): array
    {
        return $this->call('POST', 'system/reinstall', ['template' => $osTemplate]);
    }

    public function restartPanelServices(): array
    {
        return $this->call('POST', 'system/restart-panel');
    }

    public function getServerLoad(): array
    {
        return $this->call('GET', 'system/load');
    }

    public function getSystemStatus(): array
    {
        return $this->call('GET', 'system/status');
    }
}
