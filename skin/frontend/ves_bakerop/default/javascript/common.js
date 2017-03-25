(function($) {
    $(window).ready( function(){
         /* automatic keep header always displaying on top */
        if( $("body").hasClass("layout-boxed-md") || $("body").hasClass("layout-boxed-lg") ){

        }else if( $("body").hasClass("keep-header") ){
            var mb = parseInt($("#header-main").css("margin-bottom"));
            var hideheight =  $("#topbar").height()+mb+mb; 
            var hh =  $("#header").height() + mb;  
            var updateTopbar = function(){
                 var pos = $(window).scrollTop();
                 if( pos >= hideheight ){
                    //$("#page").css( "padding-top", hh );
                    $("#header").addClass('hide-bar');
                    $("#header").addClass( "navbar navbar-fixed-top" );
                  
                }else {
                    $("#header").removeClass('hide-bar');
                } 
            }
            $(window).scroll(function() {
               updateTopbar();
            });
        }
        /*lazy load image*/
        if($("img.lazy").length > 0) {
            $("img.lazy").lazyload({ event: "scroll whenever-i-want", threshold : 200, effect: "show"});/*effect: show | fadein*/
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $(document).trigger('whenever-i-want');
            });
            $(".carousel").on('slid.bs.carousel', function (e) {
                var $active = $(e.target).find('.carousel-inner > .item.active').first();
                if($active.find("img.lazy").length > 0) {
                    $active.find("img.lazy").lazyload({ event: "whenever-i-want", threshold : 200, effect: "show"});/*effect: show | fadein*/
                    $active.find("img.lazy").trigger('whenever-i-want');
                }
            });
        }
        
    });

})(jQuery);