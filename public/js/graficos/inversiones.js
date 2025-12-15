var chart = c3.generate({
    bindto: '#inversiones',
    data: {
        // iris data from R
        columns: [
            ['G. Amort.', 30],
            ['Inmovil.', 120]
        ],
        type: 'pie',
        onclick: function (d, i) {
            console.log("onclick", d, i);
        },
        onmouseover: function (d, i) {
            console.log("onmouseover", d, i);
        },
        onmouseout: function (d, i) {
            console.log("onmouseout", d, i);
        }
    }
});











