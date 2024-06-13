<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UfImportar;
use Illuminate\Support\Facades\DB;

class ImportUfs extends Command
{
    protected $signature = 'import:ufs';
    protected $description = 'Importa UFs de um arquivo CSV';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $file = fopen(storage_path('app/public/uf.csv'), 'r');
            fgetcsv($file);

            while (($row = fgetcsv($file)) !== FALSE) {
                UfImportar::create([
                    'uf' => $row[1]
                ]);
            }

            fclose($file);
            DB::commit();
            $this->info('UFs importadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erro ao importar UFs: ' . $e->getMessage());
        }
    }
}
