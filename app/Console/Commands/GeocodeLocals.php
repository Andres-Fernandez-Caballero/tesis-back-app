<?php

namespace App\Console\Commands;

use App\Models\Local;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GeocodeLocals extends Command
{
    protected $signature = 'locals:geocode
                            {--dry-run : Muestra qué se geocodificaría sin guardar cambios}
                            {--force  : Re-geocodifica incluso locales que ya tienen coordenadas}';

    protected $description = 'Geocodifica locales sin lat/lng usando Google Maps Geocoding API';

    public function handle(): int
    {
        $key = config('services.google.maps_api_key');

        if (! $key) {
            $this->error('GOOGLE_MAPS_API_KEY no está configurada en .env');
            return self::FAILURE;
        }

        $query = $this->option('force')
            ? Local::query()
            : Local::whereNull('latitude')->orWhereNull('longitude');

        $locals = $query->get();

        if ($locals->isEmpty()) {
            $this->info('✓ Todos los locales ya tienen coordenadas.');
            return self::SUCCESS;
        }

        $dryRun = $this->option('dry-run');
        $label  = $dryRun ? ' [DRY-RUN]' : '';

        $this->info("Procesando {$locals->count()} local(es)...{$label}");

        $bar  = $this->output->createProgressBar($locals->count());
        $bar->start();
        $ok   = 0;
        $fail = 0;

        foreach ($locals as $local) {
            $address = implode(', ', array_filter([
                $local->direccion,
                $local->localidad,
                'Buenos Aires',
                'Argentina',
            ]));

            try {
                $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key'     => $key,
                ]);

                $data = $response->json();

                if (($data['status'] ?? '') === 'OK' && ! empty($data['results'])) {
                    $loc = $data['results'][0]['geometry']['location'];

                    if (! $dryRun) {
                        $local->update([
                            'latitude'  => $loc['lat'],
                            'longitude' => $loc['lng'],
                        ]);
                    }

                    $this->newLine();
                    $this->line("  <fg=green>✓</> {$local->nombre_local}: {$loc['lat']}, {$loc['lng']}");
                    $ok++;
                } else {
                    $status = $data['status'] ?? 'UNKNOWN';
                    $this->newLine();
                    $this->line("  <fg=yellow>✗</> {$local->nombre_local}: sin resultados (status: {$status})");
                    $fail++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->line("  <fg=red>✗</> {$local->nombre_local}: {$e->getMessage()}");
                $fail++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completado: <fg=green>{$ok} geocodificados</>, <fg=yellow>{$fail} fallidos</>.");

        if ($dryRun) {
            $this->comment('DRY-RUN: no se guardó ningún cambio. Ejecutá sin --dry-run para aplicar.');
        }

        return self::SUCCESS;
    }
}
