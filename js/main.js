$('document').ready(function() {
    baseUrl = "http://localhost/~gnaag/panakrala/";

/* TURN ALL LINKS TO TARGET="_BLANK" */
    $.expr[':'].external = function(obj) {
        return !obj.href.match(/^mailto\:/) &&
            (obj.hostname != location.hostname);
    };

    $(function() {
        // Add 'external' CSS class to all external links
        $('a:external').addClass('external');

        // turn target into target=_blank for elements w external class
        $(".external").attr('target', '_blank');

    })

    /* PROCESS CONTACT FORM WITH AJAX */

    $('.contactForm').ajaxForm({
        target: ".contactFormSubmit",
        success: function() {
            $("#submitContactForm").prop('disabled', true);
            $('#contactFormResult').text("Ďakujeme za správu, onedlho Vám odpíšeme.")
        },
        error: function() {
            $('#contactFormResult').text("Vaša správa sa bohužiaľ stratila v hlbinách internetu. Skúste to ešte raz.");
        }
    });

    /* PROCESS REGISTRATION FORM WITH AJAX */

    $('.regForm').ajaxForm({
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
            if ($.isEmptyObject(data)) {} else {
                var items = [];
                $.each(data, function(key, val) {
                    items.push("<li id='" + key + "'>" + val.name + "</li>");
                });

                $("." + eventId + " .guestList").replaceWith("<ol>" + items.join('') + "</ol>");
            }
        });
    }


        /* SHOW/HIDE hiddenInfo on button click */

        $('.hiddenInfo').children('.hideBtn').click(function() {
            $('html, body').animate({
                scrollTop: ($('.hiddenInfo').children('.hideBtn').offset().top - 60)
            }, 500);
            $('.hiddenInfo').children('.foldable').slideToggle('500', "swing");
        })

    /* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
    $('#topMenuHamburger').click(function() {
        var x = document.getElementById("topMenu");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    });

    /* MOVE OBJECT (KING) WHEN SCROLLING PAGE */

    function kingMove(king) {
        $(king).closest(".kingDiv").css("left", $(window).scrollTop())
        let xs = $(window).scrollTop() / 10 | 0;
        if (xs % 4 == 0) {
            $(king).addClass("kingwalk-2");
            $(king).removeClass("kingwalk-3");
            $(king).removeClass("kingwalk-1");
        } else if (xs % 3 == 0) {
            $(king).addClass("kingwalk-3");
            $(king).removeClass("kingwalk-1");
            $(king).removeClass("kingwalk-2");
        } else if (xs % 2 == 0) {
            $(king).addClass("kingwalk-1");
            $(king).removeClass("kingwalk-2");
            $(king).removeClass("kingwalk-3");
        } else {
            $(king).addClass("kingwalk-2");
            $(king).removeClass("kingwalk-1");
            $(king).removeClass("kingwalk-3");
        }
    }

    /* DISPLAY OBJECTS (TOOLTIPS) WHEN SCROLLING PAGE */

    function kingTooltips(king) {
        let kingPos = $(king).closest(".kingDiv").position().left;
        if (kingPos > 50 && kingPos < 250) {
            $(".kingTip-1").show();
            $(".kingTip-2").hide();
            $(".kingTip-3").hide();
        } else if (kingPos > 350 && kingPos < 550) {
            $(".kingTip-1").hide();
            $(".kingTip-2").show();
            $(".kingTip-3").hide();
        } else if (kingPos > 750 && kingPos < 950) {
            $(".kingTip-1").hide();
            $(".kingTip-2").hide();
            $(".kingTip-3").show();
        } else {
            $(".kingTip-1").hide();
            $(".kingTip-2").hide();
            $(".kingTip-3").hide();
        }
    }

    $(function() {
        $(window).scroll(function() {
            kingMove(".topking");
            kingTooltips(".topking");
        });
        $(document).ready(function() {
            kingMove(".topking");
            kingTooltips(".topking");
        });
    });

});
