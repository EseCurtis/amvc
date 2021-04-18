//Global Variables
const srcPath      = amvcPageData.siteUrl+"/src/";
const amvcAPI      = amvcPageData.siteUrl+'/amvc-files/amvc.api';
const requestURI   = window.location.pathname;


//Default loader for amvc.js
let loadtimecounter;
let loadtime;

let loader = {
    start:()=>{
        loadtime = 0;
        loadtimecounter = setInterval(() => {loadtime++}, 1);
        console.log('changing..route');
    },
    stop:()=>{
        clearInterval(loadtimecounter);
        console.log('changed..route within '+loadtime+'ms');
    },
}
