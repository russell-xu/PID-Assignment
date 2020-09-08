document.addEventListener("DOMContentLoaded", function () {
  let start_month = document.getElementById('start_month')
  let end_month = document.getElementById('end_month')

  let date = new Date()
  let yyyy = date.getFullYear()
  let mm = (date.getMonth() + 1 < 10 ? '0' : '') + (date.getMonth() + 1)

  let current_month = `${yyyy}-${mm}`
  end_month.value = current_month

  let query_month_revenue = () => {
    fetch('report_php/month_revenue.php', {
      method: 'POST',
      body: JSON.stringify(
        {
          start_month: start_month.value,
          end_month: end_month.value
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
        const month_range = []
        const month_range_price = []

        for (let i = 0; i < myJson.length; i++) {
          month_range.push(myJson[i]['monthtime'])
          month_range_price.push(myJson[i]['total_price'])
        }

        let canvas_box = document.getElementById('month_canvas_box')
        canvas_box.innerHTML = '<canvas id="month_chart"></canvas>'

        let ctx = document.getElementById('month_chart').getContext('2d')
        let month_chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: month_range,
            datasets: [{
              label: '月營收',
              data: month_range_price,
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            }]
          },
          options: {
            title: {
              display: true,
              fontSize: 20,
              text: '月營收'
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
  query_month_revenue()

  let month_submit = document.getElementById('month_submit')
  month_submit.addEventListener('click', () => { query_month_revenue()

    console.log(start_month.value); })
})
