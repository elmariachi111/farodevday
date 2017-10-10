exports.config = {
    helpers: {
      WebDriverIO: {
        // load variables from the environment and provide defaults
        url: "https://www.amazon.com",
        browser: "chrome",
        port: 9515,
        smartWait: 5000
      }
    },
    tests: "./amazon/*_test.js",
    timeout: 10000,
    output: "./amazon/output",
    include: {
      "I": "./steps_file.js"
    },
    bootstrap: false,
    mocha: {},
    name: "codeception"
  };