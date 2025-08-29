<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Contract;
use Illuminate\Support\Facades\Log;

class ContractPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission = 'view')
    {
        // Récupérer le contrat depuis la route
        $contract = $request->route('contract');
        
        if (!$contract instanceof Contract) {
            // Si ce n'est pas une instance de Contract, essayer de le récupérer par ID
            $contractId = $contract;
            $contract = Contract::find($contractId);
            
            if (!$contract) {
                abort(404, 'Contrat non trouvé');
            }
        }

        // Vérifier les permissions selon l'action demandée
        switch ($permission) {
            case 'view':
                if (!$this->canView($request, $contract)) {
                    abort(403, 'Vous n\'avez pas l\'autorisation de voir ce contrat');
                }
                break;
                
            case 'edit':
                if (!$this->canEdit($request, $contract)) {
                    abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce contrat');
                }
                break;
                
            case 'sign':
                if (!$this->canSign($request, $contract)) {
                    abort(403, 'Vous n\'avez pas l\'autorisation de signer ce contrat');
                }
                break;
                
            case 'export':
                if (!$this->canExport($request, $contract)) {
                    abort(403, 'Vous n\'avez pas l\'autorisation d\'exporter ce contrat');
                }
                break;
                
            default:
                abort(403, 'Permission non reconnue');
        }

        return $next($request);
    }

    /**
     * Vérifier si l'utilisateur peut voir le contrat
     */
    private function canView(Request $request, Contract $contract): bool
    {
        $user = $request->user();
        
        if (!$user) {
            return false;
        }

        // Admin peut tout voir
        if ($user->hasRole('admin')) {
            return true;
        }

        // Commercial peut voir ses propres contrats ou ceux de ses prospects
        if ($user->hasRole('commercial')) {
            // Contrat généré par l'utilisateur
            if ($contract->generated_by === $user->id) {
                return true;
            }
            
            // Prospect assigné à l'utilisateur
            if ($contract->client && $contract->client->assigned_to === $user->id) {
                return true;
            }
        }

        // Gestionnaire peut voir tous les contrats
        if ($user->hasRole('gestionnaire')) {
            return true;
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier le contrat
     */
    private function canEdit(Request $request, Contract $contract): bool
    {
        $user = $request->user();
        
        if (!$user) {
            return false;
        }

        // Ne peut pas modifier un contrat signé ou annulé
        if (in_array($contract->status, [Contract::STATUS_SIGNED, Contract::STATUS_CANCELLED])) {
            return false;
        }

        // Admin peut tout modifier
        if ($user->hasRole('admin')) {
            return true;
        }

        // Commercial peut modifier ses propres contrats en brouillon
        if ($user->hasRole('commercial')) {
            return $contract->generated_by === $user->id && $contract->status === Contract::STATUS_DRAFT;
        }

        // Gestionnaire peut modifier les contrats en brouillon
        if ($user->hasRole('gestionnaire')) {
            return $contract->status === Contract::STATUS_DRAFT;
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur peut signer le contrat
     */
    private function canSign(Request $request, Contract $contract): bool
    {
        $user = $request->user();
        
        if (!$user) {
            return false;
        }

        // Ne peut signer que les brouillons
        if ($contract->status !== Contract::STATUS_DRAFT) {
            return false;
        }

        // Admin et gestionnaire peuvent signer
        if ($user->hasRole(['admin', 'gestionnaire'])) {
            return true;
        }

        // Commercial peut signer ses propres contrats
        if ($user->hasRole('commercial')) {
            return $contract->generated_by === $user->id;
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur peut exporter le contrat
     */
    private function canExport(Request $request, Contract $contract): bool
    {
        // Pour l'export, utiliser les mêmes règles que pour la visualisation
        return $this->canView($request, $contract);
    }
}