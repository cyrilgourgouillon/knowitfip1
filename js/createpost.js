$(document).ready(function(){

     $("#btnKnowledge, #btnRequest").click(function(event) {
          var id = this.id;
          var other = '#' + ['btnKnowledge','btnRequest'].find(function(e){ return e !== id;});
          chooseType('#'+id, other);
     });

     function chooseType(selected, other){
          $(selected).removeClass('btn-unselected');
          $(other).addClass('btn-unselected');
     }


     var slider = document.getElementById('input-slider');
     noUiSlider.create(slider,{
               start : 10,
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

     $("#postBtn").click(function(event) {
          elems = collectElements();
          if(elems.isCorrect){
               $.post('php/controler/post.php', {
                    function : 'createPost',
                    data : {
                          id: user.id,
                          inputs : elems.inputs,
                          postTag : elems.postTag
                    }
               }, function(feedback){
                    if(feedback.success){
                         if(elems.inputs.type === "knowledge"){
                              window.location = 'knowledges.html';
                         }else{
                              window.location = 'requests.html';
                         }
                    }else{
                         console.log('An error occured while creating a new post')
                    }
                    
               });
          }
     });

     function collectElements(){
          var elems= {
               isCorrect : true,
               inputs : {}
          };

          //Title & Description
          [
               {id: "#postulationTitre", dbName : "titre"},
               {id: "#postulationTextarea", dbName : "description"}
           ].forEach(function(e){
               if($(e.id).val() === ""){
                    $(e.id).addClass('is-invalid');
                    elems.isCorrect = false;
               }else{
                    elems.inputs[e.dbName] = $(e.id).val();
                    $(e.id).removeClass('is-invalid');
               }
          });

           //type
          var selected = [
          {btn : '#btnKnowledge', dbName : 'knowledge'},
          {btn : '#btnRequest', dbName : 'request'}
          ].find(function(e){ return $(e.btn).hasClass('active'); });
          if(selected){
               elems.inputs['type'] = selected.dbName;
               $("#selectText").removeClass('text-danger');
          }else{
               $("#selectText").addClass('text-danger');
               elems.isCorrect = false;
          }

          //nbHeure
          elems.inputs['tmp_estime'] =  parseInt($("#nbHeure").html());

          //tags 
          var tags = getAllTag($("#listCategorie"));
          if(tags.length){
               elems['postTag'] = tags;
               $('#inputTag').removeClass('is-invalid');
          }else{
               $('#inputTag').addClass('is-invalid');
               elems.isCorrect = false;
          }          
          return elems;
     }

});