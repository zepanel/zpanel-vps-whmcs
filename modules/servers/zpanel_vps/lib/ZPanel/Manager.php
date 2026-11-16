<?php

namespace WHMCS\Module\Server\ZPanelVPS\ZPanel;

/**
 * zPanel VPS Business Logic Manager for WHMCS
 * Inspired by Plesk WHMCS Plugin (Plesk/Manager.php)
 */
class Manager
{
    private ApiClient $client;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
        $host = $params['serverip'] ?: $params['serverhostname'];
        $apiKey = $params['serverpassword'] ?: $params['serveraccesshash'];
        $this->client = new ApiClient($host, $apiKey, 8056);
    }

    public function createAccount(): array
    {
        return $this->client->call('POST', 'vps/create', [
            'hostname'     => $this->params['domain'] ?? '',
            'root_password'=> $this->params['password'] ?? '',
            'cpu_cores'    => (int)($this->params['configoption2'] ?? 4),
            'ram_mb'       => (int)($this->params['configoption3'] ?? 8192),
            'disk_gb'      => (int)($this->params['configoption4'] ?? 160),
            'bandwidth_tb' => (int)($this->params['configoption5'] ?? 5),
            'os_template'  => $this->params['configoption6'] ?? 'ubuntu-24-04-zpanel',
        ]);
    }

    public function suspendAccount(): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/suspend");
    }

    public function unsuspendAccount(): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/unsuspend");
    }

    public function terminateAccount(): array
    {
        return $this->client->call('DELETE', "vps/{$this->params['serviceid']}");
    }

    public function reboot(): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/reboot");
    }

    public function start(): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/start");
    }

    public function stop(): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/stop");
    }

    public function resetRootPassword(string $newPassword): array
    {
        return $this->client->call('POST', "vps/{$this->params['serviceid']}/reset-root", ['password' => $newPassword]);
    }

    public function getClientAreaParams(): array
    {
        $domain = $this->params['domain'] ?? '';
        $serverIp = $this->params['serverip'] ?? $this->params['customfields']['server_ip'] ?? '';
        $panelHost = !empty($domain) ? $domain : $serverIp;
        $clientPort = 8026;

        $token = $this->client->getSSOToken($this->params['username'] ?? 'root', false);
        $ssoUrl = !empty($panelHost) ? "https://{$panelHost}:{$clientPort}/auth/sso?token={$token}" : '#';

        return [
            'domain'        => $domain,
            'server_ip'     => $serverIp,
            'panel_host'    => $panelHost,
            'client_port'   => $clientPort,
            'sso_url'       => $ssoUrl,
            'status'        => $this->params['status'] ?? 'Active',
            'package_name'  => $this->params['package'] ?? 'Cloud VPS',
            'cpu_cores'     => $this->params['configoption2'] ?? 4,
            'ram_mb'        => $this->params['configoption3'] ?? 8192,
            'disk_gb'       => $this->params['configoption4'] ?? 160,
            'bandwidth_tb'  => $this->params['configoption5'] ?? 5,
            'os_name'       => $this->params['configoption6'] ?? 'Ubuntu 24.04 LTS (zPanel Pre-installed)',
            'service_id'    => $this->params['serviceid'],
        ];
    }
}

