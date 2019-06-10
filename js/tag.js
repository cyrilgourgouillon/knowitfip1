var tags;

$( document ).ready(function(){
     loadTags();
});

function loadTags(){
     $.post('php/controler/competence.php',{
          function : 'getAllCompetence',
          data : ['']
     }, function(feedback){
          if(feedback.success){
               tags = feedback.data;
               var libelles = tags.map(function(e) {
                    return e.libelle;
               });
               $("input[tag-type='tagInput']").each(function(index, el) {
                    autocomplete(el,libelles);
               });
          }
     });
}

$("button[tag-type='tagSubmitButton']").click(function(event) {
     //get the input
     var input = $(this).parent().prev();

     //get the tag of the input text
     var tag = tags.find(function(e){
          return e.libelle === input.val();
     });

     //get the ul tag list
     var ul = $(this).parent().parent().parent().next();

     //if tag is in the array and not already in the list
     if(tag != null && !getAllTag(ul).includes(tag.id)){
          input.removeClass('is-invalid');
          input.val('');
          addTag(ul,tag);
     }else{
          input.addClass('is-invalid');
     }
});

function addTag(ul, tag){
     var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];
     var classe = classes[Math.floor(Math.random()*classes.length)];
     htmlLiTag='<li class="list-group-item d-flex justify-content-between align-items-center "><span  tag-type="libelle" tag-id="' + tag.id + '" class="badge badge-pill text-uppercase '+ classe +'"> ' + tag.libelle + ' </span><button tag-type="btnDelete" type="button" class="close btn rouded-hover" aria-label="Close"><span aria-hidden="true">&times;</span></button></li>'
     ul.append(htmlLiTag);
}

$('body').on('click', "button[tag-type='btnDelete']", function(event) {
     $(this).parent().remove();
});

function getAllTag(ul){
     return ul.find('span[tag-type="libelle"]').map(function(index, el) {
          return $(el).attr('tag-id');
     }).toArray();
}


/**
 * W3S AUTOCOMPLETE FUNCTION
 *
 * @param      DOM INPUT   inp     The inp
 * @param      Array  arr     The arr
 * @return     {boolean}  { description_of_the_return_value }
 */                 
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    }
  }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});
}