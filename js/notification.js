var notifications;

setInterval(loadNotification, 3000); 

function loadNotification(){
   $.post("php/controler/notification.php",{
      function : 'getNotificationByUser',
      data : {
         id : user.id
      }
   }, function(feedback){
      if(feedback.success){
         notifications = feedback.data;
         showNotifications(notifications);
      }else{
         console.log('An error occured while loading the notifications');
      }
   });
}
function showNotifications(notifications){
   $("#notifications").empty();
   var unreadNumber = notifications.reduce(function(acc, e){
      if(e.has_been_seen === "0"){
         return ++acc;
      }
      return acc;
   },0);
   if(unreadNumber !== 0)
      $("#notificationCounter").html(unreadNumber);

   notifications.forEach(function(notification){
      showNotification(notification);
   });
}
function showNotification(notification){
   var html = '';
   
   if(notification.type === "Candidature"){
      html += '<a class="dropdown-item d-flex align-items-center pb-3" href="confirmation.html?candidature='+notification.idObjet+'"><div class="mr-3">';
   }
   if(notification.type === "Candidat"){
      html += '<a class="dropdown-item d-flex align-items-center pb-3" href="candidat.html?post='+notification.idObjet+'"><div class="mr-3">';
   }
   if(notification.type === "Postulation"){
      html += '<a class="dropdown-item d-flex align-items-center pb-3" href="postulations.html"><div class="mr-3">';
   }
   if(notification.type === "Session"){
      html += '<a class="dropdown-item d-flex align-items-center pb-3" href="sessions.html"><div class="mr-3">';
   }
   if(notification.type === "Bilan"){
      html += '<a class="dropdown-item d-flex align-items-center pb-3" href="finsession.html?session='+ notification.idObjet +'"><div class="mr-3">';
   }
   html += '<div class="icon-circle bg-primary">';
   html += '<i class="far fa-bell text-white"></i></i></div></div><div>';
   html += '<div class="small text-gray-500">'+ notification.date +'</div>';
   if(notification.has_been_seen === "0"){
      html += '<span class="font-weight-bold">';
   }else{
      html += '<span>';
   }
   
   html +=  notification.texte+'</span></div></a>';
   $("#notifications").append(html);
}

$("#notifBtn").click(function(event) {
   loadNotification()
   var notificationsIds = notifications.map(function(e){
      return e.id;
   });
   hasBeenRead(notificationsIds);
});

function hasBeenRead(notificationsIds){
   $.post("php/controler/notification.php",{
      function : 'seeNotification',
      data : {
         ids : notificationsIds
      }
   }, function(feedback){
      if(feedback.success){
         $("#notificationCounter").html("");
      }else{
         console.log('An error occured while loading the network');
      }
   });
}