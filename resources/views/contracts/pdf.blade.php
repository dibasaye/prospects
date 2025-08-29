<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Contrat de Réservation – YAYE DIA BTP</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  /* --- Mise en page A4 imprimable --- */
  @page {
    size: A4;
    margin: 18mm 16mm 18mm 16mm; 
  }

  :root{
    --fs-base: 11pt;
    --fs-small: 10pt;
    --fs-tiny: 9pt;
    --lh: 1.5;
    --accent: #000;
    --spacing: 3.5mm;
  }

  html, body { height: 100%; background: #fff; }
  body {
    font: 400 var(--fs-base)/var(--lh) "Times New Roman", Times, serif;
    color: #000; -webkit-print-color-adjust: exact; print-color-adjust: exact;
    margin: 0;
  }

  /* --- Structure page --- */
  .page {
    page-break-after: always;
    padding: 15mm 20mm 20mm 20mm;
    position: relative;
    box-sizing: border-box;
    min-height: 297mm;
    max-width: 210mm;
    margin: 0 auto;
  }



  .page::before {
    content: "";
    position: absolute;
    top: 50%; left: 50%;
    width: 180mm; height: 180mm;
    @if(!empty($watermarkImg))
    background: url('{{ $watermarkImg }}') no-repeat center center;
    background-size: contain;
    opacity: 0.05;
    @endif
    transform: translate(-50%, -50%);
    z-index: 0;
    pointer-events: none;
  }


  .header-image {
    width: 100%;
    display: block;
    margin-bottom: 8mm;
  }

  .top-refs {
    font-size: var(--fs-small);
    line-height: 1.35;
    text-align: center;
    margin-bottom: 6mm;
    white-space: pre-line;
  }

  .title {
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin: 10mm 0 6mm;
  }

  .article {
    margin: 6mm 0 3mm;
    font-weight: 700;
    page-break-after: avoid;
  }

  p { 
    text-align: justify; 
    hyphens: auto; 
    margin: 0 0 var(--spacing) 0; 
    line-height: var(--lh);
  }
  .indent { text-indent: 8mm; }
  .center { text-align: center; }
  .small { font-size: var(--fs-small); }
  .tiny { font-size: var(--fs-tiny); }

  .parties { margin: 6mm 0; }
  .kv {
    display: grid;
    grid-template-columns: 60mm 1fr;
    gap: 2mm 6mm;
    margin: 4mm 0 3mm;
    font-size: var(--fs-small);
  }
  .kv b { display: inline-block; }

  .pageno {
    position: absolute; 
    left: 0; 
    right: 0; 
    bottom: 5mm;
    text-align: center; 
    font-size: var(--fs-tiny);
    color: #666;
  }

  .signatures {
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 15mm;
    margin: 15mm 0 10mm;
    page-break-inside: avoid;
  }
  .sign-box { border: 0; padding: 0; min-height: 28mm; }
  .muted { color: #000; opacity: .9; }

  /* --- Footer avec image --- */
  .bottom-refs {
    font-size: var(--fs-tiny);
    line-height: 1.3;
    text-align: center;
    margin-top: 8mm;
    padding-top: 4mm;
    border-top: 0.5px solid #ddd;
    page-break-before: avoid;
  }


  .bottom-refs img {
  width: 100%;
  max-height: 10mm;
  object-fit: cover;
  margin-bottom: 2mm;
}
  .pb { page-break-before: always; }
  .pa { page-break-after: always; }

  a { color: var(--accent); text-decoration: none; }
</style>
</head>
@php
    $headerImg = $header_image ?? '';
    $footerImg = $footer_image ?? '';
    $watermarkImg = $watermark_image ?? '';
@endphp
<body>

<!-- ========================= PAGE 1 ========================= -->
<section class="page">
  <img class="header-image" src="{{ $headerImg }}" alt="En-tête YAYE DIA BTP" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto 20px;">

  <h1 class="title">Contrat de réservation</h1>

  <p class="center"><b>ENTRE-LES SOUSSIGNÉS :</b></p>

  <p class="indent">
    La société « <b>YAYE DIA BTP</b> », Société par actions simplifiée (SAS) ayant son siège social à
    Cité Keur-Gorgui lot 33 et 34 et immatriculée au registre du commerce sous le numéro
    <b>SN DKR 2024 B 31686</b>, NINEA : <b>011440188</b>. Représentée Madame <b>Fatou Faye</b>
    agissant en qualité de Gérante, dûment habilité aux fins des présentes.
  </p>

  <p class="center"><i>Ci-après dénommée "YAYE DIA BTP" ou « Promoteur »</i>,</p>

  <p class="center"><b>Et</b></p>

  <!-- Affichage du contenu du contrat -->
  @if(isset($custom_content) && !empty($custom_content))
    {!! $custom_content !!}
  @else
    <!-- Le contenu par défaut est déjà inclus dans $custom_content par le contrôleur -->
    <!-- Cette section ne devrait normalement jamais être atteinte -->
    <div style="color: red; font-weight: bold;">
      Erreur : Le contenu du contrat est manquant. Veuillez recharger la page.
    </div>
  @endif






  <div class="bottom-refs tiny">
    @if(!empty($footerImg))
    <img src="{{ $footerImg }}" alt="Bannière footer" style="width: 100%; max-width: 300px; height: auto; display: block; margin: 20px auto 0;">
    @endif
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires :  SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 1 -</div>
</section>

<!-- ========================= PAGE 2 ========================= -->
<section class="page">
  <img class="header-image" src="{{ $headerImg }}" alt="En-tête YAYE DIA BTP" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto 20px;">

  <p>
    YAYE DIA BTP propose à la commercialisation les terrains issus du lotissement de cette assiette dans
    le cadre de son projet et le client souhaite en acquérir selon les termes et conditions prévues dans les
    présentes.
  </p>

  <p>
    C’est dans ce contexte que les Parties ont convenu de la signature du présent contrat (le « Contrat »),
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
   L’assiette de l’ensemble immobilier est constituée par le terrain situé à <b>{{ $contract->site->name }}</b>, 
  d’une superficie totale de {{ $contract->site->surface ?? '___' }}, suivant la délibération N°002 /AKM.
  </p>

  <p class="article">Article 3 : Réservation</p>
  <p>
   Le présent contrat est conclu en vue de l’acquisition par le Client du terrain faisant l’objet des lots N°124
   / 126 d’une superficie de 225m2 chacune dans le cadre de l’ensemble immobilier visé à l’article 2.
   En conséquence, YAYE DIA BTP accepte de céder au Client qui consent à acheter le terrain objet des
   présentes, selon les conditions et modalités prévues dans le présent contrat.
   La présente réservation est formalisée par un acte de cession du programme désigné à l’article 4.2 du
   présent contrat
  </p>


   <p class="article">Article 4 : Conditions et modalités financières</p>
  <p>
  4.1 Prix de vente du terrain : {{ number_format($contract->total_amount, 0, ',', ' ') }} FCFA.<br>
  Acompte : {{ number_format($contract->paid_amount, 0, ',', ' ') }} FCFA.<br>
  Durée de paiement : {{ $contract->payment_duration_months }} mois.
  </p>

  <p>
    Le prix de vente ainsi fixé ne tient pas compte :

      <p class="indent">
        - Des frais que le Client a l’intention d’utiliser ou de solliciter pour financer la présente acquisition ;
      </p>
      <p class="indent">
         - Tous autres frais non expressément mentionnés dans les présentes.

      </p>

    </p>

    <p>
 4.2 Modalité de paiement et domiciliation
Le prix stipulé au présent contrat est payable sur une durée de 24 mois à raison d’un acompte d’un
million cent mille (1 100 000) Francs CFA et d’une mensualité de 245 900 Francs CFA.
  </p>

  <p>
    Les mensualités sont payables au plus tard le 10 de chaque mois.
  </p>

  <p class="article">Article 5 : Frais d’ouverture de dossier</p>
<p class="indent">
  Un montant total de cent mille francs (100 000 FCFA) est versé par le client pour le traitement du lot de
  terrain au titre des frais de son dossier. Ce montant n’est pas remboursable.
</p>

<p class="article">Article 6 : Durée et Prise d’effet</p>
<p class="indent">
  Le présent contrat prend effet à compter de la date de sa signature pour une durée de vingt-quatre (24)
  mois.
</p>

<p class="article">Article 7 : Rétraction</p>
<p class="indent">
  Le Client dispose de la faculté de se rétracter dans un délai de 24h par ses soins ou par les soins de
  son représentant, sans avoir à se justifier. Le délai de rétractation ne commence à courir qu’à compter
  du lendemain de la signature du présent contrat, la signature valant notification du délai de rétractation. 
  Ce délai expire à la fin des 24h à compter du lendemain de la signature du présent contrat.
</p>
<p class="indent">
  Le Client peut exercer auprès de YAYE DIA BTP la faculté de rétractation par tout moyen écrit
  permettant d’attester de sa réception par le destinataire.
</p>
<p class="indent">
  En cas de rétractation exercée dans le délai, le présent acte est caduc et ne peut recevoir aucune
  exécution, même partielle. En cas de rétractation, les frais des présentes sont à la charge définitive de
  YAYE DIA BTP.
</p>
<p class="indent">
  En cas de rétractation à l’expiration du délai des 24h, YAYE DIA BTP effectue, à titre de pénalité, une
  retenue de 10% du prix de vente.
</p>
<p class="indent">
  Le remboursement du reliquat des montants éventuels versés par le Client intervient dans un délai de
  six (06) mois à compter de la date à laquelle la notification de rétractation, qui peut être faite par tout moyen
  écrit permettant d’attester de sa réception, a été servie.
</p>

<p class="article">Article 8 : Résolution du contrat</p>
<p class="indent">
  8.1 Résolution de plein droit faute de paiement du prix à son échéance. Il est expressément stipulé qu'à 
  défaut de paiement d'une somme quelconque formant partie du prix, trois (03) mois après son exacte 
  échéance, le présent contrat est résolu de plein droit au bénéfice exclusif de YAYE DIA BTP qui peut y renoncer. 
  En cas d’exercice de cette faculté de résolution
  
</p>





  





  <div class="bottom-refs tiny">
    @if(!empty($footerImg))
    <img src="{{ $footerImg }}" alt="Bannière footer" style="width: 100%; max-width: 300px; height: auto; display: block; margin: 20px auto 0;">
    @endif
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires :  SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 2 -</div>
</section>

<!-- ========================= PAGE 3 ========================= -->
<section class="page">
  <img class="header-image" src="{{ $headerImg }}" alt="En-tête YAYE DIA BTP" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto 20px;">
  <p>
  Unilatérale, YAYE DIA BTP notifie la rupture du contrat par exploit d’huissier mentionnant son intention
  de se prévaloir des stipulations du présent paragraphe. La résolution du présent contrat entraîne des pénalités
  à hauteur de 10% du prix de vente du terrain au profit de YAYE DIA BTP. Le remboursement du reliquat des
  montants éventuels versés par le Client ne peut intervenir que dans un délai de six (06) mois à compter
  de la date à laquelle l’exploit d’huissier notifiant la résolution du contrat a été servi.
</p>

<p class="article">Article 9 : Intérêts de retard</p>
<p class="indent">
  Toute somme formant partie du prix qui n’est pas payée à date échue sera, de plein droit et sans qu'il
  ait besoin d'une mise en demeure, passible d'une indemnité fixée à 5% du montant mensuel dû par
  le Client. Les sommes dues sont stipulées indivisibles. En conséquence, en cas de décès du Réservataire avant
  sa complète libération, il existe une solidarité entre ses héritiers et représentants pour le paiement tant
  de ce qui resterait alors dû, que des frais de la signification judiciaire.
</p>

<p class="article">Article 10 : Réalisation de la vente</p>
<p class="indent">
  L’acte de vente est établi par YAYE DIA BTP après paiement complet du prix de vente par le Client.
  L’acte est adressé au Client par tout moyen écrit permettant d’attester de sa réception par le
  destinataire. Il comporte les informations relatives à la vente et est accompagné, le cas échéant, du
  plan cadastral du terrain, de l’attestation de la demande de bail ainsi que de tous autres documents relatifs
  à la vente.
</p>

<p class="article">Article 11 : Données à caractère personnel</p>
<p class="indent">
  Les données à caractère personnel recueillies par YAYE DIA BTP SAS font l’objet d’un traitement
  informatique ou analogique destiné à satisfaire aux exigences légales, réglementaires et conventionnelles en vigueur.
</p>
<p class="indent">
  YAYE DIA BTP garantit que les traitements opérés sur les données à caractère personnel du
  réservataire le sont en conformité avec les exigences de légitimité, de licéité, de loyauté, de sécurité et
  de conservation telles que prescrites dans la loi n° 2008-12 du 25 janvier 2008 relative à la protection
  des données à caractère personnel. Il garantit en outre que ces données sont uniquement destinées
  aux finalités pour lesquelles elles sont recueillies.
</p>
<p class="indent">
  Seules les personnes habilitées ont accès aux données à caractère personnel faisant l’objet de
  traitement. Conformément à la loi n° 2008-12 relative à la protection des données à caractère personnel, 
  le Client bénéficie d’un droit d’accès et de rectification aux informations le concernant. Il peut exercer ce droit en
  s’adressant directement à YAYE DIA BTP, par courrier électronique à l’adresse indiquée dans le présent
  contrat.
</p>

<p class="article">Article 12 : Force majeure</p>
<p class="indent">
  12.1 En cas de défaillance dans l’exécution de l’une des obligations prévues à la présente convention,
  la partie débitrice n’est pas considérée comme défaillante ni tenue à réparation si l’exécution de
  l’obligation a été rendue impossible par un cas de force majeure. Au sens du présent contrat, la force
  majeure est entendue comme un événement extérieur, irrésistible échappant au contrôle du débiteur
  qui ne pouvait être raisonnablement prévu lors de la conclusion du présent Contrat et dont les effets ne
  peuvent être évités par des mesures appropriées et qui empêche l’exécution de son obligation par le
  débiteur.
</p>
<p class="indent">
  12.2 La partie en situation de se prévaloir d’un des cas de force majeure au sens du paragraphe ci-dessus :
  <br>a. Avertit, sans délai, par tout moyen écrit permettant d’attester de sa réception par le destinataire,
  de l’existence de la force majeure, relatée de manière circonstanciée en indiquant la durée
  prévisible de l’événement et les dispositions que l’auteur de la notification a prises ou qu’il a tenté de prendre
  pour remédier aux conséquences de la force majeure ;
  <br>b. Fait ses meilleurs efforts pour trouver une solution de remplacement ou en tout cas reprendre
  l’exécution du contrat dès que c’est raisonnablement praticable.
</p>
<p class="indent">
  12.3 L’exécution du contrat se trouve entièrement suspendue dès la survenance du cas de
  force majeure, si du moins l’obligation dont l’exécution est empêchée constitue l’une des obligations
  significatives de la Convention. Si la suspension du contrat dure plus de six (6) mois, le cocontractant
  de la partie soumise à la force majeure est autorisé à résoudre le contrat par notification délivrée à
  l’autre partie par tout moyen écrit permettant d’attester de sa réception par le destinataire. Le cocontractant
  peut avant cela provoquer une entrevue avec cette partie afin de déterminer les conditions dans lesquelles
  la Convention peut, le cas échéant, être poursuivie.
</p>

<p class="article">Article 13 : Dispositions générales</p>
<p class="indent">
  13.1 Modification - Interprétation du contrat<br>
  Le présent contrat ne peut être modifié que par avenant signé par les Parties. Si l’une quelconque
  des stipulations du contrat est nulle au regard d’une règle de droit, d’une loi en vigueur, ou toute autre
  circonstance de droit ou de fait, elle est réputée non écrite mais n’entraîne pas la nullité du contrat.
  En cas de difficulté d’interprétation entre l’un quelconque des titres du contrat et l’une des clauses,
  les titres sont considérés comme inexistants.
</p>
<p class="indent">
  13.2 Remise de documents<br>
  Le Client reconnaît avoir reçu :<br>
  - Un exemplaire du présent contrat ;<br>
  - Un exemplaire du plan.
</p>



  
  <div class="bottom-refs tiny">
    @if(!empty($footerImg))
    <img src="{{ $footerImg }}" alt="Bannière footer" style="width: 100%; max-width: 300px; height: auto; display: block; margin: 20px auto 0;">
    @endif
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires :  SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 3 -</div>
  
</section>

<!-- ========================= PAGE 4 : Signatures ========================= -->
<section class="page">
  <img class="header-image" src="{{ $headerImg }}" alt="En-tête YAYE DIA BTP" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto 20px;">
 
<p class="indent">
  13.3 Élection de domicile<br>
  Pour l'exécution des présentes, et de leurs suites, les parties élisent domicile en leur siège social,
  domicile ou résidence respectifs indiqués en tête des présentes.
</p>

<p class="indent">
  13.4 Langue du contrat<br>
  Le présent contrat ainsi que tous les documents qui y sont attachés sont rédigés dans la langue
  française. Si pour la commodité de l’une des parties, le document contractuel était rédigé en langue
  étrangère, cette version n’aurait qu’une valeur informative, seule la version en langue française fait foi.
</p>

<p class="indent">
  13.5 Droit applicable – Juridiction compétente<br>
  Le présent pacte, ainsi que les droits des parties, devront être interprétés conformément à la
  législation en vigueur au Sénégal et tout litige concernant le présent pacte et qui ne pourrait être
  résolu à l’amiable relèvera exclusivement du Centre d’Arbitrage de Médiation-Conciliation de la
  chambre de Commerce de Dakar. Si cette procédure ne prospère pas, le contentieux sera soumis à la
  juridiction sénégalaise qui sera compétente.
</p>

<p class="center small">
  Fait en deux (02) exemplaires originaux, À Dakar, <strong>Date :</strong> {{ now()->format('d/m/Y') }}
</p>



  <div class="signatures">
    <div class="sign-box">
      <p><b>Pour YAYE DIA BTP</b></p>
      <p class="muted">Gérante : Fatou Faye</p>
    </div>
    <div class="sign-box">
      <p><b>Le Client</b></p>
      <p class="muted">OMAR BADJI</p>
    </div>
  </div>

  <div class="bottom-refs tiny">
    @if(!empty($footerImg))
    <img src="{{ $footerImg }}" alt="Bannière footer" style="width: 100%; max-width: 300px; height: auto; display: block; margin: 20px auto 0;">
    @endif
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires :  SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 4 -</div>
</section>

</body>
</html>
