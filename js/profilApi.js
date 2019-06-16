//Load the user information from the profile
function loadProfil(id){
    $.post('php/controler/utilisateur.php',{
        function : 'showProfile',
        data : {
            id: id
        }
    },
        function (feedback) {
            if(feedback.success){
                showUser(feedback.data);
                showTag(feedback.data.competences);
              }else{
                console.log("Une erreur est survenue lors du chargement du profil");
              }
        }
    );
}


//Show the data of the feedback on the profile
function showUser(data){

    $('#pseudo').html(data.pseudo);
    $('#description').html(data.description);
    $('#nbKnowledge1').html(data.knowledge_count);
    $('#nbRequest1').html(data.request_count);
    $('#age').html(data.age);

    if(data.avatar == null)
        $('#inputImage').attr('src','user_pics/default.jpg');
    else
        $('#inputImage').attr('src',data.avatar);
     
   $('#credit').html(data.credit);
}

//Show the tag
function showTag(tags){
     var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];
     tags.forEach( function(tag) {
          var classe = classes[Math.floor(Math.random()*classes.length)];
          var badge = '<span class="badge mx-1 '+ classe +'">'+tag.libelle+'</span>'
          $("#competences-badge").append(badge);

          var bar = '<div class="mb-2"><span class="small ">'+tag.libelle+' • '+tag.experience+' exp</span><div class="progress mb-3"><div class="progress-bar bg-warning" role="progressbar" style="width: '+(tag.experience/10)+'%" aria-valuenow="'+(tag.experience/10)+'" aria-valuemin="0" aria-valuemax="100"></div> </div></div>';
          $("#competences-details").append(bar);
     });
}