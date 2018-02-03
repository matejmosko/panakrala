$('document').ready(function() {
    $('form').ajaxForm({
     target: ".formResult",
     success: function(){alert("Great Work")},
     error: function(){alert("Something's wrong")}
   });
});
