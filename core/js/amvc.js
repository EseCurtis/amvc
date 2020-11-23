//Global Variables
const amvcAPI  = _URL_+'/amvc.api';
const requestURI    = window.location.pathname;
let loadtimecounter;
let loadtime;

//Main  AMVC class
class AMVC {
    //routing
    routes(currentRoutes){
        currentRoutes.push({route:'$',script:'$'});
        let comparisonRoute = cTrim(window.location.pathname.toLowerCase(), '/').split('/');
        let mainRoute = [];
        let validRoutes = [];
        currentRoutes.forEach(currentRoute=>{
            
            let routeValues = '{';
            let comparisonRouteSliced = comparisonRoute;
            
            currentRoute.route = cTrim(currentRoute.route.toLowerCase(), '/').split('/');
            for (let i=0;i<currentRoute.route.length;i++) {
                if(currentRoute.route[i] == '*'){  
                    comparisonRouteSliced = comparisonRoute.slice(0, i);
                    currentRoute.route = currentRoute.route.slice(0, i);
                    break;
                }
            }

            if(currentRoute.route.length == comparisonRouteSliced.length){
                
                for(let i=0;i<currentRoute.route.length;i++){
                    if(currentRoute.route[i][0] == '{'){
                        currentRoute.route[i] = currentRoute.route[i].replace(/^[{]/g, '').replace(/[}]$/g, '').split(':');
                        let currentValue = comparisonRouteSliced[i].match(currentRoute.route[i][1]);
                        routeValues += '"'+currentRoute.route[i][0]+'":"'+currentValue+'"';
                        currentRoute.route[i] = currentRoute.route[i][1];
                    }
                    mainRoute.push(currentRoute.route[i]);
                }
                routeValues += '}';
                routeValues = JSON.parse(routeValues);
                let mainRouteNow = mainRoute.join('/');
                let comparisonRouteNow = comparisonRouteSliced.join('/');
                
                if(comparisonRouteNow.match(mainRouteNow)){
                    if(comparisonRouteNow.match(mainRouteNow)[0].length == comparisonRouteNow.length){
                        currentRoute.script(routeValues);
                        validRoutes.push(currentRoute);
                    }
                }
                mainRoute = [];
                routeValues = '';
            }
        });

        return validRoutes;
    }

    //Interact with backend interactor (api)
    interact(callback, interactorKey,  fd = new FormData()){
        fd.append('_amvc_request_', generateRequestData('_interact', interactorKey, null));
        ajax(fd, amvcAPI, output=>{
            callback(output);
        });
    }

    //performing queries by sql key
    SQLgetArray(callback, sqlKey){
        if(callback && sqlKey){
            let fd = new FormData();
            fd.append('_amvc_request_', generateRequestData('_sql_get_array', sqlKey, null));
            ajax(fd, amvcAPI, output=>{
                output = JSON.parse(output); 
                callback(output);
            });
        }else{
            console.error('too few arguments supplied');
        }
    }

    //loading element from another page or link
    loadElement(url, sourceElement, callback = ()=>{}){
        sourceElement = {
            element : sourceElement.split(' ')[0],
            index : sourceElement.split(' ')[1] ? sourceElement.split(' ')[1] : 0,
        };
        url = {
            url : url.split(' ')[0],
            element : url.split(' ')[1] ? url.split(' ')[1] : 'body',
        };
        ajax(null, url.url, output=>{ 
            output = new DOMParser().parseFromString(output, "text/html").querySelectorAll(url.element);
            output.forEach(element => {
                document.querySelectorAll(sourceElement.element)[sourceElement.index].innerHTML = element.innerHTML;
            });
            callback(output);
        }); 
    }

    //asyncronous loading 
    asyncLoad(url = null){
        loader.start();
        stopAllIntervals();
        ajax(null, url, output=>{
            appendToBody(output);
            let pageTitle =  new DOMParser().parseFromString(output, "text/html").title;
            aHistorize(url);
            document.title = pageTitle;
            setTimeout(()=>{
                refreshCode();
                loader.stop();
            }, 500);
        }); 
    }

    //asyncronous loading based on routes
    routeLoad(route = null){
        route = route.replace(/^[/]/g, '');
        let linkUrl = _URL_+'/'+route;
        loader.start();
        stopAllIntervals();
        ajax(null, linkUrl, output=>{
            appendToBody(output);
            aHistorize(linkUrl);
            setTimeout(()=>{
                refreshCode();
                loader.stop();
            }, 500);
        }); 
    }

    //asyncronous reloading
    asyncReload(){
        loader.start();
        stopAllIntervals();
        ajax(null, location.href, output=>{  
            appendToBody(output);
            setTimeout(()=>{
                refreshCode();
                loader.stop();
            }, 500);
        });
    }
}

//Working functions

const ajax = (requestData, url, callback, method = "POST")=>{
        var xmlhttp;
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function(){
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                callback(xmlhttp.responseText);
            }
        }
        xmlhttp.open(method, url, true);
        xmlhttp.send(requestData);
        return;
};

//strips a specific character off the end and begining of a string
const cTrim = (str = null, char = null)=>{
    str = str.split('');
    if(str[0] == char){
        str[0] = '';
    }
    if(str[str.length-1] == char){
        str[str.length-1] = '';
    }
    return str.join('');
}

//Handles all the BOM history 
const aHistorize = (url)=>{
    window.history.pushState(null, null, url);
}
const generateRequestData = (command, data1, data2)=>{
    let requestData = {
        "command":command,
        "data_1":data1,
        "data_2":data2
    };
    return requestData;
}

