<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Log;

class CleanOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina las actividades que tienen más de 24 horas de antigüedad';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de actividades antiguas...');
        
        $last24Hours = Carbon::now()->subHours(24);
        $count = Activity::where('created_at', '<', $last24Hours)->count();
        
        if ($count > 0) {
            $deleted = Activity::where('created_at', '<', $last24Hours)->delete();
            $this->info("Se han eliminado $deleted registros de actividad antiguos.");
            Log::info("Limpieza de actividades: Se han eliminado $deleted registros de actividad antiguos.");
        } else {
            $this->info('No hay actividades antiguas para eliminar.');
            Log::info('Limpieza de actividades: No hay actividades antiguas para eliminar.');
        }
        
        return Command::SUCCESS;
    }
}
