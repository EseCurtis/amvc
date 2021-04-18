//Main AMVC class
class AMVC {
    //routing
    routes(currentRoutes){
        currentRoutes.push({route:'$',script:'$'});
        let comparisonRoute = cTrim((location.origin+location.pathname).toLowerCase(), '/').split('/');
        let mainRoute = [];
        let validRoutes = [];
        currentRoutes.forEach(currentRoute=>{
            let routeValues = '{';
            let comparisonRouteSliced = comparisonRoute;
            
            currentRoute.route = cTrim(amvcPageData.siteUrl+"/"+currentRoute.route.toLowerCase(), '/').split('/');
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
        let interactionData = (generateRequestData('_interaction', interactorKey, null));
        fd.append('_amvc_request_', interactionData);
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
            refreshCode();
            loader.stop();
        }); 
    }

    //asyncronous loading based on routes
    routeLoad(route = null){
        route = route.replace(/^[/]/g, '');
        let linkUrl = amvcPageData.siteUrl+'/'+route;
        loader.start();
        stopAllIntervals();
        ajax(null, linkUrl, output=>{
            appendToBody(output);
            aHistorize(linkUrl);
            refreshCode();
            loader.stop();
        }); 
    }

    //asyncronous reloading
    asyncReload(){
        loader.start();
        stopAllIntervals();
        ajax(null, location.href, output=>{  
            appendToBody(output);
            refreshCode();
            loader.stop();
        });
    }
}
