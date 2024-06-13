<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RodoviaImportar;
use Illuminate\Support\Facades\DB;

class ImportRodovias extends Command
{
    protected $signature = 'import:rodovias';
    protected $description = 'Importa rodovias de um arquivo CSV';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $file = fopen(storage_path('app/public/rodovias.csv'), 'r');
            fgetcsv($file);

            while (($row = fgetcsv($file)) !== FALSE) {
                RodoviaImportar::create([
                    'uf_id' => $row[1],
                    'rodovia' => $row[2],
                ]);
            }

            fclose($file);
            DB::commit();
            $this->info('Rodovias importadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro ao importar rodovias: ' . $e->getMessage());
        }
    }
}
