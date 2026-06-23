<?php

namespace App\Console\Commands;

use App\Domain\Catalog\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FetchProductImages extends Command
{
    protected $signature = 'pos:fetch-product-images {--force : Re-download images even if they already exist}';

    protected $description = 'Download real product images from Unsplash';

    protected array $imageMap = [
        'Americano' => '1509042239860-f550ce710b93',
        'Latte' => '1570968915860-54d5c301fa9f',
        'Cappuccino' => '1572442388796-11668a67e53d',
        'Espresso' => '1559496417-e7f25cb247f3',
        'Mocha' => '1578314675249-a6910f80cc4e',
        'Green Tea' => '1556881286-fc6915169721',
        'Earl Grey' => '1564890369478-c89ca6d9cde9',
        'Chai Latte' => '1563822249366-3efb23b8e0c9',
        'Croissant' => '1555507036-ab1f4038027a',
        'Blueberry Muffin' => '1557958118-97cee8645095',
        'Chocolate Cake' => '1578985545062-69928b1d9587',
        'Berry Blast' => '1505252585461-04db1eb84625',
        'Tropical Mango' => '1534353473418-4cfa6c56fd38',
    ];

    public function handle(): void
    {
        $products = Product::all();
        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($products as $product) {
            $photoId = $this->imageMap[$product->name] ?? null;

            if (! $photoId) {
                $this->warn("No image mapping for: {$product->name}");
                $failed++;

                continue;
            }

            $filename = Str::slug($product->name).'.jpg';
            $path = 'products/'.$filename;

            if (! $this->option('force') && $product->image === $path && Storage::disk('public')->exists($path)) {
                $skipped++;

                continue;
            }

            $this->line("Downloading image for: {$product->name}");

            $url = "https://images.unsplash.com/photo-{$photoId}?w=400&q=80&fm=jpg&fit=crop";

            try {
                $response = Http::timeout(15)->get($url);

                if ($response->successful()) {
                    Storage::disk('public')->put($path, $response->body());

                    $product->update(['image' => $path]);
                    $this->info("  ✓ Saved: {$path}");
                    $downloaded++;
                } else {
                    Log::warning('Failed to download image', [
                        'product' => $product->name,
                        'status' => $response->status(),
                    ]);
                    $this->warn("  ✗ HTTP {$response->status()}");
                    $failed++;
                }
            } catch (\Exception $e) {
                Log::warning('Image download exception', [
                    'product' => $product->name,
                    'error' => $e->getMessage(),
                ]);
                $this->warn("  ✗ Error: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Result', 'Count'],
            [
                ['Downloaded', (string) $downloaded],
                ['Skipped', (string) $skipped],
                ['Failed', (string) $failed],
                ['Total', (string) ($downloaded + $skipped + $failed)],
            ],
        );
    }
}
