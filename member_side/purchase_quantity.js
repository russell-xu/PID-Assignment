let purchase_quantity = document.querySelectorAll('.purchase_quantity')
let username = document.querySelectorAll('.username')
let product_id = document.querySelectorAll('.product_id')
let product_price = document.querySelectorAll('.product_price')
let item_sum_price = document.querySelectorAll('.item_sum_price')

let cart_sum_quantity = document.querySelector('#sum_quantity')
let total_amount = document.querySelector('#total_amount')


for (let i = 0; i < purchase_quantity.length; i++) {
  purchase_quantity[i].addEventListener('change', () => {
    calculate_sum_quantity()
    item_sum_price[i].innerHTML = `$${parseInt(purchase_quantity[i].value) * parseInt(product_price[i].value)}`
    calculate_sum_price()

    fetch('purchase_quantity.php', {
      method: 'POST',
      body: JSON.stringify(
        {
          quantity: purchase_quantity[i].value,
          name: username[i].value,
          id: product_id[i].value
        }
      ),
      headers: new Headers({
        'Content-Type': 'application/json'
      })
    })
  })
}

let calculate_sum_quantity = () => {
  let calculate_cart_sum = 0
  for (let i = 0; i < purchase_quantity.length; i++) {
    calculate_cart_sum += parseInt(purchase_quantity[i].value)
  }
  cart_sum_quantity.innerHTML = calculate_cart_sum
}

let calculate_sum_price = () => {
  let calculate_sum = 0
  for (let i = 0; i < item_sum_price.length; i++) {
    calculate_sum += parseInt(item_sum_price[i].innerHTML.substr(1))
  }
  total_amount.innerHTML = `總金額：$${calculate_sum + 60}`
}