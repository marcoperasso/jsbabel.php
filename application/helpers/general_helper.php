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
        'ar-SA' => 'العربية (المملكة العربية السعودية)',
        'bg-BG' => 'български (България)',
        'ca-ES' => 'català (català)',
        'zh-TW' => '中文(台灣)',
        'cs-CZ' => 'čeština (Česká republika)',
        'da-DK' => 'dansk (Danmark)',
        'de-DE' => 'Deutsch (Deutschland)',
        'el-GR' => 'Ελληνικά (Ελλάδα)',
        'en-US' => 'English (United States)',
        'fi-FI' => 'suomi (Suomi)',
        'fr-FR' => 'français (France)',
        'he-IL' => 'עברית (ישראל)',
        'hu-HU' => 'magyar (Magyarország)',
        'is-IS' => 'íslenska (Ísland)',
        'it-IT' => 'italiano (Italia)',
        'ja-JP' => '日本語 (日本)',
        'ko-KR' => '한국어 (대한민국)',
        'nl-NL' => 'Nederlands (Nederland)',
        'nb-NO' => 'norsk, bokmål (Norge)',
        'pl-PL' => 'polski (Polska)',
        'pt-BR' => 'Português (Brasil)',
        'rm-CH' => 'Rumantsch (Svizra)',
        'ro-RO' => 'română (România)',
        'ru-RU' => 'русский (Россия)',
        'hr-HR' => 'hrvatski (Hrvatska)',
        'sk-SK' => 'slovenčina (Slovenská republika)',
        'sq-AL' => 'shqipe (Shqipëria)',
        'sv-SE' => 'svenska (Sverige)',
        'th-TH' => 'ไทย (ไทย)',
        'tr-TR' => 'Türkçe (Türkiye)',
        'ur-PK' => 'اُردو (پاکستان)',
        'id-ID' => 'Bahasa Indonesia (Indonesia)',
        'uk-UA' => 'українська (Україна)',
        'be-BY' => 'Беларускі (Беларусь)',
        'sl-SI' => 'slovenski (Slovenija)',
        'et-EE' => 'eesti (Eesti)',
        'lv-LV' => 'latviešu (Latvija)',
        'lt-LT' => 'lietuvių (Lietuva)',
        'tg-Cyrl-TJ' => 'Тоҷикӣ (Тоҷикистон)',
        'fa-IR' => 'فارسى (ایران)',
        'vi-VN' => 'Tiếng Việt (Việt Nam)',
        'hy-AM' => 'Հայերեն (Հայաստան)',
        'az-Latn-AZ' => 'Azərbaycan­ılı (Azərbaycan)',
        'eu-ES' => 'euskara (euskara)',
        'hsb-DE' => 'hornjoserbšćina (Němska)',
        'mk-MK' => 'македонски јазик (Македонија)',
        'tn-ZA' => 'Setswana (Aforika Borwa)',
        'xh-ZA' => 'isiXhosa (uMzantsi Afrika)',
        'zu-ZA' => 'isiZulu (iNingizimu Afrika)',
        'af-ZA' => 'Afrikaans (Suid Afrika)',
        'ka-GE' => 'ქართული (საქართველო)',
        'fo-FO' => 'føroyskt (Føroyar)',
        'hi-IN' => 'हिंदी (भारत)',
        'mt-MT' => 'Malti (Malta)',
        'se-NO' => 'davvisámegiella (Norga)',
        'ms-MY' => 'Bahasa Melayu (Malaysia)',
        'kk-KZ' => 'Қазақ (Қазақстан)',
        'ky-KG' => 'Кыргыз (Кыргызстан)',
        'sw-KE' => 'Kiswahili (Kenya)',
        'tk-TM' => 'türkmençe (Türkmenistan)',
        'uz-Latn-UZ' => 'U\'zbek (U\'zbekiston Respublikasi)',
        'tt-RU' => 'Татар (Россия)',
        'bn-IN' => 'বাংলা (ভারত)',
        'pa-IN' => 'ਪੰਜਾਬੀ (ਭਾਰਤ)',
        'gu-IN' => 'ગુજરાતી (ભારત)',
        'or-IN' => 'ଓଡ଼ିଆ (ଭାରତ)',
        'ta-IN' => 'தமிழ் (இந்தியா)',
        'te-IN' => 'తెలుగు (భారత దేశం)',
        'kn-IN' => 'ಕನ್ನಡ (ಭಾರತ)',
        'ml-IN' => 'മലയാളം (ഭാരതം)',
        'as-IN' => 'অসমীয়া (ভাৰত)',
        'mr-IN' => 'मराठी (भारत)',
        'sa-IN' => 'संस्कृत (भारतम्)',
        'mn-MN' => 'Монгол хэл (Монгол улс)',
        'bo-CN' => 'བོད་ཡིག (ཀྲུང་ཧྭ་མི་དམངས་སྤྱི་མཐུན་རྒྱལ་ཁབ།)',
        'cy-GB' => 'Cymraeg (y Deyrnas Unedig)',
        'km-KH' => 'ខ្មែរ (កម្ពុជា)',
        'lo-LA' => 'ລາວ (ສ.ປ.ປ. ລາວ)',
        'gl-ES' => 'galego (galego)',
        'kok-IN' => 'कोंकणी (भारत)',
        'syr-SY' => 'ܣܘܪܝܝܐ (سوريا)',
        'si-LK' => 'සිංහල (ශ්‍රී ලංකා)',
        'iu-Cans-CA' => 'ᐃᓄᒃᑎᑐᑦ (ᑲᓇᑕᒥ)',
        'am-ET' => 'አማርኛ (ኢትዮጵያ)',
        'ne-NP' => 'नेपाली (नेपाल)',
        'fy-NL' => 'Frysk (Nederlân)',
        'ps-AF' => 'پښتو (افغانستان)',
        'fil-PH' => 'Filipino (Pilipinas)',
        'dv-MV' => 'ދިވެހިބަސް (ދިވެހި ރާއްޖެ)',
        'ha-Latn-NG' => 'Hausa (Nigeria)',
        'yo-NG' => 'Yoruba (Nigeria)',
        'quz-BO' => 'runasimi (Qullasuyu)',
        'nso-ZA' => 'Sesotho sa Leboa (Afrika Borwa)',
        'ba-RU' => 'Башҡорт (Россия)',
        'lb-LU' => 'Lëtzebuergesch (Luxembourg)',
        'kl-GL' => 'kalaallisut (Kalaallit Nunaat)',
        'ig-NG' => 'Igbo (Nigeria)',
        'ii-CN' => 'ꆈꌠꁱꂷ (ꍏꉸꏓꂱꇭꉼꇩ)',
        'arn-CL' => 'Mapudungun (Chile)',
        'moh-CA' => 'Kanien\'kéha',
        'br-FR' => 'brezhoneg (Frañs)',
        'ug-CN' => 'ئۇيغۇرچە (جۇڭخۇا خەلق جۇمھۇرىيىتى)',
        'mi-NZ' => 'Reo Māori (Aotearoa)',
        'oc-FR' => 'Occitan (França)',
        'co-FR' => 'Corsu (France)',
        'gsw-FR' => 'Elsässisch (Frànkrisch)',
        'sah-RU' => 'саха (Россия)',
        'qut-GT' => 'K\'iche (Guatemala)',
        'rw-RW' => 'Kinyarwanda (Rwanda)',
        'wo-SN' => 'Wolof (Sénégal)',
        'prs-AF' => 'درى (افغانستان)',
        'gd-GB' => 'Gàidhlig (An Rìoghachd Aonaichte)',
        'ar-IQ' => 'العربية (العراق)',
        'zh-CN' => '中文(中华人民共和国)',
        'de-CH' => 'Deutsch (Schweiz)',
        'en-GB' => 'English (United Kingdom)',
        'es-MX' => 'Español (México)',
        'fr-BE' => 'français (Belgique)',
        'it-CH' => 'italiano (Svizzera)',
        'nl-BE' => 'Nederlands (België)',
        'nn-NO' => 'norsk, nynorsk (Noreg)',
        'pt-PT' => 'português (Portugal)',
        'sr-Latn-CS' => 'srpski (Srbija i Crna Gora (Prethodno))',
        'sv-FI' => 'svenska (Finland)',
        'az-Cyrl-AZ' => 'Азәрбајҹан (Азәрбајҹан)',
        'dsb-DE' => 'dolnoserbšćina (Nimska)',
        'se-SE' => 'davvisámegiella (Ruoŧŧa)',
        'ga-IE' => 'Gaeilge (Éire)',
        'ms-BN' => 'Bahasa Melayu (Brunei Darussalam)',
        'uz-Cyrl-UZ' => 'Ўзбек (Ўзбекистон)',
        'bn-BD' => 'বাংলা (বাংলাদেশ)',
        'mn-Mong-CN' => 'ᠮᠤᠨᠭᠭᠤᠯ ᠬᠡᠯᠡ (ᠪᠦᠭᠦᠳᠡ ᠨᠠᠢᠷᠠᠮᠳᠠᠬᠤ ᠳᠤᠮᠳᠠᠳᠤ ᠠᠷᠠᠳ ᠣᠯᠣᠰ)',
        'iu-Latn-CA' => 'Inuktitut (Kanatami)',
        'tzm-Latn-DZ' => 'Tamazight (Djazaïr)',
        'quz-EC' => 'runasimi (Ecuador)',
        'ar-EG' => 'العربية (مصر)',
        'zh-HK' => '中文(香港特別行政區)',
        'de-AT' => 'Deutsch (Österreich)',
        'en-AU' => 'English (Australia)',
        'es-ES' => 'Español (España, alfabetización internacional)',
        'fr-CA' => 'français (Canada)',
        'sr-Cyrl-CS' => 'српски (Србија и Црна Гора (Претходно))',
        'se-FI' => 'davvisámegiella (Suopma)',
        'quz-PE' => 'runasimi (Piruw)',
        'ar-LY' => 'العربية (ليبيا)',
        'zh-SG' => '中文(新加坡)',
        'de-LU' => 'Deutsch (Luxemburg)',
        'en-CA' => 'English (Canada)',
        'es-GT' => 'Español (Guatemala)',
        'fr-CH' => 'français (Suisse)',
        'hr-BA' => 'hrvatski (Bosna i Hercegovina)',
        'smj-NO' => 'julevusámegiella (Vuodna)',
        'ar-DZ' => 'العربية (الجزائر)',
        'zh-MO' => '中文(澳門特別行政區)',
        'de-LI' => 'Deutsch (Liechtenstein)',
        'en-NZ' => 'English (New Zealand)',
        'es-CR' => 'Español (Costa Rica)',
        'fr-LU' => 'français (Luxembourg)',
        'bs-Latn-BA' => 'bosanski (Bosna i Hercegovina)',
        'smj-SE' => 'julevusámegiella (Svierik)',
        'ar-MA' => 'العربية (المملكة المغربية)',
        'en-IE' => 'English (Ireland)',
        'es-PA' => 'Español (Panamá)',
        'fr-MC' => 'français (Principauté de Monaco)',
        'sr-Latn-BA' => 'srpski (Bosna i Hercegovina)',
        'sma-NO' => 'åarjelsaemiengiele (Nöörje)',
        'ar-TN' => 'العربية (تونس)',
        'en-ZA' => 'English (South Africa)',
        'es-DO' => 'Español (República Dominicana)',
        'sr-Cyrl-BA' => 'српски (Босна и Херцеговина)',
        'sma-SE' => 'åarjelsaemiengiele (Sveerje)',
        'ar-OM' => 'العربية (عمان)',
        'en-JM' => 'English (Jamaica)',
        'es-VE' => 'Español (Republica Bolivariana de Venezuela)',
        'bs-Cyrl-BA' => 'босански (Босна и Херцеговина)',
        'sms-FI' => 'sääm´ǩiõll (Lää´ddjânnam)',
        'ar-YE' => 'العربية (اليمن)',
        'en-029' => 'English (Caribbean)',
        'es-CO' => 'Español (Colombia)',
        'sr-Latn-RS' => 'srpski (Srbija)',
        'smn-FI' => 'sämikielâ (Suomâ)',
        'ar-SY' => 'العربية (سوريا)',
        'en-BZ' => 'English (Belize)',
        'es-PE' => 'Español (Perú)',
        'sr-Cyrl-RS' => 'српски (Србија)',
        'ar-JO' => 'العربية (الأردن)',
        'en-TT' => 'English (Trinidad y Tobago)',
        'es-AR' => 'Español (Argentina)',
        'sr-Latn-ME' => 'srpski (Crna Gora)',
        'ar-LB' => 'العربية (لبنان)',
        'en-ZW' => 'English (Zimbabwe)',
        'es-EC' => 'Español (Ecuador)',
        'sr-Cyrl-ME' => 'српски (Црна Гора)',
        'ar-KW' => 'العربية (الكويت)',
        'en-PH' => 'English (Philippines)',
        'es-CL' => 'Español (Chile)',
        'ar-AE' => 'العربية (الإمارات العربية المتحدة)',
        'es-UY' => 'Español (Uruguay)',
        'ar-BH' => 'العربية (البحرين)',
        'es-PY' => 'Español (Paraguay)',
        'ar-QA' => 'العربية (قطر)',
        'en-IN' => 'English (India)',
        'es-BO' => 'Español (Bolivia)',
        'en-MY' => 'English (Malaysia)',
        'es-SV' => 'Español (El Salvador)',
        'en-SG' => 'English (Singapore)',
        'es-HN' => 'Español (Honduras)',
        'es-NI' => 'Español (Nicaragua)',
        'es-PR' => 'Español (Puerto Rico)',
        'es-US' => 'Español (Estados Unidos)');

    return $locales;
}

function get_display_name($locale) {
    $locales = get_locales();
    if (!in_array($locale, $locales))
        return "";
    return $locales[$locale];
}

?>
