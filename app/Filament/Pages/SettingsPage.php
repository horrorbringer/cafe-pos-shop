<?php

namespace App\Filament\Pages;

use App\Domain\Shop\Models\Setting;
use App\Filament\Resources\Notifications\NotificationChannelResource;
use App\Filament\Resources\Notifications\NotificationLogResource;
use App\Filament\Resources\Notifications\NotificationRecipientResource;
use App\Filament\Resources\Notifications\NotificationRuleResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class SettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Settings';

    protected string $view = 'filament.pages.settings-page';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?string $shopName = null;

    public ?string $shopAddress = null;

    public ?string $shopPhone = null;

    public ?string $shopLogo = null;

    public ?string $shopCurrency = null;

    public ?float $shopTaxRate = null;

    public bool $paymentsCashEnabled = true;

    public bool $paymentsKhqrEnabled = false;

    public ?string $paymentsProvider = null;

    public ?string $receiptHeader = null;

    public ?string $receiptFooter = null;

    public ?string $receiptPrinter = null;

    public ?string $receiptTemplate = 'classic';

    public bool $receiptShowAddress = true;

    public bool $receiptShowPhone = true;

    public bool $receiptShowLogo = false;

    public bool $receiptShowOrderType = true;

    public bool $receiptShowTable = true;

    public bool $receiptShowCashier = true;

    public bool $receiptShowModifiers = true;

    public bool $receiptShowDiscount = true;

    public bool $receiptShowPayment = true;

    public bool $receiptShowNotes = true;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $this->shopName = Setting::getValue('shop_name', '');
        $this->shopAddress = Setting::getValue('shop_address', '');
        $this->shopPhone = Setting::getValue('shop_phone', '');
        $this->shopLogo = Setting::getValue('shop_logo', '');
        $this->shopCurrency = Setting::getValue('shop_currency', 'USD');
        $this->shopTaxRate = (float) Setting::getValue('shop_tax_rate', 0);

        $this->paymentsCashEnabled = Setting::getValue('payments_cash_enabled', true);
        $this->paymentsKhqrEnabled = Setting::getValue('payments_khqr_enabled', false);
        $this->paymentsProvider = Setting::getValue('payments_provider', '');

        $this->receiptHeader = Setting::getValue('receipt_header', '');
        $this->receiptFooter = Setting::getValue('receipt_footer', '');
        $this->receiptPrinter = Setting::getValue('receipt_printer', '');
        $this->receiptTemplate = Setting::getValue('receipt_template', 'classic');
        $this->receiptShowAddress = Setting::getValue('receipt_show_address', true);
        $this->receiptShowPhone = Setting::getValue('receipt_show_phone', true);
        $this->receiptShowLogo = Setting::getValue('receipt_show_logo', false);
        $this->receiptShowOrderType = Setting::getValue('receipt_show_order_type', true);
        $this->receiptShowTable = Setting::getValue('receipt_show_table', true);
        $this->receiptShowCashier = Setting::getValue('receipt_show_cashier', true);
        $this->receiptShowModifiers = Setting::getValue('receipt_show_modifiers', true);
        $this->receiptShowDiscount = Setting::getValue('receipt_show_discount', true);
        $this->receiptShowPayment = Setting::getValue('receipt_show_payment', true);
        $this->receiptShowNotes = Setting::getValue('receipt_show_notes', true);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Shop')
                            ->icon(Heroicon::OutlinedBuildingStorefront)
                            ->schema([
                                Section::make('Shop Information')
                                    ->icon(Heroicon::OutlinedBuildingStorefront)
                                    ->description('Your cafe details displayed on receipts and the digital menu.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('shopName')
                                            ->label('Shop Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('My Cafe')
                                            ->columnSpanFull(),

                                        TextInput::make('shopAddress')
                                            ->label('Address')
                                            ->maxLength(500)
                                            ->placeholder('123 Main St, City')
                                            ->helperText('Displayed on receipts.'),

                                        TextInput::make('shopPhone')
                                            ->label('Phone')
                                            ->tel()
                                            ->maxLength(50)
                                            ->placeholder('+1234567890'),

                                        FileUpload::make('shopLogo')
                                            ->label('Logo')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('logos')
                                            ->maxSize(2048)
                                            ->columnSpanFull(),

                                        Select::make('shopCurrency')
                                            ->label('Currency')
                                            ->options([
                                                'USD' => 'USD ($)',
                                                'KHR' => 'KHR (៛)',
                                                'EUR' => 'EUR (€)',
                                                'GBP' => 'GBP (£)',
                                                'THB' => 'THB (฿)',
                                            ])
                                            ->default('USD')
                                            ->required()
                                            ->helperText('Default currency for orders.'),

                                        TextInput::make('shopTaxRate')
                                            ->label('Tax Rate (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->default(0)
                                            ->suffix('%')
                                            ->helperText('Applied to all orders.'),
                                    ]),
                            ]),

                        Tab::make('Payments')
                            ->icon(Heroicon::OutlinedCreditCard)
                            ->schema([
                                Section::make('Payment Methods')
                                    ->icon(Heroicon::OutlinedCurrencyDollar)
                                    ->description('Enable or disable payment options at the counter.')
                                    ->columns(1)
                                    ->schema([
                                        Toggle::make('paymentsCashEnabled')
                                            ->label('Cash Payments')
                                            ->helperText('Accept cash at the point of sale.')
                                            ->default(true),

                                        Toggle::make('paymentsKhqrEnabled')
                                            ->label('KHQR Payments')
                                            ->helperText('Allow customers to pay via KHQR scan.')
                                            ->default(false),

                                        Select::make('paymentsProvider')
                                            ->label('KHQR Provider')
                                            ->options([
                                                'aba' => 'ABA Pay',
                                                'wing' => 'Wing',
                                                'acleda' => 'ACLEDA Bank',
                                            ])
                                            ->placeholder('None')
                                            ->nullable()
                                            ->helperText('Select a provider to enable KHQR processing.'),
                                    ]),
                            ]),

                        Tab::make('Notifications')
                            ->icon(Heroicon::OutlinedBell)
                            ->schema([
                                Section::make('Notification Setup')
                                    ->icon(Heroicon::OutlinedBell)
                                    ->description('Follow these 3 steps to start receiving notifications.')
                                    ->schema([
                                        Actions::make([
                                            Action::make('step1')
                                                ->label('Step 1: Add Notification Channels')
                                                ->url(fn () => NotificationChannelResource::getUrl('index'))
                                                ->icon(Heroicon::OutlinedEnvelope)
                                                ->color('primary')
                                                ->size('lg')
                                                ->link(),

                                            Action::make('step2')
                                                ->label('Step 2: Add Recipients')
                                                ->url(fn () => NotificationRecipientResource::getUrl('index'))
                                                ->icon(Heroicon::OutlinedUserPlus)
                                                ->color('gray')
                                                ->size('lg')
                                                ->link(),

                                            Action::make('step3')
                                                ->label('Step 3: Create Rules')
                                                ->url(fn () => NotificationRuleResource::getUrl('index'))
                                                ->icon(Heroicon::OutlinedClipboardDocumentList)
                                                ->color('gray')
                                                ->size('lg')
                                                ->link(),

                                            Action::make('logs')
                                                ->label('View History')
                                                ->url(fn () => NotificationLogResource::getUrl('index'))
                                                ->icon(Heroicon::OutlinedClock)
                                                ->color('gray')
                                                ->link(),
                                        ]),

                                        View::make('filament.notifications-setup-help'),
                                    ]),
                            ]),

                        Tab::make('Users')
                            ->icon(Heroicon::OutlinedUsers)
                            ->schema([
                                Section::make('User Management')
                                    ->icon(Heroicon::OutlinedUsers)
                                    ->description('Manage admin and staff accounts with role-based access.')
                                    ->schema([
                                        Actions::make([
                                            Action::make('manage_users')
                                                ->label('Manage Users')
                                                ->url(fn () => UserResource::getUrl('index'))
                                                ->icon(Heroicon::OutlinedUsers)
                                                ->color('primary')
                                                ->size('lg')
                                                ->link(),
                                        ]),
                                    ]),
                            ]),

                        Tab::make('Receipt')
                            ->icon(Heroicon::OutlinedPrinter)
                            ->schema([
                                Group::make()
                                    ->columns(2)
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                Section::make('Layout')
                                                    ->icon(Heroicon::OutlinedRectangleGroup)
                                                    ->compact()
                                                    ->columns(2)
                                                    ->schema([
                                                        Select::make('receiptTemplate')
                                                            ->label('Template')
                                                            ->options([
                                                                'classic' => 'Classic',
                                                                'minimal' => 'Minimal',
                                                                'detailed' => 'Detailed',
                                                                'compact' => 'Compact',
                                                                'branded' => 'Branded',
                                                            ])
                                                            ->default('classic')
                                                            ->helperText('Classic: Standard borders. Minimal: Clean no borders. Detailed: Full info with thick borders. Compact: Tight for thermal. Branded: Decorative with logo.')
                                                            ->live()
                                                            ->native(false)
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Select::make('receiptPrinter')
                                                            ->label('Paper Width')
                                                            ->options([
                                                                'default' => 'Standard (40 chars)',
                                                                'thermal' => 'Thermal Printer (32 chars)',
                                                                'pdf' => 'PDF / Email (48 chars)',
                                                            ])
                                                            ->default('default')
                                                            ->helperText('Wider formats show more columns.')
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Action::make('printTest')
                                                            ->label('Print Test')
                                                            ->icon('heroicon-o-printer')
                                                            ->color('gray')
                                                            ->size('sm')
                                                            ->extraAttributes(['class' => 'self-end'])
                                                            ->url(fn () => route('admin.test-receipt'), shouldOpenInNewTab: true),
                                                    ]),

                                                Section::make('Show on Receipt')
                                                    ->icon(Heroicon::OutlinedEye)
                                                    ->compact()
                                                    ->columns(4)
                                                    ->schema([
                                                        Checkbox::make('receiptShowAddress')
                                                            ->label('Address')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowPhone')
                                                            ->label('Phone')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowLogo')
                                                            ->label('Logo')
                                                            ->default(false)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowOrderType')
                                                            ->label('Type')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowTable')
                                                            ->label('Table')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowCashier')
                                                            ->label('Cashier')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowModifiers')
                                                            ->label('Options')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowDiscount')
                                                            ->label('Discount')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowPayment')
                                                            ->label('Payment')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Checkbox::make('receiptShowNotes')
                                                            ->label('Notes')
                                                            ->default(true)
                                                            ->live()
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),
                                                    ]),

                                                Section::make('Header & Footer')
                                                    ->icon(Heroicon::OutlinedDocumentText)
                                                    ->compact()
                                                    ->schema([
                                                        Textarea::make('receiptHeader')
                                                            ->label('Custom Header')
                                                            ->rows(2)
                                                            ->maxLength(500)
                                                            ->placeholder('Welcome to Our Cafe!')
                                                            ->helperText('Appears after shop details.')
                                                            ->live(debounce: 500)
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),

                                                        Textarea::make('receiptFooter')
                                                            ->label('Custom Footer')
                                                            ->rows(2)
                                                            ->maxLength(500)
                                                            ->placeholder('Thank you for your visit!')
                                                            ->helperText('Appears before the closing line.')
                                                            ->live(debounce: 500)
                                                            ->afterStateUpdated(fn () => $this->saveReceipt()),
                                                    ]),
                                            ]),

                                        Group::make()
                                            ->schema([
                                                Html::make(fn () => $this->renderReceiptPreview()),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public function renderReceiptPreview(): string
    {
        $width = match ($this->receiptPrinter) {
            'thermal' => 32, 'pdf' => 48, default => 40
        };

        return view('filament.receipt-preview-live', [
            'width' => $width,
            'template' => $this->receiptTemplate ?? 'classic',
            'shopName' => $this->shopName ?? 'My Cafe',
            'address' => $this->shopAddress ?? '',
            'phone' => $this->shopPhone ?? '',
            'logoUrl' => $this->shopLogo ? Storage::url($this->shopLogo) : null,
            'header' => $this->receiptHeader ?? '',
            'footer' => $this->receiptFooter ?? '',
            'showAddress' => $this->receiptShowAddress ?? true,
            'showPhone' => $this->receiptShowPhone ?? true,
            'showLogo' => $this->receiptShowLogo ?? false,
            'showOrderType' => $this->receiptShowOrderType ?? true,
            'showTable' => $this->receiptShowTable ?? true,
            'showCashier' => $this->receiptShowCashier ?? true,
            'showModifiers' => $this->receiptShowModifiers ?? true,
            'showDiscount' => $this->receiptShowDiscount ?? true,
            'showPayment' => $this->receiptShowPayment ?? true,
            'showNotes' => $this->receiptShowNotes ?? true,
        ])->render();
    }

    private function saveReceipt(): void
    {
        Setting::setValue('receipt_header', $this->receiptHeader, 'string');
        Setting::setValue('receipt_footer', $this->receiptFooter, 'string');
        Setting::setValue('receipt_printer', $this->receiptPrinter, 'string');
        Setting::setValue('receipt_template', $this->receiptTemplate, 'string');
        Setting::setValue('receipt_show_address', $this->receiptShowAddress ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_phone', $this->receiptShowPhone ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_logo', $this->receiptShowLogo ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_order_type', $this->receiptShowOrderType ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_table', $this->receiptShowTable ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_cashier', $this->receiptShowCashier ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_modifiers', $this->receiptShowModifiers ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_discount', $this->receiptShowDiscount ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_payment', $this->receiptShowPayment ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_notes', $this->receiptShowNotes ? '1' : '0', 'boolean');
    }

    public function save(): void
    {
        Setting::setValue('shop_name', $this->shopName, 'string');
        Setting::setValue('shop_address', $this->shopAddress, 'string');
        Setting::setValue('shop_phone', $this->shopPhone, 'string');
        Setting::setValue('shop_logo', $this->shopLogo, 'string');
        Setting::setValue('shop_currency', $this->shopCurrency, 'string');
        Setting::setValue('shop_tax_rate', (string) $this->shopTaxRate, 'float');

        Setting::setValue('payments_cash_enabled', $this->paymentsCashEnabled ? '1' : '0', 'boolean');
        Setting::setValue('payments_khqr_enabled', $this->paymentsKhqrEnabled ? '1' : '0', 'boolean');
        Setting::setValue('payments_provider', $this->paymentsProvider, 'string');

        Setting::setValue('receipt_header', $this->receiptHeader, 'string');
        Setting::setValue('receipt_footer', $this->receiptFooter, 'string');
        Setting::setValue('receipt_printer', $this->receiptPrinter, 'string');
        Setting::setValue('receipt_template', $this->receiptTemplate, 'string');
        Setting::setValue('receipt_show_address', $this->receiptShowAddress ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_phone', $this->receiptShowPhone ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_logo', $this->receiptShowLogo ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_order_type', $this->receiptShowOrderType ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_table', $this->receiptShowTable ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_cashier', $this->receiptShowCashier ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_modifiers', $this->receiptShowModifiers ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_discount', $this->receiptShowDiscount ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_payment', $this->receiptShowPayment ? '1' : '0', 'boolean');
        Setting::setValue('receipt_show_notes', $this->receiptShowNotes ? '1' : '0', 'boolean');

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('All settings have been updated successfully.')
            ->send();
    }
}
