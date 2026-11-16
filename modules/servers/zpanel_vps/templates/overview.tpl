{* zPanel Dedicated & VPS Sunucu WHMCS Müşteri Alanı Şablonu *}
{* zPanel Resmi Port Mimarisi: 8026 (Müşteri), 8036 (Root/Yönetici), 8046 (Webmail), 8056 (API) *}

<link rel="stylesheet" href="modules/servers/zpanel_vps/assets/css/client.css">

<div class="zp-vps-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #0f172a; max-width: 1040px; margin: 0 auto;">
  
  <!-- Üst Başlık Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
        <h2 style="font-size: 20px; font-weight: 700; margin: 0; color: #0f172a;">{$package_name|default:'zPanel Bulut Sunucu'}</h2>
        <span style="background: #e0f2fe; color: #0369a1; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 9999px;">zPanel v1.0</span>
      </div>
      <div style="font-size: 13.5px; color: #64748b;">
        {if $domain}
          <a href="https://{$domain}:{$client_port|default:'8026'}" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 700;">
            {$domain}:{$client_port|default:'8026'}
          </a>
          &bull;
        {/if}
        Sunucu IP: <strong style="color: #0f172a; font-family: monospace;">{$server_ip|default:'Atanmadı'}</strong>
        &bull;
        İşletim Sistemi: <strong style="color: #0f172a;">{$os_name|default:'Ubuntu / AlmaLinux'}</strong>
      </div>
    </div>
    
    <div style="display: flex; gap: 10px; align-items: center;">
      {if $status eq 'Active'}
        <span style="background: #dcfce7; color: #15803d; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 12px;">Aktif</span>
      {else}
        <span style="background: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 12px;">{$status}</span>
      {/if}
      <a href="{$sso_url}" target="_blank" style="background: #2563eb; color: #ffffff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s ease;">
        zPanel Giriş (:{$client_port|default:'8026'}) &rarr;
      </a>
    </div>
  </div>

  <!-- 3 Sekme Navigasyonu -->
  <div style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
    <button type="button" class="zp-vps-tab-btn active" onclick="switchZPanelTab(event, 'zp-tab-specs')" style="background: none; border: none; padding: 10px 18px; font-size: 14px; font-weight: 600; color: #2563eb; border-bottom: 2px solid #2563eb; margin-bottom: -2px; cursor: pointer;">Sunucu Donanımı</button>
    <button type="button" class="zp-vps-tab-btn" onclick="switchZPanelTab(event, 'zp-tab-services')" style="background: none; border: none; padding: 10px 18px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer;">Servisler &amp; Portlar</button>
    <button type="button" class="zp-vps-tab-btn" onclick="switchZPanelTab(event, 'zp-tab-access')" style="background: none; border: none; padding: 10px 18px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer;">Erişim &amp; Giriş</button>
  </div>

  <!-- 1. Sekme: Makine Donanım Özellikleri -->
  <div id="zp-tab-specs" class="zp-tab-content active">
    <!-- Donanım Kartları -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">İşlemci Kapasitesi</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1;">{$cpu_cores|default:'4'} <span style="font-size: 14px; font-weight: 600; color: #64748b;">vCPU</span></div>
        <div style="font-size: 12.5px; color: #94a3b8; margin-top: 6px;">Yüksek Performans Çekirdek</div>
      </div>

      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Bellek (RAM)</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1;">{$ram_mb|default:'8192'} <span style="font-size: 14px; font-weight: 600; color: #64748b;">MB</span></div>
        <div style="font-size: 12.5px; color: #94a3b8; margin-top: 6px;">DDR4 / DDR5 ECC RAM</div>
      </div>

      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Depolama Alanı</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1;">{$disk_gb|default:'160'} <span style="font-size: 14px; font-weight: 600; color: #64748b;">GB NVMe</span></div>
        <div style="font-size: 12.5px; color: #94a3b8; margin-top: 6px;">Saf NVMe SSD RAID10</div>
      </div>

      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Aylık Trafik Kotası</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1;">{$bandwidth_tb|default:'5'} <span style="font-size: 14px; font-weight: 600; color: #64748b;">TB</span></div>
        <div style="font-size: 12.5px; color: #94a3b8; margin-top: 6px;">1 Gbps Korumalı Hat</div>
      </div>
    </div>

    <!-- Gerçek Sunucu Eylemleri Butonları -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0; color: #1e293b;">Hızlı Sunucu Eylemleri</h3>
      
      <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <button type="button" onclick="submitVpsAction('reboot', 'Sunucuyu yeniden başlatmak istediğinize emin misiniz?')" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
          <span>&#x21bb;</span> Sunucuyu Yeniden Başlat
        </button>

        <button type="button" onclick="submitVpsAction('restart_panel', 'zPanel servislerini yeniden başlatmak istediğinize emin misiniz?')" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
          <span>&#x2699;</span> zPanel Servislerini Yeniden Başlat
        </button>

        <a href="https://{$panel_host|default:$server_ip}:8046" target="_blank" style="background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
          <span>&#x2709;</span> Webmail Giriş (:8046)
        </a>
      </div>
    </div>
  </div>

  <!-- 2. Sekme: Servisler & Portlar -->
  <div id="zp-tab-services" class="zp-tab-content" style="display: none;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0; color: #1e293b;">Sunucu Ağ ve Port Yapılandırması</h3>
      <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">Statik IPv4 Adresi</td><td style="padding: 10px 0; font-family: monospace; font-weight: 700;">{$server_ip|default:'Atanmadı'}</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">Web Sunucusu</td><td style="padding: 10px 0; font-weight: 600;">zMaxServer &amp; OpenLiteSpeed (LSAPI)</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">zPanel Müşteri Paneli</td><td style="padding: 10px 0; font-family: monospace; color: #2563eb; font-weight: 700;">8026 / TCP (HTTPS)</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">zPanel Root / Yönetici Paneli</td><td style="padding: 10px 0; font-family: monospace; color: #d97706; font-weight: 700;">8036 / TCP (HTTPS)</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">Webmail Portu</td><td style="padding: 10px 0; font-family: monospace; font-weight: 700;">8046 / TCP (HTTPS)</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; color: #64748b;">Core REST API Portu</td><td style="padding: 10px 0; font-family: monospace; font-weight: 700;">8056 / TCP (HTTPS)</td></tr>
      </table>
    </div>
  </div>

  <!-- 3. Sekme: Erişim & Giriş -->
  <div id="zp-tab-access" class="zp-tab-content" style="display: none;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 14px 0; color: #1e293b;">Doğrudan Panel Erişimi</h3>
      <p style="font-size: 13.5px; color: #64748b; margin-bottom: 16px;">
        Sunucunuz üzerindeki zPanel kontrol paneline web tarayıcınızdan doğrudan erişebilirsiniz:
      </p>
      
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px;">
        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Müşteri Paneli Adresi:</div>
        <a href="https://{$panel_host|default:$server_ip}:8026" target="_blank" style="font-family: monospace; font-weight: 700; color: #2563eb; font-size: 14px; text-decoration: none;">
          https://{$panel_host|default:$server_ip}:8026
        </a>
      </div>

      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px;">
        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Root / Yönetici Paneli Adresi:</div>
        <a href="https://{$panel_host|default:$server_ip}:8036" target="_blank" style="font-family: monospace; font-weight: 700; color: #d97706; font-size: 14px; text-decoration: none;">
          https://{$panel_host|default:$server_ip}:8036
        </a>
      </div>
    </div>
  </div>

  <!-- WHMCS Modül Eylem Formu (Gizli POST) -->
  <form id="zp-vps-action-form" method="post" action="clientarea.php?action=productdetails&id={$service_id}" style="display: none;">
    <input type="hidden" name="modop" value="custom">
    <input type="hidden" name="a" id="zp-vps-action-input" value="">
  </form>

</div>

<script>
function switchZPanelTab(event, tabId) {
  event.preventDefault();
  document.querySelectorAll('.zp-vps-tab-btn').forEach(function(b) {
    b.style.color = '#64748b';
    b.style.borderBottom = 'none';
  });
  event.currentTarget.style.color = '#2563eb';
  event.currentTarget.style.borderBottom = '2px solid #2563eb';
  
  document.querySelectorAll('.zp-tab-content').forEach(function(c) {
    c.style.display = 'none';
  });
  var target = document.getElementById(tabId);
  if (target) {
    target.style.display = 'block';
  }
}

function submitVpsAction(action, confirmMsg) {
  if (confirm(confirmMsg)) {
    document.getElementById('zp-vps-action-input').value = action;
    document.getElementById('zp-vps-action-form').submit();
  }
}
</script>

