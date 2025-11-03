<?php
/**
 * Nationality Matcher
 * 
 * Fuzzy matching system to match OCR-extracted nationality text
 * with the system's nationality dropdown options
 */

require_once __DIR__ . '/../nationality_options.php';

/**
 * Match extracted nationality with dropdown options
 * 
 * @param string $extractedText Nationality text from OCR
 * @return array Match result with value and confidence
 */
function matchNationality($extractedText) {
    $result = [
        'matched' => false,
        'value' => null,
        'confidence' => 0.0,
        'original' => $extractedText
    ];
    
    if (empty($extractedText)) {
        return $result;
    }
    
    // Get nationality list from dropdown
    $nationalities = getNationalityList();
    
    // Normalize extracted text
    $normalized = strtolower(trim($extractedText));
    $normalized = preg_replace('/[^a-z\s]/', '', $normalized); // Remove special chars
    
    // 1. Direct match (case-insensitive)
    foreach ($nationalities as $nationality) {
        if (strtolower($nationality) === $normalized) {
            $result['matched'] = true;
            $result['value'] = $nationality;
            $result['confidence'] = 1.0;
            return $result;
        }
    }
    
    // 2. Country code to nationality mapping (ISO-3)
    $countryCodeMappings = getCountryCodeMappings();
    if (isset($countryCodeMappings[$normalized]) || isset($countryCodeMappings[strtoupper($extractedText)])) {
        $mappedValue = $countryCodeMappings[$normalized] ?? $countryCodeMappings[strtoupper($extractedText)];
        $result['matched'] = true;
        $result['value'] = $mappedValue;
        $result['confidence'] = 0.95;
        return $result;
    }
    
    // 3. Common variations mapping
    $variations = getCommonNationalityVariations();
    if (isset($variations[$normalized])) {
        $result['matched'] = true;
        $result['value'] = $variations[$normalized];
        $result['confidence'] = 0.90;
        return $result;
    }
    
    // 4. Fuzzy matching using Levenshtein distance
    $bestMatch = null;
    $bestDistance = PHP_INT_MAX;
    $threshold = 3; // Maximum allowed distance
    
    foreach ($nationalities as $nationality) {
        $distance = levenshtein($normalized, strtolower($nationality));
        if ($distance < $bestDistance && $distance <= $threshold) {
            $bestDistance = $distance;
            $bestMatch = $nationality;
        }
    }
    
    if ($bestMatch) {
        $result['matched'] = true;
        $result['value'] = $bestMatch;
        $result['confidence'] = max(0.50, 1.0 - ($bestDistance / 10));
        return $result;
    }
    
    // 5. Partial match (contains)
    foreach ($nationalities as $nationality) {
        if (strpos(strtolower($nationality), $normalized) !== false ||
            strpos($normalized, strtolower($nationality)) !== false) {
            $result['matched'] = true;
            $result['value'] = $nationality;
            $result['confidence'] = 0.70;
            return $result;
        }
    }
    
    // No match found
    return $result;
}

/**
 * Get list of nationalities from dropdown
 */
function getNationalityList() {
    // Get from nationality_options.php
    $html = generateNationalityOptions();
    
    // Extract option values using regex
    preg_match_all("/value='([^']+)'/", $html, $matches);
    
    // Filter out empty values
    $nationalities = array_filter($matches[1], function($val) {
        return !empty($val);
    });
    
    return array_values($nationalities);
}

/**
 * ISO-3 country codes to nationality mappings
 */
