/**
 * zPanel VPS WHMCS Client Area Script
 */
(function() {
  function drawGauge(svgId, percent, color) {
    var svg = document.getElementById(svgId);
    if (!svg) return;
    var radius = 50;
    var cx = 70;
    var cy = 65;
    var circumference = Math.PI * radius;
    var offset = circumference - (percent / 100) * circumference;

    svg.innerHTML = `
      <path d="M 20 65 A 50 50 0 0 1 120 65" fill="none" stroke="#e2e8f0" stroke-width="10" stroke-linecap="round" />
      <path d="M 20 65 A 50 50 0 0 1 120 65" fill="none" stroke="${color}" stroke-width="10" stroke-linecap="round"
            stroke-dasharray="${circumference}" stroke-dashoffset="${offset}" style="transition: stroke-dashoffset 0.8s ease;" />
    `;
  }

  window.initZPanelVPSGauges = function(cpu, ram, disk) {
    drawGauge('gauge-cpu', cpu, '#3b82f6');
    drawGauge('gauge-ram', ram, '#10b981');
    drawGauge('gauge-disk', disk, '#f59e0b');
  };

  window.switchZPanelTab = function(evt, tabId) {
    evt.preventDefault();
    document.querySelectorAll('.zp-tab-link').forEach(function(el) {
      el.classList.remove('active');
    });
    document.querySelectorAll('.zp-tab-pane').forEach(function(el) {
      el.classList.remove('active');
    });
    evt.currentTarget.classList.add('active');
    var target = document.getElementById(tabId);
    if (target) target.classList.add('active');
  };

  window.triggerZPanelVPSAction = function(action, serviceId) {
    var confirmMsg = {
      'reboot': 'Sunucuyu yeniden başlatmak istediğinize emin misiniz?',
      'stop': 'Sunucuyu kapatmak istediğinize emin misiniz?',
      'reset_root': 'Root şifresini sıfırlayıp e-posta adresinize göndermek istiyor musunuz?'
    };

    if (confirmMsg[action] && !confirm(confirmMsg[action])) {
      return;
    }

    var btn = event ? event.currentTarget : null;
    if (btn) btn.style.opacity = '0.5';

    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'modop=custom&a=' + encodeURIComponent(action) + '&serviceid=' + encodeURIComponent(serviceId)
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (btn) btn.style.opacity = '1';
      if (data.status === 'success') {
        alert(data.message || 'İşlem başarıyla tamamlandı.');
        if (action === 'reboot' || action === 'start' || action === 'stop') {
          setTimeout(function() { location.reload(); }, 1500);
        }
      } else {
        alert('Hata: ' + (data.message || 'İşlem başarısız.'));
      }
    })
    .catch(function(err) {
      if (btn) btn.style.opacity = '1';
      alert('İstek gönderilirken bir hata oluştu.');
    });
  };
})();

