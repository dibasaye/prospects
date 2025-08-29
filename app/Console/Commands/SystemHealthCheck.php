<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Models\Prospect;
use App\Services\ContractService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health-check {--fix : Corriger automatiquement les problèmes détectés}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier l\'état de santé du système de gestion des contrats';

    protected ContractService $contractService;

    /**
     * Create a new command instance.
     */
    public function __construct(ContractService $contractService)
    {
        parent::__construct();
        $this->contractService = $contractService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification de l\'état de santé du système...');
        $this->newLine();

        $issues = [];
        
        // Vérifications des données
        $issues = array_merge($issues, $this->checkDataIntegrity());
        
        // Vérifications des fichiers
        $issues = array_merge($issues, $this->checkFiles());
        
        // Vérifications de configuration
        $issues = array_merge($issues, $this->checkConfiguration());
        
        // Vérifications de performance
        $issues = array_merge($issues, $this->checkPerformance());

        // Affichage du résumé
        $this->displaySummary($issues);

        // Correction automatique si demandée
        if ($this->option('fix') && !empty($issues)) {
            $this->fixIssues($issues);
        }

        return empty($issues) ? 0 : 1;
    }

    /**
     * Vérifier l'intégrité des données
     */
    private function checkDataIntegrity(): array
    {
        $this->info('📊 Vérification de l\'intégrité des données...');
        $issues = [];

        // Contrats orphelins (sans client)
        $orphanedContracts = Contract::whereDoesntHave('client')->count();
        if ($orphanedContracts > 0) {
            $issues[] = [
                'type' => 'data',
                'severity' => 'warning',
                'message' => "$orphanedContracts contrat(s) orphelin(s) (sans client)",
                'fixable' => true
            ];
        }

        // Contrats sans numéro
        $contractsWithoutNumber = Contract::whereNull('contract_number')->orWhere('contract_number', '')->count();
        if ($contractsWithoutNumber > 0) {
            $issues[] = [
                'type' => 'data',
                'severity' => 'error',
                'message' => "$contractsWithoutNumber contrat(s) sans numéro",
                'fixable' => true
            ];
        }

        // Doublons de numéros de contrat
        $duplicateNumbers = DB::table('contracts')
            ->select('contract_number')
            ->whereNotNull('contract_number')
            ->where('contract_number', '!=', '')
            ->groupBy('contract_number')
            ->havingRaw('COUNT(*) > 1')
            ->count();
        
        if ($duplicateNumbers > 0) {
            $issues[] = [
                'type' => 'data',
                'severity' => 'error',
                'message' => "$duplicateNumbers numéro(s) de contrat en doublon",
                'fixable' => true
            ];
        }

        // Contrats avec des montants incohérents
        $inconsistentAmounts = Contract::whereRaw('paid_amount > total_amount')->count();
        if ($inconsistentAmounts > 0) {
            $issues[] = [
                'type' => 'data',
                'severity' => 'warning',
                'message' => "$inconsistentAmounts contrat(s) avec montant payé > montant total",
                'fixable' => false
            ];
        }

        $this->info("✅ Intégrité des données vérifiée");
        return $issues;
    }

    /**
     * Vérifier les fichiers nécessaires
     */
    private function checkFiles(): array
    {
        $this->info('📁 Vérification des fichiers...');
        $issues = [];

        $requiredImages = config('contracts.images', []);
        foreach ($requiredImages as $key => $path) {
            $fullPath = public_path($path);
            if (!file_exists($fullPath)) {
                $issues[] = [
                    'type' => 'file',
                    'severity' => 'warning',
                    'message' => "Image manquante: $path",
                    'fixable' => false
                ];
            }
        }

        // Vérifier les répertoires d'upload
        $uploadDirs = [
            'storage/app/public/contracts',
            'storage/app/public/contracts/signed'
        ];

        foreach ($uploadDirs as $dir) {
            $fullPath = base_path($dir);
            if (!is_dir($fullPath)) {
                $issues[] = [
                    'type' => 'file',
                    'severity' => 'error',
                    'message' => "Répertoire manquant: $dir",
                    'fixable' => true
                ];
            } elseif (!is_writable($fullPath)) {
                $issues[] = [
                    'type' => 'file',
                    'severity' => 'error',
                    'message' => "Répertoire non accessible en écriture: $dir",
                    'fixable' => false
                ];
            }
        }

        $this->info("✅ Fichiers vérifiés");
        return $issues;
    }

    /**
     * Vérifier la configuration
     */
    private function checkConfiguration(): array
    {
        $this->info('⚙️  Vérification de la configuration...');
        $issues = [];

        // Extensions PHP requises
        $requiredExtensions = ['gd', 'zip', 'xml', 'mbstring'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $issues[] = [
                    'type' => 'config',
                    'severity' => 'error',
                    'message' => "Extension PHP manquante: $ext",
                    'fixable' => false
                ];
            }
        }

        // Configuration mémoire
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);
        if ($memoryLimitBytes < 256 * 1024 * 1024) { // 256MB
            $issues[] = [
                'type' => 'config',
                'severity' => 'warning',
                'message' => "Limite mémoire PHP faible: $memoryLimit (recommandé: 256M+)",
                'fixable' => false
            ];
        }

        // Configuration max_execution_time
        $maxExecutionTime = ini_get('max_execution_time');
        if ($maxExecutionTime > 0 && $maxExecutionTime < 120) {
            $issues[] = [
                'type' => 'config',
                'severity' => 'warning',
                'message' => "Temps d'exécution max faible: {$maxExecutionTime}s (recommandé: 120s+)",
                'fixable' => false
            ];
        }

        $this->info("✅ Configuration vérifiée");
        return $issues;
    }

    /**
     * Vérifier les performances
     */
    private function checkPerformance(): array
    {
        $this->info('🚀 Vérification des performances...');
        $issues = [];

        // Nombre de contrats avec beaucoup de contenu
        $largeContentContracts = Contract::whereRaw('LENGTH(content) > 50000')->count();
        if ($largeContentContracts > 0) {
            $issues[] = [
                'type' => 'performance',
                'severity' => 'info',
                'message' => "$largeContentContracts contrat(s) avec contenu volumineux (>50k caractères)",
                'fixable' => false
            ];
        }

        // Fichiers temporaires anciens
        $tempFiles = glob(sys_get_temp_dir() . '/contract_*');
        $oldTempFiles = 0;
        foreach ($tempFiles as $file) {
            if (filemtime($file) < time() - 3600) { // Plus de 1h
                $oldTempFiles++;
            }
        }

        if ($oldTempFiles > 0) {
            $issues[] = [
                'type' => 'performance',
                'severity' => 'info',
                'message' => "$oldTempFiles fichier(s) temporaire(s) ancien(s)",
                'fixable' => true
            ];
        }

        $this->info("✅ Performances vérifiées");
        return $issues;
    }

    /**
     * Afficher le résumé
     */
    private function displaySummary(array $issues): void
    {
        $this->newLine();
        
        if (empty($issues)) {
            $this->info('🎉 Système en parfait état de santé !');
            return;
        }

        $this->warn('⚠️  Problèmes détectés:');
        $this->newLine();

        $errorCount = 0;
        $warningCount = 0;
        $infoCount = 0;
        $fixableCount = 0;

        foreach ($issues as $issue) {
            $icon = match($issue['severity']) {
                'error' => '❌',
                'warning' => '⚠️ ',
                'info' => 'ℹ️ ',
                default => '•'
            };

            $fixable = $issue['fixable'] ? ' [CORRIGEABLE]' : '';
            $this->line("$icon {$issue['message']}$fixable");

            match($issue['severity']) {
                'error' => $errorCount++,
                'warning' => $warningCount++,
                'info' => $infoCount++,
                default => null
            };

            if ($issue['fixable']) {
                $fixableCount++;
            }
        }

        $this->newLine();
        $this->info("Résumé: $errorCount erreur(s), $warningCount avertissement(s), $infoCount info(s)");
        
        if ($fixableCount > 0) {
            $this->info("$fixableCount problème(s) peuvent être corrigés automatiquement avec --fix");
        }
    }

    /**
     * Corriger les problèmes
     */
    private function fixIssues(array $issues): void
    {
        $this->info('🔧 Correction des problèmes...');

        foreach ($issues as $issue) {
            if (!$issue['fixable']) {
                continue;
            }

            switch (true) {
                case str_contains($issue['message'], 'orphelin'):
                    $deleted = Contract::whereDoesntHave('client')->delete();
                    $this->info("✅ $deleted contrat(s) orphelin(s) supprimé(s)");
                    break;

                case str_contains($issue['message'], 'sans numéro'):
                    $contracts = Contract::whereNull('contract_number')->orWhere('contract_number', '')->get();
                    foreach ($contracts as $contract) {
                        $contract->update(['contract_number' => Contract::generateContractNumber()]);
                    }
                    $this->info("✅ Numéros de contrat générés pour " . $contracts->count() . " contrat(s)");
                    break;

                case str_contains($issue['message'], 'doublon'):
                    $duplicates = DB::table('contracts')
                        ->select('contract_number')
                        ->whereNotNull('contract_number')
                        ->where('contract_number', '!=', '')
                        ->groupBy('contract_number')
                        ->havingRaw('COUNT(*) > 1')
                        ->pluck('contract_number');

                    foreach ($duplicates as $number) {
                        $contracts = Contract::where('contract_number', $number)->get();
                        $contracts->skip(1)->each(function ($contract) {
                            $contract->update(['contract_number' => Contract::generateContractNumber()]);
                        });
                    }
                    $this->info("✅ Doublons de numéros corrigés");
                    break;

                case str_contains($issue['message'], 'Répertoire manquant'):
                    // Créer les répertoires manquants
                    $uploadDirs = [
                        'storage/app/public/contracts',
                        'storage/app/public/contracts/signed'
                    ];
                    foreach ($uploadDirs as $dir) {
                        $fullPath = base_path($dir);
                        if (!is_dir($fullPath)) {
                            mkdir($fullPath, 0755, true);
                            $this->info("✅ Répertoire créé: $dir");
                        }
                    }
                    break;

                case str_contains($issue['message'], 'temporaire'):
                    $cleaned = $this->contractService->cleanupTempFiles();
                    $this->info("✅ $cleaned fichier(s) temporaire(s) nettoyé(s)");
                    break;
            }
        }
    }

    /**
     * Convertir une taille en bytes
     */
    private function convertToBytes(string $size): int
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;

        return match($last) {
            'g' => $size * 1024 * 1024 * 1024,
            'm' => $size * 1024 * 1024,
            'k' => $size * 1024,
            default => $size
        };
    }
}