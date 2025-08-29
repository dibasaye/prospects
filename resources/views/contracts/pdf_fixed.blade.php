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
    @if(!empty($watermark_image))
    background: url('{{ $watermark_image }}') no-repeat center center;
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

  a { color: var(--accent); text-decoration: none; }

  /* Styles pour le contenu personnalisé */
  .contract-content {
    position: relative;
    z-index: 1;
  }

  .contract-content * {
    max-width: 100% !important;
    word-wrap: break-word !important;
  }
</style>
</head>

@php
    $headerImg = $header_image ?? '';
    $footerImg = $footer_image ?? '';
    $watermarkImg = $watermark_image ?? '';
@endphp

<body>
<div class="page">
  <!-- En-tête avec logo -->
  @if(!empty($headerImg))
  <img class="header-image" src="{{ $headerImg }}" alt="En-tête YAYE DIA BTP" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto 20px;">
  @endif

  <!-- Titre du contrat -->
  <h1 class="title">Contrat de réservation</h1>

  <p class="center"><b>ENTRE LES SOUSSIGNÉS :</b></p>

  <p class="indent">
    La société « <b>YAYE DIA BTP</b> », Société par actions simplifiée (SAS) ayant son siège social à
    Cité Keur-Gorgui lot 33 et 34 et immatriculée au registre du commerce sous le numéro
    <b>SN DKR 2024 B 31686</b>, NINEA : <b>011440188</b>. Représentée Madame <b>Fatou Faye</b>
    agissant en qualité de Gérante, dûment habilité aux fins des présentes.
  </p>

  <p class="center"><i>Ci-après dénommée "YAYE DIA BTP" ou « Promoteur »</i>,</p>

  <p class="center"><b>Et</b></p>

  <!-- Contenu personnalisé du contrat -->
  <div class="contract-content">
    @if(isset($custom_content) && !empty($custom_content))
      {!! $custom_content !!}
    @else
      <div style="color: red; font-weight: bold; text-align: center; margin: 20px 0;">
        Erreur : Le contenu du contrat est manquant. Veuillez recharger la page.
      </div>
    @endif
  </div>

  <!-- Footer -->
  <div class="bottom-refs tiny">
    @if(!empty($footerImg))
    <img src="{{ $footerImg }}" alt="Bannière footer" style="width: 100%; max-width: 300px; height: auto; display: block; margin: 20px auto 0;">
    @endif
    RCCM: SN DKR 2024 B 31686 – NINEA : 011440188<br>
    Cptes bancaires : SN039 01001 067615921200 05 SN012 01201 036206462201 47<br>
    CITE KEUR GORGUI LOT 33 ET 34 +221 78 192 00 00 / +221 33 827 00 65<br>
    yayediasarl@gmail.com www.groupeyaye.com
  </div>
</div>
</body>
</html>