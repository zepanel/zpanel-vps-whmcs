<?php
/**
 * zPanel VPS Server Provisioning Module for WHMCS
 * Architecture inspired by Plesk WHMCS Plugin (https://github.com/plesk/whmcs-plugin)
 * 
 * zPanel Resmi Listener Portları:
 * - 8056 / TCP (HTTPS): Güvenli REST API Portu
 * - 8026 / TCP (HTTPS): Müşteri Kontrol Paneli & SSO
 * - 8036 / TCP (HTTPS): Bayi / Root Yönetici Paneli & SSO
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
        'DisplayName' => 'zPanel VPS / Cloud Server',
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
            'FriendlyName' => 'zPanel VPS Donanım Planı',
            'Type' => 'text',
            'Size' => '25',
            'Default' => 'Cloud-VPS-8',
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
        'os_template' => [
            'FriendlyName' => 'Varsayılan İşletim Sistemi',
            'Type' => 'dropdown',
            'Options' => 'ubuntu-24-04-zpanel,debian-12-zpanel,almalinux-9-zpanel',
            'Default' => 'ubuntu-24-04-zpanel',
        ],
    ];
}

function zpanel_vps_TestConnection(array $params) {
    $host = $params['serverip'] ?: $params['serverhostname'];
    $apiKey = $params['serverpassword'] ?: $params['serveraccesshash'];
    $api = new ApiClient($host, $apiKey, 8056);
    $resp = $api->testConnection();
    return $resp['success'] ? ['success' => true] : ['error' => $resp['error'] ?: 'Bağlantı hatası'];
}

function zpanel_vps_CreateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->createAccount();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Hesap oluşturulamadı');
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

function zpanel_vps_ChangePassword(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->resetRootPassword($params['password']);
    return $res['success'] ? 'success' : ($res['error'] ?: 'Şifre değiştirilemedi');
}

function zpanel_vps_AdminCustomButtonArray() {
    return [
        "Yeniden Başlat" => "reboot",
        "Başlat"         => "start",
        "Kapat"          => "stop",
    ];
}

function zpanel_vps_ClientAreaCustomButtonArray() {
    return [
        "Yeniden Başlat" => "reboot",
        "Başlat"         => "start",
        "Kapat"          => "stop",
    ];
}

function zpanel_vps_reboot(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->reboot();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Yeniden başlatılamadı');
}

function zpanel_vps_start(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->start();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Başlatılamadı');
}

function zpanel_vps_stop(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->stop();
    return $res['success'] ? 'success' : ($res['error'] ?: 'Kapatılamadı');
}

function zpanel_vps_AdminServicesTabFields(array $params) {
    $host = $params['serverip'] ?: $params['domain'];
    $clientPort = 8026;
    $adminPort = 8036;
    $apiPort = 8056;

    return [
        'zPanel REST API' => "<span class='text-primary font-weight-bold'>{$apiPort} (HTTPS)</span>",
        'Kullanıcı Portu' => "<span class='text-success font-weight-bold'>{$clientPort}</span> (https://{$host}:{$clientPort})",
        'Bayi / Root Portu' => "<span class='text-warning font-weight-bold'>{$adminPort}</span> (https://{$host}:{$adminPort})",
        'VPS Durumu' => "<span class='label label-success'>Çalışıyor (Aktif)</span>",
        'zPanel Root Giriş' => "<a href='https://{$host}:{$adminPort}' target='_blank' class='btn btn-xs btn-default'>zPanel Root Paneline Git (:{$adminPort}) &rarr;</a>",
    ];
}

function zpanel_vps_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    return [
        'tabOverviewReplacementTemplate' => 'clientarea.tpl',
        'templateVariables' => $tplVars,
    ];
}
