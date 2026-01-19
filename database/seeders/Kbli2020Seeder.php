<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Kbli2020Seeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/kbli2020.csv');
        if (!file_exists($path)) {
            $this->command->warn("File CSV tidak ditemukan: $path");
            $this->command->warn("Buat file CSV resmi KBLI 2020 dengan header:");
            $this->command->warn("section_code,section_name,division_code,division_name,group_code,group_name,class_code,class_name,subclass_code,subclass_name");
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->command->error("Gagal membuka CSV.");
            return;
        }

        // Baca header
        $header = fgetcsv($handle);
        $map = array_flip($header);

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $section_code = trim($row[$map['section_code']]);
                $section_name = trim($row[$map['section_name']]);
                $division_code = trim($row[$map['division_code']]);
                $division_name = trim($row[$map['division_name']]);
                $group_code = trim($row[$map['group_code']]);
                $group_name = trim($row[$map['group_name']]);
                $class_code = trim($row[$map['class_code']]);
                $class_name = trim($row[$map['class_name']]);
                $subclass_code = trim($row[$map['subclass_code']]);
                $subclass_name = trim($row[$map['subclass_name']]);

                if ($section_code && $section_name) {
                    DB::table('kbli_sections')->updateOrInsert(
                        ['code' => $section_code],
                        ['name' => $section_name]
                    );
                }

                if ($division_code && $division_name && $section_code) {
                    DB::table('kbli_divisions')->updateOrInsert(
                        ['code' => $division_code],
                        ['name' => $division_name, 'section_code' => $section_code]
                    );
                }

                if ($group_code && $group_name && $division_code) {
                    DB::table('kbli_groups')->updateOrInsert(
                        ['code' => $group_code],
                        ['name' => $group_name, 'division_code' => $division_code]
                    );
                }

                if ($class_code && $class_name && $group_code) {
                    DB::table('kbli_classes')->updateOrInsert(
                        ['code' => $class_code],
                        ['name' => $class_name, 'group_code' => $group_code]
                    );
                }

                if ($subclass_code && $subclass_name && $class_code) {
                    DB::table('kbli_subclasses')->updateOrInsert(
                        ['code' => $subclass_code],
                        ['name' => $subclass_name, 'class_code' => $class_code]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        } finally {
            fclose($handle);
        }
    }
}