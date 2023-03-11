<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<title>{{ $title ?? '' }}</title>

@stack('headcss')

<link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- topbar js: @see documention https://buunguyen.github.io/topbar/ -->
<script src="https://cdn.jsdelivr.net/npm/topbar"></script>
<!-- ansi_to_html: @see https://github.com/drudru/ansi_up -->
<script src="https://cdn.jsdelivr.net/npm/ansi_up"></script>
<!-- jquery: @see documention http://jquery.cuishifeng.cn/ -->
<script src="https://cdn.jsdelivr.net/npm/jquery"></script>
<!-- jquery throttle and debounce: @see https://stackoverflow.com/questions/27787768/debounce-function-in-jquery -->
<script src="https://cdn.jsdelivr.net/npm/jquery-throttle-debounce"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/iframe-resizer/js/iframeResizer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/iframe-resizer/js/iframeResizer.contentWindow.js"></script>

<script>
    $(function() {
        // Ajax global setting
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Animate requests globally
        $(document).ajaxStart(function() {
            topbar.show();
        });

        // Animate the end of requests globally
        $(document).ajaxComplete(function() {
            topbar.hide()
        });

        // iFrame Resizer
        $('iframe').each(index => {
            $($('iframe')[index]).on('load', function () {
                // http://davidjbradshaw.github.io/iframe-resizer/
                $(this).iFrameResize({
                    targetOrigin: '*',
                    sizeWidth: true,
                    sizeHeight: true,
                    autoResize: true,
                    minWidth: '100%',
                    minHeight: 'calc(100vh - 100px)',
                    heightCalculationMethod: (navigator.userAgent.indexOf('MSIE') !== -1) ? 'max' : 'lowestElement', // isOldIE ? 'max' : 'lowestElement';
                    scrolling: true,
                });

                $(this).find('.form-bottom-wrapper').hide();
            })
        })

        $(document).on('click', 'form button[type="submit"]', $.debounce(500, function(event) {
            $(this).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');
            $(this).prop('disabled', true);

            $('form').submit();
        }));

        $('form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                data: new FormData($(this)[0]),
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);

                    $('.toast').toast('show');
                    $('form button[type="submit"]').prop('disabled', false);
                    top.location.reload();
                },
                error: function(error) {
                    console.error(error);
                    $('.toast').find('.toast-body').html(error.responseJSON.message || error.responseJSON.err_msg || 'Unknown error');
                    $('.toast').toast('show');
                    $('form button[type="submit"] span').remove();
                    $('form button[type="submit"]').prop('disabled', false);
                },
            });
        });
    });
</script>

@stack('headjs')
