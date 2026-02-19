<?php
/**
 * Script to parse the MIT CSV and generate a comprehensive PortSeeder.
 * Maps each port locality to its Italian region using a known mapping.
 * Run: php scripts/build_ports_seeder.php
 */

// Mapping of Italian port localities to their region and province
$localityMap = [
    // Liguria
    'Sanremo' => ['Liguria', 'IM'], 'San Remo' => ['Liguria', 'IM'],
    'Portofino' => ['Liguria', 'GE'], 'Loano' => ['Liguria', 'SV'],
    'Genova' => ['Liguria', 'GE'], 'La Spezia' => ['Liguria', 'SP'],
    'Imperia' => ['Liguria', 'IM'], 'Savona' => ['Liguria', 'SV'],
    'Rapallo' => ['Liguria', 'GE'], 'Chiavari' => ['Liguria', 'GE'],
    'Lavagna' => ['Liguria', 'GE'], 'Santa Margherita Ligure' => ['Liguria', 'GE'],
    'Sestri Levante' => ['Liguria', 'GE'], 'Lerici' => ['Liguria', 'SP'],
    'Porto Venere' => ['Liguria', 'SP'], 'Alassio' => ['Liguria', 'SV'],
    'Andora' => ['Liguria', 'SV'], 'Arenzano' => ['Liguria', 'GE'],
    'Bordighera' => ['Liguria', 'IM'], 'Camogli' => ['Liguria', 'GE'],
    'Finale Ligure' => ['Liguria', 'SV'], 'Levanto' => ['Liguria', 'SP'],
    'Monterosso al Mare' => ['Liguria', 'SP'], 'Noli' => ['Liguria', 'SV'],
    'Oneglia' => ['Liguria', 'IM'], 'Ospedaletti' => ['Liguria', 'IM'],
    'Porto Maurizio' => ['Liguria', 'IM'], 'Recco' => ['Liguria', 'GE'],
    'Riva Trigoso' => ['Liguria', 'GE'], 'Riomaggiore' => ['Liguria', 'SP'],
    'Santo Stefano al Mare' => ['Liguria', 'IM'], 'Spotorno' => ['Liguria', 'SV'],
    'Vado Ligure' => ['Liguria', 'SV'], 'Varazze' => ['Liguria', 'SV'],
    'Ventimiglia' => ['Liguria', 'IM'], 'Vernazza' => ['Liguria', 'SP'],
    'Voltri' => ['Liguria', 'GE'], 'Multedo' => ['Liguria', 'GE'],
    'Arma di Taggia' => ['Liguria', 'IM'], 'Marina di Carrara' => ['Toscana', 'MS'],

    // Toscana
    'Viareggio' => ['Toscana', 'LU'], 'Livorno' => ['Toscana', 'LI'],
    'Piombino' => ['Toscana', 'LI'], 'Portoferraio' => ['Toscana', 'LI'],
    'Porto Azzurro' => ['Toscana', 'LI'], 'Forte dei Marmi' => ['Toscana', 'LU'],
    'Porto Ercole' => ['Toscana', 'GR'], 'Porto Santo Stefano' => ['Toscana', 'GR'],
    'Castiglioncello' => ['Toscana', 'LI'], 'Castiglione della Pescaia' => ['Toscana', 'GR'],
    'Cecina' => ['Toscana', 'LI'], 'Follonica' => ['Toscana', 'GR'],
    'Isola del Giglio' => ['Toscana', 'GR'], 'Giannutri' => ['Toscana', 'GR'],
    'Capraia' => ['Toscana', 'LI'], 'Cavo' => ['Toscana', 'LI'],
    'Marciana Marina' => ['Toscana', 'LI'], 'Marina di Campo' => ['Toscana', 'LI'],
    'Marina di Grosseto' => ['Toscana', 'GR'], 'Marina di Massa' => ['Toscana', 'MS'],
    'Marina di Pisa' => ['Toscana', 'PI'], 'Montalto di Castro' => ['Lazio', 'VT'],
    'Orbetello' => ['Toscana', 'GR'], 'Rio Marina' => ['Toscana', 'LI'],
    'Rosignano Solvay' => ['Toscana', 'LI'], 'Talamone' => ['Toscana', 'GR'],
    'Tirrenia' => ['Toscana', 'PI'], 'Vada' => ['Toscana', 'LI'],
    'Pianosa' => ['Toscana', 'LI'], 'Gorgona' => ['Toscana', 'LI'],

    // Emilia-Romagna
    'Rimini' => ['Emilia-Romagna', 'RN'], 'Ravenna' => ['Emilia-Romagna', 'RA'],
    'Cesenatico' => ['Emilia-Romagna', 'FC'], 'Bellaria' => ['Emilia-Romagna', 'RN'],
    'Cattolica' => ['Emilia-Romagna', 'RN'], 'Goro' => ['Emilia-Romagna', 'FE'],
    'Misano Adriatico' => ['Emilia-Romagna', 'RN'], 'Porto Garibaldi' => ['Emilia-Romagna', 'FE'],
    'Riccione' => ['Emilia-Romagna', 'RN'], 'Gabicce Mare' => ['Marche', 'PU'],

    // Veneto
    'Venezia' => ['Veneto', 'VE'], 'Chioggia' => ['Veneto', 'VE'],
    'Caorle' => ['Veneto', 'VE'], 'Jesolo' => ['Veneto', 'VE'],
    'Fusina' => ['Veneto', 'VE'], 'Marghera' => ['Veneto', 'VE'],
    'Porto Levante' => ['Veneto', 'RO'], 'Porto Tolle' => ['Veneto', 'RO'],
    'Porto Viro' => ['Veneto', 'RO'],

    // Friuli-Venezia Giulia
    'Trieste' => ['Friuli-Venezia Giulia', 'TS'],
    'Monfalcone' => ['Friuli-Venezia Giulia', 'GO'],
    'Grado' => ['Friuli-Venezia Giulia', 'GO'],
    'Lignano Sabbiadoro' => ['Friuli-Venezia Giulia', 'UD'],
    'Porto Lignano' => ['Friuli-Venezia Giulia', 'UD'],
    'Porto Nogaro' => ['Friuli-Venezia Giulia', 'UD'],
    'Marano Lagunare' => ['Friuli-Venezia Giulia', 'UD'],
    'Duino' => ['Friuli-Venezia Giulia', 'TS'],
    'Muggia' => ['Friuli-Venezia Giulia', 'TS'],

    // Lazio
    'Roma' => ['Lazio', 'RM'], 'Ostia' => ['Lazio', 'RM'],
    'Fiumicino' => ['Lazio', 'RM'], 'Civitavecchia' => ['Lazio', 'RM'],
    'Gaeta' => ['Lazio', 'LT'], 'Anzio' => ['Lazio', 'RM'],
    'Nettuno' => ['Lazio', 'RM'], 'Santa Marinella' => ['Lazio', 'RM'],
    'Formia' => ['Lazio', 'LT'], 'Terracina' => ['Lazio', 'LT'],
    'San Felice Circeo' => ['Lazio', 'LT'], 'Ponza' => ['Lazio', 'LT'],
    'Ventotene' => ['Lazio', 'LT'], 'Ladispoli' => ['Lazio', 'RM'],
    'Fregene' => ['Lazio', 'RM'], 'Torvaianica' => ['Lazio', 'RM'],
    'Sperlonga' => ['Lazio', 'LT'], 'Scauri' => ['Lazio', 'LT'],

    // Abruzzo
    'Pescara' => ['Abruzzo', 'PE'], 'Ortona' => ['Abruzzo', 'CH'],
    'Giulianova Lido' => ['Abruzzo', 'TE'], 'Vasto' => ['Abruzzo', 'CH'],
    'Francavilla al Mare' => ['Abruzzo', 'CH'], 'Roseto degli Abbruzzi' => ['Abruzzo', 'TE'],
    'Silvi Marina' => ['Abruzzo', 'TE'], 'Martinsicuro' => ['Abruzzo', 'TE'],
    'Tortoreto Lido' => ['Abruzzo', 'TE'],

    // Molise
    'Termoli' => ['Molise', 'CB'],

    // Campania
    'Napoli' => ['Campania', 'NA'], 'Capri' => ['Campania', 'NA'],
    'Amalfi' => ['Campania', 'SA'], 'Salerno' => ['Campania', 'SA'],
    'Sorrento' => ['Campania', 'NA'], 'Castellammare di Stabia' => ['Campania', 'NA'],
    'Pozzuoli' => ['Campania', 'NA'], 'Ischia' => ['Campania', 'NA'],
    'Portici' => ['Campania', 'NA'], 'Torre Annunziata' => ['Campania', 'NA'],
    'Torre del Greco' => ['Campania', 'NA'], 'Agropoli' => ['Campania', 'SA'],
    'Acciaroli' => ['Campania', 'SA'], 'Marina Grande' => ['Campania', 'NA'],
    'Marina della Lobra' => ['Campania', 'NA'], 'Cetara' => ['Campania', 'SA'],
    'Positano' => ['Campania', 'SA'], 'Piano di Sorrento' => ['Campania', 'NA'],
    'Maiori' => ['Campania', 'SA'], 'Palinuro' => ['Campania', 'SA'],
    'Sapri' => ['Campania', 'SA'], 'Scario' => ['Campania', 'SA'],
    'Marina di Camerota' => ['Campania', 'SA'], 'Marina di Pisciotta' => ['Campania', 'SA'],
    'Santa Maria di Castellabate' => ['Campania', 'SA'],
    'Castel Volturno' => ['Campania', 'CE'], 'Mondragone' => ['Campania', 'CE'],
    'Bagnoli' => ['Campania', 'NA'], 'Baia' => ['Campania', 'NA'],
    'Casamicciola' => ['Campania', 'NA'], 'Forio' => ['Campania', 'NA'],
    'Lacco Ameno' => ['Campania', 'NA'], 'Procida' => ['Campania', 'NA'],
    'Torre Gaveta' => ['Campania', 'NA'],
    'Castellammare del Golfo' => ['Sicilia', 'TP'],

    // Puglia
    'Bari' => ['Puglia', 'BA'], 'Brindisi' => ['Puglia', 'BR'],
    'Gallipoli' => ['Puglia', 'LE'], 'Vieste' => ['Puglia', 'FG'],
    'Otranto' => ['Puglia', 'LE'], 'Taranto' => ['Puglia', 'TA'],
    'Manfredonia' => ['Puglia', 'FG'], 'Monopoli' => ['Puglia', 'BA'],
    'Barletta' => ['Puglia', 'BT'], 'Molfetta' => ['Puglia', 'BA'],
    'Trani' => ['Puglia', 'BT'], 'Bisceglie' => ['Puglia', 'BT'],
    'Giovinazzo' => ['Puglia', 'BA'], 'Mola di Bari' => ['Puglia', 'BA'],
    'Polignano a Mare' => ['Puglia', 'BA'], 'Peschici' => ['Puglia', 'FG'],
    'Rodi Garganico' => ['Puglia', 'FG'], 'Castro Marina' => ['Puglia', 'LE'],
    'Leuca' => ['Puglia', 'LE'], 'San Cataldo' => ['Puglia', 'LE'],
    'Santa Foca di Melendugno' => ['Puglia', 'LE'],
    'Torre Cesarea' => ['Puglia', 'LE'], 'Torre San Giovanni' => ['Puglia', 'LE'],
    'Savelletri' => ['Puglia', 'BR'], 'Margherita di Savoia' => ['Puglia', 'BT'],
    'Foce Varano' => ['Puglia', 'FG'], 'Lesina' => ['Puglia', 'FG'],

    // Calabria
    'Tropea' => ['Calabria', 'VV'], 'Crotone' => ['Calabria', 'KR'],
    'Reggio Calabria' => ['Calabria', 'RC'], 'Gioia Tauro' => ['Calabria', 'RC'],
    'Vibo Valentia' => ['Calabria', 'VV'], 'Corigliano Calabro' => ['Calabria', 'CS'],
    'Bagnara Calabra' => ['Calabria', 'RC'], 'Amantea' => ['Calabria', 'CS'],
    'Bianco' => ['Calabria', 'RC'], 'Bova Marina' => ['Calabria', 'RC'],
    'Bovalino Marina' => ['Calabria', 'RC'], 'Cariati' => ['Calabria', 'CS'],
    'Cetraro' => ['Calabria', 'CS'], 'Ciro Marina' => ['Calabria', 'KR'],
    'Diamante' => ['Calabria', 'CS'], 'Marina di Monasterace' => ['Calabria', 'RC'],
    'Paola' => ['Calabria', 'CS'], 'Pizzo' => ['Calabria', 'VV'],
    'Praia a Mare' => ['Calabria', 'CS'], 'Roccella Ionica' => ['Calabria', 'RC'],
    'Scalea' => ['Calabria', 'CS'], 'Scilla' => ['Calabria', 'RC'],
    'Siderno Marina' => ['Calabria', 'RC'], 'Soverato' => ['Calabria', 'CZ'],
    'Trebisacce' => ['Calabria', 'CS'], 'Villa San Giovanni' => ['Calabria', 'RC'],
    'Belvedere Marittimo' => ['Calabria', 'CS'], 'Copanello' => ['Calabria', 'CZ'],
    'Marina di Davoli' => ['Calabria', 'CZ'], 'Melito di Porto Salvo' => ['Calabria', 'RC'],

    // Basilicata
    'Policoro' => ['Basilicata', 'MT'], 'Maratea' => ['Basilicata', 'PZ'],
    'Nova Siri' => ['Basilicata', 'MT'], 'Marina di Ginosa' => ['Basilicata', 'TA'],

    // Sicilia
    'Palermo' => ['Sicilia', 'PA'], 'Catania' => ['Sicilia', 'CT'],
    'Siracusa' => ['Sicilia', 'SR'], 'Trapani' => ['Sicilia', 'TP'],
    'Messina' => ['Sicilia', 'ME'], 'Milazzo' => ['Sicilia', 'ME'],
    'Giardini' => ['Sicilia', 'ME'], 'Augusta' => ['Sicilia', 'SR'],
    'Gela' => ['Sicilia', 'CL'], 'Porto Empedocle' => ['Sicilia', 'AG'],
    'Marsala' => ['Sicilia', 'TP'], 'Mazara del Vallo' => ['Sicilia', 'TP'],
    'Licata' => ['Sicilia', 'AG'], 'Riposto' => ['Sicilia', 'CT'],
    'Termini Imerese' => ['Sicilia', 'PA'], 'Pozzallo' => ['Sicilia', 'RG'],
    'Sciacca' => ['Sicilia', 'AG'], 'Favignana' => ['Sicilia', 'TP'],
    'Lipari' => ['Sicilia', 'ME'], 'Pantelleria' => ['Sicilia', 'TP'],
    'Ustica' => ['Sicilia', 'PA'], 'Lampedusa' => ['Sicilia', 'AG'],
    'Aci Castello' => ['Sicilia', 'CT'], 'Acitrezza' => ['Sicilia', 'CT'],
    'Capo d\'Orlando' => ['Sicilia', 'ME'], 'Cefalu' => ['Sicilia', 'PA'],
    'Isola delle Femmine' => ['Sicilia', 'PA'], 'Levanzo' => ['Sicilia', 'TP'],
    'Linosa' => ['Sicilia', 'AG'], 'Marettimo' => ['Sicilia', 'TP'],
    'Mondello' => ['Sicilia', 'PA'], 'Noto' => ['Sicilia', 'SR'],
    'Ognina' => ['Sicilia', 'SR'], 'Panarea' => ['Sicilia', 'ME'],
    'Porto Palo' => ['Sicilia', 'AG'], 'Portopalo' => ['Sicilia', 'SR'],
    'Pozzillo' => ['Sicilia', 'CT'], 'San Vito lo Capo' => ['Sicilia', 'TP'],
    'Sant\'Agata di Militello' => ['Sicilia', 'ME'],
    'Santa Marina Salina' => ['Sicilia', 'ME'], 'Santa Panagia' => ['Sicilia', 'SR'],
    'Santo Stefano di Camastra' => ['Sicilia', 'ME'],
    'Scoglitti' => ['Sicilia', 'RG'], 'Sferracavallo' => ['Sicilia', 'PA'],
    'Stromboli' => ['Sicilia', 'ME'], 'Terrasini' => ['Sicilia', 'PA'],
    'Torre Faro' => ['Sicilia', 'ME'], 'Vulcano Porto' => ['Sicilia', 'ME'],
    'Alicudi' => ['Sicilia', 'ME'], 'Bonagia' => ['Sicilia', 'TP'],
    'Filicudi Porto' => ['Sicilia', 'ME'], 'Marinella di Selinunte' => ['Sicilia', 'TP'],
    'Porticello' => ['Sicilia', 'PA'], 'Rinella' => ['Sicilia', 'ME'],
    'Saline' => ['Sicilia', 'ME'], 'Tremestieri' => ['Sicilia', 'ME'],
    'Isolotto Formica' => ['Sicilia', 'TP'],
    'Mili Marina' => ['Sicilia', 'ME'],

    // Sardegna
    'Cagliari' => ['Sardegna', 'CA'], 'Olbia' => ['Sardegna', 'SS'],
    'Porto Cervo' => ['Sardegna', 'SS'], 'Alghero' => ['Sardegna', 'SS'],
    'Porto Torres' => ['Sardegna', 'SS'], 'Arbatax' => ['Sardegna', 'NU'],
    'La Maddalena' => ['Sardegna', 'SS'], 'Calasetta' => ['Sardegna', 'SU'],
    'Carloforte' => ['Sardegna', 'SU'], 'Golfo Aranci' => ['Sardegna', 'SS'],
    'Oristano' => ['Sardegna', 'OR'], 'Palau' => ['Sardegna', 'SS'],
    'Santa Teresa di Gallura' => ['Sardegna', 'SS'],
    'Porto Conte' => ['Sardegna', 'SS'], 'Bosa' => ['Sardegna', 'OR'],
    'Cannigione' => ['Sardegna', 'SS'], 'Castelsardo' => ['Sardegna', 'SS'],
    'Stintino' => ['Sardegna', 'SS'], 'Cala Gonone' => ['Sardegna', 'NU'],
    'Cape Carbonara' => ['Sardegna', 'SU'],
    'Isola Asinara' => ['Sardegna', 'SS'], 'Portoscuso (Porto Vesme)' => ['Sardegna', 'SU'],
    'Santa Maria Navarrese' => ['Sardegna', 'NU'],
    'Sant\' Antioco' => ['Sardegna', 'SU'], 'Sarroch (Porto Foxi)' => ['Sardegna', 'CA'],
    'Torregrande' => ['Sardegna', 'OR'],

    // Marche
    'Ancona' => ['Marche', 'AN'], 'San Benedetto del Tronto' => ['Marche', 'AP'],
    'Pesaro' => ['Marche', 'PU'], 'Fano' => ['Marche', 'PU'],
    'Civitanova Marche' => ['Marche', 'MC'], 'Porto Recanati' => ['Marche', 'MC'],
    'Porto San Giorgio' => ['Marche', 'FM'], 'Numana' => ['Marche', 'AN'],
    'Senigallia' => ['Marche', 'AN'], 'Cupra Marittima' => ['Marche', 'AP'],
    'Falconara' => ['Marche', 'AN'], 'Pedaso' => ['Marche', 'FM'],
    'Marotta' => ['Marche', 'PU'],

    // Case variants from CSV
    'Roseto Degli Abbruzzi' => ['Abruzzo', 'TE'],
    'San Benedetto Del Tronto' => ['Marche', 'AP'],
    'Sant\'agata Di Militello' => ['Sicilia', 'ME'],
    'Capo D\'orlando' => ['Sicilia', 'ME'],

    // Additional coastal unmapped
    'Priolo Gargallo' => ['Sicilia', 'SR'],
    'Patti' => ['Sicilia', 'ME'],
    'Palmi' => ['Calabria', 'RC'],
    'Massa' => ['Toscana', 'MS'],
    'Tremiti' => ['Puglia', 'FG'],
    'San Pietro' => ['Sardegna', 'SU'],
    'Zaule' => ['Friuli-Venezia Giulia', 'TS'],
    'Punta Cugno' => ['Sardegna', 'SU'],
    'Pier Isab' => ['Sicilia', 'SR'],
    'Isola Santo Stefano' => ['Lazio', 'LT'],
    'Sammichele' => ['Puglia', 'BA'],
    'Avola' => ['Sicilia', 'SR'],
];

