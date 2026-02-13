<div id='display-message'>
</div>
<div id='loading-id' class='loading dis-none'>
        <div class='centrare-loading'>            
            <div class="loadingio-spinner-rolling-5owm4mbayhw"><div class="ldio-jl4i6909sug">
            <div></div>
            </div>
            </div>
        </div>
</div>
<div id='navigation'>
    <div id='meniu-button'>
        <span class="material-symbols-outlined">
        menu
        </span>
    </div>
    <div class='linkuri'>
        <a href=<?php echo "'".$root."main/dashboard/'";?>>
            <span class="material-symbols-outlined">
            dashboard
            </span>
        </a>
    </div>
    <div class='setari'>
        <a href=<?php echo "'".$root."main/settings/'";?>>
            <span class="material-symbols-outlined">
            settings
            </span>
        </a>
        <a id=<?php echo "'logout||".$root."'";?> onclick='logOut(this)' href='#'>
            <span class="material-symbols-outlined">
            logout
            </span>
        </a>
    </div>
</div>
<div id='dialog' class='dis-none'>
        <div onclick='closeDialog()' class='corner-dialog'><span class="material-symbols-outlined">close</span></div>
        <div id='dialog-mess'></div>
</div>