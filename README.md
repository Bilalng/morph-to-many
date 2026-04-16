# Morph-to-Many Yorum Sistemi

Laravel 12 ile geliştirilmiş, **polimorfik yorum sistemi** ve iç içe yanıt (reply) desteği sunan bir REST API projesidir. Yorumlar, Laravel'in `morphMany` ilişkisi sayesinde tek bir `comments` tablosu üzerinden `Post` ve `Video` gibi farklı modellere bağlanabilir. Yorumlar arası yanıt ilişkisi ise ayrı bir pivot tablo ve `belongsToMany` ile yönetilir. Proje, polimorfizm ile many-to-many ilişkisini aynı anda kullanan bir tasarım örneği sunmaktadır.

## Mimari Genel Bakış

```
posts    ──┐
            ├── comments (morphMany) ── comment_replies (pivot, belongsToMany)
videos   ──┘
```

| Tablo             | Açıklama                                                    |
|-------------------|-------------------------------------------------------------|
| `posts`           | Yorum yapılabilen model                                     |
| `videos`          | Yorum yapılabilen model                                     |
| `comments`        | Tüm yorumları tutan tablo (`commentable_type/id` kolonları) |
| `comment_replies` | Ana yorum ile yanıtları ilişkilendiren pivot tablo          |

## Gereksinimler

- PHP >= 8.2
- Composer
- MySQL
- Laravel 12

## Kurulum

### 1. Projeyi klonla

```bash
git clone https://github.com/<kullanici-adin>/morph-to-many.git
cd morph-to-many
```

### 2. Bağımlılıkları yükle

```bash
composer install
```

### 3. Ortam dosyasını ayarla

```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını açıp veritabanı bilgilerini gir:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=morph-to-many
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Veritabanını oluştur

```sql
CREATE DATABASE `morph-to-many`;
```

### 5. Migration'ları çalıştır ve örnek veri yükle

```bash
php artisan migrate --seed
```

Bu komut migration'ları çalıştırır ve ardından otomatik olarak seeder'ı tetikler. Seeder şunları oluşturur:

- 5 kullanıcı
- 5 post (her birinde 3 üst düzey yorum + her yoruma 2 yanıt)
- 3 video (her birinde 2 üst düzey yorum + her yoruma 2 yanıt)

Veritabanı zaten kuruluysa sadece seeder'ı çalıştırmak için:

```bash
php artisan db:seed
```

### 6. Sunucuyu başlat

```bash
php artisan serve
```

API `http://localhost:8000` adresinde ayağa kalkar.

---

## API Endpoint'leri

Tüm route'lar `/api/v1` prefix'i ile başlar.

### Yorumlar

| Metot  | Endpoint           | Açıklama                                    |
|--------|--------------------|---------------------------------------------|
| GET    | `/comments`        | Belirli bir modele ait yorumları listele     |
| POST   | `/comments`        | Yeni yorum oluştur (veya yanıt ver)          |
| GET    | `/comments/{id}`   | Tek bir yorumu yanıtlarıyla birlikte getir   |
| DELETE | `/comments/{id}`   | Yorumu sil                                  |

---

### GET /api/v1/comments

Bir post veya video'ya ait üst düzey (parent olmayan) yorumları döner.

**Query Parametreleri:**

| Parametre          | Tip     | Zorunlu | Açıklama                     |
|--------------------|---------|---------|------------------------------|
| `commentable_type` | string  | Evet    | `post` veya `video`          |
| `commentable_id`   | integer | Evet    | Post veya video'nun ID'si    |

**Örnek:**

```bash
curl "http://localhost:8000/api/v1/comments?commentable_type=post&commentable_id=1"
```

**Yanıt:**

```json
{
  "comments": [
    {
      "id": 1,
      "content": "Harika bir yazı!",
      "commentable_type": "post",
      "commentable_id": 1,
      "user": null,
      "replies": []
    }
  ]
}
```

---

### POST /api/v1/comments

Yeni bir yorum oluşturur. Mevcut bir yoruma yanıt vermek için `parent_id` alanını ekle.

**İstek Gövdesi:**

| Alan               | Tip     | Zorunlu | Açıklama                                  |
|--------------------|---------|---------|-------------------------------------------|
| `commentable_type` | string  | Evet    | `post` veya `video`                       |
| `commentable_id`   | integer | Evet    | Post veya video'nun ID'si                 |
| `content`          | string  | Evet    | Yorum metni                               |
| `parent_id`        | integer | Hayır   | Yanıt verilecek yorumun ID'si             |

**Örnek — yorum oluştur:**

```bash
curl -X POST http://localhost:8000/api/v1/comments \
  -H "Content-Type: application/json" \
  -d '{"commentable_type":"post","commentable_id":1,"content":"Çok güzel bir yazı!"}'
```

**Örnek — yoruma yanıt ver:**

```bash
curl -X POST http://localhost:8000/api/v1/comments \
  -H "Content-Type: application/json" \
  -d '{"commentable_type":"post","commentable_id":1,"parent_id":1,"content":"Kesinlikle katılıyorum!"}'
```

---

### DELETE /api/v1/comments/{id}

```bash
curl -X DELETE http://localhost:8000/api/v1/comments/1
```

---

Post ve video'lar için de temel CRUD endpoint'leri mevcuttur (`update` hariç):

- `GET/POST/DELETE /api/v1/posts`
- `GET/POST/DELETE /api/v1/videos`

---

## Testleri Çalıştırma

Testler **Pest** ile yazılmış olup gerçek bir MySQL veritabanına karşı çalışır. `RefreshDatabase` trait'i her testten önce migration'ları taze olarak kurar ve test bittikten sonra geri alır — mevcut verilerin hiçbiri etkilenmez.

Testleri çalıştırmadan önce `morph-to-many` veritabanının mevcut ve erişilebilir olduğundan emin ol (bağlantı bilgileri `phpunit.xml` dosyasından okunur).

```bash
php artisan test
```

Ya da doğrudan Pest ile:

```bash
./vendor/bin/pest
```

### Test Kapsamı

| Test | Ne kontrol eder |
|------|-----------------|
| `can fetch comments of a specific model` | GET isteği, ilgili post'a ait yorumları döndürüyor mu |
| `can create a new comment` | POST isteği, yorumu `comments` tablosuna kaydediyor mu |
| `can reply to an existing comment` | `parent_id` ile gelen POST, `comment_replies` pivot tablosuna ilişki satırını yazıyor mu |
