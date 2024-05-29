<?php

namespace PersistentSeeders\Tests\Fixtures;

use Illuminate\Support\Facades\DB;
use PersistentSeeders\PersistentSeeder;
use PersistentSeeders\SeederId;

class TestSeeder extends PersistentSeeder
{
    #[SeederId('a7122b42-bb2e-4c1e-ad2d-2295b8d45331')]
    public function method1(): void
    {
        DB::table('test_table')->insert(['name' => __FUNCTION__]);
    }

    #[SeederId('5f75a1fd-913b-4111-87a2-2cf43a1006a2')]
    public function method2(): void
    {
        DB::table('test_table')->insert(['name' => __FUNCTION__]);
    }

    #[SeederId('8c91242c-c398-4043-87e6-3f57b4185976')]
    public function failingSeeder(): void
    {
        throw new \Exception('Failing seeder');
    }

    #[SeederId('05706c19-ab24-49e0-9750-5cead18b92f1', 'customName')]
    public function method4(): void
    {
        DB::table('test_table')->insert(['name' => __FUNCTION__]);
    }

    public function nonMethod(): void
    {
        DB::table('test_table')->insert(['name' => __FUNCTION__]);
    }
}
