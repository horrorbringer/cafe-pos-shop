<?php

namespace App\Http\Controllers\Menu;

use App\Domain\Catalog\Models\Category;
use App\Domain\Shop\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DigitalMenuController
{
    public function __invoke(Request $request): View
    {
        $tableId = $request->query('table');

        $categories = Category::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->where('is_available', true)
                    ->with([
                        'variants' => function ($q) {
                            $q->where('is_active', true)->orderBy('sort_order');
                        },
                        'modifierGroups' => function ($q) {
                            $q->where('is_active', true)->orderBy('sort_order');
                        },
                    ])
                    ->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->get();

        $openingHours = Setting::getValue('digital_menu_opening_hours', '7:00 AM - 9:00 PM');
        $manuallyClosed = (bool) Setting::getValue('digital_menu_manually_closed', false);

        $menuSettings = [
            'title' => Setting::getValue('digital_menu_title', config('app.name', 'POS Cafe')),
            'subtitle' => Setting::getValue('digital_menu_subtitle', ''),
            'primary_color' => Setting::getValue('digital_menu_color', '#f59e0b'),
            'logo' => Setting::getValue('digital_menu_logo', null),
            'opening_hours' => $openingHours,
            'is_open' => $manuallyClosed ? false : $this->isCurrentlyOpen($openingHours),
            'promo_banner' => Setting::getValue('digital_menu_promo_banner', ''),
            'promo_banner_text' => Setting::getValue('digital_menu_promo_banner_text', 'Special Offer!'),
            'enable_khmer' => Setting::getValue('digital_menu_enable_khmer', false),
            'social_links' => $this->buildSocialLinks(
                handles: Setting::getValue('digital_menu_social_links', []),
                enabled: Setting::getValue('digital_menu_social_enabled', []),
            ),
        ];

        $productsJson = $categories->flatMap(function ($category) {
            return $category->products->map(function ($product) use ($category) {
                $variants = $product->variants;
                $minPrice = $variants->isNotEmpty() ? $product->min_price : $product->price;
                $maxPrice = $variants->isNotEmpty() ? $product->max_price : $product->price;
                $hasPriceRange = $variants->count() > 1 && $minPrice != $maxPrice;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => (float) $product->price,
                    'min_price' => (float) $minPrice,
                    'max_price' => (float) $maxPrice,
                    'has_price_range' => $hasPriceRange,
                    'image' => $product->image ? asset('storage/'.$product->image) : null,
                    'is_available' => $product->is_available,
                    'calories' => $product->calories,
                    'allergens' => $product->allergens ?: [],
                    'tags' => $product->tags ?: [],
                    'category_id' => $category->id,
                    'category_slug' => $category->slug,
                    'category_name' => $category->name,
                    'variants' => $variants->map(fn ($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'price' => (float) $v->effective_price,
                        'adjustment' => (float) $v->price_adjustment,
                    ]),
                    'modifier_groups' => $product->modifierGroups->map(fn ($group) => [
                        'id' => $group->id,
                        'name' => $group->name,
                        'is_required' => $group->is_required,
                        'max_selections' => $group->max_selections,
                        'options' => $group->options->where('is_active', true)->sortBy('sort_order')->values()->map(fn ($option) => [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => (float) $option->price,
                        ]),
                    ]),
                ];
            });
        })->values();

        $enTranslations = json_decode(file_get_contents(lang_path('en.json')), true);
        $kmTranslations = json_decode(file_get_contents(lang_path('km.json')), true);

        return view('menu.index', compact(
            'categories', 'tableId', 'menuSettings', 'productsJson', 'enTranslations', 'kmTranslations',
        ));
    }

    private function buildSocialLinks(array $handles, array $enabled = []): array
    {
        $bases = [
            'facebook' => 'https://facebook.com/',
            'instagram' => 'https://instagram.com/',
            'tiktok' => 'https://tiktok.com/@',
            'youtube' => 'https://youtube.com/@',
            'telegram' => 'https://t.me/',
            'twitter' => 'https://x.com/',
        ];

        $links = [];
        foreach ($bases as $platform => $base) {
            $isEnabled = (bool) ($enabled[$platform] ?? true);
            $handle = trim($handles[$platform] ?? '');
            if ($isEnabled && $handle !== '') {
                $links[$platform] = $base.ltrim($handle, '@');
            }
        }

        return $links;
    }

    private function isCurrentlyOpen(string $hoursText): bool
    {
        if (empty($hoursText) || ! str_contains($hoursText, '-')) {
            return true;
        }

        $parts = explode('-', $hoursText);
        $openTime = trim($parts[0]);
        $closeTime = trim($parts[1]);

        $now = Carbon::now();

        try {
            $open = Carbon::createFromTimeString($openTime);
            $close = Carbon::createFromTimeString($closeTime);

            if ($close->lessThan($open)) {
                $close->addDay();
            }

            return $now->between($open, $close);
        } catch (\Exception) {
            return true;
        }
    }
}
