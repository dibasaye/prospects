<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContractService;
use App\Models\Contract;

class ContractMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:maintenance {--cleanup : Nettoyer les fichiers temporaires} {--stats : Afficher les statistiques} {--all : Effectuer toutes les tâches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Effectuer la maintenance des contrats (nettoyage, statistiques, etc.)';

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
        $this->info('🔧 Démarrage de la maintenance des contrats...');

        if ($this->option('all') || $this->option('cleanup')) {
            $this->performCleanup();
        }

        if ($this->option('all') || $this->option('stats')) {
            $this->displayStats();
        }

        if (!$this->option('cleanup') && !$this->option('stats') && !$this->option('all')) {
            $this->warn('Aucune action spécifiée. Utilisez --help pour voir les options disponibles.');
            return 1;
        }

        $this->info('✅ Maintenance terminée avec succès !');
        return 0;
    }

    /**
     * Effectuer le nettoyage
     */
    private function performCleanup(): void
    {
        $this->info('🧹 Nettoyage des fichiers temporaires...');
        
        $cleaned = $this->contractService->cleanupTempFiles();
        
        if ($cleaned > 0) {
            $this->info("✅ $cleaned fichier(s) temporaire(s) supprimé(s)");
        } else {
            $this->info('ℹ️  Aucun fichier temporaire à supprimer');
        }

        // Nettoyage des contrats orphelins (sans client)
        $orphanedContracts = Contract::whereDoesntHave('client')->count();
        if ($orphanedContracts > 0) {
            $this->warn("⚠️  $orphanedContracts contrat(s) orphelin(s) détecté(s) (sans client)");
            
            if ($this->confirm('Voulez-vous supprimer ces contrats orphelins ?')) {
                $deleted = Contract::whereDoesntHave('client')->delete();
                $this->info("✅ $deleted contrat(s) orphelin(s) supprimé(s)");
            }
        }
    }

    /**
     * Afficher les statistiques
     */
    private function displayStats(): void
    {
        $this->info('📊 Statistiques des contrats:');
        
        $stats = $this->contractService->getContractStats();
        $metrics = $this->contractService->getPerformanceMetrics();

        // Tableau des statistiques de base
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Total des contrats', number_format($stats['total'])],
                ['Brouillons', number_format($stats['draft'])],
                ['Signés', number_format($stats['signed'])],
                ['Annulés', number_format($stats['cancelled'])],
                ['Complétés', number_format($stats['completed'])],
                ['Ce mois', number_format($stats['current_month'])],
                ['Montant total', number_format($stats['total_amount']) . ' FCFA'],
                ['Montant payé', number_format($stats['paid_amount']) . ' FCFA'],
            ]
        );

        // Métriques de performance
        $this->newLine();
        $this->info('🎯 Métriques de performance:');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Taux de conversion', $metrics['conversion_rate'] . '%'],
                ['Valeur moyenne des contrats', number_format($metrics['average_contract_value']) . ' FCFA'],
                ['Contrats signés', number_format($metrics['signed_contracts'])],
            ]
        );

        // Performance mensuelle (derniers 6 mois)
        if (!empty($metrics['monthly_performance'])) {
            $this->newLine();
            $this->info('📈 Performance mensuelle (6 derniers mois):');
            
            $monthlyData = array_slice($metrics['monthly_performance'], 0, 6);
            $monthlyTable = [];
            
            foreach ($monthlyData as $month) {
                $monthName = date('F Y', mktime(0, 0, 0, $month['month'], 1, $month['year']));
                $conversionRate = $month['total'] > 0 ? round(($month['signed'] / $month['total']) * 100, 1) : 0;
                
                $monthlyTable[] = [
                    $monthName,
                    $month['total'],
                    $month['signed'],
                    $conversionRate . '%',
                    number_format($month['total_amount']) . ' FCFA'
                ];
            }
            
            $this->table(
                ['Mois', 'Total', 'Signés', 'Conversion', 'Montant'],
                $monthlyTable
            );
        }

        // Alertes et recommandations
        $this->newLine();
        $this->info('🚨 Alertes et recommandations:');
        
        if ($metrics['conversion_rate'] < 50) {
            $this->warn('⚠️  Taux de conversion faible (' . $metrics['conversion_rate'] . '%)');
        }
        
        if ($stats['draft'] > $stats['signed']) {
            $this->warn('⚠️  Plus de brouillons que de contrats signés');
        }
        
        $unpaidAmount = $stats['total_amount'] - $stats['paid_amount'];
        if ($unpaidAmount > 0) {
            $this->warn('⚠️  Montant impayé: ' . number_format($unpaidAmount) . ' FCFA');
        }
        
        if ($stats['current_month'] == 0) {
            $this->warn('⚠️  Aucun contrat ce mois-ci');
        }
    }
}