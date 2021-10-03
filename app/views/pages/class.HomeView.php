<?php


class HomeView extends MainView implements PagesViewInterface {

  public $pageName;

  public function __construct() {
    parent::__construct();

    $this->pageName = ucfirst(Conf::get('site_name'));
  }

  // meta title tag
  public function displayMetaTitle() {
    $title = $this->pageName;
    $this->renderMetaTitle($title);
  }

  public function displayAboutSection() {
      echo ' <div class="section-about">
        <div class="container">
            <div class="about-wrapper">
                <div class="title">
                    <h1>'.  Trans::get("Master LPFR je lokalni procesor fiskalnih računa").'</h1>
                </div>
                <div class="desc">
                    <p>'. Trans::get("Lokalni procesor fiskalnih računa je obavezan element Elektronskog fiskalnog uređaja (EFU). Master LPFR je prilagođen za korišćenje od strane svih obveznika fiskalizacije. Može se koristiti uz bilo koji Elektronski sistem za izdavanje računa (ESIR) koji je akreditovan od strane Poreske uprave. Upotrebom Master LPFR-a poreski obveznici ispunjavaju svoju obavezu propisanu novim modelom fiskalizacije.").'</p>
                </div>
                <div class="button-wrapper">
                    <a href="#">'. Trans::get("Brošura").'</a>
                </div>
            </div>
            <div class="about-img">
                <img src="'. Conf::get("url").'/css/img/skica 1.png">
            </div>
        </div>
    </div>';
  }

  public function displayServiceSection() {
        echo '
             <div id="serviceSection" class="service-section">
        <div class="container">
            <div class="proces-1">
                <div class="proces-1-img">
                    <img src="'.Conf::get("url").'/css/img/proces.png">
                </div>
                <div class="proces-1-wrapper">
                    <div class="proces-title">
                        <h2>'.Trans::get('Šta su prednosti upotrebe Master LPFR?').'</h2>
                    </div>
                    <div class="proces-desc">
                        <p>'.Trans::get('Za rad ovog softverskog rešenja nije neophodna nabavka novog, dodatnog računara ili sličnog hardvera koji je namenjen radu Master LPFR-a. Master LPFR je moguće instalirati i koristiti na bilo kom postojećem računaru sa čitačem smart kartica, koji se pored toga koristi i u druge poslovne svrhe kao što su korišćenje raznih poslovnih softvera, servera, POS kasa i slično. Pored instalacije na računaru, Master LPFR je moguće instalirati i koristiti na specijalizovanim pultnim terminalima koji u sebi sadrže POS softver, specijalizovanim Android uređajima, koji u sebi sadrže Master LPFR, štampač i čitač kartica. Master LPFR omogućava korišćenje efikasnih rešenja, koja kao takva u potpunosti zadovoljavaju obavezu poreskih obveznika po pitanju novog modela fiskalizacije.').'</p>
                    </div>
                </div>
            </div>
             <div class="proces-2">

                <div class="proces-2-wrapper">
                    <div class="proces-title">
                        <h2>'.Trans::get('Master LPFR karakteristike').'</h2>
                    </div>
                    <div class="proces-desc">
                        <ul>
                            <li>'.Trans::get("Komunicira sa elektronskim sistemom-softverom za izdavanje računa (ESIR),").'</li>
                            <li>'.Trans::get("Bezbednosnim elementom (BE), I sistemom za upravljanje fiskalizacijom Poreske uprave (SUF)").'</li>
                            <li>'.Trans::get("Vrši obračun iznosa PDV-a na osnovu stavki fiskalnog računa").'</li>
                            <li>'.Trans::get("Zadužen je za generisanje izgleda štampe fiskalnog računa").'</li>
                            <li>'.Trans::get("Kompatibilan je sa svim ESIR rešenjima koja su akreditovana od strane Poreske uprave").'</li>                            
                            <li>'.Trans::get("Vrši digitalno potpisivanje fiskalnog računa uz pomoć bezbednosnog elementa (BE)").'</li>
                            <li>'.Trans::get("Predstavlja osnovnu komponenetu novog modela fiskalizacije propisanu od strane Poreske uprave").'</li>
                            <li>'.Trans::get("Prosleđuje podatke o generisanom fiskalnom računu elektronskom sistemu – softveru za izdavanje računa").'</li>
                            <li>'.Trans::get("Generisane fiskalne račune čuva u svojoj bazi i prenosi Sistemu za upravljanje fiskalizacijom (SUF) Poreske uprave").'</li>
                            <li>'.Trans::get("Podržava rad u operativnim sistemima: Windows, Android").'</li>
                        </ul>
                        <p>'.Trans::get("Napomena*: Korišćenje u Windows okruženju moguće je uz dodatak kompatibilnog Smart Card čitača.").'<br>'.Trans::get("Napomena** : Korišćenje u Android okruženju ograničeno je na određene verzije.").'</p>
                    </div>
                </div>
                <div class="proces-2-img">
                    <img src="'.Conf::get("url").'/css/img/skica 4 1.png">
                </div>
                
            </div>
             <div class="proces-3">
                <div class="proces-3-img">
                    <img src="'.Conf::get("url").'/css/img/skica 8.png">
                </div>
                <div class="proces-3-wrapper">
                    <div class="proces-title">
                        <h2>'.Trans::get('Centralni online monitoring Master LPFR-a').'</h2>
                    </div>
                    <div class="proces-desc">
                        <p>'.Trans::get('Svi korisnici Master LPFR-a imaće mogućnost tehničke podrške 24/365 u vidu monitoringa od strane tehničkog tima. Tehnički tim će u svakom momentu imati informacije o stanju svakog pojedinačnog LPFR-a, i samim tim uvid u bilo koji vid tehničkog problema sa kojima se korisnici susreću. Ukoliko su korisnici zainteresovani, omogućićemo praćenje i izveštavanje o status LPFR-a putem mail-a, telefona, ili u vidu ticket zahteva.').'</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
        ';
  }

