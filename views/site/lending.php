<style>
    body {
        background: url(landing.jpg) no-repeat;
        -moz-background-size: 100%; /* Firefox 3.6+ */
        -webkit-background-size: 100%; /* Safari 3.1+ и Chrome 4.0+ */
        -o-background-size: 100%; /* Opera 9.6+ */
        background-size: 100%; /* Современные браузеры */
    }
</style>
<script>
    $('document').ready( function() {
        if(this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 500);
    });

    $(window).resize(function() {
        if(this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 500);
    });

    $(window).bind('resizeEnd', function() {
        console.log($(this).width());
        console.log($(this).height());
        var h = $(this).width();
        h =  parseInt(h*1.71875);
        $('#h').height(h);
    });
</script>
<a href="viber://pa?chatURI=taxi-m">
    <div class="col-lg-10 appBackground" id="h" style="height: 3300px;"></div>
</a>