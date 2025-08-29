<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Contract;
use App\Models\Prospect;
use App\Models\Site;
use App\Models\Lot;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ContractServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ContractService $contractService;
    protected User $user;
    protected Prospect $prospect;
    protected Site $site;
    protected Lot $lot;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->contractService = new ContractService();
        
        // Créer un utilisateur pour les tests
        $this->user = User::factory()->create([
            'role' => 'commercial'
        ]);
        
        // Authentifier l'utilisateur
        Auth::login($this->user);
        
        // Créer les données de test
        $this->site = Site::factory()->create();
        $this->lot = Lot::factory()->create([
            'site_id' => $this->site->id,
            'price' => 7000000
        ]);
        $this->prospect = Prospect::factory()->create();
        
        // Créer une réservation
        Reservation::factory()->create([
            'prospect_id' => $this->prospect->id,
            'lot_id' => $this->lot->id,
            'status' => 'confirmed'
        ]);
    }

    public function test_can_create_contract_from_reservation()
    {
        $contract = $this->contractService->createFromReservation($this->prospect);
        
        $this->assertInstanceOf(Contract::class, $contract);
        $this->assertEquals($this->prospect->id, $contract->client_id);
        $this->assertEquals($this->site->id, $contract->site_id);
        $this->assertEquals($this->lot->id, $contract->lot_id);
        $this->assertEquals(Contract::STATUS_DRAFT, $contract->status);
        $this->assertEquals($this->user->id, $contract->generated_by);
        $this->assertEquals(7000000, $contract->total_amount);
    }

    public function test_cannot_create_duplicate_contract()
    {
        // Créer un premier contrat
        $this->contractService->createFromReservation($this->prospect);
        
        // Essayer de créer un second contrat pour le même prospect
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Un contrat existe déjà pour ce client.');
        
        $this->contractService->createFromReservation($this->prospect);
    }

    public function test_cannot_create_contract_without_reservation()
    {
        $prospectWithoutReservation = Prospect::factory()->create();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Aucune réservation active avec un lot associé.');
        
        $this->contractService->createFromReservation($prospectWithoutReservation);
    }

    public function test_can_update_contract_content()
    {
        $contract = Contract::factory()->create([
            'status' => Contract::STATUS_DRAFT,
            'content' => 'Ancien contenu'
        ]);
        
        $newContent = 'Nouveau contenu du contrat';
        $result = $this->contractService->updateContent($contract, $newContent);
        
        $this->assertTrue($result['success']);
        $this->assertTrue($result['changed']);
        $this->assertEquals('Contenu mis à jour avec succès.', $result['message']);
        
        $contract->refresh();
        $this->assertEquals(htmlspecialchars($newContent, ENT_QUOTES | ENT_HTML5, 'UTF-8', false), $contract->content);
    }

    public function test_cannot_update_signed_contract_content()
    {
        $contract = Contract::factory()->create([
            'status' => Contract::STATUS_SIGNED
        ]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ce contrat ne peut plus être modifié.');
        
        $this->contractService->updateContent($contract, 'Nouveau contenu');
    }

    public function test_no_update_when_content_unchanged()
    {
        $content = 'Contenu identique';
        $contract = Contract::factory()->create([
            'status' => Contract::STATUS_DRAFT,
            'content' => htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8', false)
        ]);
        
        $result = $this->contractService->updateContent($contract, $content);
        
        $this->assertTrue($result['success']);
        $this->assertFalse($result['changed']);
        $this->assertEquals('Aucune modification détectée.', $result['message']);
    }

    public function test_can_sign_contract()
    {
        $contract = Contract::factory()->create([
            'status' => Contract::STATUS_DRAFT
        ]);
        
        $signatureData = [
            'signature_date' => now(),
            'notes' => 'Contrat signé par le client'
        ];
        
        $signedContract = $this->contractService->signContract($contract, $signatureData);
        
        $this->assertEquals(Contract::STATUS_SIGNED, $signedContract->status);
        $this->assertNotNull($signedContract->signature_date);
        $this->assertEquals($this->user->id, $signedContract->signed_by_agent);
        $this->assertEquals('Contrat signé par le client', $signedContract->notes);
    }

    public function test_contract_number_generation_is_unique()
    {
        $number1 = Contract::generateContractNumber();
        $number2 = Contract::generateContractNumber();
        
        $this->assertNotEquals($number1, $number2);
        $this->assertStringStartsWith('CTR-', $number1);
        $this->assertStringStartsWith('CTR-', $number2);
    }

    public function test_can_get_contract_stats()
    {
        // Créer quelques contrats de test
        Contract::factory()->count(3)->create(['status' => Contract::STATUS_DRAFT]);
        Contract::factory()->count(2)->create(['status' => Contract::STATUS_SIGNED]);
        Contract::factory()->count(1)->create(['status' => Contract::STATUS_CANCELLED]);
        
        $stats = $this->contractService->getContractStats();
        
        $this->assertEquals(6, $stats['total']);
        $this->assertEquals(3, $stats['draft']);
        $this->assertEquals(2, $stats['signed']);
        $this->assertEquals(1, $stats['cancelled']);
        $this->assertEquals(0, $stats['completed']);
    }

    public function test_contract_can_edit_content_based_on_status()
    {
        $draftContract = Contract::factory()->create(['status' => Contract::STATUS_DRAFT]);
        $signedContract = Contract::factory()->create(['status' => Contract::STATUS_SIGNED]);
        
        $this->assertTrue($draftContract->canEditContent());
        $this->assertFalse($signedContract->canEditContent());
    }

    public function test_contract_payment_percentage_calculation()
    {
        $contract = Contract::factory()->create([
            'total_amount' => 1000000,
            'paid_amount' => 250000
        ]);
        
        $this->assertEquals(25.0, $contract->payment_percentage);
    }

    public function test_contract_is_fully_paid()
    {
        $fullyPaid = Contract::factory()->create([
            'total_amount' => 1000000,
            'paid_amount' => 1000000
        ]);
        
        $partiallyPaid = Contract::factory()->create([
            'total_amount' => 1000000,
            'paid_amount' => 500000
        ]);
        
        $this->assertTrue($fullyPaid->isFullyPaid());
        $this->assertFalse($partiallyPaid->isFullyPaid());
    }
}