<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KbliController extends Controller
{
    public function index(Request $request)
    {
        $judul = 'Master KBLI 2020';
        $perPage = (int) $request->input('perPage', 25);
        if ($perPage <= 0) $perPage = 25;
        $q = trim($request->input('q', ''));

        $items = DB::table('kbli_subclasses as sc')
            ->leftJoin('kbli_classes as c', 'sc.class_code', '=', 'c.code')
            ->leftJoin('kbli_groups as g', 'c.group_code', '=', 'g.code')
            ->leftJoin('kbli_divisions as d', 'g.division_code', '=', 'd.code')
            ->leftJoin('kbli_sections as s', 'd.section_code', '=', 's.code')
            ->select([
                'sc.code as subclass_code', 'sc.name as subclass_name',
                'c.code as class_code', 'c.name as class_name',
                'g.code as group_code', 'g.name as group_name',
                'd.code as division_code', 'd.name as division_name',
                's.code as section_code', 's.name as section_name',
            ])
            ->when($q !== '', function($query) use ($q) {
                $like = "%$q%";
                $query->where(function($w) use ($like) {
                    $w->where('sc.code', 'like', $like)
                      ->orWhere('sc.name', 'like', $like)
                      ->orWhere('c.code', 'like', $like)
                      ->orWhere('c.name', 'like', $like)
                      ->orWhere('g.code', 'like', $like)
                      ->orWhere('g.name', 'like', $like)
                      ->orWhere('d.code', 'like', $like)
                      ->orWhere('d.name', 'like', $like)
                      ->orWhere('s.code', 'like', $like)
                      ->orWhere('s.name', 'like', $like);
                });
            })
            ->orderBy('sc.code')
            ->paginate($perPage)
            ->withQueryString();

        $counts = [
            'sections'  => DB::table('kbli_sections')->count(),
            'divisions' => DB::table('kbli_divisions')->count(),
            'groups'    => DB::table('kbli_groups')->count(),
            'classes'   => DB::table('kbli_classes')->count(),
            'subs'      => DB::table('kbli_subclasses')->count(),
        ];

        return view('admin.kbli.index', compact('judul', 'items', 'counts', 'q', 'perPage'));
    }

    public function importForm()
    {
        $judul = 'Import KBLI 2020';
        return view('admin.kbli.import', compact('judul'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'truncate' => 'nullable|boolean',
        ]);

        if ($request->boolean('truncate')) {
            DB::table('kbli_subclasses')->truncate();
            DB::table('kbli_classes')->truncate();
            DB::table('kbli_groups')->truncate();
            DB::table('kbli_divisions')->truncate();
            DB::table('kbli_sections')->truncate();
        }

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['file' => 'Gagal membaca file CSV.']);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'Header CSV tidak ditemukan.']);
        }

        // Normalisasi header ke lower case
        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $map = array_flip($header);

        // Minimal kolom wajib
        $requiredMinimal = ['division_code','group_code','class_code','subclass_code'];
        foreach ($requiredMinimal as $col) {
            if (!isset($map[$col])) {
                fclose($handle);
                return back()->withErrors(['file' => "Kolom '$col' tidak ada di CSV."]);
            }
        }

        // Pemetaan section dari division (KBLI 2020)
        $sectionMap = [
            'A' => ['min' => 1,  'max' => 3,  'name' => 'Pertanian, Kehutanan, dan Perikanan'],
            'B' => ['min' => 5,  'max' => 9,  'name' => 'Pertambangan dan Penggalian'],
            'C' => ['min' => 10, 'max' => 33, 'name' => 'Industri Pengolahan'],
            'D' => ['min' => 35, 'max' => 35, 'name' => 'Pengadaan Listrik, Gas, Uap/Air Panas'],
            'E' => ['min' => 36, 'max' => 39, 'name' => 'Pengelolaan Air, Sampah, dan Daur Ulang'],
            'F' => ['min' => 41, 'max' => 43, 'name' => 'Konstruksi'],
            'G' => ['min' => 45, 'max' => 47, 'name' => 'Perdagangan Besar/Eceran; Reparasi Mobil/Sepeda Motor'],
            'H' => ['min' => 49, 'max' => 53, 'name' => 'Transportasi dan Pergudangan'],
            'I' => ['min' => 55, 'max' => 56, 'name' => 'Penyediaan Akomodasi dan Makan Minum'],
            'J' => ['min' => 58, 'max' => 63, 'name' => 'Informasi dan Komunikasi'],
            'K' => ['min' => 64, 'max' => 66, 'name' => 'Jasa Keuangan dan Asuransi'],
            'L' => ['min' => 68, 'max' => 68, 'name' => 'Real Estat'],
            'M' => ['min' => 69, 'max' => 75, 'name' => 'Jasa Profesional, Ilmiah, dan Teknis'],
            'N' => ['min' => 77, 'max' => 82, 'name' => 'Jasa Persewaan, Ketenagakerjaan, Perjalanan, Penunjang'],
            'O' => ['min' => 84, 'max' => 84, 'name' => 'Administrasi Pemerintahan, Pertahanan, Jamsos Wajib'],
            'P' => ['min' => 85, 'max' => 85, 'name' => 'Pendidikan'],
            'Q' => ['min' => 86, 'max' => 88, 'name' => 'Kesehatan dan Kegiatan Sosial'],
            'R' => ['min' => 90, 'max' => 93, 'name' => 'Seni, Hiburan, dan Rekreasi'],
            'S' => ['min' => 94, 'max' => 96, 'name' => 'Kegiatan Jasa Lainnya'],
            'T' => ['min' => 97, 'max' => 98, 'name' => 'Kegiatan Jasa Rumah Tangga'],
            'U' => ['min' => 99, 'max' => 99, 'name' => 'Kegiatan Badan Internasional/Ekstra Internasional'],
        ];
        $deriveSection = function(string $divisionCode) use ($sectionMap): array {
            $divNum = (int) ltrim($divisionCode, '0');
            foreach ($sectionMap as $letter => $rng) {
                if ($divNum >= $rng['min'] && $divNum <= $rng['max']) {
                    return [$letter, $rng['name']];
                }
            }
            return ['', ''];
        };

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_map(fn($v) => trim((string) $v), $row);

                $division_code = $row[$map['division_code']] ?? '';
                $group_code    = $row[$map['group_code']] ?? '';
                $class_code    = $row[$map['class_code']] ?? '';
                $subclass_code = $row[$map['subclass_code']] ?? '';

                $section_code  = isset($map['section_code']) ? ($row[$map['section_code']] ?? '') : '';
                $section_name  = isset($map['section_name']) ? ($row[$map['section_name']] ?? '') : '';
                $division_name = isset($map['division_name']) ? ($row[$map['division_name']] ?? '') : '';
                $group_name    = isset($map['group_name']) ? ($row[$map['group_name']] ?? '') : '';
                $class_name    = isset($map['class_name']) ? ($row[$map['class_name']] ?? '') : '';
                $subclass_name = isset($map['subclass_name']) ? ($row[$map['subclass_name']] ?? '') : '';

                if (!$section_code && $division_code) {
                    [$section_code, $section_name] = $deriveSection($division_code);
                }

                if ($section_code !== '') {
                    DB::table('kbli_sections')->updateOrInsert(
                        ['code' => $section_code],
                        ['name' => $section_name]
                    );
                }
                if ($division_code !== '') {
                    DB::table('kbli_divisions')->updateOrInsert(
                        ['code' => $division_code],
                        ['name' => $division_name, 'section_code' => $section_code]
                    );
                }
                if ($group_code !== '') {
                    DB::table('kbli_groups')->updateOrInsert(
                        ['code' => $group_code],
                        ['name' => $group_name, 'division_code' => $division_code]
                    );
                }
                if ($class_code !== '') {
                    DB::table('kbli_classes')->updateOrInsert(
                        ['code' => $class_code],
                        ['name' => $class_name, 'group_code' => $group_code]
                    );
                }
                if ($subclass_code !== '') {
                    DB::table('kbli_subclasses')->updateOrInsert(
                        ['code' => $subclass_code],
                        ['name' => $subclass_name, 'class_code' => $class_code]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        fclose($handle);
        return redirect()->route('kbli.index')->with('success', 'Import KBLI selesai.');
    }

    public function downloadTemplate()
    {
        $filename = 'kbli2020_template.csv';
        $headersLine = 'section_code,section_name,division_code,division_name,group_code,group_name,class_code,class_name,subclass_code,subclass_name';
        $samples = [
            'A,Pertanian,01,Pertanian tanaman dan peternakan,011,Tanaman semusim,0111,Budidaya padi,01111,Budidaya padi',
            'A,Pertanian,03,Perikanan,031,Perikanan tangkap,0311,Perikanan laut,03111,Penangkapan ikan di laut',
        ];
        $csv = "\xEF\xBB\xBF" . $headersLine . "\r\n" . implode("\r\n", $samples) . "\r\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}