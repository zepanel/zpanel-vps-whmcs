<?php
/**
 * zPanel VPS & Dedicated Sunucu Otomasyon Modülü (WHMCS)
 * Resmi Port Mimarisi:
 * - 8056 / TCP (HTTPS): Core REST API Portu
 * - 8026 / TCP (HTTPS): Müşteri Paneli
 * - 8036 / TCP (HTTPS): Root / Yönetici Paneli
 * - 8046 / TCP (HTTPS): Webmail
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/ZPanel/ApiClient.php';
require_once __DIR__ . '/lib/ZPanel/Manager.php';

use WHMCS\Module\Server\ZPanelVPS\ZPanel\ApiClient;
use WHMCS\Module\Server\ZPanelVPS\ZPanel\Manager;

function zpanel_vps_MetaData() {
    return [
        'DisplayName' => 'zPanel Dedicated / VPS Sunucu',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8056',
        'DefaultSSLPort' => '8056',
        'ServiceSingleSignOnLabel' => 'zPanel Paneline Giriş Yap (:8026)',
        'AdminSingleSignOnLabel' => 'zPanel Root Girişi (:8036)',
        'MetricBilling' => true,
    ];
}

function zpanel_vps_ConfigOptions() {
    return [
        'vps_plan' => [
            'FriendlyName' => 'zPanel VPS Donanım Paketi',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Cloud-NVMe-VPS',
            'Description' => 'zPanel sunucu donanım şablonu adı',
            'SimpleMode' => true,
        ],
        'cpu_cores' => [
            'FriendlyName' => 'vCPU Çekirdek Sayısı',
            'Type' => 'dropdown',
            'Options' => '1,2,4,8,16,32',
            'Default' => '4',
        ],
        'ram_mb' => [
            'FriendlyName' => 'Bellek (RAM MB)',
            'Type' => 'dropdown',
            'Options' => '2048,4096,8192,16384,32768,65536',
            'Default' => '8192',
        ],
        'disk_gb' => [
            'FriendlyName' => 'Depolama Alanı (NVMe GB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '160',
        ],
        'bandwidth_tb' => [
            'FriendlyName' => 'Aylık Trafik Kotası (TB)',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '5',
        ],
        'os_name' => [
            'FriendlyName' => 'İşletim Sistemi',
            'Type' => 'dropdown',
            'Options' => 'Ubuntu 24.04 LTS,Ubuntu 22.04 LTS,Debian 12,AlmaLinux 9',
            'Default' => 'Ubuntu 24.04 LTS',
        ],
    ];
}

function zpanel_vps_MetricProvider(array $params) {
    require_once __DIR__ . '/lib/ZPanel/MetricsProvider.php';
    return new \WHMCS\Module\Server\ZPanelVPS\ZPanel\MetricsProvider($params);
}

function zpanel_vps_TestConnection(array $params) {
    try {
        $host = $params['serverip'] ?: $params['serverhostname'];
        $apiKey = $params['serverpassword'] ?: $params['serveraccesshash'];
        $api = new ApiClient($host, $apiKey, 8056);
        $resp = $api->testConnection();
        return $resp['success'] ? ['success' => true] : ['error' => $resp['error'] ?: 'Bağlantı hatası'];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

function zpanel_vps_CreateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->createAccount();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Hesap açılamadı');
}

function zpanel_vps_SuspendAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->suspendAccount();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Askıya alınamadı');
}

function zpanel_vps_UnsuspendAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->unsuspendAccount();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Aktif edilemedi');
}

function zpanel_vps_TerminateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->terminateAccount();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Silinemedi');
}

function zpanel_vps_ChangePackage(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->changePackage();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Paket güncellenemedi');
}

function zpanel_vps_AdminCustomButtonArray() {
    return [
        "Sunucuyu Başlat (Power On)" => "start",
        "Sunucuyu Kapat (Power Off)" => "stop",
        "Sunucuyu Yeniden Başlat (Reboot)" => "reboot",
        "Panel Servislerini Yeniden Başlat" => "restart_panel",
        "Root Şifresini Sıfırla" => "reset_root_password",
        "İşletim Sistemini Sıfırla" => "reinstall",
        "Acil Durum Web Konsolu (VNC)" => "vnc",
    ];
}

function zpanel_vps_ClientAreaCustomButtonArray() {
    return [
        "Sunucuyu Başlat" => "start",
        "Sunucuyu Kapat" => "stop",
        "Sunucuyu Yeniden Başlat" => "reboot",
        "Panel Servislerini Yeniden Başlat" => "restart_panel",
        "Root Şifresini Sıfırla" => "reset_root_password",
        "Acil Durum Web Konsolu" => "vnc",
    ];
}

function zpanel_vps_start(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->start();
        return $res['success'] ? 'success' : ($res['error'] ?: 'Sunucu başlatılamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_stop(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->stop();
        return $res['success'] ? 'success' : ($res['error'] ?: 'Sunucu kapatılamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_reboot(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->reboot();
        return $res['success'] ? 'success' : ($res['error'] ?: 'Yeniden başlatılamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_restart_panel(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->restartPanel();
        return $res['success'] ? 'success' : ($res['error'] ?: 'Panel servisleri yeniden başlatılamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_reset_root_password(array $params) {
    try {
        $newPass = $_POST['new_root_password'] ?? null;
        $mgr = new Manager($params);
        $res = $mgr->resetRootPassword($newPass);
        return $res['success'] ? 'success' : ($res['error'] ?: 'Root şifresi sıfırlanamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_reinstall(array $params) {
    try {
        $template = $_POST['os_template'] ?? ($params['configoption6'] ?? 'Ubuntu 24.04 LTS');
        $mgr = new Manager($params);
        $res = $mgr->reinstall($template);
        return $res['success'] ? 'success' : ($res['error'] ?: 'Yeniden kurulum başlatılamadı');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_AdminLink(array $params) {
    $host = $params['serverip'] ?: ($params['serverhostname'] ?? '127.0.0.1');
    $port = 8036;
    $url = "https://{$host}:{$port}";
    return sprintf(
        '<a href="%s" target="_blank" class="btn btn-default btn-sm">' .
        '<i class="fas fa-server"></i> zPanel Root Paneline Giriş Yap (:8036)' .
        '</a>',
        htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
    );
}

function zpanel_vps_AdminServicesTabFields(array $params) {
    $serverIp = $params['serverip'] ?? '-';
    $cpu = $params['configoption2'] ?? '4';
    $ram = $params['configoption3'] ?? '8192';
    $disk = $params['configoption4'] ?? '160';

    return [
        'Sunucu Durumu' => '<span class="label label-success">Çevrimiçi / Online</span>',
        'Tahsis Edilen Kaynaklar' => sprintf('<strong>%s vCPU</strong> &bull; <strong>%d MB RAM</strong> &bull; <strong>%d GB NVMe</strong>', htmlspecialchars($cpu), (int)$ram, (int)$disk),
        'Yönetici Portu' => '<code>8036 / HTTPS</code>',
        'Müşteri Portu' => '<code>8026 / HTTPS</code>',
        'Doğrudan Giriş' => '<a href="https://' . htmlspecialchars($serverIp, ENT_QUOTES, 'UTF-8') . ':8036" target="_blank" class="btn btn-xs btn-primary"><i class="fas fa-external-link-alt"></i> Root Konsolu</a>',
    ];
}

function zpanel_vps_ServiceSingleSignOn(array $params) {
    $serverIp = $params['serverip'] ?: ($params['serverhostname'] ?? '127.0.0.1');
    return [
        'success' => true,
        'redirectTo' => "https://{$serverIp}:8026/sso?user=" . urlencode($params['username'] ?? ''),
    ];
}

function zpanel_vps_AdminSingleSignOn(array $params) {
    $serverIp = $params['serverip'] ?: ($params['serverhostname'] ?? '127.0.0.1');
    return [
        'success' => true,
        'redirectTo' => "https://{$serverIp}:8036/admin/sso",
    ];
}

function zpanel_vps_UsageUpdate(array $params) {
    if (!class_exists('WHMCS\Database\Capsule')) {
        return 'success';
    }

    try {
        $serverId = (int)($params['serverid'] ?? 0);
        $services = \WHMCS\Database\Capsule::table('tblhosting')
            ->where('server', $serverId)
            ->whereIn('domainstatus', ['Active', 'Suspended'])
            ->get();

        foreach ($services as $service) {
            \WHMCS\Database\Capsule::table('tblhosting')
                ->where('id', $service->id)
                ->update([
                    'disklimit'  => (float)($params['configoption4'] ?? 160) * 1024,
                    'bwlimit'    => (float)($params['configoption5'] ?? 5) * 1024 * 1024,
                    'lastupdate' => date('Y-m-d H:i:s'),
                ]);
        }

        return 'success';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_vnc(array $params) {
    try {
        $serverIp = $params['serverip'] ?: ($params['serverhostname'] ?? '127.0.0.1');
        $vncUrl = "https://{$serverIp}:8036/console";
        header("Location: {$vncUrl}");
        exit;
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_vps_AdminServicesTabFieldsSave(array $params) {
    return 'success';
}

function zpanel_vps_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    $templateFile = file_exists(__DIR__ . '/templates/overview.tpl') ? 'templates/overview.tpl' : 'clientarea.tpl';

    return [
        'tabOverviewReplacementTemplate' => $templateFile,
        'templateVariables' => $tplVars,
    ];
}
