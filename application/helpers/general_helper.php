<?php

function encode_URI_Component($str) {
    $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
    return strtr(rawurlencode($str), $revert);
}

function hexEncode($s) {
    return array_shift(unpack('H*', $s));
}

function htmlSpaceIfEmpty($string) {
    return empty($string) ? '&nbsp;' : html_escape($string);
}

function replace_func($m) {
    return array_count_values($m) === 1 ? '\\' . $m[0] : "(.*)";
}

function match_base_string($match, $string_to_test) {
    // $trimRegExp = "/[\r\n\s]+/m";
    $baseRegExp = "/[-\[\]{}()*+?.,\\^$|#]|(\%\d+\%)/m";
    $pattern = '/' . preg_replace_callback($baseRegExp, "replace_func", $match) . '/m';

    return preg_match($pattern, $string_to_test);
}

function get_country($locale) {
    switch ($locale) {
        case 'ar-SA': return 'SA';
        case 'bg-BG': return 'BG';
        case 'ca-ES': return 'ES';
        case 'zh-TW': return 'TW';
        case 'cs-CZ': return 'CZ';
        case 'da-DK': return 'DK';
        case 'de-DE': return 'DE';
        case 'el-GR': return 'GR';
        case 'en-US': return 'US';
        case 'fi-FI': return 'FI';
        case 'fr-FR': return 'FR';
        case 'he-IL': return 'IL';
        case 'hu-HU': return 'HU';
        case 'is-IS': return 'IS';
        case 'it-IT': return 'IT';
        case 'ja-JP': return 'JP';
        case 'ko-KR': return 'KR';
        case 'nl-NL': return 'NL';
        case 'nb-NO': return 'NO';
        case 'pl-PL': return 'PL';
        case 'pt-BR': return 'BR';
        case 'rm-CH': return 'CH';
        case 'ro-RO': return 'RO';
        case 'ru-RU': return 'RU';
        case 'hr-HR': return 'HR';
        case 'sk-SK': return 'SK';
        case 'sq-AL': return 'AL';
        case 'sv-SE': return 'SE';
        case 'th-TH': return 'TH';
        case 'tr-TR': return 'TR';
        case 'ur-PK': return 'PK';
        case 'id-ID': return 'ID';
        case 'uk-UA': return 'UA';
        case 'be-BY': return 'BY';
        case 'sl-SI': return 'SI';
        case 'et-EE': return 'EE';
        case 'lv-LV': return 'LV';
        case 'lt-LT': return 'LT';
        case 'tg-Cyrl-TJ': return 'TJ';
        case 'fa-IR': return 'IR';
        case 'vi-VN': return 'VN';
        case 'hy-AM': return 'AM';
        case 'az-Latn-AZ': return 'AZ';
        case 'eu-ES': return 'ES';
        case 'hsb-DE': return 'DE';
        case 'mk-MK': return 'MK';
        case 'tn-ZA': return 'ZA';
        case 'xh-ZA': return 'ZA';
        case 'zu-ZA': return 'ZA';
        case 'af-ZA': return 'ZA';
        case 'ka-GE': return 'GE';
        case 'fo-FO': return 'FO';
        case 'hi-IN': return 'IN';
        case 'mt-MT': return 'MT';
        case 'se-NO': return 'NO';
        case 'ms-MY': return 'MY';
        case 'kk-KZ': return 'KZ';
        case 'ky-KG': return 'KG';
        case 'sw-KE': return 'KE';
        case 'tk-TM': return 'TM';
        case 'uz-Latn-UZ': return 'UZ';
        case 'tt-RU': return 'RU';
        case 'bn-IN': return 'IN';
        case 'pa-IN': return 'IN';
        case 'gu-IN': return 'IN';
        case 'or-IN': return 'IN';
        case 'ta-IN': return 'IN';
        case 'te-IN': return 'IN';
        case 'kn-IN': return 'IN';
        case 'ml-IN': return 'IN';
        case 'as-IN': return 'IN';
        case 'mr-IN': return 'IN';
        case 'sa-IN': return 'IN';
        case 'mn-MN': return 'MN';
        case 'bo-CN': return 'CN';
        case 'cy-GB': return 'GB';
        case 'km-KH': return 'KH';
        case 'lo-LA': return 'LA';
        case 'gl-ES': return 'ES';
        case 'kok-IN': return 'IN';
        case 'syr-SY': return 'SY';
        case 'si-LK': return 'LK';
        case 'iu-Cans-CA': return 'CA';
        case 'am-ET': return 'ET';
        case 'ne-NP': return 'NP';
        case 'fy-NL': return 'NL';
        case 'ps-AF': return 'AF';
        case 'fil-PH': return 'PH';
        case 'dv-MV': return 'MV';
        case 'ha-Latn-NG': return 'NG';
        case 'yo-NG': return 'NG';
        case 'quz-BO': return 'BO';
        case 'nso-ZA': return 'ZA';
        case 'ba-RU': return 'RU';
        case 'lb-LU': return 'LU';
        case 'kl-GL': return 'GL';
        case 'ig-NG': return 'NG';
        case 'ii-CN': return 'CN';
        case 'arn-CL': return 'CL';
        case 'moh-CA': return 'CA';
        case 'br-FR': return 'FR';
        case 'ug-CN': return 'CN';
        case 'mi-NZ': return 'NZ';
        case 'oc-FR': return 'FR';
        case 'co-FR': return 'FR';
        case 'gsw-FR': return 'FR';
        case 'sah-RU': return 'RU';
        case 'qut-GT': return 'GT';
        case 'rw-RW': return 'RW';
        case 'wo-SN': return 'SN';
        case 'prs-AF': return 'AF';
        case 'gd-GB': return 'GB';
        case 'ar-IQ': return 'IQ';
        case 'zh-CN': return 'CN';
        case 'de-CH': return 'CH';
        case 'en-GB': return 'GB';
        case 'es-MX': return 'MX';
        case 'fr-BE': return 'BE';
        case 'it-CH': return 'CH';
        case 'nl-BE': return 'BE';
        case 'nn-NO': return 'NO';
        case 'pt-PT': return 'PT';
        case 'sr-Latn-CS': return 'CS';
        case 'sv-FI': return 'FI';
        case 'az-Cyrl-AZ': return 'AZ';
        case 'dsb-DE': return 'DE';
        case 'se-SE': return 'SE';
        case 'ga-IE': return 'IE';
        case 'ms-BN': return 'BN';
        case 'uz-Cyrl-UZ': return 'UZ';
        case 'bn-BD': return 'BD';
        case 'mn-Mong-CN': return 'CN';
        case 'iu-Latn-CA': return 'CA';
        case 'tzm-Latn-DZ': return 'DZ';
        case 'quz-EC': return 'EC';
        case 'ar-EG': return 'EG';
        case 'zh-HK': return 'HK';
        case 'de-AT': return 'AT';
        case 'en-AU': return 'AU';
        case 'es-ES': return 'ES';
        case 'fr-CA': return 'CA';
        case 'sr-Cyrl-CS': return 'CS';
        case 'se-FI': return 'FI';
        case 'quz-PE': return 'PE';
        case 'ar-LY': return 'LY';
        case 'zh-SG': return 'SG';
        case 'de-LU': return 'LU';
        case 'en-CA': return 'CA';
        case 'es-GT': return 'GT';
        case 'fr-CH': return 'CH';
        case 'hr-BA': return 'BA';
        case 'smj-NO': return 'NO';
        case 'ar-DZ': return 'DZ';
        case 'zh-MO': return 'MO';
        case 'de-LI': return 'LI';
        case 'en-NZ': return 'NZ';
        case 'es-CR': return 'CR';
        case 'fr-LU': return 'LU';
        case 'bs-Latn-BA': return 'BA';
        case 'smj-SE': return 'SE';
        case 'ar-MA': return 'MA';
        case 'en-IE': return 'IE';
        case 'es-PA': return 'PA';
        case 'fr-MC': return 'MC';
        case 'sr-Latn-BA': return 'BA';
        case 'sma-NO': return 'NO';
        case 'ar-TN': return 'TN';
        case 'en-ZA': return 'ZA';
        case 'es-DO': return 'DO';
        case 'sr-Cyrl-BA': return 'BA';
        case 'sma-SE': return 'SE';
        case 'ar-OM': return 'OM';
        case 'en-JM': return 'JM';
        case 'es-VE': return 'VE';
        case 'bs-Cyrl-BA': return 'BA';
        case 'sms-FI': return 'FI';
        case 'ar-YE': return 'YE';
        case 'en-029': return '029';
        case 'es-CO': return 'CO';
        case 'sr-Latn-RS': return 'RS';
        case 'smn-FI': return 'FI';
        case 'ar-SY': return 'SY';
        case 'en-BZ': return 'BZ';
        case 'es-PE': return 'PE';
        case 'sr-Cyrl-RS': return 'RS';
        case 'ar-JO': return 'JO';
        case 'en-TT': return 'TT';
        case 'es-AR': return 'AR';
        case 'sr-Latn-ME': return 'ME';
        case 'ar-LB': return 'LB';
        case 'en-ZW': return 'ZW';
        case 'es-EC': return 'EC';
        case 'sr-Cyrl-ME': return 'ME';
        case 'ar-KW': return 'KW';
        case 'en-PH': return 'PH';
        case 'es-CL': return 'CL';
        case 'ar-AE': return 'AE';
        case 'es-UY': return 'UY';
        case 'ar-BH': return 'BH';
        case 'es-PY': return 'PY';
        case 'ar-QA': return 'QA';
        case 'en-IN': return 'IN';
        case 'es-BO': return 'BO';
        case 'en-MY': return 'MY';
        case 'es-SV': return 'SV';
        case 'en-SG': return 'SG';
        case 'es-HN': return 'HN';
        case 'es-NI': return 'NI';
        case 'es-PR': return 'PR';
        case 'es-US': return 'US';

        default :return "";
    }
}

