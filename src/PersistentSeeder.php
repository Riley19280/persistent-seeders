<?php

namespace PersistentSeeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ReflectionException;

class PersistentSeeder extends Seeder
{
    public function run(): void
    {
        $seedersToRun = $this->getSeedersToRun();

        foreach ($seedersToRun as $seeder) {
            $this->runSeeder($seeder);
        }
    }

    private function runSeeder(SeederId $seederId): void
    {
        try {
            if ($this->command) {
                $this->command->warn("  Running persistent seeder $seederId->name ($seederId->id)");
            }

            $this->{$seederId->functionName}();
        } finally {
            $date = Carbon::now();
            DB::table(config('persistent_seeders.table_name'))->insert([
                'id'         => $seederId->id,
                'name'       => $seederId->name,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    private function getSeedersToRun(): array
    {
        $seedFunctions = $this->getSeederMethodNames();

        $seederIds = collect($seedFunctions)
            ->transform(function(string $seedMethodName) {
                return $this->getSeederIdForMethod($seedMethodName);
            })
            ->keyBy('id');

        $seederUuids = $seederIds->pluck('id');

        $existingSeeds = DB::table(config('persistent_seeders.table_name'))
            ->whereIn('id', $seederUuids)
            ->get()
            ->keyBy('id');

        return $seederIds->diffKeys($existingSeeds)->toArray();
    }

    private function getSeederIdForMethod(string $methodName): ?SeederId
    {
        try {
            $methodReflection = new \ReflectionMethod($this, $methodName);
        } catch (ReflectionException $exception) {
            return null;
        }

        foreach ($methodReflection->getAttributes(SeederId::class) as $attribute) {
            $seederId = new SeederId(...$attribute->getArguments());

            if ($seederId->name === null) {
                $seederId->name = $methodName;
            }

            $seederId->functionName = $methodName;

            return $seederId;
        }

        return null;
    }

    private function getSeederMethodNames(): array
    {
        $result = [];

        foreach (get_class_methods($this) as $methodName) {
            if ($this->getSeederIdForMethod($methodName)) {
                $result[] = $methodName;
            }
        }

        return $result;
    }
}
