const assert = require('chai').assert

Feature('Amazon');

Before(I => {
    I.amOnPage('https://www.amazon.com');
})

Scenario('test amazon checkout', function*(I) {
    
    I.see('Explore Amazon');  
    I.fillField('#nav-search div.nav-search-field input', 'raspberry pi');
    I.click('#nav-search input[type=submit]');
    I.seeElement('h1#s-result-count');
    let productTitle = yield I.grabTextFrom('.s-result-list li.s-result-item:first-of-type h2');
    I.click('.s-result-list li.s-result-item:first-of-type h2');
    I.seeElement('h1#title');
    let detailTitle = yield I.grabTextFrom('h1#title');
    assert.equal(detailTitle, productTitle);
    I.click('#add-to-cart-button');
    I.click('#nav-cart');
    I.see('Proceed to checkout');
    I.click('Proceed to checkout');
    I.see('Sign in');
});
