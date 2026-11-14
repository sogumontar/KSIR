<?php

namespace Tests\Feature;

use App\Livewire\Laundry\ShowcaseManager;
use App\Models\LaundryMerchantSetting;
use App\Models\LaundryShowcase;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LaundryShowcaseTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $u = User::factory()->create(['is_admin' => false, 'menu_laundry' => true]);
        LaundryMerchantSetting::create([
            'user_id'         => $u->id,
            'store_name'      => 'CLEAN LAB',
            'website_slug'    => 'clean-lab',
            'website_enabled' => true,
            'website_config'  => LaundryMerchantSetting::defaultWebsiteConfig(),
        ]);
        return $u;
    }

    public function test_owner_creates_category_and_photos_are_compressed_to_files(): void
    {
        Storage::fake('public');
        $owner = $this->owner();

        $before = UploadedFile::fake()->image('b.jpg', 2400, 1800);
        $after  = UploadedFile::fake()->image('a.png', 2400, 1800);

        $c = Livewire::actingAs($owner)->test(ShowcaseManager::class)
            ->call('openAdd')
            ->set('title', 'Unyellowing')
            ->set('description', 'Menghilangkan midsole menguning')
            ->set('videoUrl', 'https://youtu.be/dQw4w9WgXcQ')
            ->call('save')
            ->assertHasNoErrors();

        $s = LaundryShowcase::first();
        $this->assertNotNull($s);
        $this->assertSame($owner->id, $s->user_id);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', $s->video_embed_url);

        $c->call('openPhotoForm', $s->id)
            ->set('photoLabel', 'Nike AF1')
            ->set('beforeUpload', $before)
            ->set('afterUpload', $after)
            ->call('savePhoto')
            ->assertHasNoErrors();

        $photo = $s->photos()->first();
        $this->assertNotNull($photo);
        Storage::disk('public')->assertExists($photo->before_path);
        Storage::disk('public')->assertExists($photo->after_path);

        $path = Storage::disk('public')->path($photo->before_path);
        [$w, $h] = getimagesize($path);
        $this->assertLessThanOrEqual(1000, $w);
        $this->assertLessThanOrEqual(1000, $h);
        $this->assertLessThan(200 * 1024, filesize($path));
    }

    public function test_public_page_shows_showcase_and_deleting_removes_files(): void
    {
        Storage::fake('public');
        $owner = $this->owner();

        $s = LaundryShowcase::create([
            'user_id'     => $owner->id,
            'title'       => 'Helmet Detailing',
            'description' => 'Busa dicuci bersih',
        ]);

        Livewire::actingAs($owner)->test(ShowcaseManager::class)
            ->call('openPhotoForm', $s->id)
            ->set('beforeUpload', UploadedFile::fake()->image('b.jpg', 800, 600))
            ->set('afterUpload', UploadedFile::fake()->image('a.jpg', 800, 600))
            ->call('savePhoto');

        $photo = $s->photos()->first();
        $this->get('/cleanlab')
            ->assertOk()
            ->assertSee('Helmet Detailing')
            ->assertSee('Busa dicuci bersih');

        $s->delete();
        Storage::disk('public')->assertMissing($photo->before_path);
        Storage::disk('public')->assertMissing($photo->after_path);
    }

    public function test_user_cannot_touch_another_owners_showcase(): void
    {
        $owner = $this->owner();
        $other = User::factory()->create(['is_admin' => false, 'menu_laundry' => true]);
        $s = LaundryShowcase::create(['user_id' => $owner->id, 'title' => 'Private']);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($other)->test(ShowcaseManager::class)
            ->call('delete', $s->id);
    }
}
