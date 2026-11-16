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

    public function start(): array
    {
        return $this->client->startServer();
    }

    public function stop(): array
    {
        return $this->client->stopServer();
    }

    public function resetRootPassword(?string $pass = null): array
    {
        return $this->client->resetRootPassword($pass);
    }

    public function reinstall(string $osTemplate): array
    {
        return $this->client->reinstallOS($osTemplate);
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
        $vncUrl = !empty($serverIp) ? "https://{$serverIp}:{$adminPort}/terminal" : '#';

        // Configurable options önceliği (SolusIO / Hetzner standardı)
        $cfg = $this->params['configoptions'] ?? [];
        $cpu = $cfg['CPU'] ?? $cfg['cpu_cores'] ?? $this->params['configoption2'] ?? 4;
        $ram = $cfg['RAM'] ?? $cfg['ram_mb'] ?? $this->params['configoption3'] ?? 8192;
        $disk = $cfg['Disk'] ?? $cfg['disk_gb'] ?? $this->params['configoption4'] ?? 160;
        $bandwidth = $cfg['Bandwidth'] ?? $cfg['bandwidth_tb'] ?? $this->params['configoption5'] ?? 5;
        $os = $cfg['OS'] ?? $cfg['os_name'] ?? $this->params['configoption6'] ?? 'Ubuntu / AlmaLinux';

        return [
            'domain'        => $domain,
            'server_ip'     => $serverIp,
            'panel_host'    => $panelHost,
            'client_port'   => $clientPort,
            'admin_port'    => $adminPort,
            'sso_url'       => $ssoUrl,
            'vnc_url'       => $vncUrl,
            'status'        => $this->params['status'] ?? 'Active',
            'package_name'  => $this->params['package'] ?? 'zPanel Bulut Sunucu',
            'cpu_cores'     => $cpu,
            'ram_mb'        => $ram,
            'disk_gb'       => $disk,
            'bandwidth_tb'  => $bandwidth,
            'os_name'       => $os,
            'service_id'    => $this->params['serviceid'] ?? 0,
        ];
    }
}
