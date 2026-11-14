<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryMerchantSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Website Clean Lab - Inventory Pro')]
class WebsiteManager extends Component
{
    use WithFileUploads;

    public string $activeTab = 'hero';

    public bool $websiteEnabled = true;
    public string $websiteSlug  = 'clean-lab';

    // Tab 1: Brand & Hero
    public string $brandName    = '';
    public string $tagline      = '';
    public string $heroBadge    = '';
    public string $heroTitle    = '';
    public string $heroSubtitle = '';
    public $logoUpload;
    public $heroImageUpload;
    public ?string $existingLogoPath = null;
    public ?string $existingHeroPath = null;
    public array $stats = [];

    // Tab 2: Categories (Sepatu, Tas, Sandal, Helm, Koper, dll)
    public array $categories = [];

    // Tab 3: Pricelist Poster & Pricelist Table
    public $pricelistImageUpload;
    public ?string $existingPricelistImagePath = null;
    public array $pricelistSections = [];

    // Tab 4: Dynamic Promo Banners (Logo, Text, Potongan, Masa Berlaku, TNC)
    public array $promoBanners = [];
    public array $promoLogoUploads = [];

    // Tab 5: Modules & Features + Gallery
    public bool $showLiveServices = true;
    public bool $showLivePromos   = true;
    public bool $showOrderTracker = true;
    public array $features        = [];
    public array $gallery         = [];
    public $galleryUpload;
    public string $galleryCaption = '';

    // Tab 6: Contact & Maps
    public string $whatsappNumber     = '';
    public string $whatsappGreeting   = '';
    public string $instagramHandle    = '';
    public string $tiktokHandle       = '';
    public string $operatingHours     = '';
    public string $addressText        = '';
    public string $googleMapsLink     = '';
    public string $googleMapsEmbedUrl = '';

