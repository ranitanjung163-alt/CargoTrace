<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register – WorldCargo 360 &bull; Track Beyond Borders.</title>
    <meta name="description" content="WorldCargo 360 – Global Logistics & Shipment Tracking System. Track Beyond Borders.">
    <!-- SVG Favicon with Globe & Cargo Box -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563EB' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9.5' stroke='%232563EB' stroke-width='1.5' stroke-opacity='0.4'/%3E%3Cellipse cx='12' cy='12' rx='9.5' ry='3.5' transform='rotate(-25 12 12)' stroke='%230EA5E9' stroke-width='1.5' stroke-dasharray='2 1'/%3E%3Cpath d='M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z' fill='%232563EB' fill-opacity='0.25' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3Cpath d='M12 6.5V17.5' stroke='%231D4ED8' stroke-width='1.8'/%3E%3Cpath d='M7 9.2L12 12L17 9.2' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            primary: '#2563EB',
                            'primary-hover': '#1D4ED8',
                            navy: '#0F172A',
                            secondary: '#64748B',
                            border: '#E2E8F0',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft-sm': '0 2px 8px -2px rgba(15, 23, 42, 0.05), 0 1px 4px -1px rgba(15, 23, 42, 0.03)',
                        'soft-lg': '0 20px 40px -10px rgba(15, 23, 42, 0.08), 0 8px 16px -4px rgba(15, 23, 42, 0.04)',
                        'glow-primary': '0 4px 20px -2px rgba(37, 99, 235, 0.35)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            background-image: radial-gradient(#CBD5E1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
        /* Clean Google Translate Integration */
        body { top: 0px !important; position: static !important; }
        .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-brand-primary selection:text-white relative overflow-hidden">

    <!-- Ambient glowing light orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-10 shadow-soft-lg relative z-10">
        <!-- Top Brand Gradient Bar -->
        <div class="absolute top-0 inset-x-0 h-1.5 rounded-t-3xl bg-gradient-to-r from-blue-600 via-brand-primary to-sky-400"></div>

        <!-- Top Navigation Bar & Language Switcher (Fitur Penerjemah Bahasa) -->
        <div class="flex items-center justify-between mb-4 notranslate">
            <a href="{{ url('/') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-brand-primary transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Home
            </a>

            <!-- Language Dropdown -->
            <div class="relative" id="authLangContainer">
                <button type="button" id="authLangBtn" title="Translate / Terjemahkan Bahasa"
                        class="flex items-center space-x-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-soft-xs transition-all hover:border-brand-primary/50 focus:outline-none">
                    <span class="text-sm">🌐</span>
                    <span id="authCurrentLangFlag">🇺🇸</span>
                    <span id="authCurrentLangLabel">English</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <!-- Language Dropdown Menu -->
                <div id="authLangMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-soft-lg py-2 z-50 animate-in fade-in zoom-in-95 duration-100 select-none">
                    <div class="px-3 pb-1.5 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1 4-10z"/></svg>
                            Translate / Terjemahkan
                        </span>
                        <span class="text-[9px] font-mono text-blue-600 font-bold bg-blue-50 px-1 py-0.5 rounded">14+</span>
                    </div>
                    <div class="max-h-60 overflow-y-auto divide-y divide-slate-50 py-1">
                        <button type="button" onclick="changeAuthLanguage('en')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                <span>🇺🇸</span>
                                <span>English</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Default</span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('id')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                <span>🇮🇩</span>
                                <span>Bahasa Indonesia</span>
                            </span>
                            <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1 py-0.5 rounded">Lokal</span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('zh-CN')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇨🇳</span>
                                <span>中文 (Chinese)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('ja')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇯🇵</span>
                                <span>日本語 (Japanese)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('ar')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇸🇦</span>
                                <span>العربية (Arabic)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('de')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇩🇪</span>
                                <span>Deutsch (German)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('fr')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇫🇷</span>
                                <span>Français (French)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('es')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇪🇸</span>
                                <span>Español (Spanish)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('pt')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇧🇷</span>
                                <span>Português</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('ru')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇷🇺</span>
                                <span>Русский (Russian)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('ko')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇰🇷</span>
                                <span>한국어 (Korean)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('nl')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇳🇱</span>
                                <span>Nederlands (Dutch)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('it')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇮🇹</span>
                                <span>Italiano (Italian)</span>
                            </span>
                        </button>
                        <button type="button" onclick="changeAuthLanguage('tr')" class="w-full text-left px-3 py-1.5 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                            <span class="flex items-center gap-2 font-semibold text-slate-700 group-hover:text-blue-700">
                                <span>🇹🇷</span>
                                <span>Türkçe (Turkish)</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 via-brand-primary to-sky-400 items-center justify-center text-white mb-3 shadow-glow-primary transform hover:scale-105 transition-transform duration-200 ring-4 ring-blue-50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Customer Registration</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Register to monitor your consignments &amp; SLAs worldwide</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-slate-800 mb-1">Full Name / Contact Person</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="company_name" class="block text-xs font-bold text-slate-800 mb-1">Company / Organization</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-800 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-800 mb-1">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-800 mb-1">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-800 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-2.5 bg-slate-50/80 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-2 py-3 bg-gradient-to-r from-blue-600 via-brand-primary to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99]">
                Create Customer Account
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:underline ml-1">Sign In</a>
        </div>
    </div>

    <!-- Google Translate Element & Dynamic Multilingual Engine -->
    <div id="google_translate_element" style="position:absolute; left:-9999px; top:-9999px; opacity:0; pointer-events:none;"></div>
    <script type="text/javascript">
        const WC360_AUTH_LANGS = {
            'en': { name: 'English', flag: '🇺🇸' },
            'id': { name: 'Indonesia', flag: '🇮🇩' },
            'zh-CN': { name: '中文', flag: '🇨🇳' },
            'ja': { name: '日本語', flag: '🇯🇵' },
            'ar': { name: 'العربية', flag: '🇸🇦' },
            'de': { name: 'Deutsch', flag: '🇩🇪' },
            'fr': { name: 'Français', flag: '🇫🇷' },
            'es': { name: 'Español', flag: '🇪🇸' },
            'pt': { name: 'Português', flag: '🇧🇷' },
            'ru': { name: 'Русский', flag: '🇷🇺' },
            'ko': { name: '한국어', flag: '🇰🇷' },
            'nl': { name: 'Nederlands', flag: '🇳🇱' },
            'it': { name: 'Italiano', flag: '🇮🇹' },
            'tr': { name: 'Türkçe', flag: '🇹🇷' }
        };

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,id,zh-CN,ja,ar,de,fr,es,pt,ru,ko,nl,it,tr',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function changeAuthLanguage(langCode) {
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

            updateAuthLangUI(langCode);
            const dropdown = document.getElementById('authLangMenu');
            if (dropdown) dropdown.classList.add('hidden');
        }

        function updateAuthLangUI(langCode) {
            const info = WC360_AUTH_LANGS[langCode] || WC360_AUTH_LANGS['en'];
            const flagEl = document.getElementById('authCurrentLangFlag');
            const labelEl = document.getElementById('authCurrentLangLabel');
            if (flagEl) flagEl.textContent = info.flag;
            if (labelEl) labelEl.textContent = info.name;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const langBtn = document.getElementById('authLangBtn');
            const langMenu = document.getElementById('authLangMenu');
            if (langBtn && langMenu) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    langMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                        langMenu.classList.add('hidden');
                    }
                });
            }

            const savedLang = localStorage.getItem('wc360_selected_lang') || getAuthCookieVal('googtrans')?.split('/')[2] || 'en';
            if (savedLang && WC360_AUTH_LANGS[savedLang]) {
                updateAuthLangUI(savedLang);
            }
        });

        function getAuthCookieVal(name) {
            const v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
            return v ? v[2] : null;
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
