$(document).ready(function(){
    waitForElement();
});

function loadSession(){
    $.post('php/controler/session.php',{
        function : 'getPostAndCandidacyFromSession',
        data : {
           id: findGetParameter('session')
        }
    },function(feedback){
        if(feedback.success){
            loadPost(feedback.data);
        }else{
           console.log('An error occured while loading the data');
        }
    });
}


function loadPost(data){
    $.post('php/controler/post.php',{
        function : 'getPost',
        data : {
            id: data.post
    }
     },function(feedback){
        if(feedback.success){
            showType(feedback.data.post.type);
            loadProfil(feedback.data.post.utilisateur);
            showAuteur(feedback.data);
        }else{
            console.log('An error occured while loading the data');
        }
    });
}


function showAuteur(data){
    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    $("#titre").html(data.post.titre);
    $("#imgAuteur").attr('src', 'user_pics/'+ data.post.utilisateur +'.jpg');
    $("#pseudoAuteur").html(data.post.pseudo);
    $("#descAuteur").html(data.post.description);
    $("#nbHeureAuteur").html(data.post.tmp_estime);
    $("#dateAuteur").html(data.post.date);

    data.tag.forEach(function(tag){
        var classe = classes[Math.floor(Math.random()*classes.length)];
        var badge = '<span class="badge mx-1 '+ classe +'">'+tag.libelle+'</span>'
        $("#categories").append(badge);
    });

}

function showType(tag){
    if(tag === 'knowledge'){
        $("#type").addClass('badge-success');
        $("#type").html('Knowledge');
    }else{
        $("#type").addClass('bg-violet');
        $("#type").html('Request');
    }
}



$("#evalSession").click(function(){
    console.log($('input[name=note]:checked').val());
    if($('input[name=note]:checked').val()){
        note();
    }
});

function note(){
    $.post('php/controler/session.php',{
        function : 'evaluateSession',
        data : {
           idSession: findGetParameter('session'),
           note : $('input[name=note]:checked').val()
       }
    },function(feedback){
        if(feedback.success){
            window.location = "finsession.html?session=" + findGetParameter('session');
        }else{
           console.log('An error occured while loading the data');
       }
    });
}


function waitForElement(){
    if(typeof user !== "undefined"){

        /**
        * ALL PAGES EXECUTE
        */
        loadReseau();
        loadMessages();
        loadNotification();

        /**
         * THIS PAGE EXECUTE
        */

        loadSession();
     
    }else{
        setTimeout(waitForElement, 250);
    }
}


/**
 * Get the Get param :: From stackoverfolow
 * https://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript
 * @param      {<type>}    parameterName  The parameter name
 * @return     {Function}  { description_of_the_return_value }
 */
 function findGetParameter(parameterName) {
    var result = null,
    tmp = [];
    location.search
    .substr(1)
    .split("&")
    .forEach(function (item) {
      tmp = item.split("=");
      if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
  });
    return result;
}
