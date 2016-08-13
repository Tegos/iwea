<?php echo $header; ?>


<div class="container">
    <div class="breadcrumb">
        <a href="/">Головна</a>
        <span>Аналітика</span>
    </div>
</div>


<div class="fullwidth-block no-padding">
    <div class="container all-text-header">
        <h2 class="section-title">Класифікація даних сайтів прогнозу погоди</h2>

        <br/>
        <!--div id="table-result-input"></div-->

        <div class="text-center">Матриця відстаней між графіками максимальних температур</div>

        <div id="table-result-distance"></div>

        <br/><br/>
        <div id="table-result-group"></div>

        <br/>

        <div class="container" id="groups-chart">
            <div class="pull-left" id="container-chart-group-1"></div>
            <div class="pull-right" id="container-chart-group-2"></div>
        </div>


    </div>

    <hr/>
    <div class="fullwidth-block no-padding light-block-analyze">
        <div class="container all-text-header">
            <h2 class="section-title">Порівняльний аналіз даних прогнозу погоди різних сайтів</h2>

            <span>Інтервал: </span>
            <select id="interval">
                <option value="3">3 дні тому</option>
                <option value="5">5 днів тому</option>
                <option value="7">7 днів тому</option>
            </select>

            <span class="space"></span>
            <!--span>Сайт: </span>
            <select id="site-int">
                <?php foreach($sites as $site) { ?>
                <option><?php echo $site['name']; ?></option>
                <?php } ?>
            </select>

            <span class="space"></span-->
            <button id="analyze" class="button">Ok</button>
            <br/><br/><br/>
            <div id="table-result-analyze"></div>
            <br/>
            <div id="progress-result-analyze"></div>

        </div>
    </div>
    <hr/>



    <div class="fullwidth-block ">
        <div class="container">
            <h2 class="section-title">Різниця температур між різними джерелами</h2>
            <br/>
            <h4>
                Щоб отримати різницю температур, оберіть два джерела зі списку.
                Для вибору, неохідно натиснути на зображення джерела.
            </h4>
        </div>
    </div>
    <br/>
    <div class="forecast-table">
        <div class="container">
            <ul id="source-list-sites">
                <?php foreach ($sites as $site) { ?>
                <li><input value="<?php echo $site['id']; ?>" type="checkbox" id="cb<?php echo $site['id']; ?>"/>
                    <label for="cb<?php echo $site['id']; ?>">
                        <img title="<?php echo $site['name']; ?>"
                             src="<?php echo $site['image_url']; ?>"/></label>
                </li>
                <? } ?>
            </ul>
        </div>
    </div>

    <?php echo $chart_diff; ?>



    <script>
        //var cats = <?php echo $categories;    ?>;
        //var series = <?=$series?>;
        var series_groups = <?=$series_max?>;
        var group_1,group_2;

        function initChartGroups() {
            $('#container-chart-group-1').highcharts({
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: '<?=$city_name?>, t° max, Група 1'
                        },

                        xAxis: {
                            categories: cats
                        },
                        yAxis: {
                            title: {
                                text: 'Температура, °C'
                            },
                            labels: {
                                formatter: function () {
                                    return this.value + '°';
                                }
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true
                        },
                        plotOptions: {
                            spline: {
                                marker: {
                                    radius: 5,
                                    lineColor: '#555555',
                                    lineWidth: 1
                                },
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: group_1 ,
                        exporting: {
                            enabled: false
                        }
                    }
            );

            $('#container-chart-group-2').highcharts({
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: '<?=$city_name?>, t° max, Група 2'
                        },

                        xAxis: {
                            categories: cats
                        },
                        yAxis: {
                            title: {
                                text: 'Температура, °C'
                            },
                            labels: {
                                formatter: function () {
                                    return this.value + '°';
                                }
                            }
                        },
                        tooltip: {
                            crosshairs: true,
                            shared: true
                        },
                        plotOptions: {
                            spline: {
                                marker: {
                                    radius: 5,
                                    lineColor: '#555555',
                                    lineWidth: 1
                                },
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: group_2 ,
                        exporting: {
                            enabled: false
                        }
                    }
            );
        }



    </script>

    <?php echo $footer; ?>

