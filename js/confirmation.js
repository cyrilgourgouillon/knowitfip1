$(document).ready(function(){
    waitForElement();
});

var post;

function loadCandidature(){
     $.post('php/controler/candidature.php',{
          function : 'getCommentCandidature',
          data : {
               id: findGetParameter('candidature')
          }
     },function(feedback){
          if(feedback.success){
               showComments(feedback.data);
               post = feedback.data.post;
               loadPost(post);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showComments(data){
     $("#message").html(data.message);
     $("#tmp_estime").html(data.tmp_estime);
     $("#dateCandidat").html(data.date);
     $("#reponse").html(data.reponse);
}

function loadPost(id){
     $.post('php/controler/post.php',{
          function : 'getPost',
          data : {
               id: id
          }
     },function(feedback){
          if(feedback.success){
               loadProfil(feedback.data.post.utilisateur);
               showAuteur(feedback.data);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showAuteur(data){
    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    $("#imgAuteur").attr('src', 'user_pics/'+ data.post.utilisateur +'.jpg');
    $("#pseudoAuteur").html(data.post.pseudo);
    $("#descAuteur").html(data.post.description);
    $("#nbHeureAuteur").html(data.post.tmp_estime);

    data.tag.forEach(function(tag){
        var classe = classes[Math.floor(Math.random()*classes.length)];
        var badge = '<span class="badge mx-1 '+ classe +'">'+tag.libelle+'</span>'
        $("#categories").append(badge);
    });

    $("#dateAuteur").html(data.post.date);
}


$("#acceptBtn").click(function(event) {
     $.post('php/controler/candidature.php',{
        function : 'startSession',
        data : {
            id: findGetParameter('candidature')
        }
     },function(feedback){
          if(feedback.success){
               window.location="sessions.html";
          }else{
               console.log('Une erreur est survenue');
          }
     });
});

$("#declinetBtn").click(function(event) {
     $.post('php/controler/candidature.php',{
        function : 'annuleCandidature',
        data : {
            id: findGetParameter('candidature')
        }
     },function(feedback){
          if(feedback.success){
               window.location="postulations.html";
          }else{
               console.log('Une erreur est survenue');
          }
     });
});

     
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

          loadCandidature();

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
