const express = require('express')
const winston = require('winston')
const winstonExpress = require('express-winston');
const winstonSyslog = require('winston-syslog').Syslog;
const mysql = require('mysql')
const exphbs  = require('express-handlebars');
const redis = require('redis')
const bodyParser = require('body-parser')

const con = mysql.createConnection(process.env.MYSQL_URL);
con.connect()

const app = express()

const syslogTransport = new winston.transports.Syslog({
  host: 'logger',
  protocol: 'udp4',
  port: 514
});

const logger = new (winston.Logger)({
  transports: [ syslogTransport ]
});

app.use(winstonExpress.logger({ //logs all requests
  transports: [ syslogTransport ]
}));

app.use(bodyParser.urlencoded({ extended: false }))
app.use(bodyParser.json())

app.engine('handlebars', exphbs.create({
  helpers: {
    utc: d => { return d.toUTCString(); }
  }
}).engine); //handlebars
app.set('view engine', 'handlebars');
app.set('views', './views');

app.get('/', (req, res) => {
  logger.info("homepage");
  con.query('SELECT * FROM messages ', (error, results, fields) => {
    res.render('index', {
      messages: results
    });
  });
})

app.post('/msg', (req, res) => {
  const message = { message: req.body.message };
  const query = con.query(
    'INSERT INTO messages SET ?', message, (error, results, fields) => {
      logger.info("message created: " + message.message);
      if (error) throw error;
      res.redirect("/");
  });
})

app.get('/exc', (req, res, next) => {
    res.send(anUndefindedVariable);
});

app.use(function (err, req, res, next) {
    logger.error(err.stack);
    res.status(500).send('Something broke!')
})
  
const port = process.env.PORT || 3000 
app.listen(port, function () {
  console.log(`Example app listening on port ${port}!`)
})