$(document).ready(function(){
    waitForElement();
});

function loadKnowledges(){
     $.post('php/controler/post.php',{
          function : 'getPostByUser',
          data : {
               id: user.id,
               type : 'request'
          }
     },function(feedback){
          if(feedback.success){
               showPost(feedback.data);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showPost(posts){
     posts.forEach(function(post){
          createPost(post);
     });
}

function createPost(post){
     var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

     var html = '<div class="mt-3 card border-left-violet shadow"><div class="card-body"><div class="row no-gutters align-items-center"> <div class="col-xl-6">';
     html += '<div class="text-lg font-weight-bold ">' + post.titre + '</div>';
     html += '<div class="text-xs font-weight-bold text-gray-500 mb-1">Publié le ' + post.date + '</div>';
     html += '<div style="line-height: 1.1rem" class="mb-1">'+ post.description +' </div>';
     html += '<div class="mt-3 d-flex align-items-center justify-content-between"><div>';
     html += '<img width="30px" height="30px" class="img-profile rounded-circle mr-1" src="user_pics/' + post.utilisateur + '.jpg">';
     html += '<span class="mr-2 d-lg-inline text-gray-600 small">'+ post.pseudo +'</span>';
     html += '</div><div>';
     post.tag.forEach(function(tag){
          var classe = classes[Math.floor(Math.random()*classes.length)];
          html += '<span class="badge mx-1 px-1 '+ classe +'">'+ tag.libelle +'</span>';
     })
     html += '</div> </div></div><div class="col-xl-6  px-3"> <div class="row"><div class="col-md-6 d-flex justify-content-around align-items-center">'
     html += '<button onclick="window.location=\'post.html?post='+ post.id +'\'" class="btn rounded-circle chevron-rounded border-0 violet" id="newBtn"><i class="fas fa-chevron-right"></i></button>';
     html +='<button onclick="window.location=\'candidat.html?post='+ post.id +'\'" class="btn rounded-circle chevron-rounded border-0 text-primary" id="newBtn"><i class="fas fa-users"></i></button></div><div class="col-md-6 d-flex justify-content-around align-items-center">';
     html += '<button class="btn rounded-circle chevron-rounded border-0 text-danger" id="newBtn"> <i class="fas fa-trash"></i></button><div>';
     html += post.nbCandidat + ' candidats';
     html += '</div></div></div></div></div></div></div>';

     $("#post-container").append(html);
}

function waitForElement(){
    if(typeof user !== "undefined"){
        loadKnowledges();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}