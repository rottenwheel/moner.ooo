import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/custom.css';

import Tooltip from 'bootstrap/js/dist/tooltip';

/////
let inactivityTimeout = 30; // in seconds
let fetchInterval = 30; // in seconds
/////

inactivityTimeout = inactivityTimeout * 1000;
fetchInterval = fetchInterval * 1000;

const tooltipTriggerList = Array.from(document.querySelectorAll('[data-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl, { placement: 'top' }));
console.log(tooltipList);

let lastModifiedField = 'xmr';
const exchangeRates = {};

const runConvert = () =>
  lastModifiedField === 'xmr' ? xmrConvert() : fiatConvert();

let updateInterval
const startFetching = () => updateInterval = setInterval(fetchUpdatedExchangeRates, fetchInterval);
const stopFetching = () => {
  clearInterval(updateInterval)
  updateInterval = null;
};

let lastActivity = Date.now()

const resetActivity = () => lastActivity = Date.now()
const checkInactivity = () => {
  if (Date.now() - lastActivity > inactivityTimeout) {
    console.log('Inactivity detected, stopping exchange rate updates');
    stopFetching();
  } else {
    requestAnimationFrame(checkInactivity);
  }
}
document.addEventListener('focus', () => {
  const focused = document.hasFocus();
  console.log(`Page is ${focused ? 'visible' : 'hidden'}`);

  if (focused && !updateInterval) {
    console.log('Restarting exchange rate updates');
    startFetching();

    resetActivity();
    requestAnimationFrame(checkInactivity);
  } else {
    stopFetching();
  }
});
window.addEventListener('mousemove', resetActivity);
window.addEventListener('keydown', resetActivity);
window.addEventListener('touchstart', resetActivity);

requestAnimationFrame(checkInactivity);

document.addEventListener('DOMContentLoaded', () => {
  const copyXMRBtn = document.getElementById('copyXMRBtn');
  const copyFiatBtn = document.getElementById('copyFiatBtn');
  const xmrInput = document.getElementById('xmrInput');
  const fiatInput = document.getElementById('fiatInput');
  const selectBox = document.getElementById('selectBox');
  const convertXMRToFiatBtn = document.getElementById('convertXMRToFiat');
  const convertFiatToXMRBtn = document.getElementById('convertFiatToXMR');
  const fiatButtons = document.querySelectorAll('.fiat-btn');
  const dataSourceButtons = document.querySelectorAll('.data-source-btn')[0]?.getElementsByTagName('a');

  // Add event listeners for the currency buttons
  fiatButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      selectBox.value = button.textContent;
      runConvert();

      // Update the currency in the data source buttons
      if (dataSourceButtons) {
        for (const dataSourceButton of dataSourceButtons) {
          dataSourceButton.href = "?in=" + button.textContent + "&data_source=" + getCurrentDataSource();
        }
      }
      history.pushState(null, '', `?in=${button.textContent}&data_source=${getCurrentDataSource()}`);
    });
  });

  // Add event listeners for the copy buttons
  copyXMRBtn.addEventListener('click', copyToClipboardXMR);
  copyFiatBtn.addEventListener('click', copyToClipboardFiat);

  // Add event listeners for the XMR input field
  xmrInput.addEventListener('change', xmrConvert);
  xmrInput.addEventListener('keyup', () => {
    xmrInput.value = xmrInput.value.replace(/[^\.^,\d]/g, '').replace(/\,/, '.');
    if (xmrInput.value.split('.').length > 2) {
      xmrInput.value = xmrInput.value.slice(0, -1);
    }
    xmrConvert();
  });
  xmrInput.addEventListener('input', () => lastModifiedField = 'xmr');

  // Add event listeners for the fiat input field
  fiatInput.addEventListener('change', fiatConvert);
  fiatInput.addEventListener('keyup', () => {
    fiatInput.value = fiatInput.value.replace(/[^\.^,\d]/g, '').replace(/\,/, '.');
    if (fiatInput.value.split('.').length > 2) {
      fiatInput.value = fiatInput.value.slice(0, -1);
    }
    fiatConvert();
  });
  fiatInput.addEventListener('input', () => lastModifiedField = 'fiat');

  // Add event listener for the select box to change the conversion
  selectBox.addEventListener('change', runConvert);

  // Hide the conversion buttons if JavaScript is enabled
  convertXMRToFiatBtn.style.display = 'none';
  convertFiatToXMRBtn.style.display = 'none';

  // Fetch updated exchange rates immediately, then at the defined interval
  fetchUpdatedExchangeRates(true);
  startFetching();
});

function getCurrentDataSource() {
  // Check for data_source in URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('data_source')) {
    return urlParams.get('data_source');
  }
  // Check for cookie
  const cookieValue = document.cookie
    .split('; ')
    .find(row => row.startsWith('preferred_data_source='));
  if (cookieValue) {
    return cookieValue.split('=')[1];
  }
  // Default to CoinGecko
  return 'coingecko';
}

function fetchUpdatedExchangeRates(showAlert = false) {
  const dataSource = getCurrentDataSource();
  const endpoint = dataSource === 'haveno' ? 'haveno.php' : 'coingecko.php';

  fetch(endpoint)
    .then(response => response.json())
    .then(data => {
      // Update the exchangeRates object with the new values
      for (const [currency, value] of Object.entries(data)) {
        if (currency !== 'time') {
          exchangeRates[currency.toUpperCase()] = value.lastValue;
        }
      }

      updateTimeElement(data.time);

      // Re-execute the appropriate conversion function
      runConvert();
    })
    .catch(e => {
      const msg = `Error fetching exchange rates: ${e}`;
      showAlert ? alert(msg) : console.error(msg);
    });
}

function updateTimeElement(unixTimestamp) {
  const date = new Date(unixTimestamp * 1000);
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');
  const formattedTime = `${hours}:${minutes}:${seconds}`;

  const u = document.querySelector('u');
  u.textContent = formattedTime;
  u.parentElement.innerHTML = u.parentElement.innerHTML.replace('Europe/Berlin', Intl.DateTimeFormat().resolvedOptions().timeZone);
}

function copyToClipboardXMR() {
  const content = document.getElementById('xmrInput');
  content.select();

  // Using deprecated execCommand for compatibility with older browsers
  document.execCommand('copy');
}

function copyToClipboardFiat() {
  const content = document.getElementById('fiatInput');
  content.select();

  // Using deprecated execCommand for compatibility with older browsers
  document.execCommand('copy');
}

function fiatConvert() {
  const fiatAmount = document.getElementById('fiatInput').value;
  const xmrValue = document.getElementById('xmrInput');
  const selectBox = document.getElementById('selectBox').value;

  if (exchangeRates[selectBox]) {
    const value = fiatAmount / exchangeRates[selectBox];
    xmrValue.value = value.toFixed(12);
  }
}

function xmrConvert() {
  const xmrAmount = document.getElementById('xmrInput').value;
  const fiatValue = document.getElementById('fiatInput');
  const selectBox = document.getElementById('selectBox').value;

  if (exchangeRates[selectBox]) {
    const value = xmrAmount * exchangeRates[selectBox];
    fiatValue.value = value.toFixed(['BTC', 'LTC', 'ETH', 'XAG', 'XAU'].includes(selectBox) ? 8 : 2);
  }
}
