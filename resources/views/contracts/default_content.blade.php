<div class="parties">
    <div class="kv">
        <div><b>Monsieur :</b></div> 
        <div>{{ $client->full_name }}</div>

        <div><b>Date et lieu de naissance :</b></div> 
        <div>{{ $client->birth_date }} à {{ $client->birth_place }}</div>

        <div><b>Adresse Personnelle :</b></div> 
        <div>{{ $client->address }}</div>

        <div><b>Pays de Résidence :</b></div> 
        <div>{{ $client->country }}</div>

        <div><b>Nationalité :</b></div> 
        <div>{{ $client->nationality }}</div>

        <div><b>Type et N° de pièce d'identité :</b></div> 
        <div>{{ $client->id_type }} N° {{ $client->id_number }} délivrée le : {{ $client->id_issue_date }}</div>

        <div><b>Numéro mobile :</b></div> 
        <div>{{ $client->phone }}</div>
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
