
function setCookie(name, value){
    var date = new Date();
    date.setTime(date.getTime() + 100*365*24*60*60*1000);
    document.cookie = name + '=' + value + ';expires=' + date.toString() + ';path=/';
}

function getCookie(name){
    var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return value ? value[2] : null;
}


function deleteCookie(name){
    document.cookie = name + '=0;expires=-1;path=/';
}


function callAjax(url, method, data, callback){
    var xhr = new XMLHttpRequest();

    var response = '';

    xhr.onload = function() {
        if( xhr.status === 200 || xhr.status === 201 ){
            callback(xhr.responseText);
        } else {
            console.log(xhr.responseText);
        }
    };

    xhr.open(method, url);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));

}