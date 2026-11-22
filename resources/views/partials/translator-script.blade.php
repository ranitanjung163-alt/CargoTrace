<!-- Google Translate Element & Dynamic Multilingual Engine (Semua Negara 130+ Bahasa) -->
<div id="google_translate_element" style="position:absolute; left:-9999px; top:-9999px; opacity:0; pointer-events:none;"></div>

<script type="text/javascript">
    const WC360_ALL_LANGS = [
    {
        "code": "en",
        "flag": "🇺🇸",
        "name": "English",
        "native": "English",
        "country": "United States \/ UK \/ Global"
    },
    {
        "code": "id",
        "flag": "🇮🇩",
        "name": "Indonesian",
        "native": "Bahasa Indonesia",
        "country": "Indonesia"
    },
    {
        "code": "zh-CN",
        "flag": "🇨🇳",
        "name": "Chinese (Simplified)",
        "native": "简体中文",
        "country": "China"
    },
    {
        "code": "zh-TW",
        "flag": "🇹🇼",
        "name": "Chinese (Traditional)",
        "native": "繁體中文",
        "country": "Taiwan \/ Hong Kong"
    },
    {
        "code": "ja",
        "flag": "🇯🇵",
        "name": "Japanese",
        "native": "日本語",
        "country": "Japan"
    },
    {
        "code": "ar",
        "flag": "🇸🇦",
        "name": "Arabic",
        "native": "العربية",
        "country": "Saudi Arabia \/ UAE \/ Arab World"
    },
    {
        "code": "es",
        "flag": "🇪🇸",
        "name": "Spanish",
        "native": "Español",
        "country": "Spain \/ Mexico \/ Latin America"
    },
    {
        "code": "fr",
        "flag": "🇫🇷",
        "name": "French",
        "native": "Français",
        "country": "France \/ Canada \/ Belgium"
    },
    {
        "code": "de",
        "flag": "🇩🇪",
        "name": "German",
        "native": "Deutsch",
        "country": "Germany \/ Austria \/ Switzerland"
    },
    {
        "code": "ru",
        "flag": "🇷🇺",
        "name": "Russian",
        "native": "Русский",
        "country": "Russia \/ CIS"
    },
    {
        "code": "pt",
        "flag": "🇧🇷",
        "name": "Portuguese",
        "native": "Português",
        "country": "Brazil \/ Portugal"
    },
    {
        "code": "ko",
        "flag": "🇰🇷",
        "name": "Korean",
        "native": "한국어",
        "country": "South Korea"
    },
    {
        "code": "it",
        "flag": "🇮🇹",
        "name": "Italian",
        "native": "Italiano",
        "country": "Italy \/ Switzerland"
    },
    {
        "code": "nl",
        "flag": "🇳🇱",
        "name": "Dutch",
        "native": "Nederlands",
        "country": "Netherlands \/ Belgium"
    },
    {
        "code": "tr",
        "flag": "🇹🇷",
        "name": "Turkish",
        "native": "Türkçe",
        "country": "Turkey \/ Türkiye"
    },
    {
        "code": "hi",
        "flag": "🇮🇳",
        "name": "Hindi",
        "native": "हिन्दी",
        "country": "India"
    },
    {
        "code": "th",
        "flag": "🇹🇭",
        "name": "Thai",
        "native": "ไทย",
        "country": "Thailand"
    },
    {
        "code": "vi",
        "flag": "🇻🇳",
        "name": "Vietnamese",
        "native": "Tiếng Việt",
        "country": "Vietnam"
    },
    {
        "code": "tl",
        "flag": "🇵🇭",
        "name": "Filipino (Tagalog)",
        "native": "Filipino",
        "country": "Philippines"
    },
    {
        "code": "ms",
        "flag": "🇲🇾",
        "name": "Malay",
        "native": "Bahasa Melayu",
        "country": "Malaysia \/ Singapore \/ Brunei"
    },
    {
        "code": "pl",
        "flag": "🇵🇱",
        "name": "Polish",
        "native": "Polski",
        "country": "Poland"
    },
    {
        "code": "uk",
        "flag": "🇺🇦",
        "name": "Ukrainian",
        "native": "Українська",
        "country": "Ukraine"
    },
    {
        "code": "sv",
        "flag": "🇸🇪",
        "name": "Swedish",
        "native": "Svenska",
        "country": "Sweden"
    },
    {
        "code": "no",
        "flag": "🇳🇴",
        "name": "Norwegian",
        "native": "Norsk",
        "country": "Norway"
    },
    {
        "code": "da",
        "flag": "🇩🇰",
        "name": "Danish",
        "native": "Dansk",
        "country": "Denmark"
    },
    {
        "code": "fi",
        "flag": "🇫🇮",
        "name": "Finnish",
        "native": "Suomi",
        "country": "Finland"
    },
    {
        "code": "el",
        "flag": "🇬🇷",
        "name": "Greek",
        "native": "Ελληνικά",
        "country": "Greece \/ Cyprus"
    },
    {
        "code": "cs",
        "flag": "🇨🇿",
        "name": "Czech",
        "native": "Čeština",
        "country": "Czech Republic"
    },
    {
        "code": "hu",
        "flag": "🇭🇺",
        "name": "Hungarian",
        "native": "Magyar",
        "country": "Hungary"
    },
    {
        "code": "ro",
        "flag": "🇷🇴",
        "name": "Romanian",
        "native": "Română",
        "country": "Romania \/ Moldova"
    },
    {
        "code": "bg",
        "flag": "🇧🇬",
        "name": "Bulgarian",
        "native": "Български",
        "country": "Bulgaria"
    },
    {
        "code": "sk",
        "flag": "🇸🇰",
        "name": "Slovak",
        "native": "Slovenčina",
        "country": "Slovakia"
    },
    {
        "code": "hr",
        "flag": "🇭🇷",
        "name": "Croatian",
        "native": "Hrvatski",
        "country": "Croatia"
    },
    {
        "code": "sr",
        "flag": "🇷🇸",
        "name": "Serbian",
        "native": "Српски",
        "country": "Serbia"
    },
    {
        "code": "sl",
        "flag": "🇸🇮",
        "name": "Slovenian",
        "native": "Slovenščina",
        "country": "Slovenia"
    },
    {
        "code": "he",
        "flag": "🇮🇱",
        "name": "Hebrew",
        "native": "עברית",
        "country": "Israel"
    },
    {
        "code": "fa",
        "flag": "🇮🇷",
        "name": "Persian (Farsi)",
        "native": "فارسی",
        "country": "Iran \/ Afghanistan"
    },
    {
        "code": "ur",
        "flag": "🇵🇰",
        "name": "Urdu",
        "native": "اردو",
        "country": "Pakistan \/ India"
    },
    {
        "code": "bn",
        "flag": "🇧🇩",
        "name": "Bengali",
        "native": "বাংলা",
        "country": "Bangladesh \/ India"
    },
    {
        "code": "ta",
        "flag": "🇮🇳",
        "name": "Tamil",
        "native": "தமிழ்",
        "country": "India \/ Sri Lanka \/ Singapore"
    },
    {
        "code": "te",
        "flag": "🇮🇳",
        "name": "Telugu",
        "native": "తెలుగు",
        "country": "India"
    },
    {
        "code": "mr",
        "flag": "🇮🇳",
        "name": "Marathi",
        "native": "मराठी",
        "country": "India"
    },
    {
        "code": "gu",
        "flag": "🇮🇳",
        "name": "Gujarati",
        "native": "ગુજરાતી",
        "country": "India"
    },
    {
        "code": "kn",
        "flag": "🇮🇳",
        "name": "Kannada",
        "native": "ಕನ್ನಡ",
        "country": "India"
    },
    {
        "code": "ml",
        "flag": "🇮🇳",
        "name": "Malayalam",
        "native": "മലയാളം",
        "country": "India"
    },
    {
        "code": "pa",
        "flag": "🇮🇳",
        "name": "Punjabi",
        "native": "ਪੰਜਾਬੀ",
        "country": "India \/ Pakistan"
    },
    {
        "code": "my",
        "flag": "🇲🇲",
        "name": "Burmese (Myanmar)",
        "native": "မြန်မာစာ",
        "country": "Myanmar \/ Burma"
    },
    {
        "code": "km",
        "flag": "🇰🇭",
        "name": "Khmer",
        "native": "ភាសាខ្មែរ",
        "country": "Cambodia"
    },
    {
        "code": "lo",
        "flag": "🇱🇦",
        "name": "Lao",
        "native": "ພາສາລາວ",
        "country": "Laos"
    },
    {
        "code": "ne",
        "flag": "🇳🇵",
        "name": "Nepali",
        "native": "नेपाली",
        "country": "Nepal"
    },
    {
        "code": "si",
        "flag": "🇱🇰",
        "name": "Sinhala",
        "native": "සිංහල",
        "country": "Sri Lanka"
    },
    {
        "code": "mn",
        "flag": "🇲🇳",
        "name": "Mongolian",
        "native": "Монгол хэл",
        "country": "Mongolia"
    },
    {
        "code": "ka",
        "flag": "🇬🇪",
        "name": "Georgian",
        "native": "ქართული",
        "country": "Georgia"
    },
    {
        "code": "hy",
        "flag": "🇦🇲",
        "name": "Armenian",
        "native": "Հայերեն",
        "country": "Armenia"
    },
    {
        "code": "az",
        "flag": "🇦🇿",
        "name": "Azerbaijani",
        "native": "Azərbaycan",
        "country": "Azerbaijan"
    },
    {
        "code": "kk",
        "flag": "🇰🇿",
        "name": "Kazakh",
        "native": "Қазақ тілі",
        "country": "Kazakhstan"
    },
    {
        "code": "uz",
        "flag": "🇺🇿",
        "name": "Uzbek",
        "native": "Oʻzbekcha",
        "country": "Uzbekistan"
    },
    {
        "code": "ky",
        "flag": "🇰🇬",
        "name": "Kyrgyz",
        "native": "Кыргызча",
        "country": "Kyrgyzstan"
    },
    {
        "code": "tg",
        "flag": "🇹🇯",
        "name": "Tajik",
        "native": "Тоҷикӣ",
        "country": "Tajikistan"
    },
    {
        "code": "tk",
        "flag": "🇹🇲",
        "name": "Turkmen",
        "native": "Türkmençe",
        "country": "Turkmenistan"
    },
    {
        "code": "ps",
        "flag": "🇦🇫",
        "name": "Pashto",
        "native": "پښتو",
        "country": "Afghanistan"
    },
    {
        "code": "sw",
        "flag": "🇰🇪",
        "name": "Swahili",
        "native": "Kiswahili",
        "country": "Kenya \/ Tanzania \/ East Africa"
    },
    {
        "code": "am",
        "flag": "🇪🇹",
        "name": "Amharic",
        "native": "አማርኛ",
        "country": "Ethiopia"
    },
    {
        "code": "ha",
        "flag": "🇳🇬",
        "name": "Hausa",
        "native": "Hausa",
        "country": "Nigeria \/ Niger"
    },
    {
        "code": "yo",
        "flag": "🇳🇬",
        "name": "Yoruba",
        "native": "Yorùbá",
        "country": "Nigeria \/ Benin"
    },
    {
        "code": "ig",
        "flag": "🇳🇬",
        "name": "Igbo",
        "native": "Igbo",
        "country": "Nigeria"
    },
    {
        "code": "zu",
        "flag": "🇿🇦",
        "name": "Zulu",
        "native": "isiZulu",
        "country": "South Africa"
    },
    {
        "code": "xh",
        "flag": "🇿🇦",
        "name": "Xhosa",
        "native": "isiXhosa",
        "country": "South Africa"
    },
    {
        "code": "af",
        "flag": "🇿🇦",
        "name": "Afrikaans",
        "native": "Afrikaans",
        "country": "South Africa \/ Namibia"
    },
    {
        "code": "so",
        "flag": "🇸🇴",
        "name": "Somali",
        "native": "Soomaali",
        "country": "Somalia \/ Djibouti"
    },
    {
        "code": "mg",
        "flag": "🇲🇬",
        "name": "Malagasy",
        "native": "Malagasy",
        "country": "Madagascar"
    },
    {
        "code": "rw",
        "flag": "🇷🇼",
        "name": "Kinyarwanda",
        "native": "Ikinyarwanda",
        "country": "Rwanda"
    },
    {
        "code": "sn",
        "flag": "🇿🇼",
        "name": "Shona",
        "native": "chiShona",
        "country": "Zimbabwe"
    },
    {
        "code": "ny",
        "flag": "🇲🇼",
        "name": "Chichewa",
        "native": "Chichewa",
        "country": "Malawi"
    },
    {
        "code": "st",
        "flag": "🇱🇸",
        "name": "Sesotho",
        "native": "Sesotho",
        "country": "Lesotho \/ South Africa"
    },
    {
        "code": "om",
        "flag": "🇪🇹",
        "name": "Oromo",
        "native": "Afaan Oromoo",
        "country": "Ethiopia \/ Kenya"
    },
    {
        "code": "ti",
        "flag": "🇪🇷",
        "name": "Tigrinya",
        "native": "ትግርኛ",
        "country": "Eritrea \/ Ethiopia"
    },
    {
        "code": "ln",
        "flag": "🇨🇩",
        "name": "Lingala",
        "native": "Lingála",
        "country": "DR Congo"
    },
    {
        "code": "lg",
        "flag": "🇺🇬",
        "name": "Luganda",
        "native": "Luganda",
        "country": "Uganda"
    },
    {
        "code": "ak",
        "flag": "🇬🇭",
        "name": "Akan (Twi)",
        "native": "Twi",
        "country": "Ghana"
    },
    {
        "code": "ee",
        "flag": "🇬🇭",
        "name": "Ewe",
        "native": "Eʋegbe",
        "country": "Ghana \/ Togo"
    },
    {
        "code": "bm",
        "flag": "🇲🇱",
        "name": "Bambara",
        "native": "Bamanankan",
        "country": "Mali"
    },
    {
        "code": "kri",
        "flag": "🇸🇱",
        "name": "Krio",
        "native": "Krio",
        "country": "Sierra Leone"
    },
    {
        "code": "ts",
        "flag": "🇿🇦",
        "name": "Tsonga",
        "native": "Xitsonga",
        "country": "South Africa \/ Mozambique"
    },
    {
        "code": "nso",
        "flag": "🇿🇦",
        "name": "Sepedi",
        "native": "Sepedi",
        "country": "South Africa"
    },
    {
        "code": "sq",
        "flag": "🇦🇱",
        "name": "Albanian",
        "native": "Shqip",
        "country": "Albania \/ Kosovo"
    },
    {
        "code": "mk",
        "flag": "🇲🇰",
        "name": "Macedonian",
        "native": "Македонски",
        "country": "North Macedonia"
    },
    {
        "code": "bs",
        "flag": "🇧🇦",
        "name": "Bosnian",
        "native": "Bosanski",
        "country": "Bosnia and Herzegovina"
    },
    {
        "code": "is",
        "flag": "🇮🇸",
        "name": "Icelandic",
        "native": "Íslenska",
        "country": "Iceland"
    },
    {
        "code": "ga",
        "flag": "🇮🇪",
        "name": "Irish",
        "native": "Gaeilge",
        "country": "Ireland"
    },
    {
        "code": "cy",
        "flag": "🏴󠁧󠁢󠁷󠁬󠁳󠁿",
        "name": "Welsh",
        "native": "Cymraeg",
        "country": "Wales \/ UK"
    },
    {
        "code": "gd",
        "flag": "🏴󠁧󠁢󠁳󠁣󠁴󠁿",
        "name": "Scots Gaelic",
        "native": "Gàidhlig",
        "country": "Scotland \/ UK"
    },
    {
        "code": "mt",
        "flag": "🇲🇹",
        "name": "Maltese",
        "native": "Malti",
        "country": "Malta"
    },
    {
        "code": "et",
        "flag": "🇪🇪",
        "name": "Estonian",
        "native": "Eesti",
        "country": "Estonia"
    },
    {
        "code": "lv",
        "flag": "🇱🇻",
        "name": "Latvian",
        "native": "Latviešu",
        "country": "Latvia"
    },
    {
        "code": "lt",
        "flag": "🇱🇹",
        "name": "Lithuanian",
        "native": "Lietuvių",
        "country": "Lithuania"
    },
    {
        "code": "lb",
        "flag": "🇱🇺",
        "name": "Luxembourgish",
        "native": "Lëtzebuergesch",
        "country": "Luxembourg"
    },
    {
        "code": "eu",
        "flag": "🇪🇸",
        "name": "Basque",
        "native": "Euskara",
        "country": "Basque \/ Spain"
    },
    {
        "code": "gl",
        "flag": "🇪🇸",
        "name": "Galician",
        "native": "Galego",
        "country": "Galicia \/ Spain"
    },
    {
        "code": "ca",
        "flag": "🇦🇩",
        "name": "Catalan",
        "native": "Català",
        "country": "Catalonia \/ Spain \/ Andorra"
    },
    {
        "code": "co",
        "flag": "🇫🇷",
        "name": "Corsican",
        "native": "Corsu",
        "country": "Corsica \/ France"
    },
    {
        "code": "fy",
        "flag": "🇳🇱",
        "name": "Frisian",
        "native": "Frysk",
        "country": "Frisia \/ Netherlands"
    },
    {
        "code": "yi",
        "flag": "🇮🇱",
        "name": "Yiddish",
        "native": "ייִדיש",
        "country": "Yiddish"
    },
    {
        "code": "la",
        "flag": "🇻🇦",
        "name": "Latin",
        "native": "Latina",
        "country": "Vatican \/ Latin"
    },
    {
        "code": "eo",
        "flag": "🌐",
        "name": "Esperanto",
        "native": "Esperanto",
        "country": "International"
    },
    {
        "code": "haw",
        "flag": "🇺🇸",
        "name": "Hawaiian",
        "native": "ʻŌlelo Hawaiʻi",
        "country": "Hawaii \/ USA"
    },
    {
        "code": "sm",
        "flag": "🇼🇸",
        "name": "Samoan",
        "native": "Gagana Sāmoa",
        "country": "Samoa"
    },
    {
        "code": "mi",
        "flag": "🇳🇿",
        "name": "Maori",
        "native": "Te Reo Māori",
        "country": "New Zealand"
    },
    {
        "code": "ceb",
        "flag": "🇵🇭",
        "name": "Cebuano",
        "native": "Bisaya",
        "country": "Philippines"
    },
    {
        "code": "ilo",
        "flag": "🇵🇭",
        "name": "Ilocano",
        "native": "Ilokano",
        "country": "Philippines"
    },
    {
        "code": "jv",
        "flag": "🇮🇩",
        "name": "Javanese",
        "native": "Basa Jawa",
        "country": "Indonesia"
    },
    {
        "code": "su",
        "flag": "🇮🇩",
        "name": "Sundanese",
        "native": "Basa Sunda",
        "country": "Indonesia"
    },
    {
        "code": "ht",
        "flag": "🇭🇹",
        "name": "Haitian Creole",
        "native": "Kreyòl Ayisyen",
        "country": "Haiti"
    },
    {
        "code": "qu",
        "flag": "🇵🇪",
        "name": "Quechua",
        "native": "Runasimi",
        "country": "Peru \/ Bolivia \/ Ecuador"
    },
    {
        "code": "ay",
        "flag": "🇧🇴",
        "name": "Aymara",
        "native": "Aymar aru",
        "country": "Bolivia \/ Peru"
    },
    {
        "code": "gn",
        "flag": "🇵🇾",
        "name": "Guarani",
        "native": "Avañe'ẽ",
        "country": "Paraguay \/ Argentina"
    },
    {
        "code": "as",
        "flag": "🇮🇳",
        "name": "Assamese",
        "native": "অসমীয়া",
        "country": "India"
    },
    {
        "code": "bho",
        "flag": "🇮🇳",
        "name": "Bhojpuri",
        "native": "भोजपुरी",
        "country": "India \/ Nepal"
    },
    {
        "code": "doi",
        "flag": "🇮🇳",
        "name": "Dogri",
        "native": "डोगरी",
        "country": "India"
    },
    {
        "code": "gom",
        "flag": "🇮🇳",
        "name": "Konkani",
        "native": "कोंकणी",
        "country": "India"
    },
    {
        "code": "mai",
        "flag": "🇮🇳",
        "name": "Maithili",
        "native": "मैथिली",
        "country": "India \/ Nepal"
    },
    {
        "code": "mni-Mtei",
        "flag": "🇮🇳",
        "name": "Meiteilon (Manipuri)",
        "native": "ꯃꯩꯇꯩꯂꯣꯟ",
        "country": "India"
    },
    {
        "code": "lus",
        "flag": "🇮🇳",
        "name": "Mizo",
        "native": "Mizo ṭawng",
        "country": "India"
    },
    {
        "code": "or",
        "flag": "🇮🇳",
        "name": "Odia (Oriya)",
        "native": "ଓଡ଼ିଆ",
        "country": "India"
    },
    {
        "code": "sa",
        "flag": "🇮🇳",
        "name": "Sanskrit",
        "native": "संस्कृतम्",
        "country": "India"
    },
    {
        "code": "sd",
        "flag": "🇵🇰",
        "name": "Sindhi",
        "native": "سنڌي",
        "country": "Pakistan \/ India"
    },
    {
        "code": "dv",
        "flag": "🇲🇻",
        "name": "Dhivehi",
        "native": "ދިވެހި",
        "country": "Maldives"
    },
    {
        "code": "ku",
        "flag": "🇹🇷",
        "name": "Kurdish (Kurmanji)",
        "native": "Kurmancî",
        "country": "Kurdistan \/ Turkey"
    },
    {
        "code": "ckb",
        "flag": "🇮🇶",
        "name": "Kurdish (Sorani)",
        "native": "سۆرانی",
        "country": "Iraq \/ Iran"
    },
    {
        "code": "ug",
        "flag": "🇨🇳",
        "name": "Uyghur",
        "native": "ئۇيغۇرچە",
        "country": "China \/ Xinjiang"
    },
    {
        "code": "tt",
        "flag": "🇷🇺",
        "name": "Tatar",
        "native": "Татар теле",
        "country": "Russia \/ Tatarstan"
    },
    {
        "code": "be",
        "flag": "🇧🇾",
        "name": "Belarusian",
        "native": "Беларуская",
        "country": "Belarus"
    }
];

    const WC360_LANG_MAP = {};
    WC360_ALL_LANGS.forEach(l => { WC360_LANG_MAP[l.code] = l; });

    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            autoDisplay: false
        }, 'google_translate_element');
    }

    function changeAppLanguage(langCode) {
        if (!langCode || langCode === 'en') {
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
            document.cookie = "googtrans=/en/en; path=/;";
            document.cookie = "googtrans=/en/en; path=/; domain=" + window.location.hostname + ";";
            localStorage.setItem('wc360_selected_lang', 'en');
        } else {
            document.cookie = "googtrans=/en/" + langCode + "; path=/;";
            document.cookie = "googtrans=/en/" + langCode + "; path=/; domain=" + window.location.hostname + ";";
            localStorage.setItem('wc360_selected_lang', langCode);
        }

        const combo = document.querySelector('.goog-te-combo');
        if (combo) {
            combo.value = langCode;
            combo.dispatchEvent(new Event('change'));
        } else {
            window.location.reload();
        }

        updateAllLangUI(langCode);
        document.querySelectorAll('[data-lang-menu]').forEach(m => m.classList.add('hidden'));
    }

    // Alias for backward compatibility if called as changeWelcomeLanguage or changeAuthLanguage
    window.changeWelcomeLanguage = changeAppLanguage;
    window.changeAuthLanguage = changeAppLanguage;

    function updateAllLangUI(langCode) {
        const info = WC360_LANG_MAP[langCode] || WC360_LANG_MAP['en'] || { flag: '🇺🇸', name: 'English' };
        document.querySelectorAll('[data-lang-flag], #currentLangFlag, #welcomeCurrentLangFlag, #authCurrentLangFlag').forEach(el => {
            el.textContent = info.flag;
        });
        document.querySelectorAll('[data-lang-label], #currentLangLabel, #welcomeCurrentLangLabel, #authCurrentLangLabel').forEach(el => {
            el.textContent = info.name;
        });
    }

    function renderLangContainerList(listContainer, filterText = '') {
        if (!listContainer) return;
        const q = (filterText || '').trim().toLowerCase();
        
        // Indonesian / English synonym helper mapping for common country searches
        const synonyms = {
            'jepang': 'japan', 'jerman': 'germany', 'inggris': 'english', 'prancis': 'france',
            'spanyol': 'spain', 'rusia': 'russia', 'belanda': 'netherlands', 'korea': 'korea',
            'cina': 'china', 'tiongkok': 'china', 'arab': 'arabic', 'turki': 'turkey',
            'filipina': 'philippines', 'mesir': 'egypt', 'afrika': 'south africa',
            'italia': 'italy', 'yunani': 'greece', 'polandia': 'poland', 'norwegia': 'norway',
            'swedia': 'sweden', 'finlandia': 'finland', 'swiss': 'switzerland', 'brasil': 'brazil',
            'meksiko': 'mexico', 'kanada': 'canada', 'australia': 'australia', 'selandia baru': 'new zealand'
        };

        let targetQuery = q;
        for (const [k, v] of Object.entries(synonyms)) {
            if (q.includes(k)) {
                targetQuery += ' ' + v;
            }
        }

        const filtered = WC360_ALL_LANGS.filter(l => {
            if (!q) return true;
            const hay = (l.name + ' ' + l.native + ' ' + l.country + ' ' + l.code).toLowerCase();
            const terms = targetQuery.split(/\s+/).filter(Boolean);
            return terms.some(t => hay.includes(t));
        });

        if (filtered.length === 0) {
            listContainer.innerHTML = '<div class="px-4 py-8 text-center text-xs text-slate-400 font-medium">Tidak ada bahasa/negara yang cocok dengan pencarian</div>';
            return;
        }

        let html = '';
        filtered.forEach(l => {
            const isEn = l.code === 'en';
            const isId = l.code === 'id';
            let badge = '';
            if (isEn) {
                badge = '<span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-1.5 py-0.5 rounded">Default</span>';
            } else if (isId) {
                badge = '<span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Lokal</span>';
            } else {
                const countryBrief = l.country.split('/')[0].trim();
                badge = `<span class="text-[9px] text-slate-400 font-medium truncate max-w-[110px] text-right inline-block" title="${l.country}">${countryBrief}</span>`;
            }

            html += `
                <button type="button" onclick="changeAppLanguage('${l.code}')"
                        class="w-full text-left px-3.5 py-2 hover:bg-blue-50/80 flex items-center justify-between text-xs transition-colors group">
                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700 min-w-0 pr-2">
                        <span class="text-base shrink-0">${l.flag}</span>
                        <span class="truncate">${l.name} <span class="text-[11px] font-normal text-slate-500">(${l.native})</span></span>
                    </span>
                    <div class="shrink-0 ml-2">${badge}</div>
                </button>
            `;
        });

        listContainer.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdown instances on the page
        const containers = document.querySelectorAll('#langDropdownContainer, #welcomeLangContainer, #authLangContainer, [data-lang-container]');
        
        containers.forEach(c => {
            const btn = c.querySelector('button[id$="LangBtn"], [data-lang-btn]');
            const menu = c.querySelector('div[id$="LangMenu"], [data-lang-menu]');
            const search = c.querySelector('input[type="text"], [data-lang-search]');
            const list = c.querySelector('div[id$="ListOptions"], [data-lang-list]');

            if (btn && menu) {
                if (list) {
                    renderLangContainerList(list, '');
                }

                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = menu.classList.contains('hidden');
                    
                    // Close other menus first
                    document.querySelectorAll('[data-lang-menu], div[id$="LangMenu"]').forEach(m => {
                        if (m !== menu) m.classList.add('hidden');
                    });

                    menu.classList.toggle('hidden');
                    if (isHidden && search) {
                        setTimeout(() => search.focus(), 60);
                    }
                });

                if (search && list) {
                    search.addEventListener('input', function(e) {
                        renderLangContainerList(list, e.target.value);
                    });
                    search.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            }
        });

        document.addEventListener('click', function(e) {
            document.querySelectorAll('[data-lang-menu], div[id$="LangMenu"]').forEach(m => {
                const parent = m.closest('#langDropdownContainer, #welcomeLangContainer, #authLangContainer, [data-lang-container]');
                if (!parent || !parent.contains(e.target)) {
                    m.classList.add('hidden');
                }
            });
        });

        // Restore saved language selection
        const savedLang = localStorage.getItem('wc360_selected_lang') || getCookieVal('googtrans')?.split('/')[2] || 'en';
        if (savedLang && WC360_LANG_MAP[savedLang]) {
            updateAllLangUI(savedLang);
        }
    });

    function getCookieVal(name) {
        const v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return v ? v[2] : null;
    }
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>