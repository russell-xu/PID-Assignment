let start_date = document.getElementById('start_date')
let end_date = document.getElementById('end_date')

let date = new Date()
let yyyy = date.getFullYear()
let mm = (date.getMonth() + 1 < 10 ? '0' : '') + (date.getMonth() + 1)
let dd = (date.getDate() < 10 ? '0' : '') + date.getDate()

let today = `${yyyy}-${mm}-${dd}`
end_date.value = today

let query_single_day_revenue = () => {
  fetch('report_php/single_day_revenue.php', {
    method: 'POST',
    body: JSON.stringify(
      {
        start_date: start_date.value,
        end_date: end_date.value
      }
    ),
    headers: new Headers({
      'Content-Type': 'application/json'
    })
  })
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

      let canvas_box = document.getElementById('canvas_box')
      canvas_box.innerHTML = '<canvas id="myChart"></canvas>'

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

let day_submit = document.getElementById('day_submit')
day_submit.addEventListener('click', () => { query_single_day_revenue() })


