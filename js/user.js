var user;

class User{
    constructor(id, pseudo, avatar){
        this.id = id;
        this.pseudo = pseudo;
        this.avatar = avatar;
    }

    print(){
    	$('#profilPseudo').html(this.pseudo);
    	if(this.avatar == null)
            $('#profilImage').attr('src','user_pics/default.jpg');
        else
            $('#profilImage').attr('src',this.avatar);
    }

}

function getCrtUser(){
    $.post('php/controler/utilisateur.php',{
        function : 'getBasicUserInfo',
        data : ['']
    },function(feedback){
        handleFeedback(feedback);
    });
}

function handleFeedback(feedback){
    if(feedback.success){
        user = new User(
            feedback.data.id,
            feedback.data.pseudo,
            feedback.data.avatar
        );
        if(feedback.data.pseudo === null){
			if(location.href.split('/').pop() !=='editprofil.html')
				window.location = 'editprofil.html';
        }

        user.print();

    }else{
        window.location = 'login.html';
    }
}

getCrtUser();


