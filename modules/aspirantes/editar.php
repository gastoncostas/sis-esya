<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';
$cursante = null;

// Definir todas las localidades según la estructura de la base de datos
$localidades = [
    '24 DE SETIEMBRE',
    '25 DE MAYO',
    '7 DE ABRIL',
    '9 DE JULIO',
    'ABRA DE/L TAFÍ',
    'ABRA DEL INFIERNILLO',
    'ABRA RICA',
    'ACEQUIONES',
    'ACHERAL',
    'ACOSTILLA/S',
    'AGUA AZUL',
    'AGUA BLANCA',
    'AGUA BLANCA',
    'AGUA CHIQUITA',
    'AGUA COLORADA',
    'AGUA DE LA PEÑA',
    'AGUA DULCE',
    'AGUA NEGRA',
    'AGUA SALADA',
    'AGUA SALADA',
    'AGUADITA',
    'AGUILARES',
    'AGUIRRES',
    'ALABAMA',
    'ALARCONES',
    'ALBERDI',
    'ALDERETE',
    'ALDERETES',
    'ALISOS',
    'ALIZAL',
    'ALPACHIRI',
    'ALTA GRACIA',
    'ALTO DE LAS LECHUZAS',
    'ALTO DE LEIVA',
    'ALTO DE MEDINA',
    'ALTO EL PUESTO',
    'ALTO LAS FLORES',
    'ALTO LOS CARDONES',
    'ALTO VERDE',
    'AMAICHA',
    'AMAICHA DEL LLANO',
    'AMAICHA DEL VALLE',
    'AMBERES',
    'AMPATA',
    'AMPATILLA',
    'AMPIMPA',
    'ANCA JULI',
    'ANCHILLOS',
    'ANFAMA',
    'ANJUANA',
    'ANTA',
    'ANTA CHICA',
    'ANTA MUERTA',
    'ANTIGUO QUILMES',
    'ANTIGUO TUCUMAN',
    'ANTILLAS',
    'KM 1185',
    'KM 1256',
    'KM 825',
    'MIL. GRAL. MUÑOZ',
    'SAN MIGUEL',
    'ARANILLA',
    'ÁRBOL SOLO',
    'ÁRBOLES GRANDES',
    'ARCADIA',
    'AROCAS',
    'ASNA YACO',
    'ATAHONA',
    'BAJASTINÉ',
    'BAJÓ GRANDE',
    'BAJÓ GRANDE',
    'BAJÓ GRANDE',
    'BALDERRAMA',
    'BANDA DEL RÍO SALÍ',
    'BAÑADO',
    'BARBURÍN',
    'BARRANCAS',
    'BARRANCAS COLORADAS',
    'BARRANQUERO',
    'BATIRUANA',
    'BAZÁN',
    'BELLA VISTA',
    'BENJAMÍN ARÁOZ',
    'BENJAMÍN PAZ',
    'BLANCO POZO',
    'BOBA YACO',
    'BOCA DE LA QUEBRADA',
    'BOCA DEL TIGRE',
    'BODEGA REY',
    'BOBARÍA',
    'BOQUERÓN',
    'BUENA VISTA',
    'BUENA VISTA',
    'BURRUYACÚ',
    'CACHI HUASI',
    'CACHI YACO',
    'CACHI YACO',
    'CADILLAR',
    'CAJÓN',
    'CALANCHA',
    'CALIMONTE',
    'CAMAS AMONTONADAS',
    'CAMPO ALEGRE',
    'CAMPO AZUL',
    'CAMPO BELLO',
    'CAMPO DE LA ZANJA',
    'CAMPO EL SOLCO',
    'CAMPO GRANDE',
    'CAMPO GRANDE',
    'CAMPO HERRERA',
    'CAMPO LA FLOR',
    'CAMPO REDONDO',
    'CAMPO VOLANTE',
    'CAÑADA ANGOSTA',
    'CAÑADA DE ALSOGARAY',
    'CAÑADA DE VICLOS',
    'CAÑADA DEL ARENAL',
    'CAÑADA HONDA',
    'CAÑAS',
    'CAÑETE',
    'CAPELLANÍA',
    'CAPITÁN CÁCERES',
    'CARA HUASI',
    'CARANCHO POZO',
    'CARAPUNCO',
    'CARBÓN POZO',
    'CARPINCHO',
    'CARRETA QUEMADA',
    'CARRIZAL',
    'CASA DE PIEDRA',
    'CASA DE PIEDRA',
    'CASA DE TABLAS',
    'CASA DE/L CAMPO',
    'CASA DEL ALTO',
    'CASALES',
    'CASAS VIEJAS',
    'CASAS VIEJAS',
    'CASIYACO',
    'CASPINCHANGO',
    'CASTILLA/S',
    'CEVIL CON AGUA',
    'CEVIL GRANDE',
    'CEVIL POZO',
    'CEVIL REDONDO',
    'CEVIL SOLO',
    'CEVILARCITO',
    'CHACRAS',
    'CHAMICO/S',
    'CHAÑAR MUYO/A',
    'CHAÑAR POZO',
    'CHAÑAR SOLO',
    'CHAÑARCITO',
    'CHAÑARITO',
    'CHAÑARITO',
    'CHASQUIVIL',
    'CHAVARRÍA',
    'CHILCA/S',
    'CHOROMORO',
    'CHORRILLOS',
    'CHULCA',
    'CHURQUI',
    'CHURQUI',
    'CHUSCHA',
    'CIÉNAGAS',
    'CIUDACITA',
    'COLONIA NUEVA TRINIDAD',
    'COLONIA SANTA MARINA',
    'COLONIA VIRGINIA',
    'COCHUNA',
    'COCO',
    'COLALAO DEL VALLE',
    'COLOMBRES',
    'CONCEPCIÓN',
    'CÓNDOR HUASI',
    'COROMAMA',
    'COROMAMITA',
    'CORRAL VIEJO',
    'CORRILLO GRANDE',
    'COSSIO',
    'CAMPO DE TALAMAYO',
    'CRUZ ALTA',
    'CRUZ DEL NORTE',
    'CUESTA DE LA CHILCA',
    'DELFÍN GALLO',
    'DESMONTE',
    'DIQUE EL SALTÓN',
    'DONATO ALVAREZ',
    'DOS POZOS',
    'DURAZNITO',
    'DURAZNO/S BLANCO/S',
    'EL ALTO DE LA COCHA',
    'EL ALTO DEL TÍO',
    'EL ARBOLAR',
    'EL ARENAL',
    'EL ASERRADERO',
    'EL ASERRADERO',
    'EL ATACAL',
    'EL ATACAL',
    'EL AZUL',
    'EL BACHI',
    'EL BAJÓ',
    'EL BAÑADO DE QUILMES',
    'EL BARCO',
    'EL BARRIALITO',
    'EL BOLSÓN',
    'EL BOYERO',
    'EL BRACHO',
    'EL BRETE',
    'EL CABAO',
    'EL CADILLAL',
    'EL CAJÓN',
    'EL CARMEN',
    'EL CARMEN',
    'EL CASIALITO',
    'EL CEIBAL',
    'EL CERCADO',
    'EL CEVILAR',
    'EL CHAÑAR',
    'EL CHICAL',
    'EL CHILCAR',
    'EL CHURQUI',
    'EL CHURQUI',
    'EL CORRALITO',
    'EL CORTADERAL',
    'EL DIAMANTE',
    'EL DIVISADERO',
    'EL DURAZNITO',
    'EL DURAZNO',
    'EL ESCUDO',
    'EL ESPINILLO',
    'EL ESTANQUE',
    'EL FRASQUILLO',
    'EL GUARDAMONTE',
    'EL GUAYACAN',
    'EL HUAICO',
    'EL INDIO',
    'EL JARDÍN',
    'EL JARDÍN',
    'EL LINDERO',
    'EL MANANTIAL',
    'EL MARAIS',
    'EL MATAL',
    'EL MELÓN',
    'EL MISTÓL',
    'EL MISTÓL',
    'EL MOJÓN',
    'EL MOJÓN',
    'EL MOLINO',
    'EL MOLINO',
    'EL MOLLAR',
    'EL MOLLAR',
    'EL MOLLAR',
    'EL MOLLE',
    'EL MORADO',
    'EL NARANJAL',
    'EL NARANJITO',
    'EL NARANJO',
    'EL NÍO',
    'EL NOGAL',
    'EL NOGALAR',
    'EL NOGALITO',
    'EL NOGALITO',
    'EL OBRAJE',
    'EL OJO',
    'EL ONCE',
    'EL PACARÁ',
    'EL PAJAL',
    'EL PALANCHO',
    'EL PARAÍSO',
    'EL PARAÍSO',
    'EL PARAÍSO',
    'EL PASO',
    'EL PICHAO',
    'EL PINGOLLAR',
    'EL POLEAR',
    'EL PORTEZUELO',
    'EL PORVENIR',
    'EL PORVENIR',
    'EL POTRERILLO',
    'EL POTRERILLO',
    'EL PUESTO DEL MEDIO',
    'EL PUEBLO VIEJO',
    'EL PUESTITO',
    'EL PUESTITO DE ARRIBA',
    'EL PUESTITO DEL MEDIO',
    'EL PUESTO',
    'EL QUEBRACHITO',
    'EL REMATE',
    'EL REMATE',
    'EL REMATE',
    'EL RETIRO',
    'EL RETIRO',
    'EL RINCÓN',
    'EL RINCÓN',
    'EL RÍO',
    'EL RODEO',
    'EL RODEO',
    'EL RODEO',
    'EL RODEO',
    'EL ROSARIO',
    'EL SACRIFICIO',
    'EL SESTEADERO',
    'EL SIAMBÓN',
    'EL SIMBOL',
    'EL SOLCO',
    'EL SUNCHAL',
    'EL SUNCHO',
    'EL SUNCHO',
    'EL TAJAMAR',
    'EL TALAR',
    'EL TALAR',
    'EL TIMBÓ',
    'EL TÍO',
    'EL TIPAL',
    'EL TOBAR',
    'EL TRIUNFO',
    'EL VALLECITO',
    'EL VISCACHERAL',
    'EMPALME AGUA DULCE',
    'ESCABA',
    'ESCABA DE ABAJO',
    'ESCABA DE ARRIBA',
    'ESPERANZA',
    'ESPERANZA',
    'ESPINAL',
    'ESQUINA',
    'ESQUINA',
    'ESQUINA',
    'ESQUINA GRANDE',
    'ESTACIÓN FCGM LUISIANA',
    'ESTACIÓN SANTA ROSA',
    'ESTACIÓN ARÁOZ',
    'ESTANCIA BARROSA',
    'ESTANCIA INGAS',
    'ESTANCIA LA PRINCESA',
    'ESTANCIA SURI YACO',
    'ESTANCIA LA ARGENTINA',
    'ESTANCIA MONTECRISTO',
    'ESTANCIA TACO RALO',
    'FAMAILLÁ',
    'FAVORINA',
    'FIN DEL MUNDO',
    'FINCA ELISA',
    'FINCA LA QUERIDA',
    'FINCA MAYO',
    'FINCA PACARÁ',
    'FINCA PIEDRABUENA',
    'FUERTE QUEMADO',
    'GARMENDIA',
    'GASTONA',
    'GASTONA NORTE',
    'GASTONILLA',
    'GEREZ',
    'GOB. PIEDRABUENA',
    'GONZALO',
    'GRAMAJO',
    'GRAMAJOS',
    'GRAME',
    'GRANEROS',
    'GUARDIA',
    'GUAYACONES',
    'GÜEMES',
    'GUZMÁN',
    'HORCO MOLLE',
    'HORNITOS',
    'HUACRA',
    'HUALINCHAY',
    'HUASA PAMPA',
    'HUASA PAMPA',
    'HUASA PAMPA NORTE',
    'HUASA PAMPA SUD',
    'HUASAMAYO',
    'ICHIPUCA',
    'INDIA MUERTA',
    'ING. LA FLORIDA',
    'ING. LA FRONTERITA',
    'ING. LA TRINIDAD',
    'ING. SAN PABLO',
    'ING. SANTA ANA',
    'ING. SANTA BÁRBARA',
    'ING. SANTA LUCÍA',
    'ING. SANTA ROSA',
    'INGAS',
    'ISCHILLÓN',
    'ISLA DE SAN JOSÉ',
    'ITILCO',
    'J.B. ALBERDI',
    'JANIMAS',
    'JULIÁN YACO',
    'JUSCA POZO',
    'KM 102',
    'KM 1160',
    'KM 1194',
    'KM 1213',
    'KM 1238',
    'KM 1248',
    'KM 1340',
    'KM 37',
    'KM 46',
    'KM 55',
    'KM 66',
    'KM 771',
    'KM 794',
    'LA AGUADA',
    'LA AGÜITA',
    'LA ANGOSTURA',
    'LA BAJADA',
    'LA BOLSA',
    'LA BOLSA',
    'LA BREA',
    'LA CABAÑA',
    'LA CALERA',
    'LA CAÑADA',
    'LA CAÑADA',
    'LA CAÑADA',
    'LA CAÑADA',
    'LA CARPINTERÍA',
    'LA CÁSCARA',
    'LA CHILCA',
    'LA CIÉNAGA',
    'LA COCHA',
    'LA CRUZ',
    'LA CRUZ DE ARRIBA',
    'LA ENCRUCIJADA',
    'LA ENCRUCIJADA',
    'LA ESPERANZA',
    'LA FALDA',
    'LA FLORIDA',
    'LA FLORIDA',
    'LA FLORIDA',
    'LA FLORIDA',
    'LA FLORIDA',
    'LA GUILLERMINA',
    'LA HELADERA',
    'LA HIGUERA',
    'LA HIGUERA',
    'LA HIGUERITA',
    'LA HORQUETA',
    'LA HOYADA',
    'LA HOYADA',
    'LA IGUANA',
    'LA INVERNADA',
    'LA JAYA',
    'LA JUNTA',
    'LA LAGUNA',
    'LA LAGUNITA',
    'LA LOMA',
    'LA MADRID',
    'LA MARAVILLA',
    'LA NORIA',
    'LA OVEJERÍA',
    'LA POLA',
    'LA POSTA',
    'LA PUERTA',
    'LA QUINTA',
    'LA RAMADA',
    'LA RAMADA DE ABAJO',
    'LA RAMADITA',
    'LA REDUCCIÓN',
    'LA REINA',
    'LA RINCONADA',
    'LA RINCONADA',
    'LA SALA',
    'LA SALA',
    'LA SALAMANCA',
    'LA SOLEDAD',
    'LA SOLEDAD',
    'LA SOLEDAD',
    'LA TABLADA',
    'LA TALA',
    'LA TIPA',
    'LA TOMA',
    'LA TOTORILLA',
    'LA TRANQUERA',
    'LA TRINIDAD',
    'LA TUNA',
    'LA TUNA',
    'LA UNIÓN',
    'LA VIÑA',
    'LA VIRGINIA',
    'LA ZANJA',
    'LA ZANJA',
    'LA/S BANDERITA',
    'LA/S CEJA/S',
    'LA/S ZANJA/S',
    'LACHICO',
    'LAGUNA DE LOS AMAICHEÑOS',
    'LAGUNA BLANCA',
    'LAGUNA DE ROBLES',
    'LAGUNA DEL CARGADERO',
    'LAGUNA DEL TESORO',
    'LAGUNA GRANDE',
    'LAGUNA LARGA',
    'LAGUNAS DE VACA HUASI',
    'LAGUNITA',
    'LAGUNITA',
    'LAMPACITO',
    'LAPACHITOS',
    'LARA',
    'LAS ÁNIMAS',
    'LAS ÁNIMAS',
    'LAS ARCAS',
    'LAS AZUCENAS',
    'LAS CAÑADAS',
    'LAS CARRERAS',
    'LAS CEJAS',
    'LAS CEJAS',
    'LAS COLONIAS',
    'LAS CORTADERAS',
    'LAS CRIOLLAS',
    'LAS CUCHILLAS',
    'LAS CUEVAS',
    'LAS ENCRUCIJADA',
    'LAS HIGUERILLAS',
    'LAS HIGUERITAS',
    'LAS HIGUERITAS',
    'LAS HUASCHAS',
    'LAS JUNTA',
    'LAS JUNTAS',
    'LAS JUNTAS',
    'LAS JUNTAS',
    'LAS LAJITAS',
    'LAS LENGUAS',
    'LAS LOMITAS',
    'LAS MERCEDES',
    'LAS MESADAS',
    'LAS MESADAS',
    'LAS MORERAS',
    'LAS PALMITAS',
    'LAS PAMPITAS',
    'LAS PAVAS',
    'LAS PIEDRITAS',
    'LAS TACANAS',
    'LAS TACANAS',
    'LAS TALAS',
    'LAS TALITAS',
    'LAS TALITAS',
    'LAS TIPAS',
    'LAS TIPAS',
    'LAS TUSCAS',
    'LAS ZANJITAS',
    'LASTENIA',
    'LAUREL YACO',
    'LAURELES',
    'LAZARTE',
    'LEALES',
    'LEOCADIO PAZ',
    'LEÓN ROUGÉS',
    'LIMPIOS',
    'LLONA',
    'LOLITA',
    'LOMA DEL MEDIO',
    'LOMA GRANDE',
    'LOMA VERDE',
    'LOS ACOSTAS',
    'LOS AGUDO',
    'LOS AGUDOS',
    'LOS AGUIRRE',
    'LOS AGUIRRES',
    'LOS ALGARROBILLOS',
    'LOS ALISOS',
    'LOS ANEGADOS',
    'LOS ARRIETA',
    'LOS BAJOS',
    'LOS BRITO',
    'LOS BRITOS',
    'LOS BULACIO',
    'LOS CAMPERO',
    'LOS CERCOS',
    'LOS CHAÑARES',
    'LOS CHAÑARITOS',
    'LOS CHORRILLOS',
    'LOS CHURQUIS',
    'LOS COCHAMOLLES',
    'LOS CÓRDOBA',
    'LOS CORPITOS',
    'LOS COSTILLA',
    'LOS CUARTOS',
    'LOS DÍAZ',
    'LOS GÓMEZ',
    'LOS GÓMEZ CHICO',
    'LOS GRAMAJO/S',
    'LOS GUCHAS',
    'LOS GUTIERREZ',
    'LOS HARDOY',
    'LOS HERRERAS',
    'LOS JUÁREZ',
    'LOS LUNAREJOS',
    'LOS MEDINAS',
    'LOS MENDOZA',
    'LOS MOLLES',
    'LOS NOGALES',
    'LOS PARAÍSOS',
    'LOS PEREYRA',
    'LOS PÉREZ',
    'LOS PÉREZ',
    'LOS PINOS',
    'LOS PIZARRO/S',
    'LOS POCITOS',
    'LOS POCITOS',
    'LOS PORCELES',
    'LOS PUESTOS',
    'LOS QUEMADOS',
    'LOS RALOS',
    'LOS ROBLES',
    'LOS ROMANO',
    'LOS RUIZ',
    'LOS SANDOVALES',
    'LOS SARMIENTOS',
    'LOS SAUCES',
    'LOS SAUCES',
    'LOS SAUCES',
    'LOS SORAIRE',
    'LOS SOSA',
    'LOS SUELDOS',
    'LOS TREJOS',
    'LOS VALLISTOS',
    'LOS VÁZQUEZ',
    'LOS VILLAGRA',
    'LOS ZAZOS',
    'LOS ZELAYA',
    'LOS ZURITA',
    'LOS ZURITA',
    'LOS/AS VEGAS',
    'LOVAR',
    'LUISIANA',
    'LUJÁN',
    'LULES',
    'MACHO HUAÑUSCA',
    'MACIO',
    'MACOMITA/S',
    'MALVINAS',
    'MANANTIAL DE OVANTA',
    'MANANTIALES',
    'MANCHALÁ',
    'MANCOPA',
    'MANUEL GARCÍA FERNÁNDEZ',
    'MANUELA PEDRAZA',
    'MARAPA',
    'MARCOS PAZ',
    'MARIÑO',
    'MARTA',
    'MATO YACO',
    'MELCHO',
    'MEMBRILLO',
    'MERCEDES',
    'MIGUEL LILLO',
    'MIMILTO',
    'MISTÓL ESQUINA',
    'MISTÓL GRANDE',
    'MIXTA',
    'MOLINO VIEJO',
    'MOLLE YACO',
    'MONTE BELLO',
    'MONTE BELLO',
    'MONTE GRANDE',
    'MONTE GRANDE',
    'MONTE POZO',
    'MONTE REDONDO',
    'MONTE REDONDO',
    'MONTE REDONDO',
    'MONTEAGUDO',
    'MONTEROS',
    'MONTEROS VIEJO',
    'MORÓN',
    'MUJER MUERTA',
    'NARANJO ESQUINA',
    'NIOGASTA',
    'NUEVA BABIERA',
    'NUEVA ESPAÑA',
    'NUEVA TRINIDAD',
    'ÑORCO',
    'OJO',
    'ORÁN',
    'ORÁN',
    'OVEJERÍA',
    'OVERO POZO',
    'PACARÁ',
    'PACARÁ',
    'PADILLA',
    'PADRE MONTI',
    'PÁEZ',
    'PÁEZ',
    'PAJA BLANCA',
    'PAJA COLORADA',
    'PALÁ PALÁ',
    'PALAMPA',
    'PALOMA/S',
    'PALOMINOS',
    'PALOMITAS',
    'PALOMITAS',
    'PAMPA',
    'PAMPA MAYO',
    'PAMPA MUYO',
    'PAPEL DEL TUCUMÁN',
    'PARADA CHAVELA',
    'PARADA SUELDOS',
    'PASO DE LAS LANZAS',
    'PEDRO G. MÉNDEZ',
    'PEÑAS AZULES',
    'PEÑAS BLANCAS',
    'PEÑAS MOCHAS',
    'PEREYRA SUR',
    'PÉREZ',
    'PERUCHO',
    'PIEDRA TENDIDA',
    'PIEDRAS BLANCAS',
    'PIEDRAS COLORADAS',
    'PILCO',
    'PIRUAL',
    'PLAYA LARGA',
    'POLITO',
    'PONZACÓN',
    'PÓRTICO QUEBRADO',
    'POSTA',
    'POTRERILLO',
    'POTRERILLO',
    'POTRERO DE LAS TABLAS',
    'POTRERO RODEO GRANDE',
    'POZO CABADO',
    'POZO CAVADO',
    'POZO DEL ALGARROBO',
    'POZO EL MISTÓL',
    'POZO GRANDE',
    'POZO HONDO',
    'POZO LARGO',
    'POZO VERDE',
    'POTRERO DE LAS CABRERAS',
    'PUESTO CIÉNAGA AMARILLA',
    'PUESTO CORRAL DE BARRANCAS',
    'PUERTA ALEGRE',
    'PUERTA DE JULIPAO',
    'PUERTA DE MARAPA',
    'PUERTA DE PALAVECINO',
    'PUERTA DE SAN JAVIER',
    'PUERTA QUEMADA',
    'PUERTA VIEJA',
    'PUERTAS',
    'PUESTO CHICO',
    'PUESTO DE CHANCHO',
    'PUESTO DE DÍAZ',
    'PUESTO DE ENCALILLO',
    'PUESTO DE JULIPAO',
    'PUESTO DE UNCOS',
    'PUESTO EL ZARZO',
    'PUESTO LA QUÉNOA',
    'PUESTO LA RAMADITA',
    'PUESTO LLAMPA',
    'PUESTO LOS ROBLES',
    'PUESTO MEDINA',
    'PUESTO VIEJO',
    'PUMA POZO',
    'PUNTA CARRERA',
    'PUNTA DEL PAGO',
    'PUNTA DEL PINO',
    'PUNTA LA CUMBRE',
    'PUNTA LLAMPA',
    'QUILMES',
    'QUISCA',
    'RACO',
    'RAMOS',
    'RANCHILLOS',
    'RANCHILLOS VIEJOS',
    'RANCHO DE LA CASCADA',
    'REARTES',
    'REQUELME',
    'RETIRO',
    'RETIRO',
    'RINCÓN DE BALDERRAMA',
    'RINCÓN DE LAS TACANAS',
    'RINCÓN DE QUILMES',
    'RÍO BLANCO',
    'RÍO CHICO',
    'RÍO COLORADO',
    'RÍO NÍO',
    'RÍO SECO',
    'RÍO SECO KM 1207',
    'RODEO',
    'RODEO DE ANTA',
    'RODEO DEL ALGARROBO',
    'RODEO GRANDE',
    'RODEO GRANDE',
    'ROMERA POZO',
    'ROMERILLO',
    'RUINAS OF QUILMES',
    'RUMI PUNCO',
    'RUMI YURA',
    'SALA VIEJA',
    'SALADILLO',
    'SALADILLO',
    'SALAMANCA',
    'SALINAS',
    'SAN AGUSTÍN',
    'SAN ANDRÉS',
    'SAN ANDRÉS',
    'SAN ANTONIO',
    'SAN ANTONIO',
    'SAN ANTONIO',
    'SAN ANTONIO DE PADUA',
    'SAN ARTURO',
    'SAN CARLOS',
    'SAN CARLOS',
    'SAN CARLOS',
    'SAN EUSEBIO',
    'SAN FELIPE',
    'SAN FRANCISCO',
    'SAN GABRIEL DEL MONTE',
    'SAN IGNACIO',
    'SAN IGNACIO',
    'SAN ISIDRO',
    'SAN ISIDRO DE LULES',
    'SAN JAVIER',
    'SAN JOSÉ',
    'SAN JOSÉ',
    'SAN JOSÉ',
    'SAN JOSÉ',
    'SAN JOSÉ',
    'SAN JOSÉ DE BUENA VISTA',
    'SAN JOSÉ DE FLORES',
    'SAN JOSÉ DE LULES',
    'SAN JUAN',
    'SAN LORENZO',
    'SAN MIGUEL',
    'SAN MIGUEL DE TUCUMÁN',
    'SAN NICOLÁS',
    'SAN PATRICIO',
    'SAN PEDRO',
    'SAN PEDRO DE COLALAO',
    'SAN PEDRO MÁRTIR',
    'SAN RAFAEL',
    'SAN RAMÓN',
    'SAN RAMÓN',
    'SAN RAMÓN CHICLIGASTA',
    'SAN SEBASTIÁN',
    'SAN VICENTE',
    'SAN VICENTE',
    'SANDOVAL',
    'SANTA BÁRBARA',
    'SANTA BÁRBARA',
    'SANTA CLARA',
    'SANTA CRUZ',
    'SANTA CRUZ',
    'SANTA ELENA',
    'SANTA ISABEL',
    'SANTA LUCÍA',
    'SANTA LUCÍA',
    'SANTA RITA',
    'SANTA RITA',
    'SANTA ROSA',
    'SANTA ROSA',
    'SANTA ROSA',
    'SANTA ROSA DE LEALES',
    'SANTA SOFÍA',
    'SANTOS LUGARES',
    'SANTOS LUGARES',
    'SANTOS VIEJOS',
    'SARGENTO MOYA',
    'SAUCE GAUCHO',
    'SAUCE HUACHO',
    'SAUCE YACO',
    'SAUCE YACU',
    'SAUCE YACU',
    'SAUCES',
    'SEPULTURA/S',
    'SIETE QUEBRACHOS',
    'SIMBOLAR',
    'SIMOCA',
    'SINQUEAL',
    'SINQUIAL',
    'SOL DE MAYO',
    'SOL DE MAYO',
    'SOLDADO MALDONado',
    'SORAIRE',
    'SUD DE TREJOS',
    'SUELDOS',
    'SUNCHO PUNTA',
    'SUR DE LAZARTE',
    'SURiyACU',
    'TACANA',
    'TACANAS',
    'TACO RALO',
    'TACO RODEO',
    'TACO YACO',
    'TAFÍ DEL VALLE',
    'TAFÍ VIEJO',
    'TAFICILLO',
    'TALA BAJADA',
    'TALA CAIDO',
    'TALA COCHA',
    'TALA PAMPA',
    'TALA PASO',
    'TALA POZO',
    'TALA PUNCO',
    'TALA SACHA',
    'TALA YACO',
    'TALAR',
    'TALCOPALTA',
    'TALITA POZO',
    'TAPIA',
    'TARUCA PAMPA',
    'TATA YACU',
    'TENIENTE BERDINA',
    'TICUCHO',
    'TIMÓN HACHADO',
    'TINAJEROS',
    'TIO PUNCO',
    'TORO LOCO',
    'TORO MUERTO',
    'TORO MUERTO',
    'TORO MUERTO',
    'TOTORAL',
    'TOTORAS',
    'TOTORILLA',
    'TRANCAS',
    'TRANQUITAS',
    'TRES POZOS',
    'TRES POZOS',
    'TRINIDAD',
    'TUNA SOLA',
    'TUNALITO',
    'TUSCA POZO',
    'TUSQUITAS',
    'UCUCHACRA',
    'URIZAR',
    'UTURUNCO',
    'UTURUNCO',
    'VILLA ELENA',
    'VILLA BELGRANO',
    'VILLA BRAVA',
    'VILLA CARMELA',
    'VILLA CHICLIGASTA',
    'VILLA CLODOMIRO HILERET',
    'VILLA DESIERTO DE LUZ',
    'VILLA FIAD',
    'VILLA GLORIA',
    'VILLA LA QUEBRADITA',
    'VILLA MARIA',
    'VILLA NOUGUES',
    'VILLA NUEVA',
    'VILLA PUJIO',
    'VILLA QUINTEROS',
    'VILLA RECASTE',
    'VILLA VIEJA',
    'VILLA VIEJA',
    'VACAHUASI',
    'VESUBIO',
    'VIADUCTO EL SALADILLO',
    'VICLOS',
    'VILLAGRA',
    'VILTRAN',
    'VIPOS',
    'W.POSSE',
    'YACUCHINA',
    'YACUCHIRI',
    'YALAPA',
    'YANTA PALLANA',
    'YAPACHIN',
    'YAQULO',
    'YARAMI',
    'YASYAMAYO',
    'YERBA BUENA',
    'YERBA BUENA',
    'YONOPONGO',
    'YUCHAN',
    'YUCUMANITA',
    'ZABALIA',
    'ZAPALLAR',
    'ZARATE NORTE',
    'ZARATE SUR'
];

