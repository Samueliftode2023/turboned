<div id='container'>
    <form id='add-emplolyee'>
        <div id='date-salariat'> 
            <h3>Date generale</h3>
            <label>
                Nume*
                <input type='text' name='nume' maxlength='30'>
            </label>
            <label>
                Prenume*
                <input type='text' name='prenume'>
            </label>
            <label>
                Genul*
                <select name='genul'>
                    <option>Masculin</option>
                    <option>Feminin</option>
                </select>
            </label>
            <label>
                Apatrid
                <select onchange='apatridChecker(this)' id='apatrid-id' name='apatrid' disabled>
                    <option value='none'>Nu</option>
                    <option value='0'>Cu drept de sedere in alta tara decat Romania</option>
                    <option value='1'>Cu drept de sedera pe termen lung in Romania</option>
                </select>
            </label>
            <div id='cetatenie-range'>
                <label>
                    Cetatenie
                    <select id='cetatenie-id' name='cetatenie' onchange = 'adresaChange(this)' disabled></select>
                </label>
            </div>
        </div>
        <div id='date-adresa' class='dis-none'>
            <h3>Adresa</h3>
            <div id='aviz-apatrid'>
                
            </div>
            <label>
                Tara
                <select onchange='taraChecker(this)' id='country-id' name='tara' disabled></select>
            </label>
            <div id='judet-localitate-range'>
                <label>
                    Judetul
                    <select onchange=' citireLocalitati(this,"no")' id='judetul-id' name='judetul' disabled></select>
                </label>
                <label>
                    Localitatea
                    <select id='localitatea-id' name='localitatea'></select>
                </label>
            </div>
            <label>
                Adresa
                <br>
                <textarea rows="4" cols="50" name='adresa'></textarea>
            </label>
            <label>
                Telefon
                <input type='text' name='telefon-1'>
                <br>
                <input type='text' name='telefon-2'>
            </label>
            <label>
                Email
                <input type='text' name='email-1'>
                <br>
                <input type='text' name='email-2'>
            </label>
        </div>
        <div id='date-buletin' class='dis-none'>
            <h3>Identitate</h3>
            <label>
                Tip act de identitate:
                <select id='act-id' onchange='actDetails(this)' name='tip-act'>
                    <option value = '0'>Carte de identitate</option>
                    <option value = '2'>Buletin de identitate</option>
                    <option value = '6'>Alt tip de identitate romanesc</option>
                </select>
            </label>
            <div id='detalii-act-range'>
                <label>
                    CNP
                    <input type='text' name='cnp'>
                </label>
                <label>
                    Serie
                    <input type='text' name='seria'>
                </label>
                <label>
                    Numar
                    <input type='text' name='numar-serie'>
                </label>
                <label>
                    Data emiterii
                    <input type='date' name='data-emiterii'>
                </label>
                <label>
                    Data expirarii
                    <input type='date' name='data-expirarii'>
                </label>
                <label>
                    Emis de
                    <br>
                    <textarea rows="4" cols="50" name='emis-de'></textarea>
                </label>
            </div>
        </div>
        <div id='date-stare' class='dis-none'>
            <h3>Stare civila</h3>
            <label>
                Casatorit
                <select name='casatorit'>
                    <option>Nu</option>
                    <option>Da</option>
                </select>
            </label>
            <label>
                Copii
                <input type='number' name='copii' value='0' min='0' max='20'>
            </label>
            <label>
                Persoane in intretinere
                <input type='number' name='persoane-in-intretinere' value='0' min='0' max='20'>
            </label>
        </div>
        <div id='date-poza' class='dis-none'>
            <h3>Fotografie tip pasaport</h3>
            <label>
                <input id='up-photo' accept='.jpg, .png, .jpeg' type='file' class='dis-none' name='file'>   
                <div id='preview-photo'></div> 
                <label for='up-photo' id='upload-photo-buton'>
                    Incarca<span class="material-symbols-outlined">upload</span>
                </label>
            </label>
        </div>
        <div id='date-observatii' class='dis-none'>
            <h3>Adauga o nota</h3>
            <label>
                <div>Observatii</div>
                <textarea rows="4" cols="50" name='observatii'></textarea>
            </label>
            <div id='buton-view-data' onclick='showAllInfo()'>Vizualizeaza toate datele</div>
        </div>
        <div id='butoane-form'>
            <div id='back-form' class='dis-none back-btn'>Inapoi</div>
            <div id='next-form' class='next-btn'>Continua</div>
            <button id='save-form' class='dis-none save-btn'>Salveaza</button>
        </div>
    </form>
    <div id='view-all-info' class='dis-none'>
        <div onclick='closeView()' id='close-view'>
            <span class="material-symbols-outlined">close</span>
        </div>
        <div id='info-employee'></div>
        <div id='photo-employee'></div>
    </div>
</div>
