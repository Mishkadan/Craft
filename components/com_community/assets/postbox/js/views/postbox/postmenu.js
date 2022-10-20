
jQuery(function ($){

    var postbtn = $('#addpost').css('pointer-events','auto'),
        bkg = $('<div class="vue--joms-postbox-bkg" style="display: none">'),
        postbox =   $('.joms-postbox'),
        $svg = postbtn.find('svg').css('fill', '#2BAF52');

    $(document).on('change', '.dz-hidden-input', preView );

    bkg.click( closePostbox );

    $(document).on('click', '.joms-postbox-save', function () {
        setTimeout( closePostbox, 500 );
    } );

    postbtn.click(function (e) {
        e.preventDefault();
        if(postbox.hasClass('is-active')) {
            closePostbox();
            return false;
        }
        $svg.css({
            'transform' : 'rotate(45deg)',
            'fill'      : 'red'
        });
        postbox.addClass('is-active chat-menu');
        bkg.prependTo('body').show();
        $('body').css('overflow', 'hidden');
    });

    function preView(evt) {
        let thumbbox = $('.dz-image').not('[data-thumb]'),
            span;

        $.each(evt.target.files, function (i,v) {

            if(v.type.match('image.*')) {
                thumbbox.eq(i).attr('data-thumb', 'image');

            } else if (v.type.match('video.*')) {

                let videoURL = URL.createObjectURL(v);
                span = $('<div>').addClass('minivid');
                span.html('<video class="minivid" src="'+ videoURL +'" autoplay muted> Ваш браузер не поддерживает видео, пора на upgrade ;)</video>');
                thumbbox.eq(i).attr('data-thumb', 'video').prepend(span);

            } else if (v.type.match('audio.*')) {

                span = $('<svg viewBox="0 0 24 22" class="js-icon-blue"><use xlink:href="/#lenta-music"></use></svg>');
                thumbbox.eq(i).css('background', '#f0f8ff').attr('data-thumb', 'audio').prepend(span);

            } else {
                span = $('<svg viewBox="-4 -2 24 22" class="js-icon-file"><use xlink:href="/#joms-icon-file-zip"></use></svg>');
                thumbbox.eq(i).attr('data-thumb', 'file').prepend(span);
            }
        });
        $('.joms-postbox-preview').animate({scrollLeft: -9999},2000);
    }

    function closePostbox() {
        postbox.removeClass('is-active');
        bkg.hide();
        $svg.css({'transform': 'rotate(0deg)', 'fill' : '#2BAF52'});
        $('body').css('overflow', 'auto');
    }
});