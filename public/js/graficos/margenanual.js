var chart = c3.generate({
    bindto: '#margenanual',
    data: {
      columns: [
        ['M.Bto.', 10000, 20000, 30000]
      ],
       types: {
            'M.Bto.': 'area-spline'
        }
    },
    axis: {
        x: {
            type: 'category',
            categories: ['Año 1', 'Año 2', 'Año 3']
        }
    }
    
});

