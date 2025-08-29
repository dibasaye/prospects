<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment;
use App\Models\Contract;

return new class extends Migration
{
    public function up()
    {
        // Ajouter la colonne contract_id
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable()->after('lot_id');
            $table->foreign('contract_id')
                  ->references('id')
                  ->on('contracts')
                  ->onDelete('set null');
        });

        // Mettre à jour les enregistrements existants
        if (Schema::hasTable('payments') && Schema::hasTable('contracts')) {
            $payments = Payment::all();
            foreach ($payments as $payment) {
                // Essayer de trouver un contrat correspondant
                $contract = Contract::where('client_id', $payment->client_id)
                    ->where('site_id', $payment->site_id)
                    ->when($payment->lot_id, function($query, $lotId) {
                        return $query->where('lot_id', $lotId);
                    })
                    ->first();
                
                if ($contract) {
                    $payment->contract_id = $contract->id;
                    $payment->save();
                }
            }
        }
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
    }
};
