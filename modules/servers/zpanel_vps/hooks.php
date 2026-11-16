<?php
/**
 * zPanel VPS Module Hooks for WHMCS
 * Reference & Standards: https://developers.whmcs.com/hooks/
 */

use WHMCS\View\Menu\Item as MenuItem;

add_hook('ClientAreaPrimarySidebar', 10, function (MenuItem $primarySidebar) {
    $service = Menu::context('service');
    if (!$service || $service->product->servertype !== 'zpanel_vps') {
        return;
    }

    if ($service->domainstatus !== 'Active') {
        return;
    }

    $serverIp = $service->serverip ?: $service->domain;
    $sidebar = $primarySidebar->addChild('zPanelVpsActions', [
        'label' => 'Sunucu Yönetim Kısayolları',
        'order' => 15,
        'icon'  => 'fas fa-server',
    ]);

    $sidebar->addChild('zpanel_root_login', [
        'label' => 'zPanel Root Girişi (:8036)',
        'uri'   => "https://{$serverIp}:8036",
        'order' => 10,
        'icon'  => 'fas fa-user-shield',
        'attributes' => ['target' => '_blank'],
    ]);

    $sidebar->addChild('zpanel_client_login', [
        'label' => 'Müşteri Paneli Girişi (:8026)',
        'uri'   => "https://{$serverIp}:8026",
        'order' => 20,
        'icon'  => 'fas fa-desktop',
        'attributes' => ['target' => '_blank'],
    ]);

    $sidebar->addChild('zpanel_webmail_login', [
        'label' => 'Webmail Girişi (:8046)',
        'uri'   => "https://{$serverIp}:8046",
        'order' => 30,
        'icon'  => 'fas fa-envelope',
        'attributes' => ['target' => '_blank'],
    ]);
});

