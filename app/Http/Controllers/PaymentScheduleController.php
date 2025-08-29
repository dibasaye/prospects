<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Prospect;

class PaymentScheduleController extends Controller
{
    public function pay(Request $request, \App\Models\PaymentSchedule $schedule)
{
    if ($schedule->is_paid) {
        return back()->with('info', 'Ce paiement a déjà été effectué.');
    }

    $request->validate([
        'payment_method' => 'required|string',
        'notes' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
    ]);

    $schedule->update([
        'is_paid' => true,
        'paid_date' => now(),
        'payment_method' => $request->payment_method,
        'notes' => $request->notes,
        'amount' => $request->amount, // Montant saisi par l'utilisateur
    ]);

    $contract = $schedule->contract;
    $contract->paid_amount += $request->amount;
    $contract->remaining_amount = max(0, $contract->remaining_amount - $request->amount);
    $contract->save();

    $contract->client->notify(new \App\Notifications\PaymentReceiptNotification($schedule));

    return back()->with('success', 'Paiement enregistré et reçu envoyé au client.');
}

    public function downloadReceipt(\App\Models\PaymentSchedule $schedule)
    {
        if (!$schedule->is_paid) {
            return back()->with('error', 'Ce paiement n’a pas encore été effectué.');
        }

        $pdf = \PDF::loadView('receipts.pdf', compact('schedule'));
        return $pdf->download('recu_paiement_'.$schedule->id.'.pdf');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $status = $request->get('status', 'all');
        $month = $request->get('month', now()->format('Y-m'));
        $commercial = $request->get('commercial', 'all');

        $query = PaymentSchedule::with(['contract.client', 'contract.site', 'contract.lot'])
            ->whereHas('contract', function($q) {
                $q->where('status', 'signe');
            });

        if ($status !== 'all') {
            $query->where('is_paid', $status === 'paid');
        }

        if ($month) {
            $query->whereYear('due_date', substr($month, 0, 4))
                  ->whereMonth('due_date', substr($month, 5, 2));
        }

        if ($user->isManager() || $user->isAdmin()) {
            if ($commercial !== 'all') {
                $query->whereHas('contract.client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial);
                });
            }
        } else {
            $query->whereHas('contract.client', function($q) use ($user) {
                $q->where('assigned_to_id', $user->id);
            });
        }

        $schedules = $query->orderBy('due_date')->paginate(20);

        $stats = [
            'total_installments' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->count(),
            'paid_installments' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->where('is_paid', true)->count(),
            'pending_installments' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->where('is_paid', false)->count(),
            'total_amount' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->sum('amount'),
            'paid_amount' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->where('is_paid', true)->sum('amount'),
            'pending_amount' => PaymentSchedule::whereHas('contract', function($q) {
                $q->where('status', 'signe');
            })->where('is_paid', false)->sum('amount'),
        ];

        $commercials = User::where('role', 'commercial')
            ->where('is_active', true)
            ->get();

        $monthlyData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyData->push([
                'month' => $month->format('M Y'),
                'due_amount' => PaymentSchedule::whereHas('contract', function($q) {
                    $q->where('status', 'signe');
                })->whereYear('due_date', $month->year)
                  ->whereMonth('due_date', $month->month)
                  ->sum('amount'),
                'paid_amount' => PaymentSchedule::whereHas('contract', function($q) {
                    $q->where('status', 'signe');
                })->where('is_paid', true)
                  ->whereYear('paid_date', $month->year)
                  ->whereMonth('paid_date', $month->month)
                  ->sum('amount'),
            ]);
        }

        return view('payment_schedules.index', compact(
            'schedules', 
            'stats', 
            'commercials', 
            'monthlyData',
            'status',
            'month',
            'commercial'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        $status = $request->get('status', 'all');
        $month = $request->get('month', now()->format('Y-m'));
        $commercial = $request->get('commercial', 'all');

        $query = PaymentSchedule::with(['contract.client', 'contract.site', 'contract.lot'])
            ->whereHas('contract', function($q) {
                $q->where('status', 'signe');
            });

        if ($status !== 'all') {
            $query->where('is_paid', $status === 'paid');
        }

        if ($month) {
            $query->whereYear('due_date', substr($month, 0, 4))
                  ->whereMonth('due_date', substr($month, 5, 2));
        }

        if ($user->isManager() || $user->isAdmin()) {
            if ($commercial !== 'all') {
                $query->whereHas('contract.client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial);
                });
            }
        } else {
            $query->whereHas('contract.client', function($q) use ($user) {
                $q->where('assigned_to_id', $user->id);
            });
        }

        $schedules = $query->orderBy('due_date')->get();

        $filename = 'echeancier_paiements_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($schedules) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Client',
                'Téléphone',
                'Contrat',
                'Site',
                'Lot',
                'Échéance N°',
                'Date d\'échéance',
                'Montant',
                'Statut',
                'Date de paiement',
                'Méthode de paiement',
                'Notes'
            ]);

            foreach ($schedules as $schedule) {
                fputcsv($file, [
                    $schedule->contract->client->full_name,
                    $schedule->contract->client->phone,
                    $schedule->contract->contract_number,
                    $schedule->contract->site->name ?? 'N/A',
                    $schedule->contract->lot->reference ?? 'N/A',
                    $schedule->installment_number,
                    $schedule->due_date->format('d/m/Y'),
                    number_format($schedule->amount, 0, ',', ' ') . ' FCFA',
                    $schedule->is_paid ? 'Payé' : 'En attente',
                    $schedule->paid_date ? $schedule->paid_date->format('d/m/Y') : 'N/A',
                    $schedule->payment_method ?? 'N/A',
                    $schedule->notes ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function clientSchedules(Request $request, Prospect $client)
    {
        if (auth()->user()->isAgent() && $client->assigned_to_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $contracts = $client->contracts()
            ->with([
                'site',
                'lot' => function($query) {
                    $query->select('id', 'lot_number');
                }
            ])
            ->get();

        \Log::info('Contrats chargés:', [
            'client_id' => $client->id,
            'contrats' => $contracts->map(function($contract) {
                return [
                    'contrat_id' => $contract->id,
                    'lot_id' => $contract->lot_id,
                    'lot_number' => $contract->lot ? $contract->lot->lot_number : null
                ];
            })->toArray()
        ]);

        $schedules = PaymentSchedule::whereIn('contract_id', $contracts->pluck('id'))
            ->with(['contract.site', 'contract.lot'])
            ->orderBy('due_date', 'asc')
            ->get();

        $stats = [
            'total_contracts' => $contracts->count(),
            'total_installments' => $schedules->count(),
            'paid_installments' => $schedules->where('is_paid', true)->count(),
            'pending_installments' => $schedules->where('is_paid', false)->count(),
            'overdue_installments' => $schedules->where('is_paid', false)->filter(function($schedule) {
                return $schedule->due_date->isPast();
            })->count(),
            'total_amount' => $schedules->sum('amount'),
            'paid_amount' => $schedules->where('is_paid', true)->sum('amount'),
            'pending_amount' => $schedules->where('is_paid', false)->sum('amount'),
        ];

        $schedulesByContract = $schedules->groupBy('contract_id');

        return view('payment_schedules.client_detail', compact('client', 'contracts', 'schedules', 'schedulesByContract', 'stats'));
    }
}