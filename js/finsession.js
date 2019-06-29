$(document).ready(function(){
    waitForElement();
});

function loadCredit(){
        $.post('php/controler/session.php',{
        function : 'getCreditSession',
        data : {
           id: findGetParameter('session'),
           idUSer: user.id
        }
    },function(feedback){
        if(feedback.success){
            showCredit(feedback.data);
        }else{
           console.log('An error occured while loading the data');
        }
    });
}

function showCredit(data){
    if(data.credit > 0){
        $("#gagneouperdu").html("gagnés");
    }else{
        $("#gagneouperdu").html("perdus");
    }
    $("#credit").html(data.credit);
}

function loadExp(){
    $.post('php/controler/session.php',{
        function : 'getExperienceSession',
        data : {
           id: findGetParameter('session')
        }
    },function(feedback){
        if(feedback.success){
            showExps(feedback.data);
        }else{
           console.log('An error occured while loading the data');
        }
    });
}

function showExps(exps){
    exps.forEach(function(exp){
        showExp(exp);
    });
}

function showExp(exp){
    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];
    var classe = classes[Math.floor(Math.random()*classes.length)];

    html='';
    html += '<div class="mb-2 d-flex justify-content-between align-items-center px-3">';
    html += '<span class="badge-pill '+classe+'">'+exp.competence+'</span>';
    html += '<span class="ml-3"> + '+exp.experience+' XP </span></div>';

    $("#competences").append(html);
}

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
            showTitre(feedback.data);
        }else{
            console.log('An error occured while loading the data');
        }
    });
}


function showTitre(data){
    $("#titre").html(data.post.titre);
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




function waitForElement(){
    if(typeof user !== "undefined"){
        loadSession();
        loadCredit();
        loadExp();
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
