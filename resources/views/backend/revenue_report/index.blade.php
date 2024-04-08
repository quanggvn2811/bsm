@extends('backend.index')

@section('title', 'Basic Stock Manager' . ' | ' . 'Admin Dashboard')

@section('breadcrumb-links')
@endsection

@section('content')
    <div id="page-wrapper">
        @include('includes.messages')
        <div class="main-page">
            <div class="tables">
                <div class="row">
                    <div class="col-md-4 pd-l-0">
                        <div class="short-url-menu">
                            <div class="first">
                                <a href="{{ route('admin.categories.index', $stock->id) }}">{{ $stock->name }}</a>
                            </div>
                            <div class="second">
                                <a href="{{ route('admin.revenue_report.index', $stock->id) }}">Revenue Report</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab">
                    <button class="tablinks active" onclick="openTabContent(event, 'statistical-content')">Statistical</button>
                    <button class="tablinks" onclick="openTabContent(event, 'charts-content')">Charts</button>
                </div>
                <div class="bs-example widget-shadow" data-example-id="contextual-table" style="overflow: auto">
                    <div id="statistical-content" class="tabcontent">
                        <h3 class="data-title">statistical-content</h3>
                        <div class="data-content">
                            {{--<table class="table">
                                <thead>
                                <tr>
                                    <th style="min-width: 92px;" class="order-date">Date</th>
                                    <th style="" class="number-of-failed">Number Of Failed</th>
                                    <th style="" class="failed">Failed</th>
                                    <th style="" class="number-of-revenue">Number Of Revenue</th>
                                    <th style="" class="revenue">Revenue</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>

                                    </tr>
                                </tbody>
                            </table>--}}
                        </div>
                    </div>

                    <div id="charts-content" class="tabcontent" style="display: none">
                        <h3>charts-content</h3>
                        <p>charts-content is the capital of France.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="_orders" value="{{ json_encode($orders) }}">
    <style>
        /* Style the tab */
        .tab {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
        }

        /* Style the buttons inside the tab */
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 17px;
            width: 50%;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current tablink class */
        .tab button.active {
            background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
            padding: 6px 12px;
            /*border: 1px solid #ccc;*/
            border-top: none;
        }
        .statisticalMonth-table th {
            border: 1px solid #32c380 !important;
            text-align: center;
        }
        .statisticalMonth-table td {
            border-right: 1px solid #32c380;
            border-left: 1px solid #32c380;
            text-align: center;
        }
    </style>
    <script>
        {{--var orders = "{{ json_encode($orders) }}"--}}

        function openTabContent(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
    <script src="{{ asset('public/js/main.js')  . '?v=' . config('app.commit_version') }}"></script>
    <script src="{{ asset('public/js/revenue_report.js')  . '?v=' . config('app.commit_version') }}"></script>
@endsection
