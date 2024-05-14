<?php

namespace Database\Seeders;

use App\Models\BlockedItem;
use Illuminate\Database\Seeder;

class BlockedItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlockedItem::factory(10)->create();
    }
}
