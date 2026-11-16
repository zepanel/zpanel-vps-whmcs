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
    ];
}

function zpanel_vps_ConfigOptions() {
    return [
        'vps_plan' => [
            'FriendlyName' => 'zPanel Donanım Paketi',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Cloud-NVMe-VPS',
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

function zpanel_vps_AdminCustomButtonArray() {
    return [
        "Sunucuyu Yeniden Başlat" => "reboot",
        "Panel Servislerini Yeniden Başlat" => "restart_panel",
    ];
}

function zpanel_vps_ClientAreaCustomButtonArray() {
    return [
        "Sunucuyu Yeniden Başlat" => "reboot",
        "Panel Servislerini Yeniden Başlat" => "restart_panel",
    ];
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

function zpanel_vps_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    return [
        'tabOverviewReplacementTemplate' => 'clientarea.tpl',
        'templateVariables' => $tplVars,
    ];
}
