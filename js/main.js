$('document').ready(function() {
    $('form').ajaxForm({
     target: ".formResult",
     success: function(){},
     error: function(){alert("Something's wrong")}
   });
});
