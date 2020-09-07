let query_single_day_revenue = () => {
  fetch('report_php/single_day_revenue.php')
    .then((response) => {
      return response.json()
    })
    .then((myJson) => {
      const date_range = []
      const date_range_price = []

      for (let i = 0; i < myJson.length; i++) {
        date_range.push(myJson[i]['datetime'])
        date_range_price.push(myJson[i]['total_price'])
      }

      let ctx = document.getElementById('myChart').getContext('2d')
      let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: date_range,
          datasets: [{
            label: '單日營收',
            data: date_range_price,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          title: {
            display: true,
            fontSize: 20,
            text: '單日營收'
          },
          legend: {
            display: false,
            labels: {
              font: {
                color: 'black',
                size: 30
              }
            }
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      })
    })
}
query_single_day_revenue()

