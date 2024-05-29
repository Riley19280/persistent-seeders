<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PersistentSeeders\Tests\Fixtures\TestSeeder;

uses(RefreshDatabase::class);

test('get seed method names', function() {
    expect(invade(new TestSeeder())->getSeederMethodNames())->toBe(['method1', 'method2', 'failingSeeder', 'method4']);
});

test('get seeder id for invalid method', function() {
    expect(invade(new TestSeeder())->getSeederIdForMethod('nonMethod'))->toBeNull();
    expect(invade(new TestSeeder())->getSeederIdForMethod('invalidMethod'))->toBeNull();
});

test('get seeder id for method', function() {
    $seederId = invade(new TestSeeder())->getSeederIdForMethod('method1');
    expect($seederId->id)->toBe('a7122b42-bb2e-4c1e-ad2d-2295b8d45331');
    expect($seederId->name)->toBe('method1');
    expect($seederId->functionName)->toBe('method1');
});

test('get seeder id for method using custom name', function() {
    $seederId = invade(new TestSeeder())->getSeederIdForMethod('method4');
    expect($seederId->id)->toBe('05706c19-ab24-49e0-9750-5cead18b92f1');
    expect($seederId->name)->toBe('customName');
    expect($seederId->functionName)->toBe('method4');
});

test('get seeders to run', function() {
    $toRun = invade(new TestSeeder())->getSeedersToRun();
    expect(array_column($toRun, 'name'))->toBe(['method1', 'method2', 'failingSeeder', 'customName']);
});

test('get seeders to run with previously ran seeders', function() {
    DB::table('seeders')->insert(['id' => 'a7122b42-bb2e-4c1e-ad2d-2295b8d45331', 'name' => 'method1']);

    $toRun = invade(new TestSeeder())->getSeedersToRun();
    expect(array_column($toRun, 'name'))->toBe(['method2', 'failingSeeder', 'customName']);
});

test('seeder adds seeder record', function() {
    Carbon::setTestNow();
    invade(new TestSeeder())->runSeeder(invade(new TestSeeder())->getSeederIdForMethod('method1'));

    expect(DB::table('seeders')->get()->toArray())->toEqual([(object)[
        'id'         => 'a7122b42-bb2e-4c1e-ad2d-2295b8d45331',
        'name'       => 'method1',
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ]]);
});

test('seeder adds seeder record on failure', function() {
    Carbon::setTestNow();
    try {
        invade(new TestSeeder())->runSeeder(invade(new TestSeeder())->getSeederIdForMethod('failingSeeder'));
    } catch (Exception $exception) {
        throw $exception;
    } finally {
        expect(DB::table('seeders')->get()->toArray())->toEqual([(object)[
            'id'         => '8c91242c-c398-4043-87e6-3f57b4185976',
            'name'       => 'failingSeeder',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]]);
    }
})->expectExceptionMessage('Failing seeder');
