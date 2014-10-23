/**
 * @fileOverview グラフ表示
 */

/* -------------------------------------------------------------------------- */

$(function () {
    var tableId = 0;
    $('table.climate_data').each(function() {
        var dataTable   = this;
        var subElements = {};
        $('.subelement', this).each(function() {
            var classNames   = $(this).attr('class');
            var buffer1      = classNames.split(/\s+/, 2);
            var buffer2      = buffer1[1].split(/_/, 2);
            var elementId    = buffer2[0];
            var subElementId = buffer2[1];
            if (elementId in subElements) {
                subElements[elementId].push(subElementId);
            } else {
                subElements[elementId] = new Array();
                subElements[elementId].push(subElementId);
            }
        });

        $('.element', this).each(function() {
            var valueElement = $(this).attr('id');
            var graphData    = new Array();
            var seriesLabel  = new Array();
            var axesOptions  = {
                xaxis : {
                    label : '日付',
                    renderer : $.jqplot.DateAxisRenderer,
                    tickOptions : {
                        formatString : '%Y/%m/%d'
                    },
                    autoscale: true
                }
            };

            var yaxisCount = '';
            var unitCheck  = {};

            $.each(subElements[valueElement], function(idxSubElement, valueSubElement){
                var subElementGraphData = new Array();
                var id   = valueElement + '_' + valueSubElement;
                var unit = $('th.unit.'  + id, dataTable).text();
                var dateList  = $('td.date', dataTable);
                var valueList = $('td.value.' + id, dataTable);

                $.each(dateList, function(idxDate, valueDate) {
                    var data = new Array();
                    data.push($(valueDate).text());
                    data.push($(valueList[idxDate]).text() * 1.0);
                    subElementGraphData.push(data);
                });
                graphData.push(subElementGraphData);

                var yaxisName = 'y' + yaxisCount + 'axis';
                if (unit in unitCheck) {
                    yaxisName = 'y' + unitCheck[unit] + 'axis';
                } else {
                    unitCheck[unit] = yaxisCount;
                }

                seriesLabel.push({
                    label : $('th.subelement.' + id, dataTable).text(),
                    yaxis : yaxisName
                });
                axesOptions[yaxisName] = {
                    label : unit,
                    autoscale: true
                }
                if (yaxisCount == '') {
                    yaxisCount = 2;
                } else {
                    yaxisCount ++;
                }
            });

            var label    = $('#' + valueElement).text();
            var chartId  = "chart_" + valueElement + "_" + tableId;
            var chartBox = $("<div>").attr("id", chartId)
                                     .attr("class", "chart");
            $('#graph').append(chartBox);
            var options = {
                series: seriesLabel,
                title : $('caption', dataTable).text() + ' ' + label,
                legend : {
                    show : true,
                    location: 'ne'
                },
                axes : axesOptions
            };

            $.jqplot(chartId, graphData, options);
        });
        tableId ++;
    });
});
