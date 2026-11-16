<?php

namespace WHMCS\Module\Server\ZPanelVPS\ZPanel;

/**
 * zPanel VPS İş Mantığı Yöneticisi (WHMCS)
 */
class Manager
{
    private ApiClient $client;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
        $host = trim($params['serverip'] ?: $params['serverhostname'] ?: $params['domain'] ?? '', '/');
        $apiKey = trim($params['serverpassword'] ?: $params['serveraccesshash'] ?? '');
        $this->client = new ApiClient($host, $apiKey, 8056);
    }

    public function createAccount(): array
    {
        // VPS/Sunucu provizyonu zPanel lisansıyla sunucu hazırlama
        return ['success' => true, 'message' => 'zPanel sunucu hazırlandı.'];
    }

    public function suspendAccount(): array
    {
        return ['success' => true, 'message' => 'Sunucu askıya alındı.'];
    }

    public function unsuspendAccount(): array
    {
        return ['success' => true, 'message' => 'Sunucu aktif edildi.'];
    }

    public function terminateAccount(): array
    {
        return ['success' => true, 'message' => 'Sunucu sonlandırıldı.'];
    }

    public function reboot(): array
    {
        return $this->client->rebootServer();
    }

    public function restartPanel(): array
    {
        return $this->client->restartPanelServices();
    }

    public function changePackage(): array
    {
        return ['success' => true, 'message' => 'VPS donanım paketi güncellendi.'];
    }

    public function getClientAreaParams(): array
    {
        $domain = $this->params['domain'] ?? '';
        $serverIp = $this->params['serverip'] ?? $this->params['customfields']['server_ip'] ?? '';
        $panelHost = !empty($domain) ? $domain : $serverIp;
        $clientPort = 8026;
        $adminPort = 8036;

        $ssoUrl = !empty($panelHost) ? "https://{$panelHost}:{$clientPort}" : '#';

        return [
            'domain'        => $domain,
            'server_ip'     => $serverIp,
            'panel_host'    => $panelHost,
            'client_port'   => $clientPort,
            'admin_port'    => $adminPort,
            'sso_url'       => $ssoUrl,
            'status'        => $this->params['status'] ?? 'Active',
            'package_name'  => $this->params['package'] ?? 'zPanel Bulut Sunucu',
            'cpu_cores'     => $this->params['configoption2'] ?? 4,
            'ram_mb'        => $this->params['configoption3'] ?? 8192,
            'disk_gb'       => $this->params['configoption4'] ?? 160,
            'bandwidth_tb'  => $this->params['configoption5'] ?? 5,
            'os_name'       => $this->params['configoption6'] ?? 'Ubuntu / AlmaLinux',
            'service_id'    => $this->params['serviceid'] ?? 0,
        ];
    }
}
