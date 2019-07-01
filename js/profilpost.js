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
               showComments(feedback.data.message, feedback.data.tmp_estime, feedback.data.date);
               post = feedback.data.post;
               loadProfil(feedback.data.candidat);
               loadPost(post);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showComments(message, tmp_estime, date){
	$("#message").html(message);
	$("#tmp_estime").html(tmp_estime);
	$("#dateCandidat").html(date);
}

function loadPost(id){
     $.post('php/controler/post.php',{
          function : 'getPost',
          data : {
               id: id
          }
     },function(feedback){
          if(feedback.success){
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
	$('#acceptModal').modal('show');
});

$("#declinetBtn").click(function(event) {
	$('#declineModal').modal('show');
});
$("#acceptConfirm").click(function(event) {
	if($("#messageConfirm").val() !== ""){
		$("#messageConfirm").removeClass('is-invalid');

		 $.post('php/controler/candidature.php',{
	        function : 'accepterCandidature',
	        data : {
	            id: findGetParameter('candidature'),
	            reponse: $("#messageConfirm").val()
	        }
     	},function(feedback){
     		if(feedback.success){
     			window.location="candidat.html?post="+post;
     		}else{
     			console.log('Une erreur est survenue');
     		}
     	});

	}else{
		$("#messageConfirm").addClass('is-invalid');
	}
});
	

$("#declineConfirm").click(function(event) {

	$.post('php/controler/candidature.php',{
        function : 'refuserCandidature',
        data : {
            id: findGetParameter('candidature')
        }
 	},function(feedback){
 		if(feedback.success){
 			window.location="candidat.html?post="+post;
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
