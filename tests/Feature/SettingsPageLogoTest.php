<?php

namespace Tests\Feature;

use App\Domain\Shop\Models\Setting;
use App\Filament\Pages\SettingsPage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingsPageLogoTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->actingAs($this->admin);
    }

    public function test_logo_path_saves_to_database(): void
    {
        Storage::fake('public');

        Livewire::test(SettingsPage::class)
            ->set('shopName', 'Test Cafe')
            ->set('shopLogo', [
                UploadedFile::fake()->image('logo.png'),
            ])
            ->call('save')
            ->assertSuccessful()
            ->assertSessionHasNoErrors();

        $savedLogo = Setting::getValue('shop_logo', '');
        $this->assertNotEmpty($savedLogo, 'shop_logo should not be empty after save');
        $this->assertStringStartsWith('logos/', $savedLogo, "Expected 'logos/...' but got '$savedLogo'");
        Storage::disk('public')->assertExists($savedLogo);
    }

    public function test_logo_persists_after_mount(): void
    {
        Storage::disk('public')->put('logos/test-logo.png', 'fake-content');

        Setting::setValue('shop_logo', 'logos/test-logo.png', 'string');

        Livewire::test(SettingsPage::class)
            ->assertSet('shopLogo', ['logos/test-logo.png']);
    }
}
