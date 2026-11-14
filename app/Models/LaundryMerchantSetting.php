<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'store_name',
    'store_address',
    'header_bg_image',
    'qr_code_path',
    'payment_notes',
    'store_status',
    'website_slug',
    'website_enabled',
    'website_config',
])]
class LaundryMerchantSetting extends Model
{
    protected function casts(): array
    {
        return [
            'website_enabled' => 'boolean',
            'website_config'  => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function defaultWebsiteConfig(): array
    {
        return [
            'brand_name'           => 'CLEAN LAB',
            'tagline'              => 'PREMIUM SERVICE — SHOES, HELMETS, BAGS, HATS, SANDALS',
            'hero_badge'           => '✨ SERAHKAN PEMBERSIHAN ANDA KEPADA AHLINYA!',
            'hero_title'           => 'Bikin Sepatu, Helm, Tas, Topi & Sandal Kembali Seperti Baru.',
            'hero_subtitle'        => 'Perawatan pencucian profesional dengan teknologi modern, hasil bersih maksimal, wangi tahan lama, dan gratis konsultasi serta antar-jemput radius 3KM.',
            'whatsapp_number'      => '6285139763366',
            'whatsapp_greeting'    => 'Halo Clean Lab! Saya ingin konsultasi / order cuci sepatu, helm, tas, atau sandal.',
            'instagram_handle'     => '@clean_lab77',
            'tiktok_handle'        => '@clean_lab77',
            'operating_hours'      => 'Senin - Minggu: 09.00 - 21.00 WIB',
            'address_text'         => 'Lobby Apartement (B1/B2) Menara Cawang, Kec. Kramat Jati, Kota Jakarta Timur, 13630',
            'google_maps_link'     => 'https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6',
            'google_maps_embed_url'=> 'https://maps.google.com/maps?q=Apartemen%20Menara%20Cawang%20Jakarta%20Timur&t=&z=16&ie=UTF8&iwloc=&output=embed',
            'show_live_services'   => true,
            'show_live_promos'     => true,
            'show_order_tracker'   => true,
            'accent_color'         => 'emerald',
            'logo_path'            => 'laundry/website/cleanlab-logo.jpg',
            'hero_image_path'      => null,
            'pricelist_image_path' => 'laundry/website/cleanlab-pricelist.jpg',
            'stats' => [
                ['value' => 'Teknologi Modern', 'label' => 'Alat & Cairan Khusus Aman'],
                ['value' => 'Gratis Konsultasi', 'label' => 'Tanya Treatment Sesuai Bahan'],
                ['value' => 'Free Pickup 3KM', 'label' => 'Antar Jemput Radius 3KM'],
            ],
            'pricelist_sections' => [
                [
                    'category' => 'Shoes (Sepatu)',
                    'icon'     => 'steps',
                    'items'    => [
                        ['name' => 'Fast Cleaning', 'price' => 35000, 'note' => 'Upper & Midsole'],
                        ['name' => 'Deep Cleaning', 'price' => 50000, 'note' => 'All Parts Deep Wash'],
                        ['name' => 'Unyellowing', 'price' => 85000, 'note' => 'Pemutihan Midsole Kuning'],
                        ['name' => 'Kid Size', 'price' => 30000, 'note' => 'Ukuran Sepatu Anak'],
                    ],
                ],
                [
                    'category' => 'Sandals (Sandal)',
                    'icon'     => 'roller_skating',
                    'items'    => [
                        ['name' => 'Fast Cleaning', 'price' => 15000, 'note' => 'Pembersihan Cepat'],
                        ['name' => 'Deep Cleaning', 'price' => 30000, 'note' => 'Cuci Menyeluruh + Anti Bau'],
                        ['name' => 'Unyellowing', 'price' => 50000, 'note' => 'Hilangkan Noda Menguning'],
                        ['name' => 'Kid Size', 'price' => 10000, 'note' => 'Sandal Anak'],
                    ],
                ],
                [
                    'category' => 'Helmets (Helm)',
                    'icon'     => 'sports_motorsports',
                    'items'    => [
                        ['name' => 'Half Face Cleaning', 'price' => 20000, 'note' => 'Cuci Busa & Shell Half Face'],
                        ['name' => 'Full Face Cleaning', 'price' => 25000, 'note' => 'Cuci Busa & Visor Full Face'],
                        ['name' => 'Premium Detailing', 'price' => 50000, 'note' => 'Deep Wash + Poles + Fogging'],
                    ],
                ],
                [
                    'category' => 'Bags & Caps (Tas, Koper & Topi)',
                    'icon'     => 'work',
                    'items'    => [
                        ['name' => 'Standard Cleaning', 'price' => 35000, 'note' => 'Tas Harian / Topi / Caps'],
                        ['name' => 'Premium Detailing', 'price' => 55000, 'note' => 'Tas Bahan Khusus / Branded'],
                        ['name' => 'Carrier / Luggage Cleaning', 'price' => 35000, 'note' => 'Tas Carrier Gunung / Koper'],
                    ],
                ],
            ],
            'promo_banners' => [
                [
                    'is_active'     => true,
                    'logo_path'     => null,
                    'badge'         => '🔥 PROMO SPESIAL CLEAN LAB',
                    'title'         => 'Gratis Antar Jemput Radius 3KM & Promo Cuci Hemat!',
                    'description'   => 'Nikmati layanan jemput & antar cucian sepatu, helm, tas, topi, dan sandal langsung dari Lobby Apartemen Menara Cawang & area sekitarnya.',
                    'discount_text' => 'FREE PICKUP 3KM',
                    'valid_from'    => now()->startOfMonth()->toDateString(),
                    'valid_until'   => now()->addMonths(3)->endOfMonth()->toDateString(),
                    'tnc'           => "1. Gratis antar jemput berlaku untuk area Lobby Apartemen Menara Cawang & sekitarnya (maksimal radius 3 KM).\n2. Berlaku untuk pencucian Sepatu, Sandal, Helm, Tas, Topi & Koper.\n3. Hubungi WhatsApp 0851-3976-3366 untuk penjadwalan pickup.",
                ],
            ],
            'categories' => [
                [
                    'icon'        => 'steps',
                    'name'        => 'Shoes (Cuci Sepatu)',
                    'items_label' => 'Fast Cleaning 35K • Deep Cleaning 50K • Unyellowing 85K • Kid Size 30K',
                    'description' => 'Pencucian sepatu sneakers, kanvas, suede, leather, dan sepatu anak dengan treatment pemutihan midsole (unyellowing).',
                    'price_from'  => 30000,
                    'badge'       => 'Best Seller',
                ],
                [
                    'icon'        => 'roller_skating',
                    'name'        => 'Sandals (Cuci Sandal)',
                    'items_label' => 'Fast Cleaning 15K • Deep Cleaning 30K • Unyellowing 50K • Kid Size 10K',
                    'description' => 'Membersihkan kerak telapak kaki, noda membandel pada karet/cork, dan unyellowing sandal kesayangan.',
                    'price_from'  => 10000,
                    'badge'       => 'Mulai 10K',
                ],
                [
                    'icon'        => 'sports_motorsports',
                    'name'        => 'Helmets (Cuci & Detailing Helm)',
                    'items_label' => 'Half Face 20K • Full Face 25K • Premium Detailing 50K',
                    'description' => 'Cuci busa interior, poles visor & shell luar, serta sterilisasi bakteri agar helm wangi dan bebas bau apek.',
                    'price_from'  => 20000,
                    'badge'       => 'Higienis',
                ],
                [
                    'icon'        => 'work',
                    'name'        => 'Bags & Caps (Tas, Topi & Carrier)',
                    'items_label' => 'Standard Cleaning 35K • Premium Detailing 55K • Carrier Cleaning 35K',
                    'description' => 'Perawatan tas sekolah/kerja, tas carrier gunung, koper, hingga topi (caps/bucket hat) tanpa merusak bentuk.',
                    'price_from'  => 35000,
                    'badge'       => 'Premium Care',
                ],
                [
                    'icon'        => 'luggage',
                    'name'        => 'Luggage & Koper Travel',
                    'items_label' => 'Koper Kabin, Medium, Large & Tas Travel',
                    'description' => 'Pembersihan noda perjalanan, roda, handle, serta disinfeksi bagian dalam koper agar siap dipakai liburan kembali.',
                    'price_from'  => 35000,
                    'badge'       => 'Travel Ready',
                ],
                [
                    'icon'        => 'auto_awesome',
                    'name'        => 'Unyellowing & Premium Detailing',
                    'items_label' => 'Midsole Sepatu/Sandal Menguning & Deep Detailing',
                    'description' => 'Treatment khusus menghilangkan warna kekuningan akibat oksidasi pada sol sepatu/sandal serta detailing menyeluruh.',
                    'price_from'  => 50000,
                    'badge'       => 'Special Care',
                ],
            ],
            'features' => [
                [
                    'icon'        => 'precision_manufacturing',
                    'title'       => 'Teknologi Modern',
                    'description' => 'Menggunakan peralatan dan cairan pembersih modern yang aman untuk segala jenis material sepatu, tas, dan helm.',
                ],
                [
                    'icon'        => 'workspace_premium',
                    'title'       => 'Hasil Premium & Terjamin',
                    'description' => 'Dikerjakan oleh teknisi berpengalaman dengan standar quality control tinggi agar bersih maksimal dan wangi.',
                ],
                [
                    'icon'        => 'forum',
                    'title'       => 'Konsultasi Gratis',
                    'description' => 'Diskusikan kondisi noda atau bahan sepatu, tas, dan helm Anda secara gratis via WhatsApp sebelum dicuci.',
                ],
                [
                    'icon'        => 'two_wheeler',
                    'title'       => 'Antar Jemput Radius 3KM',
                    'description' => 'Sibuk beraktivitas? Kami siap menjemput dan mengantar cucian Anda untuk area Menara Cawang & radius 3KM.',
                ],
            ],
            'gallery' => [],
        ];
    }

    public function getWebsiteConfigWithDefaults(): array
    {
        $defaults = self::defaultWebsiteConfig();
        $saved    = is_array($this->website_config) ? $this->website_config : [];

        $merged = array_merge($defaults, $saved);

        if (empty($merged['brand_name'])) {
            $merged['brand_name'] = $this->store_name ?: $defaults['brand_name'];
        }
        if (empty($merged['google_maps_link'])) {
            $merged['google_maps_link'] = $defaults['google_maps_link'];
        }
        if (empty($merged['logo_path'])) {
            $merged['logo_path'] = $defaults['logo_path'];
        }
        if (empty($merged['pricelist_image_path'])) {
            $merged['pricelist_image_path'] = $defaults['pricelist_image_path'];
        }
        if (empty($merged['pricelist_sections']) || !is_array($merged['pricelist_sections'])) {
            $merged['pricelist_sections'] = $defaults['pricelist_sections'];
        }
        if (!isset($merged['promo_banners']) || !is_array($merged['promo_banners'])) {
            $merged['promo_banners'] = $defaults['promo_banners'];
        }
        if (empty($merged['categories']) || !is_array($merged['categories'])) {
            $merged['categories'] = $defaults['categories'];
        }
        if (empty($merged['features']) || !is_array($merged['features'])) {
            $merged['features'] = $defaults['features'];
        }
        if (!isset($merged['gallery']) || !is_array($merged['gallery'])) {
            $merged['gallery'] = [];
        }

        // Upgrade legacy default phone/address if unchanged from initial placeholder
        if (($merged['whatsapp_number'] ?? '') === '6285156000000') {
            $merged['whatsapp_number']   = $defaults['whatsapp_number'];
            $merged['instagram_handle']  = $defaults['instagram_handle'];
            $merged['address_text']      = $defaults['address_text'];
            $merged['categories']        = $defaults['categories'];
            $merged['features']          = $defaults['features'];
            $merged['stats']             = $defaults['stats'];
        }

        return $merged;
    }
}