    public function mount(): void
    {
        $setting = LaundryMerchantSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'store_name'      => 'CLEAN LAB',
                'website_slug'    => $this->resolveDefaultSlug(),
                'website_enabled' => true,
                'website_config'  => LaundryMerchantSetting::defaultWebsiteConfig(),
            ]
        );

        if (empty($setting->website_slug)) {
            $setting->website_slug = $this->resolveDefaultSlug($setting->id);
            $setting->save();
        }

        $config = $setting->getWebsiteConfigWithDefaults();

        $this->websiteEnabled             = (bool) ($setting->website_enabled ?? true);
        $this->websiteSlug                = $setting->website_slug ?: 'clean-lab';
        $this->brandName                  = $config['brand_name'] ?? 'CLEAN LAB';
        $this->tagline                    = $config['tagline'] ?? '';
        $this->heroBadge                  = $config['hero_badge'] ?? '';
        $this->heroTitle                  = $config['hero_title'] ?? '';
        $this->heroSubtitle               = $config['hero_subtitle'] ?? '';
        $this->existingLogoPath           = $config['logo_path'] ?? 'laundry/website/cleanlab-logo.jpg';
        $this->existingHeroPath           = $config['hero_image_path'] ?? null;
        $this->existingPricelistImagePath = $config['pricelist_image_path'] ?? 'laundry/website/cleanlab-pricelist.jpg';
        $this->stats                      = $config['stats'] ?? [];
        $this->categories                 = array_values($config['categories'] ?? []);
        $this->pricelistSections          = array_values($config['pricelist_sections'] ?? []);
        $this->promoBanners               = array_values($config['promo_banners'] ?? []);
        $this->showLiveServices           = (bool) ($config['show_live_services'] ?? true);
        $this->showLivePromos             = (bool) ($config['show_live_promos'] ?? true);
        $this->showOrderTracker           = (bool) ($config['show_order_tracker'] ?? true);
        $this->features                   = array_values($config['features'] ?? []);
        $this->gallery                    = array_values($config['gallery'] ?? []);
        $this->whatsappNumber             = $config['whatsapp_number'] ?? '';
        $this->whatsappGreeting           = $config['whatsapp_greeting'] ?? '';
        $this->instagramHandle            = $config['instagram_handle'] ?? '';
        $this->tiktokHandle               = $config['tiktok_handle'] ?? '@clean_lab77';
        $this->operatingHours             = $config['operating_hours'] ?? '';
        $this->addressText                = $config['address_text'] ?? '';
        $this->googleMapsLink             = $config['google_maps_link'] ?? 'https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6';
        $this->googleMapsEmbedUrl         = $config['google_maps_embed_url'] ?? '';
    }

    private function resolveDefaultSlug(?int $ignoreId = null): string
    {
        $base = 'clean-lab';
        $exists = LaundryMerchantSetting::where('website_slug', $base)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        return $exists ? 'clean-lab-' . Auth::id() : $base;
    }

    public function addCategory(): void
    {
        $this->categories[] = [
            'icon'        => 'steps',
            'name'        => 'Layanan Baru',
            'items_label' => 'Sepatu / Tas / Helm / Koper',
            'description' => 'Deskripsi singkat perawatan untuk kategori barang ini.',
            'price_from'  => 35000,
            'badge'       => 'Baru',
        ];
    }

    public function removeCategory(int $index): void
    {
        unset($this->categories[$index]);
        $this->categories = array_values($this->categories);
    }

    public function addPromoBanner(): void
    {
        $this->promoBanners[] = [
            'is_active'     => true,
            'logo_path'     => null,
            'badge'         => '🎉 PROMO BARU',
            'title'         => 'Judul Promo Spesial Clean Lab',
            'description'   => 'Penjelasan singkat promo yang sedang berlangsung.',
            'discount_text' => 'DISKON 20%',
            'valid_from'    => now()->toDateString(),
            'valid_until'   => now()->addMonth()->toDateString(),
            'tnc'           => "1. Berlaku untuk semua treatment.\n2. Tunjukkan promo ini saat drop-off / order via WA.",
        ];
    }

    public function removePromoBanner(int $index): void
    {
        unset($this->promoBanners[$index]);
        $this->promoBanners = array_values($this->promoBanners);
    }

    public function removePromoLogo(int $index): void
    {
        if (!empty($this->promoBanners[$index]['logo_path'])) {
            Storage::disk('public')->delete($this->promoBanners[$index]['logo_path']);
            $this->promoBanners[$index]['logo_path'] = null;
        }
    }

    public function addPricelistSection(): void
    {
        $this->pricelistSections[] = [
            'category' => 'Kategori Baru',
            'icon'     => 'steps',
            'items'    => [
                ['name' => 'Nama Treatment', 'price' => 35000, 'note' => 'Keterangan singkat'],
            ],
        ];
    }

    public function removePricelistSection(int $sIdx): void
    {
        unset($this->pricelistSections[$sIdx]);
        $this->pricelistSections = array_values($this->pricelistSections);
    }

    public function addPricelistItem(int $sIdx): void
    {
        $this->pricelistSections[$sIdx]['items'][] = [
            'name'  => 'Treatment Baru',
            'price' => 30000,
            'note'  => '',
        ];
    }

    public function removePricelistItem(int $sIdx, int $iIdx): void
    {
        unset($this->pricelistSections[$sIdx]['items'][$iIdx]);
        $this->pricelistSections[$sIdx]['items'] = array_values($this->pricelistSections[$sIdx]['items']);
    }

    public function addFeature(): void
    {
        $this->features[] = [
            'icon'        => 'verified',
            'title'       => 'Keunggulan Baru',
            'description' => 'Penjelasan keunggulan layanan Clean Lab.',
        ];
    }

    public function removeFeature(int $index): void
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function uploadGalleryItem(): void
    {
        $this->validate([
            'galleryUpload'  => 'required|image|max:5120',
            'galleryCaption' => 'nullable|string|max:120',
        ]);

        $path = $this->galleryUpload->store('laundry/website/gallery', 'public');
        $this->gallery[] = [
            'path'    => $path,
            'caption' => $this->galleryCaption ?: 'Hasil Cuci Clean Lab',
        ];

        $this->galleryUpload  = null;
        $this->galleryCaption = '';
        $this->persistConfig('Foto galeri berhasil ditambahkan.');
    }

    public function removeGalleryItem(int $index): void
    {
        if (isset($this->gallery[$index]['path'])) {
            Storage::disk('public')->delete($this->gallery[$index]['path']);
        }
        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
        $this->persistConfig('Foto galeri dihapus.');
    }

    public function removeLogo(): void
    {
        $this->existingLogoPath = null;
        $this->persistConfig('Logo dihapus.');
    }

    public function removeHeroImage(): void
    {
        if ($this->existingHeroPath) {
            Storage::disk('public')->delete($this->existingHeroPath);
            $this->existingHeroPath = null;
            $this->persistConfig('Background hero dihapus.');
        }
    }

    public function removePricelistImage(): void
    {
        $this->existingPricelistImagePath = null;
        $this->persistConfig('Gambar brosur pricelist dihapus.');
    }

    public function resetToDefaultCategories(): void
    {
        $defaults = LaundryMerchantSetting::defaultWebsiteConfig();
        $this->categories        = $defaults['categories'];
        $this->pricelistSections = $defaults['pricelist_sections'];
        $this->persistConfig('Kategori & Pricelist dikembalikan ke default Clean Lab.');
    }

    public function save(): void
    {
        $setting = LaundryMerchantSetting::firstOrCreate(['user_id' => Auth::id()]);

        $slug = Str::slug($this->websiteSlug ?: 'clean-lab');
        if ($slug === '') {
            $slug = 'clean-lab';
        }
        $this->websiteSlug = $slug;

        $this->validate([
            'websiteSlug'                => 'required|string|max:100|unique:laundry_merchant_settings,website_slug,' . $setting->id,
            'brandName'                  => 'required|string|max:120',
            'tagline'                    => 'nullable|string|max:200',
            'heroBadge'                  => 'nullable|string|max:150',
            'heroTitle'                  => 'required|string|max:255',
            'heroSubtitle'               => 'nullable|string|max:600',
            'logoUpload'                 => 'nullable|image|max:4096',
            'heroImageUpload'            => 'nullable|image|max:5120',
            'pricelistImageUpload'       => 'nullable|image|max:5120',
            'whatsappNumber'             => 'nullable|string|max:30',
            'whatsappGreeting'           => 'nullable|string|max:255',
            'instagramHandle'            => 'nullable|string|max:100',
            'tiktokHandle'               => 'nullable|string|max:100',
            'operatingHours'             => 'nullable|string|max:200',
            'addressText'                => 'nullable|string|max:500',
            'googleMapsLink'             => 'nullable|url|max:500',
            'googleMapsEmbedUrl'         => 'nullable|string|max:1000',
            'categories.*.name'          => 'required|string|max:120',
            'categories.*.price_from'    => 'nullable|numeric|min:0',
            'promoBanners.*.title'       => 'required|string|max:200',
        ]);

        if ($this->logoUpload) {
            $this->existingLogoPath = $this->logoUpload->store('laundry/website', 'public');
            $this->logoUpload       = null;
        }

        if ($this->heroImageUpload) {
            $this->existingHeroPath = $this->heroImageUpload->store('laundry/website', 'public');
            $this->heroImageUpload  = null;
        }

        if ($this->pricelistImageUpload) {
            $this->existingPricelistImagePath = $this->pricelistImageUpload->store('laundry/website', 'public');
            $this->pricelistImageUpload       = null;
        }

        foreach ($this->promoLogoUploads as $pIdx => $uploadedLogo) {
            if ($uploadedLogo && isset($this->promoBanners[$pIdx])) {
                $this->promoBanners[$pIdx]['logo_path'] = $uploadedLogo->store('laundry/website/promos', 'public');
            }
        }
        $this->promoLogoUploads = [];

        $this->persistConfig('Pengaturan Website Clean Lab berhasil disimpan!');
    }

    private function persistConfig(string $flashMessage): void
    {
        $setting = LaundryMerchantSetting::firstOrCreate(['user_id' => Auth::id()]);

        $config = [
            'brand_name'            => $this->brandName,
            'tagline'               => $this->tagline,
            'hero_badge'            => $this->heroBadge,
            'hero_title'            => $this->heroTitle,
            'hero_subtitle'         => $this->heroSubtitle,
            'logo_path'             => $this->existingLogoPath,
            'hero_image_path'       => $this->existingHeroPath,
            'pricelist_image_path'  => $this->existingPricelistImagePath,
            'stats'                 => array_values($this->stats),
            'categories'            => array_values($this->categories),
            'pricelist_sections'    => array_values($this->pricelistSections),
            'promo_banners'         => array_values($this->promoBanners),
            'show_live_services'    => $this->showLiveServices,
            'show_live_promos'      => $this->showLivePromos,
            'show_order_tracker'    => $this->showOrderTracker,
            'features'              => array_values($this->features),
            'gallery'               => array_values($this->gallery),
            'whatsapp_number'       => $this->whatsappNumber,
            'whatsapp_greeting'     => $this->whatsappGreeting,
            'instagram_handle'      => $this->instagramHandle,
            'tiktok_handle'         => $this->tiktokHandle,
            'operating_hours'       => $this->operatingHours,
            'address_text'          => $this->addressText,
            'google_maps_link'      => $this->googleMapsLink ?: 'https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6',
            'google_maps_embed_url' => $this->googleMapsEmbedUrl,
        ];

        $setting->website_enabled = $this->websiteEnabled;
        $setting->website_slug    = $this->websiteSlug ?: 'clean-lab';
        $setting->website_config  = $config;
        if (empty($setting->store_name)) {
            $setting->store_name = $this->brandName;
        }
        $setting->save();

        session()->flash('website_saved', $flashMessage);
    }

    public function render()
    {
        return view('livewire.laundry.website-manager');
    }
}
