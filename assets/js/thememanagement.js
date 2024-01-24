const wantDark = window.matchMedia("(prefers-color-scheme: dark)");

let pref = localStorage.getItem('prefered');

wantDark.addEventListener('change', (mql) => {
    if(mql.matches){
        document.body.setAttribute("theme", "dark")
    } else {
        document.body.removeAttribute('theme');
    }
});

document.addEventListener("DOMContentLoaded",(evt)=>{
    if((wantDark.matches && !pref) || pref === "dark"){ // pas de pref et l'OS veut dark *ou* pref = dark
        document.body.setAttribute("theme", "dark")
    }
    document.getElementById('ThemeSwitch').checked = (wantDark.matches && !pref) || pref === "dark";
    document.getElementById('ThemeSwitch').addEventListener("change", switchTheme)
});

function switchTheme(event){
    document.body.setAttribute("animated",'');
    setTimeout(()=>{
        document.body.removeAttribute("animated");
    }, 500);
    if (event.target.checked) {
        document.body.setAttribute('theme', 'dark');
        localStorage.setItem('prefered', "dark");
    } else {
        document.body.removeAttribute('theme');
        localStorage.setItem('prefered', "light");
    }
}