//stops all running intervals to enhance pure page reload
const stopAllIntervals = ()=>{
    let lastInterval = setInterval(()=>{},9999);
    for (let i=0;i<lastInterval;i++) {
        clearInterval(i);   
    }
}

//Handle all anchor tags with route attribute to load links asynchronously
const handlerouters=()=>{
    let links  = document.getElementsByTagName('*');
    for(i=0;i<links.length;i++){
        let activeClass = links[i].getAttribute('is-active') ? links[i].getAttribute('is-active') : 'active';
        if(links[i].getAttribute('href')){
            links[i].addEventListener('click', function(event){
                event.preventDefault();
                new AMVC().asyncLoad(event.currentTarget.getAttribute('href'));
            });
            if(links[i].getAttribute('href').toLowerCase() == currentPage.toLowerCase()){
                links[i].classList.add(activeClass);
            }
            links[i].style.cursor = "pointer";
        }
    }
};

//Default loader for amvc.js
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

//Handle arrays pagination or query returns pagination 
/** @author Bro johnson */
const paginate = (dataTo, perPage)=>{
    let arrLimit   = Math.round((dataTo.length/perPage)*2);
    let arrIndex   = 1;
    let tempArr    = [];
    let returnArr  = [];

    if(dataTo.length > perPage){
        for(i=arrIndex;i<arrLimit;i++){
            tempArr.push(dataTo.slice((i - 1) * perPage, i * perPage));
        }
        for(i=0;p_i<tempArr.length;i++){
            if(tempArr[i].length > 0){
                returnArr.push(tempArr[i]);
            }
        }
    }else{
        returnArr.push(dataTo);
    }
    return returnArr;
}

//Handles all virtual forms (asyncronous operations for forms with the async attribute)
const handleVform = ()=>{
    const vForms = document.querySelectorAll('form');
    for(i=0;i<vForms.length;i++){
        if(vForms[i].getAttribute('async') == 'true'){
            vForms[i].onsubmit = (event)=>{
                event.preventDefault();
                let vFormInputs = event.currentTarget.querySelectorAll('*');
                let submitUrl   =  event.currentTarget.getAttribute("action");
                let vForm       =  new FormData();

                if(event.currentTarget.method.toLowerCase() == 'get'){
                    submitUrl += "?";
                    for(ii=0;ii<vFormInputs.length;ii++){
                        if(vFormInputs[ii].name && vFormInputs[ii].value){
                            if(vFormInputs[ii].name.length > 0){
                                if(vFormInputs[ii].type == 'file'){
                                    vForm.append(vFormInputs[ii].name, vFormInputs[ii].files);
                                }else{
                                    submitUrl += vFormInputs[ii].name+"="+vFormInputs[ii].value+"&";
                                }
                            }
                        }
                    }
                    submitUrl = submitUrl.replace(/[&]$/g, '');
                }else{
                    for(ii=0;ii<vFormInputs.length;ii++){
                        if(vFormInputs[ii].name && vFormInputs[ii].value){
                            if(vFormInputs[ii].type == 'file'){
                                vForm.append(vFormInputs[ii].name, vFormInputs[ii].files);
                            }else{
                                vForm.append(vFormInputs[ii].name, vFormInputs[ii].value);
                            }
                        }
                    }
                }
                
                ajax(vForm, submitUrl, eval(event.currentTarget.getAttribute("callback")));
            };
        }
    }
}

//Function for creating various printing on page or in console
const print ={
    toEl:(el = null, str = null)=>{
        if(document.querySelector(el)){
            return document.querySelector(el).innerHTML = str;
        }
        return console.error('Error: the element "'+el+'" specified is not found');
    },
    inEl:(el = null, str = null)=>{
        if(document.querySelector(el)){
            return document.querySelector(el).innerHTML += str;
        }
        return console.error('Error: the element "'+el+'" specified is not found');
    },
    asEl:(el = null, str = null)=>{
        if(document.querySelector(el)){
            return document.querySelector(el).outerHTML = str;
        }
        return console.error('Error: the element "'+el+'" specified is not found');
    },
    out:(str = null)=>{
        return document.write(str);
    },
    in:(str = null)=>{
        return console.log(str);
    }
}; 
 
//Main refresh code function
const refreshCode =()=>{
    let scripts = document.getElementsByTagName('script');

    for(i=0;i<scripts.length;i++){
        if(scripts[i].innerHTML.length > 1 && scripts[i].getAttribute('reload')){
            if(scripts[i].getAttribute('reload') == 'yes'){
                eval(scripts[i].innerHTML);
            }
        }else if(scripts[i].getAttribute('reload')){
            if(scripts[i].src && scripts[i].getAttribute('reload') == 'yes'){
                ajax(null, scripts[i].src, function(code){ 
                    eval(code);
                });
            }

        }
        
    }

    handlerouters();
    handleVform();
}

//converts text to html format and appends it to the main body
const appendToBody = (str)=>{
    if(!str){
        str = '';
    }
    str = new DOMParser().parseFromString(str, "text/html");
    if(str.body){
        document.body.innerHTML = str.body.innerHTML;
    }else{
        document.body.innerHTML = str;
    }   
}

//Initial functions

onpopstate = ()=>{
    loader.start();
    let popsatateurl = window.location.href;
    ajax(null, popsatateurl, e=>{
        appendToBody(e);
        setTimeout(()=>{
            refreshCode();
            loader.stop();
        }, 500);
    });
} 

//Initialize or refresh the whole code when page reloads

let checkLoad = setInterval(()=>{
    if(document.body){
        handlerouters();
        handleVform();
        clearInterval(checkLoad);
    }  
}, 1);