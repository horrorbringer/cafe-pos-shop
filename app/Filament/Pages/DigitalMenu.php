<?php

namespace App\Filament\Pages;

use App\Domain\Shop\Models\Setting;
use App\Services\QrCodeService;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DigitalMenu extends Page
{
    protected string $view = 'filament.pages.digital-menu';

    protected static ?string $navigationLabel = 'Digital Menu';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?int $navigationSort = 3;

    protected static string|\UnitEnum|null $navigationGroup = 'Menu';

    public ?string $menuUrl = null;

    public ?string $qrCodeDataUri = null;

    public bool $isEnabled = true;

    public ?string $menuTitle = null;

    public ?string $menuSubtitle = null;

    public ?string $primaryColor = '#f59e0b';

    public ?string $logoPath = null;

    public bool $manuallyClosed = false;

    public ?string $openingHours = null;

    public ?string $promoBanner = null;

    public ?string $promoBannerText = null;

    public bool $enableKhmer = false;

    public ?string $socialFacebook = null;

    public ?string $socialInstagram = null;

    public ?string $socialTiktok = null;

    public ?string $socialYoutube = null;

    public ?string $socialTelegram = null;

    public ?string $socialTwitter = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'manager']) ?? false;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon;
    }

    public function getTitle(): string
    {
        return __('Digital Menu');
    }

    public function mount(): void
    {
        $qrService = app(QrCodeService::class);

        $this->menuUrl = $qrService->generateMenuUrl();
        $this->qrCodeDataUri = $qrService->generateMenuQrCode(null, 400);
        $this->isEnabled = (bool) Setting::getValue('digital_menu_enabled', true);
        $this->menuTitle = Setting::getValue('digital_menu_title', config('app.name', 'POS Cafe'));
        $this->menuSubtitle = Setting::getValue('digital_menu_subtitle', '');
        $this->primaryColor = Setting::getValue('digital_menu_color', '#f59e0b');
        $this->logoPath = Setting::getValue('digital_menu_logo', null);
        $this->manuallyClosed = (bool) Setting::getValue('digital_menu_manually_closed', false);
        $this->openingHours = Setting::getValue('digital_menu_opening_hours', '7:00 AM - 9:00 PM');
        $this->promoBanner = Setting::getValue('digital_menu_promo_banner', '');
        $this->promoBannerText = Setting::getValue('digital_menu_promo_banner_text', 'Special Offer!');
        $this->enableKhmer = (bool) Setting::getValue('digital_menu_enable_khmer', false);

        $socialLinks = Setting::getValue('digital_menu_social_links', []);
        $this->socialFacebook = $socialLinks['facebook'] ?? '';
        $this->socialInstagram = $socialLinks['instagram'] ?? '';
        $this->socialTiktok = $socialLinks['tiktok'] ?? '';
        $this->socialYoutube = $socialLinks['youtube'] ?? '';
        $this->socialTelegram = $socialLinks['telegram'] ?? '';
        $this->socialTwitter = $socialLinks['twitter'] ?? '';

    }

    public function saveTitle(): void
    {
        Setting::setValue('digital_menu_title', $this->menuTitle ?: config('app.name', 'POS Cafe'));
        $this->notifySaved();
    }

    public function saveSubtitle(): void
    {
        Setting::setValue('digital_menu_subtitle', $this->menuSubtitle);
        $this->notifySaved();
    }

    public function saveColor(): void
    {
        Setting::setValue('digital_menu_color', $this->primaryColor ?: '#f59e0b');
        $this->notifySaved();
    }

    public function saveLogo(): void
    {
        Setting::setValue('digital_menu_logo', $this->logoPath);
        $this->notifySaved();
    }

    public function saveEnabled(): void
    {
        Setting::setValue('digital_menu_enabled', $this->isEnabled ? '1' : '0', 'boolean');

        Notification::make()
            ->success()
            ->title($this->isEnabled ? 'Digital menu enabled' : 'Digital menu disabled')
            ->send();
    }

    public function copyUrl(): void
    {
        $this->dispatch('copy-to-clipboard', url: $this->menuUrl);

        Notification::make()
            ->success()
            ->title('URL copied to clipboard')
            ->send();
    }

    public function removeLogo(): void
    {
        $this->logoPath = null;
        Setting::setValue('digital_menu_logo', null);
        $this->notifySaved();
    }

    public function saveOpeningHours(): void
    {
        Setting::setValue('digital_menu_opening_hours', $this->openingHours ?: '7:00 AM - 9:00 PM');
        $this->notifySaved();
    }

    public function saveManuallyClosed(): void
    {
        Setting::setValue('digital_menu_manually_closed', $this->manuallyClosed);
        $this->notifySaved();
    }

    public function savePromoBanner(): void
    {
        Setting::setValue('digital_menu_promo_banner', $this->promoBanner);
        Setting::setValue('digital_menu_promo_banner_text', $this->promoBannerText);
        $this->notifySaved();
    }

    public function saveEnableKhmer(): void
    {
        Setting::setValue('digital_menu_enable_khmer', $this->enableKhmer ? '1' : '0', 'boolean');
        $this->notifySaved();
    }

    public function saveSocialLinks(): void
    {
        Setting::setValue('digital_menu_social_links', json_encode([
            'facebook' => $this->socialFacebook,
            'instagram' => $this->socialInstagram,
            'tiktok' => $this->socialTiktok,
            'youtube' => $this->socialYoutube,
            'telegram' => $this->socialTelegram,
            'twitter' => $this->socialTwitter,
        ]), 'json');

        $this->notifySaved();
    }

    private function notifySaved(): void
    {
        Notification::make()
            ->success()
            ->title('Saved')
            ->send();
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('menuTitle')
                ->label('Title')
                ->placeholder(config('app.name', 'POS Cafe'))
                ->maxLength(100)
                ->live(onBlur: true)
                ->afterStateUpdated(fn () => $this->saveTitle()),

            Textarea::make('menuSubtitle')
                ->label('Subtitle')
                ->placeholder('Welcome! Scan to order.')
                ->rows(2)
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn () => $this->saveSubtitle()),

            ColorPicker::make('primaryColor')
                ->label('Color')
                ->live(onBlur: true)
                ->afterStateUpdated(fn () => $this->saveColor()),

            FileUpload::make('logoPath')
                ->label('Logo')
                ->image()
                ->disk('public')
                ->imageEditor()
                ->directory('menu-logo')
                ->maxSize(1024)
                ->live()
                ->afterStateUpdated(fn () => $this->saveLogo()),

            Toggle::make('isEnabled')
                ->label('Enabled')
                ->live()
                ->afterStateUpdated(fn () => $this->saveEnabled()),
        ];
    }
}
