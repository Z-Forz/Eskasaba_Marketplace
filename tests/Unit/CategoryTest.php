<?php

use App\Models\Category;
use Tests\TestCase;

uses(TestCase::class);

test('category is marked as new when created within 7 days', function () {
    $newCategory = new Category([
        'name' => 'Kategori Baru',
    ]);
    $newCategory->created_at = now()->subDays(3);

    expect($newCategory->isNew())->toBeTrue();
    expect($newCategory->is_new)->toBeTrue();
});

test('category is not marked as new when created more than 7 days ago', function () {
    $oldCategory = new Category([
        'name' => 'Kategori Lama',
    ]);
    $oldCategory->created_at = now()->subDays(8);

    expect($oldCategory->isNew())->toBeFalse();
    expect($oldCategory->is_new)->toBeFalse();
});
