<?php

declare(strict_types=1);

function riverfish_data_file(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'site_data.json';
}

function riverfish_default_data(): array
{
    return [
        'siteContent' => [
            'title' => 'RIVERFISH70',
            'subtitle' => 'Свежая речная рыба и улов из с. Зырянское',
            'catalogText' => 'Натуральная речная рыба и свежий улов',
        ],
        'products' => [
            [
                'id' => 1,
                'name' => 'Щука речная',
                'description' => 'Свежая речная щука из местных водоёмов.',
                'category' => 'Речная рыба',
                'price' => '650 ₽/кг',
                'oldPrice' => '800 ₽/кг',
                'discount' => '-20%',
                'isPromo' => true,
                'image' => 'https://images.unsplash.com/photo-1510130387422-82bed34b37e9?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'id' => 2,
                'name' => 'Окунь речной',
                'description' => 'Свежий окунь из с. Зырянское.',
                'category' => 'Речная рыба',
                'price' => '850 ₽/кг',
                'oldPrice' => '1000 ₽/кг',
                'discount' => '-15%',
                'isPromo' => true,
                'image' => 'https://images.unsplash.com/photo-1544943910-4c1dc44aab44?q=80&w=1200&auto=format&fit=crop',
            ],
            [
                'id' => 3,
                'name' => 'Свежий улов',
                'description' => 'Свежий сезонный улов.',
                'category' => 'Свежий улов',
                'price' => '500 ₽',
                'oldPrice' => '',
                'discount' => '',
                'isPromo' => false,
                'image' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=1200&auto=format&fit=crop',
            ],
        ],
    ];
}

function riverfish_normalize_product(array $p): array
{
    return [
        'id' => (int) ($p['id'] ?? 0),
        'name' => trim((string) ($p['name'] ?? '')),
        'description' => trim((string) ($p['description'] ?? '')),
        'category' => trim((string) ($p['category'] ?? '')),
        'price' => trim((string) ($p['price'] ?? '')),
        'oldPrice' => trim((string) ($p['oldPrice'] ?? '')),
        'discount' => trim((string) ($p['discount'] ?? '')),
        'isPromo' => !empty($p['isPromo']),
        'image' => trim((string) ($p['image'] ?? '')),
    ];
}

function riverfish_load(): array
{
    $path = riverfish_data_file();
    if (!is_readable($path)) {
        return riverfish_default_data();
    }

    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return riverfish_default_data();
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return riverfish_default_data();
    }

    $defaults = riverfish_default_data();
    $site = $decoded['siteContent'] ?? [];
    if (!is_array($site)) {
        $site = [];
    }
    $siteContent = array_merge($defaults['siteContent'], array_intersect_key($site, $defaults['siteContent']));

    $products = [];
    if (!empty($decoded['products']) && is_array($decoded['products'])) {
        foreach ($decoded['products'] as $row) {
            if (is_array($row)) {
                $products[] = riverfish_normalize_product($row);
            }
        }
    }

    if ($products === []) {
        $products = $defaults['products'];
    }

    return [
        'siteContent' => $siteContent,
        'products' => $products,
    ];
}

function riverfish_save(array $data): bool
{
    $path = riverfish_data_file();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return false;
        }
    }

    $payload = json_encode(
        [
            'siteContent' => $data['siteContent'] ?? riverfish_default_data()['siteContent'],
            'products' => array_values(array_map('riverfish_normalize_product', $data['products'] ?? [])),
        ],
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    if ($payload === false) {
        return false;
    }

    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $payload, LOCK_EX) === false) {
        return false;
    }

    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }

    return true;
}

function riverfish_sorted_products(array $data): array
{
    $list = $data['products'] ?? [];
    usort($list, static function (array $a, array $b): int {
        return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
    });

    return $list;
}

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