// Definir todos los departamentos según la estructura de la base de datos
$departamentos = [
    'CAPITAL',
    'BURRUYACÚ',
    'CHICLIGASTA',
    'CRUZ ALTA',
    'FAMAILLÁ',
    'GRANEROS',
    'J.B. ALBERDI',
    'LA COCHA',
    'LEALES',
    'LULES',
    'MONTEROS',
    'RÍO CHICO',
    'SIMOCA',
    'TAFÍ DEL VALLE',
    'TAFÍ VIEJO',
    'TRANCAS',
    'YERBA BUENA'
];

// Obtener ID del cursante a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Cargar datos del cursante
$stmt = $conn->prepare("SELECT * FROM cursante WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$cursante = $result->fetch_assoc();
$stmt->close();

// Función auxiliar para manejar valores nulos en htmlspecialchars
function safe_html($value)
{
    return $value !== null ? htmlspecialchars($value) : '';
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado_civil = $_POST['estado_civil'];
    $hijos = intval($_POST['hijos']);
    $nombre_hijos = trim($_POST['nombre_hijos']);
    $nombre_padre = trim($_POST['nombre_padre']);
    $nombre_madre = trim($_POST['nombre_madre']);
    $vive_padre = isset($_POST['vive_padre']) ? 1 : 0;
    $vive_madre = isset($_POST['vive_madre']) ? 1 : 0;
    $direccion_real = trim($_POST['direccion_real']);
    $depto = $_POST['depto'];
    $localidad = $_POST['localidad'];
    $cod_postal = !empty(trim($_POST['cod_postal'])) ? intval(trim($_POST['cod_postal'])) : null;
    $telefono = !empty(trim($_POST['telefono'])) ? intval(trim($_POST['telefono'])) : null;
    $email = trim($_POST['email']);
    $nombre_fam_directo = trim($_POST['nombre_fam_directo']);
    $tel_fam_directo = !empty(trim($_POST['tel_fam_directo'])) ? intval(trim($_POST['tel_fam_directo'])) : null;
    $parentezco = trim($_POST['parentezco']);
    $comision = $_POST['comision'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $sit_revista = $_POST['sit_revista'];
    $novedades = trim($_POST['novedades']);
    $estado = $_POST['estado'];

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } else {
        // Verificar si el DNI ya existe (excluyendo el actual)
        $checkDni = $conn->prepare("SELECT id FROM cursante WHERE dni = ? AND id != ?");
        $checkDni->bind_param("si", $dni, $id);
        $checkDni->execute();
        $checkDni->store_result();

        if ($checkDni->num_rows > 0) {
            $error = 'El DNI ya está registrado en el sistema';
        } else {
            // Actualizar datos del cursante
            $stmt = $conn->prepare("UPDATE cursante SET 
                dni = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, 
                estado_civil = ?, hijos = ?, nombre_hijos = ?, nombre_padre = ?, 
                nombre_madre = ?, vive_padre = ?, vive_madre = ?, direccion_real = ?, 
                depto = ?, localidad = ?, cod_postal = ?, telefono = ?, email = ?, 
                nombre_fam_directo = ?, tel_fam_directo = ?, parentezco = ?, 
                comision = ?, fecha_ingreso = ?, sit_revista = ?, novedades = ?, estado = ? 
                WHERE id = ?");

            // Manejar valores nulos para la base de datos
            $fecha_nacimiento = empty($fecha_nacimiento) ? null : $fecha_nacimiento;
            $fecha_ingreso = empty($fecha_ingreso) ? null : $fecha_ingreso;
            $estado_civil = empty($estado_civil) ? null : $estado_civil;
            $nombre_hijos = empty($nombre_hijos) ? null : $nombre_hijos;
            $nombre_padre = empty($nombre_padre) ? null : $nombre_padre;
            $nombre_madre = empty($nombre_madre) ? null : $nombre_madre;
            $direccion_real = empty($direccion_real) ? null : $direccion_real;
            $depto = empty($depto) ? null : $depto;
            $localidad = empty($localidad) ? null : $localidad;
            $email = empty($email) ? null : $email;
            $nombre_fam_directo = empty($nombre_fam_directo) ? null : $nombre_fam_directo;
            $parentezco = empty($parentezco) ? null : $parentezco;
            $comision = empty($comision) ? null : $comision;
            $sit_revista = empty($sit_revista) ? null : $sit_revista;
            $novedades = empty($novedades) ? null : $novedades;
            $estado = empty($estado) ? null : $estado;

            $stmt->bind_param(
                "sssssisssiisssissssisssssi",
                $dni,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $estado_civil,
                $hijos,
                $nombre_hijos,
                $nombre_padre,
                $nombre_madre,
                $vive_padre,
                $vive_madre,
                $direccion_real,
                $depto,
                $localidad,
                $cod_postal,
                $telefono,
                $email,
                $nombre_fam_directo,
                $tel_fam_directo,
                $parentezco,
                $comision,
                $fecha_ingreso,
                $sit_revista,
                $novedades,
                $estado,
                $id
            );

            if ($stmt->execute()) {
                $success = 'Cursante actualizado correctamente';
                // Recargar datos actualizados
                $stmt_reload = $conn->prepare("SELECT * FROM cursante WHERE id = ?");
                $stmt_reload->bind_param("i", $id);
                $stmt_reload->execute();
                $result_reload = $stmt_reload->get_result();
                $cursante = $result_reload->fetch_assoc();
                $stmt_reload->close();
            } else {
                $error = 'Error al actualizar el cursante: ' . $stmt->error;
            }
        }
        $checkDni->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Editar Cursante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="../../assets/css/editar_asp.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>

        <h1>Editar Cursante</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="dni" class="required">DNI</label>
                    <input type="text" id="dni" name="dni" value="<?php echo safe_html($cursante['dni']); ?>" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo safe_html($cursante['nombre']); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="apellido" class="required">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo safe_html($cursante['apellido']); ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo safe_html($cursante['fecha_nacimiento']); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo safe_html($cursante['fecha_ingreso']); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Seleccionar</option>
                        <option value="ASPIRANTE" <?php echo ($cursante['estado'] === 'ASPIRANTE') ? 'selected' : ''; ?>>Aspirante</option>
                        <option value="SUPLENTE" <?php echo ($cursante['estado'] === 'SUPLENTE') ? 'selected' : ''; ?>>Suplente</option>
                        <option value="CURSANTE" <?php echo ($cursante['estado'] === 'CURSANTE') ? 'selected' : ''; ?>>Cursante</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comision" class="required">Comisión</label>
                    <select id="comision" name="comision" required>
                        <option value="">Seleccionar</option>
                        <option value="A" <?php echo ($cursante['comision'] === 'A') ? 'selected' : ''; ?>>Comisión A</option>
                        <option value="B" <?php echo ($cursante['comision'] === 'B') ? 'selected' : ''; ?>>Comisión B</option>
                        <option value="C" <?php echo ($cursante['comision'] === 'C') ? 'selected' : ''; ?>>Comisión C</option>
                        <option value="D" <?php echo ($cursante['comision'] === 'D') ? 'selected' : ''; ?>>Comisión D</option>
                        <option value="E" <?php echo ($cursante['comision'] === 'E') ? 'selected' : ''; ?>>Comisión E</option>
                        <option value="F" <?php echo ($cursante['comision'] === 'F') ? 'selected' : ''; ?>>Comisión F</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccionar</option>
                        <option value="SOLTERO/A" <?php echo ($cursante['estado_civil'] === 'SOLTERO/A') ? 'selected' : ''; ?>>Soltero/a</option>
                        <option value="CASADO/A" <?php echo ($cursante['estado_civil'] === 'CASADO/A') ? 'selected' : ''; ?>>Casado/a</option>
                        <option value="DIVORCIADO/A" <?php echo ($cursante['estado_civil'] === 'DIVORCIADO/A') ? 'selected' : ''; ?>>Divorciado/a</option>
                        <option value="VIUDO/A" <?php echo ($cursante['estado_civil'] === 'VIUDO/A') ? 'selected' : ''; ?>>Viudo/a</option>
                        <option value="CONCUBINATO" <?php echo ($cursante['estado_civil'] === 'CONCUBINATO') ? 'selected' : ''; ?>>Concubinato</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hijos">Número de Hijos</label>
                    <input type="number" id="hijos" name="hijos" value="<?php echo safe_html($cursante['hijos']); ?>" min="0">
                </div>

                <div class="form-group">
                    <label for="nombre_hijos">Nombres de Hijos</label>
                    <textarea id="nombre_hijos" name="nombre_hijos" rows="2"><?php echo safe_html($cursante['nombre_hijos']); ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_padre">Nombre del Padre</label>
                    <input type="text" id="nombre_padre" name="nombre_padre" value="<?php echo safe_html($cursante['nombre_padre']); ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="nombre_madre">Nombre de la Madre</label>
                    <input type="text" id="nombre_madre" name="nombre_madre" value="<?php echo safe_html($cursante['nombre_madre']); ?>" maxlength="200">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vive_padre">¿Vive el padre?</label>
                    <input type="checkbox" id="vive_padre" name="vive_padre" value="1" <?php echo ($cursante['vive_padre'] == 1) ? 'checked' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="vive_madre">¿Vive la madre?</label>
                    <input type="checkbox" id="vive_madre" name="vive_madre" value="1" <?php echo ($cursante['vive_madre'] == 1) ? 'checked' : ''; ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion_real">Dirección Real</label>
                <input type="text" id="direccion_real" name="direccion_real" value="<?php echo safe_html($cursante['direccion_real']); ?>" maxlength="300">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="depto">Departamento</label>
                    <select id="depto" name="depto">
                        <option value="">Seleccionar</option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>"
                                <?php echo ($cursante['depto'] === $d) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($d); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="localidad">Localidad</label>
                    <select id="localidad" name="localidad">
                        <option value="">Seleccionar</option>
                        <?php foreach ($localidades as $l): ?>
                            <option value="<?php echo htmlspecialchars($l); ?>" <?php echo ($cursante['localidad'] === $l) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($l); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cod_postal">Código Postal</label>
                    <input type="text" id="cod_postal" name="cod_postal" value="<?php echo safe_html($cursante['cod_postal']); ?>" maxlength="10">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo safe_html($cursante['telefono']); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo safe_html($cursante['email']); ?>" maxlength="150">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_fam_directo">Nombre Familiar Directo</label>
                    <input type="text" id="nombre_fam_directo" name="nombre_fam_directo" value="<?php echo safe_html($cursante['nombre_fam_directo']); ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="tel_fam_directo">Teléfono Familiar</label>
                    <input type="text" id="tel_fam_directo" name="tel_fam_directo" value="<?php echo safe_html($cursante['tel_fam_directo']); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="parentezco">Parentezco</label>
                    <input type="text" id="parentezco" name="parentezco" value="<?php echo safe_html($cursante['parentezco']); ?>" maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="sit_revista">Situación de Revista</label>
                    <select id="sit_revista" name="sit_revista">
                        <option value="">Seleccionar</option>
                        <option value="A.R.T." <?php echo ($cursante['sit_revista'] === 'A.R.T.') ? 'selected' : ''; ?>>A.R.T.</option>
                        <option value="NOTA MÉDICA" <?php echo ($cursante['sit_revista'] === 'NOTA MÉDICA') ? 'selected' : ''; ?>>Nota Médica</option>
                        <option value="DISPONIBLE" <?php echo ($cursante['sit_revista'] === 'DISPONIBLE') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="PASIVO" <?php echo ($cursante['sit_revista'] === 'PASIVO') ? 'selected' : ''; ?>>Pasivo</option>
                        <option value="ACTIVO" <?php echo ($cursante['sit_revista'] === 'ACTIVO') ? 'selected' : ''; ?>>Activo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="novedades">Novedades</label>
                <textarea id="novedades" name="novedades" rows="4"><?php echo safe_html($cursante['novedades']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Cursante</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/editar_asp.js"></script>
</body>

</html>