// Parse CSV
$csv = file_get_contents(__DIR__ . '/../storage/ports_italy_mit.csv');
$lines = explode("\n", $csv);
$header = str_getcsv(array_shift($lines));

$ports = [];
$unmapped = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    $row = str_getcsv($line);
    if (count($row) < 11) continue;

    $data = array_combine($header, $row);
    $name = $data['nome'];
    $locality = $data['localita'];
    $lat = !empty($data['lat']) ? (float)$data['lat'] : null;
    $lon = !empty($data['lon']) ? (float)$data['lon'] : null;

    // Try to find region mapping
    $regionInfo = $localityMap[$locality] ?? $localityMap[$name] ?? null;

    if (!$regionInfo) {
        $unmapped[] = $name . ' (' . $locality . ')';
        continue;
    }

    // Skip inland/non-coastal ports (no coordinates and not recognizable)
    if (!$lat && !$lon) {
        // We'll still include them, just without coords
    }

    $ports[] = [
        'name' => $name,
        'city' => $locality,
        'province' => $regionInfo[1],
        'region' => $regionInfo[0],
        'latitude' => $lat,
        'longitude' => $lon,
        'locode' => $data['locode'],
    ];
}

echo "Mapped: " . count($ports) . " ports\n";
echo "Unmapped: " . count($unmapped) . " ports\n";
if ($unmapped) {
    echo "\nUnmapped localities:\n";
    foreach ($unmapped as $u) {
        echo "  - $u\n";
    }
}

// Output as JSON for further processing
file_put_contents(__DIR__ . '/../storage/ports_italy_mapped.json', json_encode($ports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved to storage/ports_italy_mapped.json\n";
