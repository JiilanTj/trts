# 📋 PANDUAN DEPLOYMENT SISTEM CHAT REALTIME

## 🏠 **Untuk AAPanel/Shared Hosting (Production)**

### 📁 **1. Upload Files**
```bash
# Upload semua file project ke public_html/
# Pastikan structure seperti ini:
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
├── artisan
├── composer.json
└── package.json
```

### ⚙️ **2. Konfigurasi Environment**
Update file `.env`:
```env
# Database Configuration (sesuaikan dengan hosting)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Broadcasting (PENTING: Gunakan 'sync' untuk shared hosting)
BROADCAST_CONNECTION=sync

# Cache & Session (sesuaikan dengan hosting capabilities)
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# App URL (sesuaikan dengan domain hosting)
APP_URL=https://yourdomain.com
```

### 🌐 **3. Setup .htaccess**
Buat file `.htaccess` di root domain:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

Dan di folder `public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 🗄️ **4. Database Migration**
Jalankan via hosting panel atau phpMyAdmin:
```sql
-- Import semua migration files atau jalankan:
-- php artisan migrate (jika ada akses terminal)
```

### 🔄 **5. Sistem Chat Polling**
Sistem chat akan otomatis menggunakan **polling** (tanpa WebSocket) di production:

**Cara Kerja:**
- ✅ Dashboard: Update setiap 15 detik
- ✅ Chat Room: Polling message baru setiap 3 detik  
- ✅ Statistics: Update setiap 30 detik
- ✅ Browser Notifications: Otomatis aktif

---

## 🧪 **Untuk Development (Localhost)**

### 🚀 **1. Quick Start**
```bash
# Clone project
git clone [repository-url]
cd bangobos

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build
```

### ⚡ **2. Start Chat System**
```bash
# Option A: Manual (2 terminals)
Terminal 1: php artisan serve
Terminal 2: php artisan reverb:start

# Option B: Automatic script
chmod +x start-chat.sh
./start-chat.sh
```

### 🔌 **3. WebSocket Development**
Environment untuk development:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=351906
REVERB_APP_KEY=78ntko33t7wfckfqxzzb
REVERB_APP_SECRET=mmv9xulrrsg0uqikbcv4
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

## 🎯 **Features Chat System**

### ✅ **Sudah Implemented**
- 📊 Real-time dashboard statistics
- 💬 Chat room management (assign, close, etc)
- 🔄 Dual system: WebSocket (dev) + Polling (production)
- 📱 Responsive UI dengan style konsisten
- 🔔 Browser notifications
- ⚡ Auto-scroll messages
- 🏷️ Status & priority management
- 📈 Chat statistics & reporting

### 🔄 **Auto-Detection System**
```javascript
// System otomatis detect environment:
if (localhost) {
    // Gunakan WebSocket (Laravel Reverb)
    useWebSocket();
} else {
    // Gunakan Polling (Production)
    usePolling();
}
```

---

## 🔧 **Troubleshooting**

### ❌ **Masalah Umum di Shared Hosting**

**1. 500 Internal Server Error**
```bash
# Pastikan .htaccess sudah benar
# Cek folder permissions: 755 untuk folder, 644 untuk files
# Cek error logs di hosting panel
```

**2. Chat tidak update real-time**
```javascript
// Buka browser console, pastikan muncul:
"🔄 Using Polling (Production)"

// Jika tidak, cek:
// - Network tab untuk API calls
// - CSRF token
// - Database connection
```

**3. Assets tidak load**
```bash
# Build assets sebelum upload:
npm run build

# Upload folder public/build/ ke hosting
```

### ✅ **Solusi Performance**

**1. Optimasi Polling**
```javascript
// Adjust interval di chat.js:
// Dashboard: 15 detik (bisa diperpanjang ke 30s)
// Messages: 3 detik (bisa diperpanjang ke 5s jika perlu)
```

**2. Database Optimization**
```sql
-- Add indexes untuk performa:
CREATE INDEX idx_chat_rooms_status ON chat_rooms(status);
CREATE INDEX idx_chat_messages_room_id ON chat_messages(chat_room_id);
CREATE INDEX idx_chat_messages_created_at ON chat_messages(created_at);
```

---

## 📞 **Testing**

### 🧪 **Test Production Deployment**
1. Upload ke staging/production
2. Buka: `https://yourdomain.com/admin/chat`
3. Cek browser console: 
   - ✅ "🔄 Using Polling (Production)"
   - ✅ API calls berhasil
   - ✅ Tidak ada error JavaScript

### 🔍 **Monitor Real-time**
- Dashboard auto-update setiap 15s
- Chat messages polling setiap 3s
- Statistics update setiap 30s
- Browser notifications bekerja

---

## 📞 **Support**

Jika ada masalah:
1. 📋 Cek browser console untuk error
2. 🔍 Cek network tab untuk failed API calls  
3. 📝 Cek error logs di hosting panel
4. 💾 Pastikan database connection OK

**Sistem ini 100% kompatibel dengan shared hosting!** 🎉
