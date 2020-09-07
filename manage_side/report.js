let query_single_day_revenue = () => {
  fetch('report_php/single_day_revenue.php')
    .then((response) => {
      return response.json()
    })
    .then((myJson) => {
      let CI = myJson[0].CI
      let MaxT = myJson[0].MaxT
      let MinT = myJson[0].MinT
      let PoP = myJson[0].PoP
      let Wx = myJson[0].Wx
      let Wx_id = myJson[0].Wx_id

      let current_weather_box = document.querySelector('#current_weather_box')
      current_weather_box.innerHTML = `
      <div class="card text-center text-white bg-dark">
        <img src="./img/${Wx_id}.svg" class="card-img-top current_weather_img" alt="">
        <div class="card-body">
          <h5 class="card-title">今日</h5>
          <p class="card-text">${Wx}</p>
          <p class="card-text">溫度：${MinT} - ${MaxT}°C</p>
          <p class="card-text">降雨機率：${PoP}%</p>
          <p class="card-text">${CI}</p>
        </div>
      </div>
      `
    })
}


let ctx = document.getElementById('myChart').getContext('2d')
let myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['2020-09-01', '2020-09-02', '2020-09-03', '2020-09-04', '2020-09-05', '2020-09-06', '2020-09-07'],
    datasets: [{
      label: '單日營收',
      data: [120, 19, 3, 5, 2, 3, 100],
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