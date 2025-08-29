<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Contrat de Réservation – YAYE DIA BTP</title>
<style>
  /* --- Mise en page A4 --- */
  body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 12.2pt;
    line-height: 1.45;
    color: #000;
    margin: 0;
    padding: 0;
  }

  .page {
    page-break-after: always;
    position: relative;
    padding: 12mm 16mm;
    box-sizing: border-box;
    min-height: 297mm;
  }

  .page::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 180mm;
    height: 180mm;
    background: url('{{ public_path('images/image.png') }}') no-repeat center center;
    background-size: contain;
    opacity: 0.05;
    transform: translate(-50%, -50%);
    z-index: -1;
    pointer-events: none;
  }

  .header-image {
    width: 100%;
    max-width: 15cm;
    display: block;
    margin: 0 auto 8mm;
  }

  .title {
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 16pt;
    margin: 10mm 0 6mm;
  }

  p { 
    margin: 0 0 3.2mm 0;
    text-align: justify;
  }

  .indent { 
    text-indent: 1cm;
    margin-left: 0;
  }

  .center { 
    text-align: center;
  }

  .small { 
    font-size: 11pt;
  }

  .tiny { 
    font-size: 10pt;
  }

  .kv {
    display: table;
    width: 100%;
    margin: 3mm 0 2mm;
  }

  .kv div {
    display: table-row;
  }

  .kv b, .kv span {
    display: table-cell;
    padding: 1mm 0;
    vertical-align: top;
  }

  .kv b {
    width: 52mm;
  }

  .signatures {
    display: flex;
    justify-content: space-between;
    margin-top: 18mm;
  }

  .sign-box {
    width: 48%;
    border-top: 1px solid #000;
    padding-top: 5mm;
    margin-top: 10mm;
  }

  .muted {
    opacity: 0.9;
  }

  .bottom-refs {
    position: absolute;
    bottom: 12mm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10pt;
    line-height: 1.35;
  }

  .bottom-refs img {
    max-width: 100%;
    height: auto;
    margin-bottom: 2mm;
  }

  .pageno {
    position: absolute;
    bottom: 5mm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10pt;
  }

  .article {
    font-weight: bold;
    margin: 4mm 0 2mm;
  }
</style>
</head>
<body>

<!-- Page 1 -->
<div class="page">
  <img class="header-image" src="{{ asset('images/yayedia.png') }}" alt="YAYE DIA BTP">
  
  <h1 class="title">Contrat de réservation</h1>

  <p class="center"><b>ENTRE-LES SOUSSIGNÉS :</b></p>

  <p class="indent">
    La société « <b>YAYE DIA BTP</b> », Société par actions simplifiée (SAS) ayant son siège social à
    Cité Keur-Gorgui lot 33 et 34 et immatriculée au registre du commerce sous le numéro
    <b>SN DKR 2024 B 31686</b>, NINEA : <b>011440188</b>. Représentée par Madame <b>Fatou Faye</b>
    agissant en qualité de Gérante, dûment habilité aux fins des présentes.
  </p>

  <p class="center"><i>Ci-après dénommée "YAYE DIA BTP" ou « Promoteur »</i>,</p>
  <p class="center"><b>Et</b></p>

  <div class="parties">
    <div class="kv">
      <div><b>Monsieur :</b> <span>{{ $contract->client->full_name }}</span></div>
      <div><b>Date et lieu de naissance :</b> <span>{{ $contract->client->birth_date ? $contract->client->birth_date->format('d/m/Y') : '' }} à {{ $contract->client->birth_place ?? '' }}</span></div>
      <div><b>Adresse Personnelle :</b> <span>{{ $contract->client->address ?? '' }}</span></div>
      <div><b>Pays de Résidence :</b> <span>{{ $contract->client->country ?? '' }}</span></div>
      <div><b>Nationalité :</b> <span>{{ $contract->client->nationality ?? '' }}</span></div>
      <div><b>Type et N° de pièce d'identité :</b> <span>{{ $contract->client->id_type ?? '' }} N° {{ $contract->client->id_number }} délivrée le : {{ $contract->client->id_issue_date ? $contract->client->id_issue_date->format('d/m/Y') : '' }}</span></div>
      <div><b>Numéro mobile :</b> <span>{{ $contract->client->phone ?? '' }}</span></div>
    </div>

    <p class="center"><i>Ci-après dénommé(e) "le Client",</i></p>
    <p class="center small"><i>Ci-après également dénommé(e)s individuellement la « Partie » et ensemble les « Parties ».</i></p>
  </div>

  <p class="title">IL A ÉTÉ PRÉALABLEMENT EXPOSÉ CE QUI SUIT :</p>

  <p>
    La société YAYE DIA BTP est une société spécialisée dans l'intermédiation et les prestations de
    services immobiliers. YAYE DIA BTP propose des produits de qualité garantissant la conformité
    aux standards les plus élevés de sa profession afin de répondre ainsi aux exigences de sa clientèle.
  </p>

  <p>
    Dans le cadre de ses activités et fort de son expérience, YAYE DIA BTP apporte son expertise et son
    savoir-faire dans le domaine de la promotion immobilière et foncière. C'est ainsi qu'elle offre plusieurs
    services, notamment la viabilisation, l'aménagement, la réalisation de projets immobiliers ou encore la
    commercialisation des biens ou de terrains.
  </p>

  <p>
    L'extrait de Délibération N° 002 du 26-01-2019 du Conseil Municipal de Keur Moussa relative à
    l'affectation de terre du domaine national sise à <b>{{ $contract->site->name }}</b> d'une superficie de 50 ha 00 ca,
    extrait des 324 HA 69 a 80 ca et établis par l'arrêté N° 046/AKM/SP portant le projet de
    lotissement dudit village pour sa restructuration.
    Un protocole a été signé avec la promotrice Madame Fatou Faye gérante de la société YAYE DIA BTP,
    pour la réalisation du protocole d'Accord du 27 Novembre 2020 prévoyant la restructuration dudit
    village. En compensation de la réalisation de ce projet la mairie de la commune de Keur Moussa cède
    à la société YAYE DIA BTP 30% des parcelles loties.
  </p>

  <div class="bottom-refs tiny">
    <img src="{{ asset('images/footer-image.png') }}" alt="Bannière footer">
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires : SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 1 -</div>
</div>