function get_locales() {
    static $locales = array(
        'sma-NO' => 'åarjelsaemiengiele (Nöörje)',
        'sma-SE' => 'åarjelsaemiengiele (Sveerje)',
        'af-ZA' => 'Afrikaans (Suid Afrika)',
        'az-Latn-AZ' => 'Azərbaycan­ılı (Azərbaycan)',
        'id-ID' => 'Bahasa Indonesia (Indonesia)',
        'ms-BN' => 'Bahasa Melayu (Brunei Darussalam)',
        'ms-MY' => 'Bahasa Melayu (Malaysia)',
        'bs-Latn-BA' => 'bosanski (Bosna i Hercegovina)',
        'br-FR' => 'brezhoneg (Frañs)',
        'ca-ES' => 'català (català)',
        'cs-CZ' => 'čeština (Česká republika)',
        'co-FR' => 'Corsu (France)',
        'cy-GB' => 'Cymraeg (y Deyrnas Unedig)',
        'da-DK' => 'dansk (Danmark)',
        'se-NO' => 'davvisámegiella (Norga)',
        'se-SE' => 'davvisámegiella (Ruoŧŧa)',
        'se-FI' => 'davvisámegiella (Suopma)',
        'de-DE' => 'Deutsch (Deutschland)',
        'de-LI' => 'Deutsch (Liechtenstein)',
        'de-LU' => 'Deutsch (Luxemburg)',
        'de-AT' => 'Deutsch (Österreich)',
        'de-CH' => 'Deutsch (Schweiz)',
        'dsb-DE' => 'dolnoserbšćina (Nimska)',
        'et-EE' => 'eesti (Eesti)',
        'gsw-FR' => 'Elsässisch (Frànkrisch)',
        'en-AU' => 'English (Australia)',
        'en-BZ' => 'English (Belize)',
        'en-CA' => 'English (Canada)',
        'en-029' => 'English (Caribbean)',
        'en-IN' => 'English (India)',
        'en-IE' => 'English (Ireland)',
        'en-JM' => 'English (Jamaica)',
        'en-MY' => 'English (Malaysia)',
        'en-NZ' => 'English (New Zealand)',
        'en-PH' => 'English (Philippines)',
        'en-SG' => 'English (Singapore)',
        'en-ZA' => 'English (South Africa)',
        'en-TT' => 'English (Trinidad y Tobago)',
        'en-GB' => 'English (United Kingdom)',
        'en-US' => 'English (United States)',
        'en-ZW' => 'English (Zimbabwe)',
        'es-AR' => 'Español (Argentina)',
        'es-BO' => 'Español (Bolivia)',
        'es-CL' => 'Español (Chile)',
        'es-CO' => 'Español (Colombia)',
        'es-CR' => 'Español (Costa Rica)',
        'es-EC' => 'Español (Ecuador)',
        'es-SV' => 'Español (El Salvador)',
        'es-ES' => 'Español (España, alfabetización internacional)',
        'es-US' => 'Español (Estados Unidos)',
        'es-GT' => 'Español (Guatemala)',
        'es-HN' => 'Español (Honduras)',
        'es-MX' => 'Español (México)',
        'es-NI' => 'Español (Nicaragua)',
        'es-PA' => 'Español (Panamá)',
        'es-PY' => 'Español (Paraguay)',
        'es-PE' => 'Español (Perú)',
        'es-PR' => 'Español (Puerto Rico)',
        'es-VE' => 'Español (Republica Bolivariana de Venezuela)',
        'es-DO' => 'Español (República Dominicana)',
        'es-UY' => 'Español (Uruguay)',
        'eu-ES' => 'euskara (euskara)',
        'fil-PH' => 'Filipino (Pilipinas)',
        'fo-FO' => 'føroyskt (Føroyar)',
        'fr-BE' => 'français (Belgique)',
        'fr-CA' => 'français (Canada)',
        'fr-FR' => 'français (France)',
        'fr-LU' => 'français (Luxembourg)',
        'fr-MC' => 'français (Principauté de Monaco)',
        'fr-CH' => 'français (Suisse)',
        'fy-NL' => 'Frysk (Nederlân)',
        'ga-IE' => 'Gaeilge (Éire)',
        'gd-GB' => 'Gàidhlig (An Rìoghachd Aonaichte)',
        'gl-ES' => 'galego (galego)',
        'ha-Latn-NG' => 'Hausa (Nigeria)',
        'hsb-DE' => 'hornjoserbšćina (Němska)',
        'hr-BA' => 'hrvatski (Bosna i Hercegovina)',
        'hr-HR' => 'hrvatski (Hrvatska)',
        'ig-NG' => 'Igbo (Nigeria)',
        'iu-Latn-CA' => 'Inuktitut (Kanatami)',
        'xh-ZA' => 'isiXhosa (uMzantsi Afrika)',
        'zu-ZA' => 'isiZulu (iNingizimu Afrika)',
        'is-IS' => 'íslenska (Ísland)',
        'it-IT' => 'italiano (Italia)',
        'it-CH' => 'italiano (Svizzera)',
        'smj-SE' => 'julevusámegiella (Svierik)',
        'smj-NO' => 'julevusámegiella (Vuodna)',
        'kl-GL' => 'kalaallisut (Kalaallit Nunaat)',
        'moh-CA' => 'Kanien\'kéha',
        'qut-GT' => 'K\'iche (Guatemala)',
        'rw-RW' => 'Kinyarwanda (Rwanda)',
        'sw-KE' => 'Kiswahili (Kenya)',
        'lv-LV' => 'latviešu (Latvija)',
        'lb-LU' => 'Lëtzebuergesch (Luxembourg)',
        'lt-LT' => 'lietuvių (Lietuva)',
        'hu-HU' => 'magyar (Magyarország)',
        'mt-MT' => 'Malti (Malta)',
        'arn-CL' => 'Mapudungun (Chile)',
        'nl-BE' => 'Nederlands (België)',
        'nl-NL' => 'Nederlands (Nederland)',
        'nb-NO' => 'norsk, bokmål (Norge)',
        'nn-NO' => 'norsk, nynorsk (Noreg)',
        'oc-FR' => 'Occitan (França)',
        'pl-PL' => 'polski (Polska)',
        'pt-BR' => 'Português (Brasil)',
        'pt-PT' => 'português (Portugal)',
        'mi-NZ' => 'Reo Māori (Aotearoa)',
        'ro-RO' => 'română (România)',
        'rm-CH' => 'Rumantsch (Svizra)',
        'quz-EC' => 'runasimi (Ecuador)',
        'quz-PE' => 'runasimi (Piruw)',
        'quz-BO' => 'runasimi (Qullasuyu)',
        'sms-FI' => 'sääm´ǩiõll (Lää´ddjânnam)',
        'smn-FI' => 'sämikielâ (Suomâ)',
        'nso-ZA' => 'Sesotho sa Leboa (Afrika Borwa)',
        'tn-ZA' => 'Setswana (Aforika Borwa)',
        'sq-AL' => 'shqipe (Shqipëria)',
        'sk-SK' => 'slovenčina (Slovenská republika)',
        'sl-SI' => 'slovenski (Slovenija)',
        'sr-Latn-BA' => 'srpski (Bosna i Hercegovina)',
        'sr-Latn-ME' => 'srpski (Crna Gora)',
        'sr-Latn-CS' => 'srpski (Srbija i Crna Gora (Prethodno))',
        'sr-Latn-RS' => 'srpski (Srbija)',
        'fi-FI' => 'suomi (Suomi)',
        'sv-FI' => 'svenska (Finland)',
        'sv-SE' => 'svenska (Sverige)',
        'tzm-Latn-DZ' => 'Tamazight (Djazaïr)',
        'vi-VN' => 'Tiếng Việt (Việt Nam)',
        'tr-TR' => 'Türkçe (Türkiye)',
        'tk-TM' => 'türkmençe (Türkmenistan)',
        'uz-Latn-UZ' => 'U\'zbek (U\'zbekiston Respublikasi)',
        'wo-SN' => 'Wolof (Sénégal)',
        'yo-NG' => 'Yoruba (Nigeria)',
        'el-GR' => 'Ελληνικά (Ελλάδα)',
        'az-Cyrl-AZ' => 'Азәрбајҹан (Азәрбајҹан)',
        'ba-RU' => 'Башҡорт (Россия)',
        'be-BY' => 'Беларускі (Беларусь)',
        'bs-Cyrl-BA' => 'босански (Босна и Херцеговина)',
        'bg-BG' => 'български (България)',
        'ky-KG' => 'Кыргыз (Кыргызстан)',
        'kk-KZ' => 'Қазақ (Қазақстан)',
        'mk-MK' => 'македонски јазик (Македонија)',
        'mn-MN' => 'Монгол хэл (Монгол улс)',
        'ru-RU' => 'русский (Россия)',
        'sah-RU' => 'саха (Россия)',
        'sr-Cyrl-BA' => 'српски (Босна и Херцеговина)',
        'sr-Cyrl-CS' => 'српски (Србија и Црна Гора (Претходно))',
        'sr-Cyrl-RS' => 'српски (Србија)',
        'sr-Cyrl-ME' => 'српски (Црна Гора)',
        'tt-RU' => 'Татар (Россия)',
        'tg-Cyrl-TJ' => 'Тоҷикӣ (Тоҷикистон)',
        'uz-Cyrl-UZ' => 'Ўзбек (Ўзбекистон)',
        'uk-UA' => 'українська (Україна)',
        'hy-AM' => 'Հայերեն (Հայաստան)',
        'ka-GE' => 'ქართული (საქართველო)',
        'he-IL' => 'עברית (ישראל)',
        'ur-PK' => 'اُردو (پاکستان)',
        'ar-JO' => 'العربية (الأردن)',
        'ar-AE' => 'العربية (الإمارات العربية المتحدة)',
        'ar-BH' => 'العربية (البحرين)',
        'ar-DZ' => 'العربية (الجزائر)',
        'ar-IQ' => 'العربية (العراق)',
        'ar-KW' => 'العربية (الكويت)',
        'ar-SA' => 'العربية (المملكة العربية السعودية)',
        'ar-MA' => 'العربية (المملكة المغربية)',
        'ar-YE' => 'العربية (اليمن)',
        'ar-TN' => 'العربية (تونس)',
        'ar-SY' => 'العربية (سوريا)',
        'ar-OM' => 'العربية (عمان)',
        'ar-QA' => 'العربية (قطر)',
        'ar-LB' => 'العربية (لبنان)',
        'ar-LY' => 'العربية (ليبيا)',
        'ar-EG' => 'العربية (مصر)',
        'ps-AF' => 'پښتو (افغانستان)',
        'prs-AF' => 'درى (افغانستان)',
        'fa-IR' => 'فارسى (ایران)',
        'ug-CN' => 'ئۇيغۇرچە (جۇڭخۇا خەلق جۇمھۇرىيىتى)',
        'kok-IN' => 'कोंकणी (भारत)',
        'ne-NP' => 'नेपाली (नेपाल)',
        'mr-IN' => 'मराठी (भारत)',
        'sa-IN' => 'संस्कृत (भारतम्)',
        'hi-IN' => 'हिंदी (भारत)',
        'th-TH' => 'ไทย (ไทย)',
        'ko-KR' => '한국어 (대한민국)',
        'zh-CN' => '中文(中华人民共和国)',
        'zh-TW' => '中文(台灣)',
        'zh-SG' => '中文(新加坡)',
        'zh-MO' => '中文(澳門特別行政區)',
        'zh-HK' => '中文(香港特別行政區)',
        'ja-JP' => '日本語 (日本)');
    return $locales;
}

function get_display_name($locale) {
    $locales = get_locales();
    if (!in_array($locale, $locales))
        return "";
    return $locales[$locale];
}

?>
