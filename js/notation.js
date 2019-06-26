$(document).ready(function(){
    waitForElement();
});

function loadSession(){
    $.post('php/controler/session.php',{
        function : 'getSessionInfo',
        data : {
             id: findGetParameter('session')
        }
   },function(feedback){
        if(feedback.success){
            loadPost(feedback.data.post);
        }else{
             console.log('An error occured while loading the data');
        }
   });
}


function loadPost(){
     $.post('php/controler/post.php',{
          function : 'getPost',
          data : {
               id: findGetParameter('post')
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


$("#postBtn").click(function(){
    var elems =  collectElements();
    if(elems !== false){
        $.post('php/controler/candidature.php',{
            function : 'candidaterPost',
            data : {
                idUser: user.id,
                idPost : findGetParameter('post'),
                data : elems
            }
            },function(feedback){
                if(feedback.success){
                    location.reload("postulation.html");
                }else{
                    console.log("An error occured : " + feedback.message);
                }
        });
    }
});

$("#evalSession").click(function(){

    $.post('php/controler/competenceUtilisateur.php',{
        function : 'getSessionInfo',
        data : {
             idSession: findGetParameter('session'),
             idUser : user.id
        }
   },function(feedback){
        if(feedback.success){
            loadPost(feedback.data.post);
        }else{
             console.log('An error occured while loading the data');
        }
   });


});




function waitForElement(){
    if(typeof user !== "undefined"){
        loadPost();
    }
    else{
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