<!-- Page 2 -->
<div class="page">
  <img class="header-image" src="{{ asset('images/yayedia.png') }}" alt="YAYE DIA BTP">

  <p>
    YAYE DIA BTP propose à la commercialisation les terrains issus du lotissement de cette assiette dans
    le cadre de son projet et le client souhaite en acquérir selon les termes et conditions prévues dans les
    présentes.
  </p>

  <p>
    C'est dans ce contexte que les Parties ont convenu de la signature du présent contrat (le « Contrat »),
    qui définit les termes et conditions de leurs engagements respectifs.
  </p>

  <p style="text-align: center; margin: 10mm 0;">
    Ceci exposé, il a été convenu et arrêté ce qui suit :
  </p>

  <p class="article">Article 1 : Objet du contrat</p>
  <p>
    Le présent contrat a pour objet de fixer les conditions et modalités suivant lesquelles YAYE DIA BTP
    réserve au Client un lot de terrain en vue de son acquisition.
  </p>

  <p class="article">Article 2 : Désignation du terrain</p>
  <p>
    L'assiette de l'ensemble immobilier est constituée par le terrain situé à <b>{{ $contract->site->name }}</b>, 
    d'une superficie totale de {{ $contract->site->surface ?? '___' }}, suivant la délibération N°002 /AKM.
  </p>

  <p class="article">Article 3 : Réservation</p>
  <p>
    Le présent contrat est conclu en vue de l'acquisition par le Client du terrain faisant l'objet des lots N°124
    / 126 d'une superficie de 225m2 chacune dans le cadre de l'ensemble immobilier visé à l'article 2.
    En conséquence, YAYE DIA BTP accepte de céder au Client qui consent à acheter le terrain objet des
    présentes, selon les conditions et modalités prévues dans le présent contrat.
    La présente réservation est formalisée par un acte de cession du programme désigné à l'article 4.2 du
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
  </p>

  <p class="indent">
    - Des frais que le Client a l'intention d'utiliser ou de solliciter pour financer la présente acquisition ;
  </p>
  <p class="indent">
    - Tous autres frais non expressément mentionnés dans les présentes.
  </p>

  <p>
    4.2 Modalité de paiement et domiciliation
    Le prix stipulé au présent contrat est payable sur une durée de 24 mois à raison d'un acompte d'un
    million cent mille (1 100 000) Francs CFA et d'une mensualité de 245 900 Francs CFA.
  </p>

  <p>
    Les mensualités sont payables au plus tard le 10 de chaque mois.
  </p>

  <div class="bottom-refs tiny">
    <img src="{{ asset('images/footer-image.png') }}" alt="Bannière footer">
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires : SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 2 -</div>
</div>

