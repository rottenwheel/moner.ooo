<?php
// For the HTML meta specification, e.g. <!DOCTYPE html><html lang="en">
$lang_meta = "fa"; // https://www.w3schools.com/tags/ref_language_codes.asp
$rtl = true;

$page_title = "تبدیل XMR به EUR/BTC/CHF/USD و بسیاری دیگر"; // The browser tab title or search engine title
$meta_description = "نرخ تبادل زنده مونرو در ارزهای مختلف، رایگان برای همه."; // Search engine description / text
$meta_keywords = "مونرو, XMR, ارز فیات, ارزش, نرخ, زنده, تبادل, تبدیل"; // Search engine keywords

$title_h1 = "تبدیل به"; // ↓ XMR [...] ↓

$moneroooTable = "Service provided by <a href='https://moner.ooo/'>Moner.ooo</a>, Data provided by <a href='https://www.coingecko.com/en/coins/monero' hreflang='en' rel='external'>CoinGecko</a>";

// Info Text
$info = "نرخ‌های تبادل در این سایت فقط برای اطلاع‌رسانی هستند. آن‌ها تضمین نمی‌شوند که دقیق باشند و ممکن است بدون اطلاع قبلی تغییر کنند. نرخ‌های تبادل تقریباً هر دقیقه یکبار به‌روز می‌شوند. آخرین به‌روزرسانی در <u title='ساعت:دقیقه:ثانیه (hh:mm:ss)'>".$time."</u>، Europe/Berlin. داده‌ها ارائه شده توسط <a class='text-white' href='https://www.coingecko.com/en/coins/monero' hreflang='en' rel='external' target='_blank'>CoinGecko</a>.<br/><a target='_blank' href='https://kuno.anne.media/donate/onml/' rel='external' hreflang='en'><img loading='lazy' src='./img/kuno-monero-26x26.png' width='17' height='17' alt='Kuno - Moner.ooo donation page'></a>&nbsp;<a target='_blank' href='https://kuno.anne.media/' class='text-white' rel='external' hreflang='en'>Kuno – جمع‌آوری کمک مالی با مونرو</a> | <a class='text-white' href='{$github_url}' hreflang='en' rel='external' target='_blank'>GitHub</a>";
$servers_guru = " | <a style='text-decoration:none; font-weight:bold;' class='text-white' href='https://servers.guru/' hreflang='en' rel='external' target='_blank'>میزبانی وب ارائه شده توسط<img loading='lazy' src='./img/servers-guru.svg' height='19' alt='Servers Guru' title='Servers Guru' /></a>";

$clipboard_copy_tooltip = "کپی به کلیپ‌بورد";
$l_fiatSelect = "Currency choice";
$l_fiatInput = "Fiat value input field";
$l_xmrInput = "Monero value input field";

// Tooltip Titel
$l_eur = "یورو";
$l_btc = "بیت‌کوین";
$l_chf = "فرانک سوئیس";
$l_usd = "دلار آمریکا";
$l_ltc = "لایت‌کوین";
$l_gbp = "پوند استرلینگ";
$l_rub = "روبل روسیه";
$l_jpy = "ین";
$l_try = "لیر ترکیه";
$l_cad = "دلار کانادا";
$l_aud = "دلار استرالیا";
$l_hkd = "دلار هنگ‌کنگ";
$l_sgd = "دلار سنگاپور";
$l_pln = "زلوتی";
$l_zar = "راند آفریقای جنوبی";
$l_inr = "روپیه هند";
$l_aed = "درهم امارات";
$l_eth = "اتریوم";
$l_uah = "هریونیا";
$l_krw = "وون کره جنوبی";
$l_brl = "رئال برزیل";
$l_myr = "رینگیت مالزی";
$l_cny = "رنمینبی";
$l_xag = "نقره (اونس تروا)";
$l_xau = "طلا (اونس تروا)";
$l_vnd = "دونگ ویتنام";
$l_vef = "بولیوار ونزوئلا";
$l_thb = "بات تایلند";
$l_sar = "ریال سعودی";
$l_sek = "کرون سوئد";
$l_pkr = "روپیه پاکستان";
$l_nzd = "دلار نیوزیلند";
$l_php = "پزو فیلیپین";
$l_nok = "کرون نروژ";
$l_lkr = "روپیه سریلانکا";
$l_mmk = "کیات میانمار";
$l_huf = "فورینت مجارستان";
$l_ils = "شکل جدید اسرائیل";
$l_kwd = "دینار کویت";
$l_ngn = "نایرا نیجریه";
$l_idr = "روپیه اندونزی";
$l_twd = "دلار جدید تایوان";
$l_ars = "پزو آرژانتین";
$l_bdt = "تاکا بنگلادش";
$l_bhd = "دینار بحرین";
$l_bmd = "دلار برمودا";
$l_czk = "کرونای چک";
$l_dkk = "کرون دانمارک";
$l_clp = "پزو شیلی";
$l_mxn = "پزو مکزیک";
$l_bch = "بیت‌کوین کش";
$l_bnb = "بایننس کوین";
$l_eos = "EOS.IO";
$l_xrp = "ریپل";
$l_xlm = "استلار لومنس";
$l_link = "چین‌لینک";
$l_dot = "پولکادات";
$l_yfi = "یرن.فایننس";
$l_gel = "لاری گرجستان";
$l_xdr = "حقوق برداشت ویژه";

// More Monero links
$getmonero = '<a class="text-white" href="https://www.monerotalk.live/" hreflang="en" target="_blank" rel="external">Monero Talk</a> | <a class="text-white" href="https://www.monero.observer/resources/" hreflang="en" target="_blank" rel="external">Monero Observer</a> | <a class="text-white" href="https://ccs.getmonero.org/" hreflang="en" target="_blank" rel="external">Community Crowdfunding System (CCS)</a> | <a class="text-white" href="https://www.getmonero.org/" hreflang="en" target="_blank" rel="external">Official website</a>';
$countrymonero = '';
$rtl = 'true';