  public function displayFaqSection() {
        echo '
            <div id="faqSectionId" class="faqSection">
        <div class="container">
            <div class="faq-title">
                <h1>'.Trans::get("Najčešće postavljana pitanja").'</h1>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Koji je rok za prelazak na novi model fiskalizacije, odnosno upotrebu Master LPFR-a?").'</p>
                <p>'.Trans::get("Prelazni rok za uvođenje nove fiskalizacije u Republici Srbije je od 01.11.2021 do 30.04.2021. Poreski obveznici već od 01.11.2021 mogu ispuniti svoju zakonsku obavezu na ovaj način. Istekom prelaznog roka, nabavka MASTER LPFR rešenja postaje njihova zakonska obaveza.").'</p>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Koje su tehničke karakteristike Master LPFR rešenja?").'</p>
                <p>'.Trans::get("Master LPFR je kompatibilan sa svim POS aplikacijama koje su akreditovane od strane Poreske uprave. Predstavlja vrstu HTTP sevirsa baziranu na JSON-u i može se instalirati na Windows i Android sistemima. Master LPFR omogućava izdavanje fiskalnih računa čak I kada internet konekcija nije prisutna.").'</p>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Kako mogu da instaliram Master LPFR i ispunim svoju zakonsku obavezu?").'</p>
                <p>'.Trans::get("Proces instalacije, licenciranja i preuzimanja možete izvršiti u samo 3 klika preko web portala lpfr.online").'</p>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Napredne funkcije Master LPFR rešenja").'</p>
                <p>'.Trans::get("Pored toga što sadrži sve obavezne elemente propisane od strane Poreske uprave i u potpunosti zadovoljava specifikaciju Master LPFR elemenata fiskalnog uređaja (EFU),").'</p>
                <p>'.Trans::get("Master LPFR je proširen sa naprednim fnkcionanostima koje korisnicima omogućavaju jednostavnij, brži i komforniji rad:").'</p>
                <ul>
                <li>'.Trans::get("Štampu fisklanog računa n abilo kom štampaču").'</li>
                <li>'.Trans::get("Komunikaciju putem XML-a").'</li>
                <li>'.Trans::get("Komunikacija preko datoteka*").'</li>
                </ul>
                <p>'.Trans::get("Napomena*: Napredne funkcionalnosti su promenljive, odnosno, mogu se dodatno prilagođavati po zahtevu korisnika.").'</p>
                <p>'.Trans::get("Za sva pitanja i sugestije, molimo Vas da nam pišete.").'</p>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Demo verzija Master LPFR").'</p>
                <p>'.Trans::get("Uskoro će na portalu www.lpfr.rs biti dostupna i demo verzija za testiranje").'</p>
            </div>
            <div class="faqs">
                <p class="question">'.Trans::get("Zainteresovani ste za saradnju?").'</p>
                <p>'.Trans::get("Za sve zainteresovane poslovne partnere, otvoreni smo za saradnju. Pozovite nas ili nam pišite.").'</p>
            </div>
        </div>
    </div>
        ';
  }




  // meta description, keywords and og tags
  public function displayAdditionalMetaTags() {
    $this->displayStaticAdditionalMetaTags(array('title' => $this->pageName));
  }

}
?>