function getCountryCodeMappings() {
    return [
        // ISO-3 codes
        'afg' => 'Afghan',
        'alb' => 'Albanian',
        'dza' => 'Algerian',
        'usa' => 'American',
        'arg' => 'Argentinian',
        'aus' => 'Australian',
        'aut' => 'Austrian',
        'bhr' => 'Bahraini',
        'bgd' => 'Bangladeshi',
        'brb' => 'Barbadian',
        'bwa' => 'Batswana',
        'bel' => 'Belgian',
        'bol' => 'Bolivian',
        'bra' => 'Brazilian',
        'gbr' => 'British',
        'bgr' => 'Bulgarian',
        'khm' => 'Cambodian',
        'cmr' => 'Cameroonian',
        'can' => 'Canadian',
        'chl' => 'Chilean',
        'chn' => 'Chinese',
        'col' => 'Colombian',
        'com' => 'Comoros',
        'cri' => 'Costa Rican',
        'hrv' => 'Croatian',
        'cub' => 'Cuban',
        'cyp' => 'Cyprus',
        'cze' => 'Czech',
        'dnk' => 'Danish',
        'deu' => 'German',
        'dom' => 'Dominican',
        'nld' => 'Dutch',
        'ecu' => 'Ecuadorian',
        'egy' => 'Egyptian',
        'are' => 'Emirati',
        'slv' => 'Salvadorian',
        'est' => 'Estonian',
        'eth' => 'Ethiopian',
        'fji' => 'Fijian',
        'fin' => 'Finnish',
        'fra' => 'French',
        'gha' => 'Ghanaian',
        'grc' => 'Greek',
        'gtm' => 'Guatemalan',
        'hti' => 'Haitian',
        'hnd' => 'Honduran',
        'hun' => 'Hungarian',
        'isl' => 'Icelandic',
        'ind' => 'Indian',
        'idn' => 'Indonesian',
        'irn' => 'Iranian',
        'irq' => 'Iraqi',
        'irl' => 'Irish',
        'isr' => 'Israeli',
        'ita' => 'Italian',
        'jam' => 'Jamaican',
        'jpn' => 'Japanese',
        'jor' => 'Jordanian',
        'ken' => 'Kenyan',
        'kor' => 'South Korean',
        'kwt' => 'Kuwaiti',
        'lao' => 'Lao',
        'lva' => 'Latvian',
        'lbn' => 'Lebanese',
        'lby' => 'Libyan',
        'ltu' => 'Lithuanian',
        'lux' => 'Luxembourg',
        'mdg' => 'Malagasy',
        'mys' => 'Malaysian',
        'mli' => 'Malian',
        'mlt' => 'Maltese',
        'mus' => 'Mauritius',
        'mex' => 'Mexican',
        'mng' => 'Mongolian',
        'mar' => 'Moroccan',
        'moz' => 'Mozambican',
        'mmr' => 'Myanmar',
        'nam' => 'Namibian',
        'npl' => 'Nepalese',
        'nzl' => 'New Zealand',
        'nic' => 'Nicaraguan',
        'nga' => 'Nigerian',
        'nor' => 'Norwegian',
        'omn' => 'Omani',
        'pak' => 'Pakistani',
        'pan' => 'Panamanian',
        'pry' => 'Paraguayan',
        'per' => 'Peruvian',
        'phl' => 'Philippine',
        'pol' => 'Polish',
        'prt' => 'Portuguese',
        'qat' => 'Qatari',
        'rou' => 'Romanian',
        'rus' => 'Russian',
        'sau' => 'Saudi',
        'sct' => 'Scottish',
        'sen' => 'Senegalese',
        'srb' => 'Serbian',
        'sgp' => 'Singaporean',
        'svk' => 'Slovak',
        'zaf' => 'South African',
        'esp' => 'Spanish',
        'lka' => 'Sri Lankan',
        'kna' => 'St Kitts',
        'sdn' => 'Sudanese',
        'swe' => 'Swedish',
        'che' => 'Swiss',
        'syr' => 'Syrian',
        'twn' => 'Taiwanese',
        'tjk' => 'Tajikistani',
        'tza' => 'Tanzanian',
        'tha' => 'Thai',
        'ton' => 'Tongan',
        'tun' => 'Tunisian',
        'tur' => 'Turkish',
        'uga' => 'Ugandan',
        'ukr' => 'Ukrainian',
        'ury' => 'Uruguayan',
        'ven' => 'Venezuelan',
        'vnm' => 'Vietnamese',
        'wls' => 'Welsh',
        'yem' => 'Yemeni',
        'zmb' => 'Zambian',
        'zwe' => 'Zimbabwean'
    ];
}

