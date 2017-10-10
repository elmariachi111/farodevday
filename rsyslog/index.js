const express = require('express')
const morgan = require('morgan')

const app = express()

app.use(morgan('combined'))

app.get('/', (req, res) => {
  res.send('Hello World!')
})

app.get('/exc', (req, res, next) => {
    res.send(anUndefindedVariable);
});

app.use(function (err, req, res, next) {
    console.error(err.stack)
    res.status(500).send('Something broke!')
})
  
const port = process.env.PORT || 3000 
app.listen(port, function () {
  console.log(`Example app listening on port ${port}!`)
})