const express = require('express')
const winston = require('winston')
const winstonExpress = require('express-winston');
const winstonSyslog = require('winston-syslog').Syslog;
const mysql = require('mysql')
const redis = require('redis')
const bodyParser = require('body-parser')

const app = express()
const con = mysql.createConnection(process.env.MYSQL_URL);
con.connect()

app.use(winstonExpress.logger({
  transports: [
    new winston.transports.Syslog({
      host: 'logger',
      protocol: 'udp4',
      port: 514
    })
  ]
}));

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