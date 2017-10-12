const express = require('express')
const morgan = require('morgan')
const mysql = require('mysql')
const redis = require('redis')
var bodyParser = require('body-parser')

const app = express()
const con = mysql.createConnection(process.env.MYSQL_URL);
con.connect()

app.use(morgan('combined'))

app.use(bodyParser.urlencoded({ extended: false }))
app.use(bodyParser.json())

app.get('/', (req, res) => {
  res.send('Hello World!')
})

app.post('/msg', (req, res) => {
  const message = { message: req.body.message};
  const query = con.query(
    'INSERT INTO messages SET ?', message, (error, results, fields) => {
      res.send(200);
      if (error) throw error;
  });
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