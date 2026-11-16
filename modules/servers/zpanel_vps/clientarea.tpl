{* zPanel VPS WHMCS Client Area Template *}
{* Reference: https://github.com/WHMCS/sample-provisioning-module *}

<link rel="stylesheet" href="modules/servers/zpanel_vps/assets/css/client.css">
<script src="modules/servers/zpanel_vps/assets/js/client.js"></script>

<div class="zp-vps-wrap">
  <!-- Üst Başlık Kartı -->
  <div class="zp-vps-header">
    <div class="zp-vps-title">
      <h2>{$package_name}</h2>
      <div class="zp-vps-meta">
        {if $domain}
          <a href="https://{$domain}:{$client_port}" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 700;">
            {$domain}:{$client_port}
          </a>
          &bull;
        {/if}
        Sunucu IP: <strong>{$server_ip|default:'Atanmadı'}</strong>
      </div>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
      <span class="zp-badge-active">{$status}</span>
      <a href="{$sso_url}" target="_blank" class="zp-btn-action zp-btn-sso" style="padding: 8px 18px; border-radius: 9999px;">
        zPanel Giriş (:{$client_port}) &rarr;
      </a>
    </div>
  </div>

  <!-- 6 Sade Sekme (Kaydırma Çubuğu Kesinlikle Yok) -->
  <div class="zp-tabs-nav">
    <a href="#" class="zp-tab-link active" onclick="switchZPanelTab(event, 'zp-tab-overview')">Genel Bakış</a>
    <a href="#" class="zp-tab-link" onclick="switchZPanelTab(event, 'zp-tab-management')">Yönetim</a>
    <a href="#" class="zp-tab-link" onclick="switchZPanelTab(event, 'zp-tab-addons')">Eklentiler</a>
    <a href="#" class="zp-tab-link" onclick="switchZPanelTab(event, 'zp-tab-billing')">Faturalama</a>
    <a href="#" class="zp-tab-link" onclick="switchZPanelTab(event, 'zp-tab-activity')">Aktivite</a>
    <a href="#" class="zp-tab-link" onclick="switchZPanelTab(event, 'zp-tab-cancel')">İptal Talebi</a>
  </div>

  <!-- 1. Sekme: Genel Bakış -->
  <div id="zp-tab-overview" class="zp-tab-pane active">
    <!-- 3 Gauge İbre Kartları -->
    <div class="zp-gauge-grid">
      <div class="zp-gauge-card">
        <div class="zp-gauge-title">CPU Kullanımı</div>
        <svg id="gauge-cpu" class="zp-gauge-svg"></svg>
        <div class="zp-gauge-val">12%</div>
        <div class="zp-gauge-sub">{$cpu_cores} vCPU Çekirdek</div>
      </div>
      <div class="zp-gauge-card">
        <div class="zp-gauge-title">Bellek (RAM)</div>
        <svg id="gauge-ram" class="zp-gauge-svg"></svg>
        <div class="zp-gauge-val">28%</div>
        <div class="zp-gauge-sub">2.2 GB / {$ram_mb} MB</div>
      </div>
      <div class="zp-gauge-card">
        <div class="zp-gauge-title">Depolama (NVMe)</div>
        <svg id="gauge-disk" class="zp-gauge-svg"></svg>
        <div class="zp-gauge-val">15%</div>
        <div class="zp-gauge-sub">24 GB / {$disk_gb} GB NVMe</div>
      </div>
    </div>

    <!-- 8 Hızlı İşlem Butonu -->
    <div class="zp-actions-grid">
      <button type="button" class="zp-btn-action zp-btn-reboot" onclick="triggerZPanelVPSAction('reboot', '{$service_id}')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        <span>Yeniden Başlat</span>
      </button>

      <button type="button" class="zp-btn-action zp-btn-start" onclick="triggerZPanelVPSAction('start', '{$service_id}')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>Başlat</span>
      </button>

      <button type="button" class="zp-btn-action zp-btn-stop" onclick="triggerZPanelVPSAction('stop', '{$service_id}')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
        <span>Kapat</span>
      </button>

      <button type="button" class="zp-btn-action" onclick="alert('HTML5 VNC Konsolu güvenli tünel üzerinden açılıyor...');">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        <span>VNC Konsol</span>
      </button>

      <button type="button" class="zp-btn-action" onclick="triggerZPanelVPSAction('reset_root', '{$service_id}')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        <span>Root Şifresi</span>
      </button>

      <button type="button" class="zp-btn-action" onclick="alert('İşletim sistemi yeniden kurulum sihirbazı başlatılıyor.');">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        <span>Yeniden Kur</span>
      </button>

      <button type="button" class="zp-btn-action" onclick="alert('Sunucu anlık görüntüsü (Snapshot) alınıyor...');">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        <span>Snapshot</span>
      </button>

      <a href="{$sso_url}" target="_blank" class="zp-btn-action zp-btn-sso">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
        <span>zPanel SSO (:{$client_port})</span>
      </a>
    </div>

    <!-- Detay Tablosu -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0;">Sunucu Donanım &amp; Ağ Detayları</h3>
      <table class="zp-info-table">
        <tr><td>İşletim Sistemi</td><td>{$os_name}</td></tr>
        <tr><td>Statik IPv4</td><td style="font-family: monospace; font-weight: bold;">{$server_ip|default:'Atanmadı'}</td></tr>
        <tr><td>Müşteri Portu</td><td style="font-family: monospace;">8026 (HTTPS)</td></tr>
        <tr><td>Aylık Trafik Limiti</td><td>{$bandwidth_tb} TB / Ay</td></tr>
      </table>
    </div>
  </div>

  <div id="zp-tab-management" class="zp-tab-pane"><div style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">DNS, Ters DNS (rDNS) ve Güvenlik Duvarı kuralları zPanel API üzerinden yönetilmektedir.</div></div>
  <div id="zp-tab-addons" class="zp-tab-pane"><div style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">Ek IP, Otomatik Yedekleme ve SSL Eklentileri.</div></div>
  <div id="zp-tab-billing" class="zp-tab-pane"><div style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">Fatura dönemi ve yenileme detayları.</div></div>
  <div id="zp-tab-activity" class="zp-tab-pane"><div style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">Yeniden başlatma ve güvenlik günlükleri.</div></div>
  <div id="zp-tab-cancel" class="zp-tab-pane"><div style="background:#fff; border:1px solid #e2e8f0; padding:20px; border-radius:12px;">Hizmet iptal talebi oluşturma ekranı.</div></div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.initZPanelVPSGauges) {
      window.initZPanelVPSGauges(12, 28, 15);
    }
  });
</script>