<!-- Page 3 -->
<div class="page">
  <img class="header-image" src="{{ asset('images/yayedia.png') }}" alt="YAYE DIA BTP">

  <p class="article">Article 5 : Frais d'ouverture de dossier</p>
  <p class="indent">
    Un montant total de cent mille francs (100 000 FCFA) est versé par le client pour le traitement du lot de
    terrain au titre des frais de son dossier. Ce montant n'est pas remboursable.
  </p>

  <p class="article">Article 6 : Durée et Prise d'effet</p>
  <p class="indent">
    Le présent contrat prend effet à compter de la date de sa signature pour une durée de vingt-quatre (24)
    mois.
  </p>

  <p class="article">Article 7 : Rétraction</p>
  <p class="indent">
    Le Client dispose de la faculté de se rétracter dans un délai de 24h par ses soins ou par les soins de
    son représentant, sans avoir à se justifier. Le délai de rétractation ne commence à courir qu'à compter
    du lendemain de la signature du présent contrat, la signature valant notification du délai de rétractation. 
    Ce délai expire à la fin des 24h à compter du lendemain de la signature du présent contrat.
  </p>
  <p class="indent">
    Le Client peut exercer auprès de YAYE DIA BTP la faculté de rétractation par tout moyen écrit
    permettant d'attester de sa réception par le destinataire.
  </p>
  <p class="indent">
    En cas de rétractation exercée dans le délai, le présent acte est caduc et ne peut recevoir aucune
    exécution, même partielle. En cas de rétractation, les frais des présentes sont à la charge définitive de
    YAYE DIA BTP.
  </p>
  <p class="indent">
    En cas de rétractation à l'expiration du délai des 24h, YAYE DIA BTP effectue, à titre de pénalité, une
    retenue de 10% du prix de vente.
  </p>
  <p class="indent">
    Le remboursement du reliquat des montants éventuels versés par le Client intervient dans un délai de
    six (06) mois à compter de la date à laquelle la notification de rétractation, qui peut être faite par tout moyen
    écrit permettant d'attester de sa réception, a été servie.
  </p>

  <p class="article">Article 8 : Résolution du contrat</p>
  <p class="indent">
    8.1 Résolution de plein droit faute de paiement du prix à son échéance. Il est expressément stipulé qu'à 
    défaut de paiement d'une somme quelconque formant partie du prix, trois (03) mois après son exacte 
    échéance, le présent contrat est résolu de plein droit au bénéfice exclusif de YAYE DIA BTP qui peut y renoncer.
  </p>

  <div class="bottom-refs tiny">
    <img src="{{ asset('images/footer-image.png') }}" alt="Bannière footer">
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires : SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 3 -</div>
</div>

<!-- Page 4 -->
<div class="page">
  <img class="header-image" src="{{ asset('images/yayedia.png') }}" alt="YAYE DIA BTP">

  <p class="indent">
    Unilatérale, YAYE DIA BTP notifie la rupture du contrat par exploit d'huissier mentionnant son intention
    de se prévaloir des stipulations du présent paragraphe. La résolution du présent contrat entraîne des pénalités
    à hauteur de 10% du prix de vente du terrain au profit de YAYE DIA BTP. Le remboursement du reliquat des
    montants éventuels versés par le Client ne peut intervenir que dans un délai de six (06) mois à compter
    de la date à laquelle l'exploit d'huissier notifiant la résolution du contrat a été servi.
  </p>

  <p class="article">Article 9 : Intérêts de retard</p>
  <p class="indent">
    Toute somme formant partie du prix qui n'est pas payée à date échue sera, de plein droit et sans qu'il
    ait besoin d'une mise en demeure, passible d'une indemnité fixée à 5% du montant mensuel dû par
    le Client. Les sommes dues sont stipulées indivisibles. En conséquence, en cas de décès du Réservataire avant
    sa complète libération, il existe une solidarité entre ses héritiers et représentants pour le paiement tant
    de ce qui resterait alors dû, que des frais de la signification judiciaire.
  </p>

  <p class="article">Article 10 : Réalisation de la vente</p>
  <p class="indent">
    L'acte de vente est établi par YAYE DIA BTP après paiement complet du prix de vente par le Client.
    L'acte est adressé au Client par tout moyen écrit permettant d'attester de sa réception par le
    destinataire. Il comporte les informations relatives à la vente et est accompagné, le cas échéant, du
    plan cadastral du terrain, de l'attestation de la demande de bail ainsi que de tous autres documents relatifs
    à la vente.
  </p>

  <p class="article">Article 11 : Données à caractère personnel</p>
  <p class="indent">
    Les données à caractère personnel recueillies par YAYE DIA BTP SAS font l'objet d'un traitement
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
    Seules les personnes habilitées ont accès aux données à caractère personnel faisant l'objet de
    traitement. Conformément à la loi n° 2008-12 relative à la protection des données à caractère personnel, 
    le Client bénéficie d'un droit d'accès et de rectification aux informations le concernant. Il peut exercer ce droit en
    s'adressant directement à YAYE DIA BTP, par courrier électronique à l'adresse indiquée dans le présent
    contrat.
  </p>

  <div class="bottom-refs tiny">
    <img src="{{ asset('images/footer-image.png') }}" alt="Bannière footer">
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires : SN039 01001 067615921200 05    SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34   +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com    www.groupeyaye.com
  </div>
  <div class="pageno">- 4 -</div>
</div>

</body>
</html>
