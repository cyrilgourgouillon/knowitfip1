$(document).ready(function(){
    waitForElement();
});

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
              isCandidat(findGetParameter('post'), user.id)
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function isCandidat(post, user){
	 $.post('php/controler/candidature.php',{
          function : 'isCandidat',
          data : {
               post: post,
               user: user
          }
     },function(feedback){
     	if(feedback.success){
     		if(feedback.data){
     			showCandidat(feedback.data);
     		}else{
     			showForm();
     		}
     	}else{
     		console.log('Une erreur est survenue');
     	}
     });
}

function showCandidat(data){
	$("#candidature").removeClass('d-none');
	$("#imgCandidat").attr('src', 'user_pics/'+ data.idUser +'.jpg');
	$("#message").html(data.message);
	$("#nbHeureCandidat").html(data.tmp_estime);
	$("#dateCandidat").html(data.date);
}

function showForm(){
	$("#postForm").removeClass('d-none');
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

function showAuteur(data){
    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    $("#titre").html(data.post.titre);
    $("#imgAuteur").attr('src', 'user_pics/'+ data.post.utilisateur +'.jpg');
    $("#pseudoAuteur").html(data.post.pseudo);
    $("#descAuteur").html(data.post.description);
    $("#nbHeureAuteur").html(data.post.tmp_estime);
    $("#ecrirePour").html(data.post.pseudo);

    data.tag.forEach(function(tag){
        var classe = classes[Math.floor(Math.random()*classes.length)];
        var badge = '<span class="badge mx-1 '+ classe +'">'+tag.libelle+'</span>'
        $("#categories").append(badge);
    });

    $("#dateAuteur").html(data.post.date);

    var slider = document.getElementById('input-slider');
     noUiSlider.create(slider,{
               start : data.post.tmp_estime,
               connect : [true, false],
               range: {
                    'min': 1,
                    'max': 40
                }
          }
);

     slider.noUiSlider.on('update', function (values, handle) {
         $("#nbHeure").html(Math.round(values[handle]));
     });

     slider.setAttribute('disabled', true);

    $("#heureCheck").change(function() {
        if(this.checked){
            slider.removeAttribute('disabled');
        }
        else{
            slider.setAttribute('disabled', false);
            slider.noUiSlider.set(data.post.tmp_estime);
        }
    });


    if(user.id === data.post.utilisateur){
        $('#postBtn').addClass('disabled');
        $('#postulationTextarea').prop('disabled', true);
        $('#heureCheck').prop('disabled', true);
    }

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

        loadPost();
     
    }else{
        setTimeout(waitForElement, 250);
    }
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

function collectElements(){
    if($("#postulationTextarea").val() !== ""){
        $("#postulationTextarea").removeClass('is-invalid');
        return{
            message : $("#postulationTextarea").val(),
            tmp_estime : parseInt($("#nbHeure").html())
        }
    }
    $("#postulationTextarea").addClass('is-invalid');
    return false;
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
