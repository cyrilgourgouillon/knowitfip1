$(document).ready(function(){
    waitForElement();
});

$('#modifConfirmButton').click(function(){
    collectElements();
});

//Load the user feed
function loadFeed(){
    $.post('php/controler/filActu.php',{
        function : 'afficherFilActu',
        data : {
            id: user.id
        }
    },
        function (feedback) {
            if(feedback.success)
                showFeed(feedback.data);
            else
                console.log("Une erreur est survenue lors du chargement du fil");
        }
    );
}

function showFeed(data){
    
    /*
    var request = data.filter(function(e){
        return e.type === "request";
    })

    var knowledge = data.filter(function(e){
        return e.type === "knowledge";
    })

    */

    data.forEach(function(post){
        createPost(post);
    });

}

function createPost(post){

    var html = '';

    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    if(post.type === 'request'){
        html += '<div class="mt-3 card border-left-violet shadow">';
    }else{
        html += '<div class="mt-3 card border-left-success shadow">';
    }
    
    html +='<div class="card-body"><div class="row no-gutters align-items-center"><div class="col">';
    html +='<div class="text-lg font-weight-bold ">'+ post.titre +'</div>';
    html +='<div class="text-xs font-weight-bold text-gray-500 mb-1">Publié le '+ post.date +'</div>';
    html +='<div style="line-height: 1.1rem" class="mb-1">'+ post.description +'</div><div class="mt-3 d-flex align-items-center justify-content-between"><div>';
    html +='<img width="30px" height="30px" class="img-profile rounded-circle mr-1"  src="user_pics/' + post.idUser + '.jpg">'
    html +='<span class="mr-2 d-lg-inline text-gray-600 small">'+ post.pseudo +'</span></div><div>';

    post.tag.forEach(function(tag){
        var classe = classes[Math.floor(Math.random()*classes.length)];
        html += '<span class="badge mx-1 px-1 '+ classe +'">'+ tag.libelle +'</span>';
    });

    html +='</div></div></div><div class="col-md-3 d-flex justify-content-center">';
    if(post.type === "knowledge"){
        html +='<button onclick="window.location=\'post.html?post='+ post.idPost +'\'" class="btn rounded-circle chevron-rounded border-0 text-success" id="newBtn"><i class="fas fa-chevron-right"></i></button>';
        }else{
            html +='<button onclick="window.location=\'post.html?post='+ post.idPost +'\'" class="btn rounded-circle chevron-rounded border-0 violet" id="newBtn"><i class="fas fa-chevron-right"></i></button>';
        }

    html +='</div></div></div></div>';

    if(post.type === 'request'){
        $("#requestContainer").append(html);
    }else{
        $("#knowledgeContainer").append(html);
    }

}




function waitForElement(){
    if(typeof user !== "undefined"){
        loadFeed();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}
