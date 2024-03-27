<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="Glance Design Dashboard Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
SmartPhone Compatible web template, free WebDesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" defer src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('template/css/bootstrap.css') }}" rel='stylesheet' type='text/css' />

    <!-- Custom CSS -->
    <link href="{{ asset('template/css/style.css') }}" rel='stylesheet' type='text/css' />

    <!-- font-awesome icons CSS -->
    <link href="{{ asset('template/css/font-awesome.css') }}" rel="stylesheet">
    <!-- //font-awesome icons CSS-->

    <!-- side nav css file -->
    <link href='{{ asset('template/css/SidebarNav.min.css') }}' media='all' rel='stylesheet' type='text/css'/>
    <!-- //side nav css file -->

    <!-- js-->
    <script src="{{ asset('template/js/jquery-1.11.1.min.js') }}"></script>
    <script src="{{ asset('template/js/modernizr.custom.js') }}"></script>

    <!--webfonts-->
    <link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
    <!--//webfonts-->

    <!-- chart -->
    <script src="{{ asset('template/js/Chart.js') }}"></script>
    <!-- //chart -->

    <!-- Metis Menu -->
    <script src="{{ asset('template/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('template/js/custom.js') }}"></script>
    <link href="{{ asset('template/css/custom.css') }}" rel="stylesheet">
    <!--//Metis Menu -->

    <script src="{{ asset('public/js/bootstrap_3.4.1_js_bootstrap.min.js') }}"></script>

    <style>
        #chartdiv {
            width: 100%;
            height: 295px;
        }
        .short-url-menu {
            display: flex;
            align-content: center;
            text-transform: uppercase;
        }
        .short-url-menu .first,
        .short-url-menu .angle-right,
        .short-url-menu .second {
            font-size: 14px;
            font-weight: 550;
            font-style: italic;
            padding: 5px 10px;
            cursor: pointer;
        }
        .short-url-menu a {
            text-decoration: none;
        }
        .short-url-menu .first {
            color: #17a2b8;
            border: solid 1px #17a2b8;
        }
        .short-url-menu .first a {
            color: #17a2b8;
        }
        .short-url-menu .first:hover {
            opacity: .6;
        }

        .short-url-menu .second {
            color: #6c757d;
            border: solid 1px #6c757d;
            border-left: none;
        }
        .short-url-menu .second a {
            color: #6c757d;
        }
        .short-url-menu .second:hover {
            opacity: .6;
        }
        .pd-l-0 {
            padding-left: 0;
        }
    </style>
    <!--pie-chart --><!-- index page sales reviews visitors pie chart -->
    <script src="{{ asset('template/js/pie-chart.js') }}" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $('#demo-pie-1').pieChart({
                barColor: '#2dde98',
                trackColor: '#eee',
                lineCap: 'round',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-2').pieChart({
                barColor: '#8e43e7',
                trackColor: '#eee',
                lineCap: 'butt',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });

            $('#demo-pie-3').pieChart({
                barColor: '#ffc168',
                trackColor: '#eee',
                lineCap: 'square',
                lineWidth: 8,
                onStep: function (from, to, percent) {
                    $(this.element).find('.pie-value').text(Math.round(percent) + '%');
                }
            });


        });

    </script>
    <!-- //pie-chart --><!-- index page sales reviews visitors pie chart -->

    <!-- requried-jsfiles-for owl -->
    <link href="{{ asset('template/css/owl.carousel.css') }}" rel="stylesheet">
    <script src="{{ asset('template/js/owl.carousel.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#owl-demo").owlCarousel({
                items : 3,
                lazyLoad : true,
                autoPlay : true,
                pagination : true,
                nav:true,
            });
        });
    </script>
    <!-- //requried-jsfiles-for owl -->
</head>
<div style="position: fixed; right: 100px; top: 80px; z-index: 9999; display: none" class="alert alert-success bsm-alert">
    <span class="alert-message">Update successfully!</span>
</div>
