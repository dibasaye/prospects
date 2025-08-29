<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Prospect;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommercialPerformanceController extends Controller
{
public function __construct()
    {
        // Apply role middleware only to non-API methods
        $this->middleware('role:responsable_commercial|administrateur')->except([
            'getCommercialDetails',
            'getPaymentsList',
            'getProspectsList',
            'getCommercialsList'
        ]);
    }
    

    public function performance()
    {
        try {
            // Convertir les données en collections
            $monthlyStats = collect($this->getMonthlyStats());
            $commercials = collect($this->getAllComercials());

            return view('dashboard.commercial_performance', [
                'globalStats' => $this->getGlobalStats(),
                'topComercials' => $this->getTopComercials(),
                'monthlyStats' => $monthlyStats,
                'commercials' => $commercials,
                'paymentDetails' => $this->getPaymentDetails(),
                'prospectDetails' => $this->getProspectDetails(),
                'commercialDetails' => $this->getCommercialUsers()
            ]);
        } catch (\Exception $e) {
            Log::error('Performance dashboard error: '.$e->getMessage());
            return back()->withError('Une erreur est survenue lors du chargement des données.');
        }
    }

    protected function getGlobalStats()
    {
        return Cache::remember('global_stats', 3600, function() {
            return [
                'total_commercials' => User::where('role', 'commercial')->count(),
                'total_prospects' => Prospect::count(),
                'total_payments' => Payment::sum('amount'),
                'conversion_rate' => $this->calculateGlobalConversionRate(),
            ];
        });
    }

    protected function getTopComercials($limit = 5)
    {
        return Cache::remember('top_commercials', 1800, function() use ($limit) {
            return User::where('role', 'commercial')
                ->withCount([
                    'assignedProspects',
                    'assignedProspects as converted_prospects_count' => function($query) {
                        $query->where('status', 'converted');
                    },
                    'confirmedPayments'
                ])
                ->withSum('confirmedPayments', 'amount')
                ->orderBy('confirmed_payments_sum_amount', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($user) {
                    $conversionRate = $user->assigned_prospects_count > 0
                        ? round(($user->converted_prospects_count / $user->assigned_prospects_count) * 100, 2)
                        : 0;

                    return [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'total_payments' => $user->confirmed_payments_sum_amount ?? 0,
                        'payments_count' => $user->confirmed_payments_count,
                        'conversion_rate' => $conversionRate,
                    ];
                });
        });
    }

    protected function getMonthlyStats()
    {
        $stats = DB::table('payments')
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total_payments'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return collect($stats)->map(function ($item) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                'total_payments' => (float) $item->total_payments,
                'conversion_rate' => $this->getMonthlyConversionRate($item->month)
            ];
        });
    }

    protected function getAllComercials()
    {
        return Cache::remember('all_commercials', 3600, function() {
            return User::where('role', 'commercial')
                ->withCount([
                    'assignedProspects',
                    'assignedProspects as active_prospects' => function($query) {
                        $query->where('status', 'active');
                    },
                    'assignedProspects as converted_prospects' => function($query) {
                        $query->where('status', 'converted');
                    },
                    'generatedContracts',
                    'confirmedPayments'
                ])
                ->withSum('confirmedPayments', 'amount')
                ->withSum(['confirmedPayments as this_month_payments' => function($query) {
                    $query->whereMonth('payment_date', Carbon::now()->month);
                }], 'amount')
                ->withSum(['confirmedPayments as last_month_payments' => function($query) {
                    $query->whereMonth('payment_date', Carbon::now()->subMonth()->month);
                }], 'amount')
                ->get()
                ->map(function($user) {
                    $conversionRate = $user->assigned_prospects_count > 0
                        ? round(($user->converted_prospects_count / $user->assigned_prospects_count) * 100, 2)
                        : 0;

                    return [
                        'id' => $user->id,
                        'name' => $user->full_name,
                        'email' => $user->email,
                        'total_prospects' => $user->assigned_prospects_count,
                        'active_prospects' => $user->active_prospects,
                        'converted_prospects' => $user->converted_prospects,
                        'total_contracts' => $user->generated_contracts_count,
                        'total_payments' => $user->confirmed_payments_sum_amount ?? 0,
                        'payments_count' => $user->confirmed_payments_count,
                        'avg_payment_amount' => $user->confirmed_payments_count > 0 
                            ? ($user->confirmed_payments_sum_amount / $user->confirmed_payments_count)
                            : 0,
                        'this_month_payments' => $user->this_month_payments ?? 0,
                        'last_month_payments' => $user->last_month_payments ?? 0,
                        'conversion_rate' => $conversionRate,
                    ];
                })
                ->sortByDesc('total_payments')
                ->values();
        });
    }

    protected function getPaymentDetails()
    {
        try {
            return Payment::with(['confirmedBy:id,first_name,last_name', 'client:id,name'])
                ->orderBy('payment_date', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            Log::error('Error fetching payment details: '.$e->getMessage());
            return collect();
        }
    }

    protected function getProspectDetails()
    {
        try {
            return Prospect::with(['assignedTo:id,first_name,last_name'])
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching prospect details: '.$e->getMessage());
            return collect();
        }
    }

    protected function getCommercialUsers()
    {
        try {
            return User::where('role', 'commercial')
                ->select('id', 'first_name', 'last_name', 'email', 'phone', 'created_at')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching commercial users: '.$e->getMessage());
            return collect();
        }
    }

    protected function calculateGlobalConversionRate()
    {
        $totalProspects = Prospect::count();
        $convertedProspects = Prospect::where('status', 'converted')->count();

        return $totalProspects > 0
            ? round(($convertedProspects / $totalProspects) * 100, 2)
            : 0;
    }
    
    /**
     * Get monthly conversion rate for a specific month
     *
     * @param string $month Year and month in format 'Y-m'
     * @return float
     */
    protected function getMonthlyConversionRate($month)
    {
        try {
            // Get total prospects created in this month
            $totalProspects = Prospect::whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();
            
            // Get converted prospects in this month
            $convertedProspects = Prospect::where('status', 'converted')
                ->whereYear('converted_at', substr($month, 0, 4))
                ->whereMonth('converted_at', substr($month, 5, 2))
                ->count();
            
            return $totalProspects > 0
                ? round(($convertedProspects / $totalProspects) * 100, 2)
                : 0;
        } catch (\Exception $e) {
            Log::error('Error calculating monthly conversion rate for ' . $month . ': ' . $e->getMessage());
            return 0;
        }
    }

    public function export()
    {
        try {
            $commercials = $this->getAllComercials();
            
            // Préparer les en-têtes du CSV
            $headers = [
                'Nom',
                'Email',
                'Prospects Total',
                'Prospects Actifs',
                'Prospects Convertis',
                'Taux de Conversion',
                'Contrats Générés',
                'Paiements Total',
                'Nombre de Paiements',
                'Moyenne par Paiement',
                'Paiements ce Mois',
                'Paiements Mois Dernier'
            ];

            // Créer le nom du fichier
            $filename = 'performance_commerciaux_' . now()->format('d-m-Y_His') . '.csv';

            // Configurer les en-têtes HTTP
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Ouvrir le flux de sortie
            $output = fopen('php://output', 'w');
            
            // Ajouter le BOM UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Écrire les en-têtes
            fputcsv($output, $headers, ';');

            // Écrire les données
            foreach ($commercials as $commercial) {
                $row = [
                    $commercial['name'],
                    $commercial['email'],
                    $commercial['total_prospects'],
                    $commercial['active_prospects'],
                    $commercial['converted_prospects'],
                    number_format($commercial['conversion_rate'], 2) . '%',
                    $commercial['total_contracts'],
                    number_format($commercial['total_payments'], 0, ',', ' ') . ' F',
                    $commercial['payments_count'],
                    number_format($commercial['avg_payment_amount'], 0, ',', ' ') . ' F',
                    number_format($commercial['this_month_payments'], 0, ',', ' ') . ' F',
                    number_format($commercial['last_month_payments'], 0, ',', ' ') . ' F'
                ];
                
                fputcsv($output, $row, ';');
            }

            // Fermer le flux
            fclose($output);
            exit;

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'export des performances:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de l\'export des données.');
        }
    }
    
    /**
     * Get commercial details for AJAX request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommercialDetails(Request $request)
    {
        try {
            $commercialId = $request->get('commercial_id');
            
            if (!$commercialId) {
                return response()->json(['error' => 'Commercial ID is required'], 400);
            }
            
            $commercial = User::where('role', 'commercial')
                ->withCount([
                    'assignedProspects',
                    'assignedProspects as converted_prospects_count' => function($query) {
                        $query->where('status', 'converted');
                    },
                    'confirmedPayments'
                ])
                ->withSum('confirmedPayments', 'amount')
                ->findOrFail($commercialId);
            
            $conversionRate = $commercial->assigned_prospects_count > 0
                ? round(($commercial->converted_prospects_count / $commercial->assigned_prospects_count) * 100, 2)
                : 0;
            
            $data = [
                'id' => $commercial->id,
                'name' => $commercial->full_name,
                'email' => $commercial->email,
                'phone' => $commercial->phone,
                'total_prospects' => $commercial->assigned_prospects_count,
                'converted_prospects' => $commercial->converted_prospects_count,
                'total_payments' => $commercial->confirmed_payments_sum_amount ?? 0,
                'payments_count' => $commercial->confirmed_payments_count,
                'conversion_rate' => $conversionRate,
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error fetching commercial details: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }
    
    /**
     * Get payments list for AJAX request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentsList(Request $request)
    {
        try {
            $commercialId = $request->get('commercial_id');
            
            $query = Payment::with(['client:id,first_name,last_name'])
                ->orderBy('payment_date', 'desc')
                ->limit(50);
            
            // Filter by commercial if provided
            if ($commercialId) {
                $query->whereHas('client', function($q) use ($commercialId) {
                    $q->where('assigned_to_id', $commercialId);
                });
            }
            
            $payments = $query->get()->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'amount' => $payment->amount,
                    'client_name' => $payment->client ? $payment->client->full_name : 'N/A',
                    'status' => $payment->validation_status,
                ];
            });
            
            return response()->json($payments);
        } catch (\Exception $e) {
            Log::error('Error fetching payments list: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }
    
    /**
     * Get prospects list for AJAX request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProspectsList(Request $request)
    {
        try {
            $commercialId = $request->get('commercial_id');
            
            $query = Prospect::with(['assignedTo:id,first_name,last_name'])
                ->orderBy('created_at', 'desc')
                ->limit(50);
            
            // Filter by commercial if provided
            if ($commercialId) {
                $query->where('assigned_to_id', $commercialId);
            }
            
            $prospects = $query->get()->map(function($prospect) {
                return [
                    'id' => $prospect->id,
                    'name' => $prospect->full_name,
                    'phone' => $prospect->phone,
                    'status' => $prospect->status,
                    'created_at' => $prospect->created_at->format('Y-m-d'),
                    'assigned_to' => $prospect->assignedTo ? $prospect->assignedTo->full_name : 'N/A',
                ];
            });
            
            return response()->json($prospects);
        } catch (\Exception $e) {
            Log::error('Error fetching prospects list: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }
    
    /**
     * Get commercials list for AJAX request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommercialsList()
    {
        try {
            $commercials = User::where('role', 'commercial')
                ->select('id', 'first_name', 'last_name', 'email')
                ->get()
                ->map(function($commercial) {
                    return [
                        'id' => $commercial->id,
                        'name' => $commercial->full_name,
                        'email' => $commercial->email,
                    ];
                });
            
            return response()->json($commercials);
        } catch (\Exception $e) {
            Log::error('Error fetching commercials list: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }
}