/**
 * Common nationality variations and misspellings
 */
function getCommonNationalityVariations() {
    return [
        // Country name to nationality
        'india' => 'Indian',
        'pakistan' => 'Pakistani',
        'bangladesh' => 'Bangladeshi',
        'united states' => 'American',
        'united states of america' => 'American',
        'us' => 'American',
        'u.s.a' => 'American',
        'united kingdom' => 'British',
        'uk' => 'British',
        'great britain' => 'British',
        'england' => 'British',
        'united arab emirates' => 'Emirati',
        'uae' => 'Emirati',
        'saudi arabia' => 'Saudi',
        'south africa' => 'South African',
        'new zealand' => 'New Zealand',
        'sri lanka' => 'Sri Lankan',
        'ceylon' => 'Sri Lankan',
        'deutschland' => 'German',
        'germany' => 'German',
        'france' => 'French',
        'spain' => 'Spanish',
        'italy' => 'Italian',
        'china' => 'Chinese',
        'japan' => 'Japanese',
        'korea' => 'South Korean',
        'south korea' => 'South Korean',
        'philippines' => 'Philippine',
        'thailand' => 'Thai',
        'malaysia' => 'Malaysian',
        'singapore' => 'Singaporean',
        'indonesia' => 'Indonesian',
        'egypt' => 'Egyptian',
        'lebanon' => 'Lebanese',
        'jordan' => 'Jordanian',
        'syria' => 'Syrian',
        'iraq' => 'Iraqi',
        'iran' => 'Iranian',
        'kuwait' => 'Kuwaiti',
        'oman' => 'Omani',
        'qatar' => 'Qatari',
        'bahrain' => 'Bahraini',
        'yemen' => 'Yemeni',
        'afghanistan' => 'Afghan',
        'nepal' => 'Nepalese',
        'myanmar' => 'Myanmar',
        'burma' => 'Myanmar',
        'vietnam' => 'Vietnamese',
        'canada' => 'Canadian',
        'mexico' => 'Mexican',
        'brazil' => 'Brazilian',
        'argentina' => 'Argentinian',
        'chile' => 'Chilean',
        'colombia' => 'Colombian',
        'peru' => 'Peruvian',
        'venezuela' => 'Venezuelan',
        'russia' => 'Russian',
        'poland' => 'Polish',
        'ukraine' => 'Ukrainian',
        'sweden' => 'Swedish',
        'norway' => 'Norwegian',
        'denmark' => 'Danish',
        'finland' => 'Finnish',
        'netherlands' => 'Dutch',
        'belgium' => 'Belgian',
        'switzerland' => 'Swiss',
        'austria' => 'Austrian',
        'portugal' => 'Portuguese',
        'greece' => 'Greek',
        'turkey' => 'Turkish',
        'israel' => 'Israeli',
        'australia' => 'Australian',
        'morocco' => 'Moroccan',
        'tunisia' => 'Tunisian',
        'libya' => 'Libyan',
        'algeria' => 'Algerian',
        'sudan' => 'Sudanese',
        'ethiopia' => 'Ethiopian',
        'kenya' => 'Kenyan',
        'tanzania' => 'Tanzanian',
        'uganda' => 'Ugandan',
        'nigeria' => 'Nigerian',
        'ghana' => 'Ghanaian',
        'senegal' => 'Senegalese',
        'cameroon' => 'Cameroonian',
        'zimbabwe' => 'Zimbabwean',
        'zambia' => 'Zambian',
        'namibia' => 'Namibian',
        'mozambique' => 'Mozambican'
    ];
}
?>

