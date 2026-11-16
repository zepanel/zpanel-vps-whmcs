{* zPanel Dedicated & VPS Sunucu WHMCS Müşteri Alanı Şablonu *}
{* zPanel Resmi Port Mimarisi: 8026 (Müşteri), 8036 (Root/Yönetici), 8046 (Webmail), 8056 (API) *}

<link rel="stylesheet" href="modules/servers/zpanel_vps/assets/css/client.css">

<div class="zp-vps-wrap">
  
  <!-- Üst Başlık Kartı -->
  <div class="zp-vps-header">
    <div>
      <div class="zp-vps-title-row">
        <h2 class="zp-vps-title">{$package_name|default:'zPanel Bulut Sunucu'}</h2>
        <span class="zp-badge-ver">zPanel v1.0</span>
      </div>
      <div class="zp-vps-meta">
        {if $domain}
          <a href="https://{$domain}:{$client_port|default:'8026'}" target="_blank" class="zp-code-val">
            {$domain}:{$client_port|default:'8026'}
          </a>
          &bull;
        {/if}
        Sunucu IP: <strong class="zp-code-val">{$server_ip|default:'Atanmadı'}</strong>
        &bull;
        İşletim Sistemi: <strong class="zp-strong-val">{$os_name|default:'Ubuntu / AlmaLinux'}</strong>
      </div>
    </div>
    
    <div class="zp-header-actions">
      {if $status eq 'Active'}
        <span class="zp-badge-active">Aktif</span>
      {else}
        <span class="zp-badge-other">{$status}</span>
      {/if}
      <a href="{$sso_url}" target="_blank" class="zp-btn-primary">
        zPanel Giriş (:{$client_port|default:'8026'}) &rarr;
      </a>
    </div>
  </div>

  <!-- 3 Sekme Navigasyonu -->
  <div class="zp-tabs-nav">
    <button type="button" class="zp-vps-tab-btn active" onclick="switchZPanelTab(event, 'zp-tab-specs')">Sunucu Donanımı</button>
    <button type="button" class="zp-vps-tab-btn" onclick="switchZPanelTab(event, 'zp-tab-services')">Servisler &amp; Portlar</button>
    <button type="button" class="zp-vps-tab-btn" onclick="switchZPanelTab(event, 'zp-tab-access')">Erişim &amp; Giriş</button>
  </div>

  <!-- 1. Sekme: Makine Donanım Özellikleri -->
  <div id="zp-tab-specs" class="zp-tab-content active">
    <!-- Donanım Kartları -->
    <div class="zp-specs-grid">
      <div class="zp-spec-card">
        <div class="zp-spec-label">İşlemci Kapasitesi</div>
        <div class="zp-spec-val">{$cpu_cores|default:'4'} <span class="zp-spec-unit">vCPU</span></div>
        <div class="zp-spec-sub">Yüksek Performans Çekirdek</div>
      </div>

      <div class="zp-spec-card">
        <div class="zp-spec-label">Bellek (RAM)</div>
        <div class="zp-spec-val">{$ram_mb|default:'8192'} <span class="zp-spec-unit">MB</span></div>
        <div class="zp-spec-sub">DDR4 / DDR5 ECC RAM</div>
      </div>

      <div class="zp-spec-card">
        <div class="zp-spec-label">Depolama Alanı</div>
        <div class="zp-spec-val">{$disk_gb|default:'160'} <span class="zp-spec-unit">GB NVMe</span></div>
        <div class="zp-spec-sub">Saf NVMe SSD RAID10</div>
      </div>

      <div class="zp-spec-card">
        <div class="zp-spec-label">Aylık Trafik Kotası</div>
        <div class="zp-spec-val">{$bandwidth_tb|default:'5'} <span class="zp-spec-unit">TB</span></div>
        <div class="zp-spec-sub">1 Gbps Korumalı Hat</div>
      </div>
    </div>

    <!-- Gerçek Sunucu Eylemleri Butonları -->
    <div class="zp-card">
      <h3 class="zp-card-title">Hızlı Sunucu Eylemleri</h3>
      
      <div class="zp-actions-group">
        <button type="button" onclick="submitVpsAction('reboot', 'Sunucuyu yeniden başlatmak istediğinize emin misiniz?')" class="zp-btn-danger-outline">
          <span>&#x21bb;</span> Sunucuyu Yeniden Başlat
        </button>

        <button type="button" onclick="submitVpsAction('restart_panel', 'zPanel servislerini yeniden başlatmak istediğinize emin misiniz?')" class="zp-btn-secondary-outline">
          <span>&#x2699;</span> zPanel Servislerini Yeniden Başlat
        </button>

        <a href="https://{$panel_host|default:$server_ip}:8046" target="_blank" class="zp-btn-neutral-outline">
          <span>&#x2709;</span> Webmail Giriş (:8046)
        </a>
      </div>
    </div>
  </div>

  <!-- 2. Sekme: Servisler & Portlar -->
  <div id="zp-tab-services" class="zp-tab-content">
    <div class="zp-card">
      <h3 class="zp-card-title">Sunucu Ağ ve Port Yapılandırması</h3>
      <table class="zp-table">
        <tr><td>Statik IPv4 Adresi</td><td class="zp-code-val">{$server_ip|default:'Atanmadı'}</td></tr>
        <tr><td>Web Sunucusu</td><td class="zp-strong-val">zMaxServer &amp; OpenLiteSpeed (LSAPI)</td></tr>
        <tr><td>zPanel Müşteri Paneli</td><td class="zp-code-val">8026 / TCP (HTTPS)</td></tr>
        <tr><td>zPanel Root / Yönetici Paneli</td><td class="zp-code-val">8036 / TCP (HTTPS)</td></tr>
        <tr><td>Webmail Portu</td><td class="zp-code-val">8046 / TCP (HTTPS)</td></tr>
        <tr><td>Core REST API Portu</td><td class="zp-code-val">8056 / TCP (HTTPS)</td></tr>
      </table>
    </div>
  </div>

  <!-- 3. Sekme: Erişim & Giriş -->
  <div id="zp-tab-access" class="zp-tab-content">
    <div class="zp-card">
      <h3 class="zp-card-title">Doğrudan Panel Erişimi</h3>
      <p class="zp-vps-meta">
        Sunucunuz üzerindeki zPanel kontrol paneline web tarayıcınızdan doğrudan erişebilirsiniz:
      </p>
      
      <div class="zp-access-box">
        <div class="zp-access-label">Müşteri Paneli Adresi:</div>
        <a href="https://{$panel_host|default:$server_ip}:8026" target="_blank" class="zp-access-link-client">
          https://{$panel_host|default:$server_ip}:8026
        </a>
      </div>

      <div class="zp-access-box">
        <div class="zp-access-label">Root / Yönetici Paneli Adresi:</div>
        <a href="https://{$panel_host|default:$server_ip}:8036" target="_blank" class="zp-access-link-admin">
          https://{$panel_host|default:$server_ip}:8036
        </a>
      </div>
    </div>
  </div>

  <!-- WHMCS Modül Eylem Formu (Gizli POST) -->
  <form id="zp-vps-action-form" method="post" action="clientarea.php?action=productdetails&id={$service_id}" class="zp-hidden">
    <input type="hidden" name="token" value="{$csrfToken}">
    <input type="hidden" name="modop" value="custom">
    <input type="hidden" name="a" id="zp-vps-action-input" value="">
  </form>

</div>

<script>
function switchZPanelTab(event, tabId) {
  event.preventDefault();
  document.querySelectorAll('.zp-vps-tab-btn').forEach(function(b) {
    b.classList.remove('active');
  });
  event.currentTarget.classList.add('active');
  
  document.querySelectorAll('.zp-tab-content').forEach(function(c) {
    c.classList.remove('active');
  });
  var target = document.getElementById(tabId);
  if (target) {
    target.classList.add('active');
  }
}

function submitVpsAction(action, confirmMsg) {
  if (confirm(confirmMsg)) {
    document.getElementById('zp-vps-action-input').value = action;
    document.getElementById('zp-vps-action-form').submit();
  }
}
</script>
