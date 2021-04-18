//Handle arrays pagination or query returns pagination 
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