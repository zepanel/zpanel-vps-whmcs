# zPanel Dedicated & VPS Sunucu API Dokümantasyonu

zPanel VPS ve Dedicated sunucu otomasyon modülü, sunucu üzerindeki zPanel Core REST API (:8056) ile güvenli iletişim kurar. Web sunucu katmanında yüksek verimli **zMaxServer (OpenLiteSpeed & LSPHP / LSAPI)** çalışmaktadır.

---

## 1. Resmi Port ve Servis Mimarisi

| Port | Protokol | Servis / Görev | Açıklama |
|---|---|---|---|
| **8026** | TCP / HTTPS | zPanel Müşteri Paneli | Sunucu sahibinin web barındırma ve servis yönetim arayüzü |
| **8036** | TCP / HTTPS | zPanel Root / Yönetici Paneli | Sistem seviyesi yönetim, OpenLiteSpeed ve servis ayarları |
| **8046** | TCP / HTTPS | Webmail Servisi | Webmail istemcisi |
| **8056** | TCP / HTTPS | Core REST API | Uzaktan yönetim, yeniden başlatma ve donanım kontrolü |

---

## 2. Kimlik Doğrulama (Authentication)

Modül istekleri `/etc/zpanel/api.env` dosyasında tanımlı `API_INTERNAL_TOKEN` anahtarı ile imzalanır:

```http
Authorization: Bearer <API_INTERNAL_TOKEN>
Content-Type: application/json
Accept: application/json
```

---

## 3. Core REST API Uç Noktaları

### 3.1. Bağlantı Testi & Durum Denetimi
- **Uç Nokta:** `GET https://<server-ip>:8056/v1/health`
- **Başarılı Yanıt (200 OK):**
```json
{
  "status": "healthy",
  "version": "1.0.0",
  "uptime": "14 days, 3 hours",
  "webserver": "zMaxServer (OpenLiteSpeed / LSAPI)"
}
```

### 3.2. Sunucuyu Yeniden Başlatma (System Reboot)
- **Uç Nokta:** `POST https://<server-ip>:8056/v1/system/reboot`
- **Başarılı Yanıt (200 OK):**
```json
{
  "success": true,
  "message": "Sunucu yeniden başlatma sinyali gönderildi."
}
```

### 3.3. zPanel Panel ve Web Sunucusu Servislerini Yeniden Başlatma
- **Uç Nokta:** `POST https://<server-ip>:8056/v1/system/restart-panel`
- **Başarılı Yanıt (200 OK):**
```json
{
  "success": true,
  "message": "zPanel Core ve zMaxServer servisleri başarıyla yeniden başlatıldı."
}
```

---

## 4. Donanım ve Kaynak Metrikleri

Modül, WHMCS üzerinde tanımlanan ürün yapılandırma seçeneklerini (`cpu_cores`, `ram_mb`, `disk_gb`, `bandwidth_tb`, `os_name`) müşteri alanında doğrudan ve şeffaf olarak gösterir. Sanallaştırma katmanına ait olmayan yapay veya sahte göstergeler içermez.
