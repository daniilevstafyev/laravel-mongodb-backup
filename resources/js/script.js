const developersList = [];

document.addEventListener('DOMContentLoaded', function() {
  let addDeveloperForm = document.getElementById('form-add-developer');
  if (addDeveloperForm) {
    addDeveloperForm.addEventListener('submit', onAddDeveloperFormSubmit);
  }
  let finalForm = document.getElementById('form-final');
  if (finalForm) {
    finalForm.addEventListener('submit', onCopyDataSubmit);
  }
});

const onAddDeveloperFormSubmit = function (e) {
  // prevent form submit
  e.preventDefault();

  // get developer name
  let devName = document.getElementById('input-developer').value;


  // generate slug of developer
  let today = new Date();
  devName += '-' + (today.getMonth() + 1);
  devName += '-' + (today.getDate());
  
  // add developer to list
  developersList.push(devName);

  // generate html
  listHtml = generateDevListHTML(developersList);
  document.getElementById('list-developers').innerHTML = listHtml;
  
  // empty input for new one
  document.getElementById('input-developer').value = '';
};

const generateDevListHTML = function(items) {
  html = "";
  for (const item of items) {
    html += `<li class="list-group-item">${item}</li>`;
  }
  return html;
};

const onCopyDataSubmit = function(e) {
  e.preventDefault();
  let form = e.target;
  console.log(form);
  const prodClusterUrl = document.getElementById('prodClusterUrl').value;
  const devClusterUrl = document.getElementById('devClusterUrl').value;
  const databaseName = document.getElementById('databaseName').value;
  let errorMsg = "";
  if (!prodClusterUrl) {
    errorMsg += "Please enter target Monogdb Url.<br/>";
  }
  if (!devClusterUrl) {
    errorMsg += "Please enter destination Monogdb Url.<br/>";
  }
  if (!databaseName) {
    errorMsg += "Please enter database Name.<br/>";
  }
  if (developersList.length === 0) {
    errorMsg += "Please add developers' names.<br/>";
  }

  const errorMsgDiv = document.getElementById('error-msg');
  const successMsgDiv = document.getElementById('success-msg');
  if (errorMsg) {
    // if there are errors, show error message
    errorMsgDiv.innerHTML = errorMsg;
    errorMsgDiv.classList.remove('d-none');
    successMsgDiv.classList.add('d-none');
  } else {
    errorMsgDiv.classList.add('d-none');
    document.getElementById('prodClusterUrlInput').value = prodClusterUrl;
    document.getElementById('devClusterUrlInput').value = devClusterUrl;
    document.getElementById('developersInput').value = JSON.stringify(developersList);
    document.getElementById('dbNameInput').value = databaseName;
    form.submit();
  }
};