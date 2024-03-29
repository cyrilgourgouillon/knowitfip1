var imgChange = false;

$(document).ready(function(){
    waitForElement();
});

$('#modifConfirmButton').click(function(){
    collectElements();
});

//Load the user information from the profile
function loadUser(){
    $.post('php/controler/utilisateur.php',{
        function : 'getUser',
        data : {
            id: user.id
        }
    },
        function (feedback) {
            if(feedback.success)
                showUser(feedback.data);
            else
                console.log("Une erreur est survenue lros du chargement du profil");
        }
    );
}

//Show the data of the feedback on the profile
function showUser(data){
    data.tag.forEach(function(e){
        addTag($("#listCompetence"), e);
    });
    data.wishTag.forEach(function(e){
        addTag($("#listCompetenceSouhaite"), e);
    });

    if(data.user.date_naissance !== '0000-00-00')
        $('#inputDate').val(data.user.date_naissance);

    if(data.user.avatar == null)
        $('#inputImage').attr('src','user_pics/default.jpg');
    else
        $('#inputImage').attr('src',data.user.avatar);

    $('#inputPseudo').val(data.user.pseudo);
    $('#inputDescription').val(data.user.description);
    $('#inputNom').val(data.user.nom);
    $('#inputPrenom').val(data.user.prenom);
    $('#inputMail').val(data.user.mail);
}

//Collect elements and lauch saveModification
function collectElements(){
    //bool to check if we send the modification
    var send = true;

    //Data form the inputs
    var data = {
        pseudo : $('#inputPseudo').val(),
        date_naissance : $('#inputDate').val(),
        description : $('#inputDescription').val(),
        nom : $('#inputNom').val(),
        prenom : $('#inputPrenom').val(),
        mail : $('#inputMail').val()
    }

    //Check password if it was modified
    if($("#inputPassword").val() !== ""){
        var result = checkPassword($("#inputPassword"),$("#inputPasswordConfirm"));
        if(result !== false)
            data.mdp = result;
        else
            send = false;
    }

    // Collect tags
    var userTag = getAllTag($("#listCompetence"));
    var wishTag = getAllTag($("#listCompetenceSouhaite"));

    if(send){
        saveModication(data, userTag, wishTag);
    }
}

//Check password
function checkPassword(firstPassword, secondPassword){
    var success = true;

    //test password 5 caracs minimum
    [firstPassword, secondPassword].forEach( function(e) {
        if(e.val().length <= 5){
            e.addClass('is-invalid');
            success = false;
        }else{
            e.removeClass('is-invalid');
        }
    });

    //test if password are the same
    if(success === true){
            if(firstPassword.val() !== secondPassword.val()){
                firstPassword.addClass('is-invalid');
                secondPassword.addClass('is-invalid');
                success = false;
            }else{
                firstPassword.removeClass('is-invalid');
                secondPassword.removeClass('is-invalid');
            }
    }

    if(success)
        return firstPassword.val();
    else 
        return false;
}


//Send modification to the server and redirect if true on the profil
function saveModication(data, userTag, wishTag){
    // Format empry array before sending
    if(userTag.length === 0){
        userTag = [""];
    }
    if(wishTag.length === 0){
        wishTag = [""];
    }
    if(data.date_naissance ===''){
        delete data.date_naissance;
    }

    handleImage();

    $.post('php/controler/utilisateur.php',{
        function : 'editUser',
        data : {
            id: user.id,
            data : data,
            userTag : userTag,
            wishTag : wishTag
        }
    }, function(feedback){
        if(feedback.success){
            if(!imgChange){ 
                window.location = 'profil.html';
            }
        }else{
            console.log('An error occurend while sending the profil modification');
        }
    });
}

function handleImage(){
    if(imgChange){
       if($('#inputImage').is('div')){
            var file_data = $('#imageFile').prop('files')[0];   
            var form_data = new FormData();                  
            form_data.append('file', file_data);        
            form_data.append('function', 'addAvatar');     
            form_data.append('data[id]',  user.id);     
            $.ajax({
                url: 'php/controler/utilisateur.php', // point to server-side PHP script 
                dataType: 'json', 
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,     
                type: 'post',
                success: function(feedback){
                    if(feedback.success){
                         window.location = 'profil.html';
                     }else{
                        $("#inputImage").addClass('border border-danger');
                        console.log('Une erreur est survenue : ' + feedback.message);
                     }
                }
             });
       }else {
            $.post('php/controler/utilisateur.php',{
                function : 'deleteAvatar',
                data : {
                    id: user.id,
                },function(){
                    window.location = 'profil.html';
                }
            });
       }
      
       
    }
}

$("#btnImportImage").click(function(event) {
    $('#imageFile').click();
});

$("#btnDeleteImage").click(function(event) {
    var img = '<img id="inputImage" width="200px" height="200px" class="img-profile rounded-circle" src="user_pics/default.jpg">';
    $('#inputImage').after(img).remove();
    imgChange = true;
});


$('#imageFile').change(function () {
    var file = this.files[0];
    var reader = new FileReader();
    reader.onloadend = function () {
        var img = '<div id="inputImage" style="margin-left:auto; margin-right:0; background-size: cover; background-position: center; width:200px; height :200px; background-image : url(\'' + reader.result + '\')" class="img-profile text-center-responsive-xl rounded-circle" ></div>'
        $('#inputImage').after(img).remove();
        imgChange = true;
    }
    if (file) {
        reader.readAsDataURL(file);
    } else {
    }
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

        loadUser();
     
    }else{
        setTimeout(waitForElement, 250);
    }
}