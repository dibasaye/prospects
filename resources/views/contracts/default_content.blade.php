<div class="parties">
    <div class="kv">
        <div><b>Monsieur :</b></div> 
        <div>{{ $client->full_name }}</div>

        <div><b>Date et lieu de naissance :</b></div> 
        <div>{{ $client->birth_date ?? 'Non spécifié' }} à {{ $client->birth_place ?? 'Non spécifié' }}</div>

        <div><b>Adresse Personnelle :</b></div> 
        <div>{{ $client->address ?? 'Non spécifiée' }}</div>

        <div><b>Pays de Résidence :</b></div> 
        <div>{{ $client->country ?? 'Non spécifié' }}</div>

        <div><b>Nationalité :</b></div> 
        <div>{{ $client->nationality ?? 'Non spécifiée' }}</div>

        <div><b>Type et N° de pièce d'identité :</b></div> 
        <div>{{ $client->id_type ?? 'Non spécifié' }} N° {{ $client->id_number ?? 'Non spécifié' }} délivrée le : {{ $client->id_issue_date ?? 'Non spécifié' }}</div>

        <div><b>Numéro mobile :</b></div> 
        <div>{{ $client->phone ?? 'Non spécifié' }}</div>
    </div>
</div>

<p class="center"><i>Ci-après dénommé "l'Acquéreur" ou le « Client »</i>,</p>

<p class="center"><b>IL A ÉTÉ CONVENU CE QUI SUIT :</b></p>

<p class="article">PRÉAMBULE</p>

<p>
    YAYE DIA BTP propose à la commercialisation les terrains issus du lotissement de cette assiette dans
    le cadre de son projet et le client souhaite en acquérir selon les termes et conditions prévues dans les
    présentes.
</p>

<p>
    C'est dans ce contexte que les Parties ont convenu de la signature du présent contrat (le « Contrat »),
    qui définit les termes et conditions de leurs engagements respectifs.
</p>

<p style="text-align: center; font-weight: normal; margin: 10mm 0;">
    Ceci exposé, il a été convenu et arrêté ce qui suit :
</p>

<p class="article">Article 1 : Objet du contrat</p>
<p>
    Le présent contrat a pour objet de fixer les conditions et modalités suivant lesquelles YAYE DIA BTP
    réserve au Client un lot de terrain en vue de son acquisition.
</p>

<p class="article">Article 2 : Désignation du terrain</p>
<p>
    L'assiette de l'ensemble immobilier est constituée par le terrain situé à <b>{{ $contract->site->name ?? 'Site non spécifié' }}</b>, 
    d'une superficie totale de {{ $contract->site->surface ?? '___' }}, suivant la délibération N°002 /AKM.
</p>

<p class="article">Article 3 : Réservation</p>
<p>
    Le présent contrat est conclu en vue de l'acquisition par le Client du terrain faisant l'objet du lot 
    {{ $contract->lot->reference ?? 'Non spécifié' }} dans le cadre de l'ensemble immobilier visé à l'article 2.
    En conséquence, YAYE DIA BTP accepte de céder au Client qui consent à acheter le terrain objet des
    présentes, selon les conditions et modalités prévues dans le présent contrat.
</p>

<p class="article">Article 4 : Conditions et modalités financières</p>
<p>
    4.1 Prix de vente du terrain : {{ number_format($contract->total_amount ?? 0, 0, ',', ' ') }} FCFA.<br>
    Acompte : {{ number_format($contract->paid_amount ?? 0, 0, ',', ' ') }} FCFA.<br>
    Durée de paiement : {{ $contract->payment_duration_months ?? '___' }} mois.
</p>

<p>
    Le prix de vente ainsi fixé ne tient pas compte :
</p>
<p class="indent">
    - Des frais que le Client a l'intention d'utiliser ou de solliciter pour financer la présente acquisition ;
</p>
<p class="indent">
    - Tous autres frais non expressément mentionnés dans les présentes.
</p>

<p>
    4.2 Modalité de paiement et domiciliation<br>
    Le prix stipulé au présent contrat est payable sur une durée de {{ $contract->payment_duration_months ?? '24' }} mois 
    à raison d'un acompte de {{ number_format($contract->paid_amount ?? 0, 0, ',', ' ') }} Francs CFA 
    et d'une mensualité de {{ number_format($contract->monthly_payment ?? 0, 0, ',', ' ') }} Francs CFA.
</p>

<p>
    Les mensualités sont payables au plus tard le 10 de chaque mois.
</p>

<p class="article">Article 5 : Frais d'ouverture de dossier</p>
<p class="indent">
    Un montant total de cent mille francs (100 000 FCFA) est versé par le client pour le traitement du lot de
    terrain au titre des frais de son dossier. Ce montant n'est pas remboursable.
</p>

<p class="article">Article 6 : Durée et Prise d'effet</p>
<p class="indent">
    Le présent contrat prend effet à compter de la date de sa signature pour une durée de {{ $contract->payment_duration_months ?? 'vingt-quatre' }} mois.
</p>

<p class="article">Article 7 : Rétraction</p>
<p class="indent">
    Le Client dispose de la faculté de se rétracter dans un délai de 24h par ses soins ou par les soins de
    son représentant, sans avoir à se justifier. Le délai de rétractation ne commence à courir qu'à compter
    du lendemain de la signature du présent contrat, la signature valant notification du délai de rétractation.
</p>

<p class="center small" style="margin-top: 20mm;">
    Fait en deux (02) exemplaires originaux, À Dakar, <strong>Date :</strong> {{ now()->format('d/m/Y') }}
</p>

<div class="signatures">
    <div class="sign-box">
        <p><b>Pour YAYE DIA BTP</b></p>
        <p class="muted">Gérante : Fatou Faye</p>
    </div>
    <div class="sign-box">
        <p><b>Le Client</b></p>
        <p class="muted">{{ $client->full_name }}</p>
    </div>
</div>
