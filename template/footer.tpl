<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <p class="colophon">© <?php echo date('Y'); ?>, Михавко Іван</p>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <!--div class="social-links">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-vk"></i></a>
                    <a href="#"><i class="fa fa-google-plus"></i></a>
                </div-->
            </div>
        </div>


    </div>
</footer>
</div>


<!--[if lt IE 9]>
<script src="/assets/js/ie-support/html5.js"></script>
<script src="/assets/js/ie-support/respond.js"></script>
<![endif]-->

<script async src="/assets/js/yepnope.js" onload="loadJSCss();"></script>

<script>
    function loadJSCss() {
        yepnope.injectJs('/assets/js//highcharts.js', function () {
            yepnope.injectJs('/assets/js/exporting.js');
        });

        yepnope.injectJs('/assets/js/jquery-1.11.1.min.js', function () {
            yepnope.injectJs('/assets/js/plugins.js');

            yepnope.injectJs('/assets/js/jquery.easy-autocomplete.js');
            yepnope.injectJs('/assets/js/jquery.ddslick.min.js');
            yepnope.injectJs('/assets/js/app.js');

            yepnope.injectJs('/assets/js/functions.js');

            yepnope.injectJs('/assets/js/main.js');
            yepnope.injectJs('/assets/js/chart_setting.js');
        });

        // css


        yepnope.injectCss('/assets/css/roboto.css', function () {

            yepnope.injectCss('/assets/css/responsive.css');

            yepnope.injectCss('/assets/css/easy-autocomplete.css');
            yepnope.injectCss('/assets/css/style.css');
            yepnope.injectCss('/assets/css/add.css');
            yepnope.injectCss('/assets/fonts/font-awesome.min.css');
        });


    }

</script>

<!--script async defer src="/assets/js/jquery-1.11.1.min.js"></script-->
<!--script src="/assets/js/jquery-ui.js"></script-->

<!--script async defer src="/assets/js/highcharts.js"></script-->
<!--script async defer src="/assets/js/exporting.js"></script-->
<!--script async defer src="/assets/js/plugins.js"></script>
<script async defer src="/assets/js/jquery.easy-autocomplete.js"></script>
<script async defer src="/assets/js/jquery.ddslick.min.js"></script>
<script async defer src="/assets/js/app.js"></script>
<!--script src="/assets/js/path.js"></script-->
<!--script async defer src="/assets/js/graph.js"></script-->
<!--script async defer src="/assets/js/functions.js"></script-->

<!--script async defer src="/assets/js/main.js"></script-->
<!--script async defer src="/assets/js/chart_setting.js"></script-->

<!--script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                },
                i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })
    (window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-80203069-1', 'auto');
    ga('send', 'pageview');



</script-->

<script>
    window.ga = window.ga || function () {
                (ga.q = ga.q || []).push(arguments)
            };
    ga.l = +new Date;
    ga('create', 'UA-80203069-1', 'auto');
    ga('send', 'pageview');
</script>

<!--script async src='//www.google-analytics.com/analytics.js'></script-->
<script async src='/assets/js/analytics.js'></script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter38292185 = new Ya.Metrika({
                    id: 38292185,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true,
                    trackHash: true
                });
            } catch (e) {
            }
        });

        var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () {
                    n.parentNode.insertBefore(s, n);
                };
        s.type = "text/javascript";
        s.async = true;
        s.src = "/assets/js/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/38292185" style="position:absolute; left:-9999px;" alt="Yandex"/></div>
</noscript>
<!-- /Yandex.Metrika counter -->


</body>

<!--link rel="stylesheet" href="/assets/css/easy-autocomplete.css" media="none"
onload="if(media!='all')media='all'"/-->
<!--link rel="stylesheet" href="/assets/css/roboto.css" media="none" onload="if(media!='all')media='all'"/-->
<!--link href="/assets/fonts/font-awesome.min.css" rel="stylesheet" type="text/css" media="none"
      onload="if(media!='all')media='all'"/-->

<!--link rel="stylesheet" href="/assets/css/style.css" media="none" onload="if(media!='all')media='all'"/-->
<!--link rel="stylesheet" href="/assets/css/add.css" media="none" onload="if(media!='all')media='all'"/-->

</html>