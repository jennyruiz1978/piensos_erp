var chart = c3.generate({
    bindto: '#ventasmensuales',
    data: {
      columns: [
        ['Año 1', 100, 200, 100, 200, 150, 250, 100, 150, 200, 250, 350, 210],
        ['Año 2', 200, 300, 200, 300, 250, 350, 250, 200, 350, 300, 400, 350],
        ['Año 3', 350, 400, 300, 400, 350, 450, 300, 350, 500, 450, 550, 400]
      ],
      type: "bar"
    },
    axis: {
        x: {
            type: 'category',
            categories: ['E', 'F', 'M', 'A', 'M', 'J', 'JL', 'A', 'S', 'O', 'N', 'D']
        }
    }
    
});




