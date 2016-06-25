var charts = {
    activity: null,
    country: null,
};
var filter = {
    type: null,
    value: null
};

var requestChart = function(type) {
    var url = "api/cs/total/" + type;
    if (filter.type && filter.type != type) {
        url += "/" + filter.type + "/" + filter.value;
    }

    console.log(url);
    $.get(url, function(data) {
        drawChart(type, data.results)
    })
};

var drawChart = function (type, data) {


    var chartData = new google.visualization.DataTable();
    chartData.addColumn('string', type);
    chartData.addColumn('number', 'Amount');
    chartData.addColumn({type:'string', label:'name' });

    chartData.addRows(objectToArray(data));

    for (var i = 0; i < data.length; i++) {
        chartData.setFormattedValue(i, 1, chartData.getValue(i, 1).formatMoney(0, ".", " "));
    }

    /*
    var total = google.visualization.data.group(chartData, [{
        type: 'boolean',
        column: 0,
        modifier: function () {return true;}
    }], [{
        type: 'number',
        column: 1,
        aggregation: google.visualization.data.sum
    }]);


    chartData.addRow(['Total: ' + total.getValue(0, 1).formatMoney(0, ".", " "), 0, "total"]);
    */

    var options = {
        //width: 600,
        height: 250,
        chartArea: {
            left: 0,
            top: 0,
            width: '100%',
            height: '100%'
        },
        pieSliceText: 'value',
        legend: {
            position: 'labeled',
            labeledValueText: 'value',
            textStyle: {
                //fontSize: 14,
                bold: false
            }
        },

        sliceVisibilityThreshold: 0
    };

    if (!charts[type]) {
        charts[type] = new google.visualization.PieChart(document.getElementById(type + 'Chart'));
    }

    google.visualization.events.removeAllListeners(charts[type]);

    google.visualization.events.addListener(charts[type], 'select', function() {
        var selectedItem = charts[type].getSelection()[0];
        if (selectedItem) {
            var value = chartData.getValue(selectedItem.row, 2);
            filter.type = type;
            filter.value = value;
            console.log(charts[type].getSelection());

            $.each(charts, function(key, value) {
                requestChart(key);
            });
        }
    });

    charts[type].draw(chartData, options);

};

var objectToArray = function (dataObject) {
    var arr = [];
    for (var i = 0; i < dataObject.length; i++) {
        arr.push(
            Object.keys(dataObject[i]).map(function(k) {
                var value = dataObject[i][k];
                var float = parseFloat(value);
                if (!isNaN(float)) {
                    return Math.abs(float);
                } else {
                    return value;
                }

            })
        );
    }

    return arr;
};

Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "") + " Kč";
};

$(function() {
    requestChart("activity");
    requestChart("country");
});
