$(document).ready(function(){
    waitForElement();
});

function waitForElement(){
    if(typeof user !== "undefined"){

        /**
        * ALL PAGES EXECUTE
        */
        loadReseau();
        loadMessages();
        loadNotification();
     
    }else{
        setTimeout(waitForElement, 250);
    }
}