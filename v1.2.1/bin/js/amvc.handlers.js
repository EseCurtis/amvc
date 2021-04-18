//Handles all the BOM history 
const aHistorize = (url)=>{
    window.history.pushState(null, null, url);
}

//stops all running intervals to enhance pure page reload
const stopAllIntervals = ()=>{
    let lastInterval = setInterval(()=>{}, 9999);
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
            if(cTrim(links[i].getAttribute('href').replace(amvcPageData.siteUrl, '').toLowerCase(), "/") == cTrim(location.href.replace(amvcPageData.siteUrl, ''), "/")){
                links[i].classList.add(activeClass);
            }
            if(links[i].getAttribute("async")){
                links[i].addEventListener('click', event=>{
                    event.preventDefault();
                    new AMVC().asyncLoad(event.currentTarget.getAttribute('href'));
                });
            }
        }
    }
};

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
                if(submitUrl == "amvc.api"){
                    submitUrl = amvcAPI;
                    let apiCommand = generateRequestData("_interaction", event.currentTarget.getAttribute("api-key"));
                    vForm.append("_amvc_request_", apiCommand);
                }

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

//Handles all src path for automatic directory path
const handleSrcpath = ()=>{
    const srcElements = document.querySelectorAll('*');
    srcElements.forEach(srcElement=>{
        if(srcElement.getAttribute("srcpath")){
            if(srcElement.getAttribute("srcpath").length > 1){
                let srcAttribs = srcElement.getAttribute("srcpath").split(",");
                srcAttribs.forEach(srcAttrib=>{
                    let newAttributeValue = srcPath+srcElement.getAttribute(srcAttrib);
                    if(srcElement.getAttribute(srcAttrib)){
                        srcElement.setAttribute(srcAttrib, newAttributeValue);
                    }
                });
            }
        }
    });
}


//converts text to html format and appends it to the main body
const appendToBody = (str = '')=>{
    str = new DOMParser().parseFromString(str, "text/html");
    str.head = "";
    if(str.body){
        document.body.innerHTML = str.body.innerHTML;
        let bodyAttributes      = str.body.attributes;
        for(i=0;i<bodyAttributes.length;i++){
            document.body.setAttribute(bodyAttributes[i].name, bodyAttributes[i].value);
        }
    }else{
        document.body.innerHTML = str;
    }
}


const generateRequestData = (command, data1, data2)=>{
    let requestData = {
        "command":command,
        "data_1":data1,
        "data_2":data2
    };
    return JSON.stringify(requestData);
}

//Initialize or refresh the whole code when page reloads

let checkLoad = setInterval(()=>{
    if(document.body){
        handlers();
        clearInterval(checkLoad);
    }  
}, 1);


const handlers = ()=>{
    handlerouters();
    handleVform();
    handleSrcpath();
}
 


//Initial functions

onpopstate = ()=>{
    if(popStateLoad){
        loader.start();
        let popsatateurl = window.location.href;
        ajax(null, popsatateurl, e=>{
            appendToBody(e);
            refreshCode();
            loader.stop();
        });
    }
} 




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
    handlers();
}