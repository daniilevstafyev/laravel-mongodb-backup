const developersList = [];

document.addEventListener('DOMContentLoaded', function() {
  let addDeveloperForm = document.getElementById('form-add-developer');
  addDeveloperForm.addEventListener('submit', onAddDeveloperFormSubmit);
  let btnCopyData = document.getElementById('btn-copy-data');
  btnCopyData.addEventListener('click', onCopyDataSubmit);
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

const onCopyDataSubmit = function() {
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
    const token = document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/connect", true); 
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("X-CSRF-TOKEN", token);
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        // Response
        var response = this.responseText;
        successMsgDiv.innerHTML = response;
        successMsgDiv.classList.remove('d-none');
        errorMsgDiv.classList.add('d-none');
      } else {
        errorMsgDiv.innerHTML = "Connection Failed Or Something went wrong.";
        errorMsgDiv.classList.remove('d-none');
        successMsgDiv.classList.add('d-none');
      }
    };
    var data = {
      prodClusterUrl: prodClusterUrl,
      devClusterUrl: devClusterUrl,
      developers: developersList,
      dbName: databaseName,
    };
    xhttp.send(JSON.stringify(data));
  }
};