$('document').ready(function() {
    baseUrl = "http://localhost/~gnaag/panakrala/";

    $('form').ajaxForm({
        target: ".formResult",
        success: function() {
            ajaxGetEvent("turban", "2018-02-13_historicky");
        },
        error: function() {
            alert("Something's wrong");
        }
    });

    function ajaxGetEvent(projectId, eventId) {
        link = baseUrl + "admin/ajax.php?script=eventGetGuests&eventId=" + eventId + "&projectId=" + projectId;
        $.getJSON(link, function(data) {
            console.log(data);
            var items = [];
            $.each(data, function(key, val) {
                items.push("<li id='" + key + "'>" + val.name + "</li>");
            });

            $("." + eventId + " .guestList").replaceWith("<ol>" + items.join('') + "</ol>");
        });
    }

    /* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
    $('#topMenuHamburger').click(function() {
        var x = document.getElementById("topMenu");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
    }
  });

});
